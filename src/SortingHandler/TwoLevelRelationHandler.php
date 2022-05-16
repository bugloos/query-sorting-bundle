<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\AbstractSortingHandler;
use Bugloos\QuerySortingBundle\SortingHandler\Contract\WithRelationInterface;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class TwoLevelRelationHandler extends AbstractSortingHandler implements WithRelationInterface
{
    public function getSortProperty($rootAlias, $rootClass, $relationsAndFieldName): string
    {
        [$relationAlias, $subRelationAlias, $subRelationField] = $relationsAndFieldName;

        $this->validateRelationNames($relationAlias, $rootClass);

        $relationClass = $this->getRelationClass($rootClass, $relationAlias);
        $this->validateRelationNames($subRelationAlias, $relationClass);

        $subRelationClass = $this->getRelationClass($relationClass, $subRelationAlias);
        $this->validateFieldNames($subRelationField, $subRelationClass);

        return $this->createOrderBySyntax($subRelationAlias, $subRelationField);
    }

    public function getRelationJoin($relationJoins, $rootAlias, $rootClass, $relationsAndFieldName): array
    {
        [$relationAlias, $subRelationAlias] = $relationsAndFieldName;

        $relationJoins = $this->addRelationJoin($relationJoins, $rootAlias, $relationAlias);

        return $this->addRelationJoin($relationJoins, $relationAlias, $subRelationAlias);
    }
}
