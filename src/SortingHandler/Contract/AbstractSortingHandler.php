<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler\Contract;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
abstract class AbstractSortingHandler
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function getSortProperty($rootAlias, $rootClass, $relationsAndFieldName): string;

    protected function validateFieldNames($field, $class): void
    {
        if (!\in_array($field, $class->getFieldNames(), true)) {
            throw new \InvalidArgumentException(
                'You have selected the wrong field for sorting'
            );
        }
    }

    protected function validateRelationNames($relationProperty, $class): void
    {
        if (!\in_array($relationProperty, $class->getAssociationNames(), true)) {
            throw new \InvalidArgumentException(
                'You have selected the wrong relation for sorting'
            );
        }
    }

    protected function getRelationClass($class, $alias): ClassMetadata
    {
        $relationEntity = $class->getAssociationMapping($alias)['targetEntity'];

        return $this->entityManager->getClassMetadata($relationEntity);
    }

    protected function addRelationJoin($relationJoins, $alias, $property)
    {
        $relationJoins[sprintf('%s.%s', $alias, $property)] = $property;

        return $relationJoins;
    }

    protected function createOrderBySyntax($alias, $column): string
    {
        return sprintf('%s.%s', $alias, $column);
    }
}
