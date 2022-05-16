<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\Service;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\WithRelationInterface;
use Bugloos\QuerySortingBundle\SortingHandler\Factory\SortingFactory;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use JsonException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class QuerySorting
{
    private const DEFAULT_CACHE_TIME = 3600;

    private const SEPARATOR = '.';

    private EntityManagerInterface $entityManager;

    private SortingFactory $sortingFactory;

    private CacheInterface $cache;

    private string $rootAlias;

    private string $rootEntity;

    private ClassMetadata $rootClass;

    private QueryBuilder $query;

    private string $cacheKey;

    private array $orders = [];

    private array $mapper = [];

    private ?int $cacheTime = null;

    private $defaultCacheTime;

    private $separator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CacheInterface $cache,
        SortingFactory $sortingFactory,
        $defaultCacheTime = self::DEFAULT_CACHE_TIME,
        $separator = self::SEPARATOR
    ) {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
        $this->sortingFactory = $sortingFactory;
        $this->defaultCacheTime = $defaultCacheTime;
        $this->separator = $separator;
    }

    public function for(QueryBuilder $query): self
    {
        $this->initializeRootQueryConfig($query);

        return $this;
    }

    /**
     * @param mixed $orders
     *
     * @throws JsonException
     */
    public function parameters($orders): self
    {
        if (empty($orders)) {
            return $this;
        }

        if (!\is_array($orders)) {
            throw new \InvalidArgumentException(
                'Order parameters should be an array type'
            );
        }

        // Remove empty value from array
        $this->orders = array_filter($orders);

        // Create cache key by request
        $this->createCacheKey($this->orders);

        return $this;
    }

    public function mappers(array $mappers): self
    {
        if (empty($mappers)) {
            return $this;
        }

        foreach ($mappers as $parameter => $mapper) {
            $this->addMapper($parameter, $mapper);
        }

        return $this;
    }

    public function addMapper(string $parameter, string $mapper): self
    {
        if (empty($parameter) || empty($mapper)) {
            return $this;
        }

        $this->mapper[$parameter] = $mapper;

        return $this;
    }

    public function cacheTime(int $cacheTime): self
    {
        $this->cacheTime = $cacheTime;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function sort(): QueryBuilder
    {
        // Early return if array is empty
        if (empty($this->orders)) {
            return $this->query;
        }

        // Calculate and cache fields.
        [$sortItems, $relationJoins] = $this->cache->get(
            $this->cacheKey,
            $this->addSorting()
        );

        $this->applyRelationJoinToQuery($relationJoins);

        $this->applyOrdersToQuery($sortItems);

        return $this->query;
    }

    private function applyOrdersToQuery($orderItems): void
    {
        foreach ($orderItems as $order => $type) {
            if ($order === array_key_first($orderItems)) {
                $this->query->orderBy($order, $type);
            } else {
                $this->query->addOrderBy($order, $type);
            }
        }
    }

    private function applyRelationJoinToQuery($relationJoins): void
    {
        // Remove exist joined from a list
        $filteredJoins = array_diff($relationJoins, $this->query->getAllAliases());

        // Add a left join to query which does not exist in the query
        if (!empty($filteredJoins)) {
            foreach ($filteredJoins as $property => $column) {
                $this->query->addSelect($column);
                $this->query->leftJoin($property, $column);
            }
        }
    }

    /**
     * @param mixed $array
     *
     * @throws JsonException
     */
    private function createCacheKey($array): void
    {
        $this->cacheKey = md5($this->rootEntity . json_encode($array, \JSON_THROW_ON_ERROR));
    }

    private function addSorting(): Closure
    {
        return function (CacheItemInterface $item) {
            $item->expiresAfter($this->cacheTime ?: $this->defaultCacheTime);

            $sortItems = [];
            $relationJoins = [];

            foreach ($this->orders as $parameter => $sortType) {
                // Check $parameter exists in mapper
                $parameter = (\array_key_exists($parameter, $this->mapper))
                    ? $this->mapper[$parameter] : $parameter;

                $relationsAndFieldName = explode($this->separator, $parameter);

                $sortingHandler = $this->sortingFactory->createSortingHandler($relationsAndFieldName);

                $sortProperty = $sortingHandler->getSortProperty(
                    $this->rootAlias,
                    $this->rootClass,
                    $relationsAndFieldName
                );

                $sortItems[$sortProperty] = $sortType;

                if ($sortingHandler instanceof WithRelationInterface) {
                    $relationJoins = $sortingHandler->getRelationJoin(
                        $relationJoins,
                        $this->rootAlias,
                        $this->rootClass,
                        $relationsAndFieldName
                    );
                }
            }

            return [$sortItems, $relationJoins];
        };
    }

    private function initializeRootQueryConfig($query): void
    {
        $rootEntities = $query->getRootEntities();
        $rootAliasArray = $query->getRootAliases();

        if (!isset($rootEntities[0], $rootAliasArray[0])) {
            throw new \InvalidArgumentException('Root Alias not defined correctly.');
        }

        $this->query = $query;
        $this->rootAlias = $rootAliasArray[0];
        $this->rootEntity = $rootEntities[0];
        $this->rootClass = $this->entityManager->getClassMetadata($this->rootEntity);
    }
}
