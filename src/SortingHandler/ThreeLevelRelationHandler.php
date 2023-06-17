<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\AbstractSortingHandler;
use Bugloos\QuerySortingBundle\SortingHandler\Contract\WithRelationInterface;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class ThreeLevelRelationHandler extends AbstractSortingHandler implements WithRelationInterface
{
    public function getSortProperty($rootAlias, $rootClass, $relationsAndFieldName): string
    {
        [$relationAlias, $secondRelationAlias, $thirdRelationAlias, $subRelationField] = $relationsAndFieldName;

        $this->validateRelationNames($relationAlias, $rootClass);

        $relationClass = $this->getRelationClass($rootClass, $relationAlias);
        $this->validateRelationNames($secondRelationAlias, $relationClass);

        $secondRelationClass = $this->getRelationClass($relationClass, $secondRelationAlias);
        $this->validateRelationNames($subRelationField, $secondRelationClass);

        $thirdRelationClass = $this->getRelationClass($secondRelationClass, $thirdRelationAlias);
        $this->validateFieldNames($subRelationField, $thirdRelationClass);

        return $this->createOrderBySyntax($thirdRelationAlias, $subRelationField);
    }

    public function getRelationJoin($relationJoins, $rootAlias, $rootClass, $relationsAndFieldName): array
    {
        [$relationAlias, $subRelationAlias, $thirdRelationAlias] = $relationsAndFieldName;

        $relationJoins = $this->addRelationJoin($relationJoins, $rootAlias, $relationAlias);

        $relationJoins = $this->addRelationJoin($relationJoins, $relationAlias, $subRelationAlias);

        return $this->addRelationJoin($relationJoins, $subRelationAlias, $thirdRelationAlias);
    }
}
