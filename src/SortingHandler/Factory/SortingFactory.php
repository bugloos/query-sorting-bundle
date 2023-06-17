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
 */
class SortingFactory
{
    private const NO_RELATION = 1;
    private const ONE_LEVEL_RELATION = 2;
    private const TWO_LEVEL_RELATION = 3;
    private const THREE_LEVEL_RELATION = 4;
    private const FOUR_LEVEL_RELATION = 5;

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSortingHandler(array $relationsAndFieldName): AbstractSortingHandler
    {
        switch (\count($relationsAndFieldName)) {
            case self::NO_RELATION:
                return new NoRelationHandler($this->entityManager);

            case self::ONE_LEVEL_RELATION:
                return new OneLevelRelationHandler($this->entityManager);

            case self::TWO_LEVEL_RELATION:
                return new TwoLevelRelationHandler($this->entityManager);

            case self::THREE_LEVEL_RELATION:
                return new ThreeLevelRelationHandler($this->entityManager);

            case self::FOUR_LEVEL_RELATION:
                return new FourLevelRelationHandler($this->entityManager);

            default:
                throw new \RuntimeException(
                    'This Bundle just support maximum two level relation'
                );
        }
    }
}
