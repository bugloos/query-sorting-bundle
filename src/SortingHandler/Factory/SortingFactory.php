<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler\Factory;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\AbstractSortingHandler;
use Bugloos\QuerySortingBundle\SortingHandler\NoRelationHandler;
use Bugloos\QuerySortingBundle\SortingHandler\OneLevelRelationHandler;
use Bugloos\QuerySortingBundle\SortingHandler\TwoLevelRelationHandler;
use Bugloos\QuerySortingBundle\SortingHandler\ThreeLevelRelationHandler;
use Bugloos\QuerySortingBundle\SortingHandler\FourLevelRelationHandler;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 * @contributor Amin Khoshzahmat <aminkhoshzahmat@gmail.com>
 */
class SortingFactory
{
    private const HANDLER_MAP = [
        1 => NoRelationHandler::class,
        2 => OneLevelRelationHandler::class,
        3 => TwoLevelRelationHandler::class,
        4 => ThreeLevelRelationHandler::class,
        5 => FourLevelRelationHandler::class,
    ];

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSortingHandler(array $relationsAndFieldName): AbstractSortingHandler
    {
        $count = \count($relationsAndFieldName);

        if (!isset(self::HANDLER_MAP[$count])) {
            throw new \RuntimeException(
                sprintf('Unsupported relation level: %d. This Bundle supports up to a maximum of four level relations.', $count)
            );
        }

        $handlerClass = self::HANDLER_MAP[$count];
        return new $handlerClass($this->entityManager);
    }
}
