<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\AbstractSortingHandler;
use Bugloos\QuerySortingBundle\SortingHandler\Contract\WithRelationInterface;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class FourLevelRelationHandler extends AbstractSortingHandler implements WithRelationInterface
{
    public function getSortProperty($rootAlias, $rootClass, $relationsAndFieldName): string
    {
        [$relationAlias, $secondRelationAlias, $thirdRelationAlias, $fourthRelationAlias, $subRelationField] = $relationsAndFieldName;

        $this->validateRelationNames($relationAlias, $rootClass);

        $relationClass = $this->getRelationClass($rootClass, $relationAlias);
        $this->validateRelationNames($secondRelationAlias, $relationClass);

        $secondRelationClass = $this->getRelationClass($relationClass, $secondRelationAlias);
        $this->validateRelationNames($subRelationField, $secondRelationClass);

        $thirdRelationClass = $this->getRelationClass($secondRelationClass, $thirdRelationAlias);
        $this->validateFieldNames($subRelationField, $thirdRelationClass);

        $fourthRelationClass = $this->getRelationClass($thirdRelationClass, $fourthRelationAlias);
        $this->validateFieldNames($subRelationField, $fourthRelationClass);

        return $this->createOrderBySyntax($fourthRelationAlias, $subRelationField);
    }

    public function getRelationJoin($relationJoins, $rootAlias, $rootClass, $relationsAndFieldName): array
    {
        [$relationAlias, $subRelationAlias, $thirdRelationAlias, $fourthRelationAlias] = $relationsAndFieldName;

        $relationJoins = $this->addRelationJoin($relationJoins, $rootAlias, $relationAlias);

        $relationJoins = $this->addRelationJoin($relationJoins, $relationAlias, $subRelationAlias);

        $relationJoins = $this->addRelationJoin($relationJoins, $subRelationAlias, $thirdRelationAlias);

        return $this->addRelationJoin($relationJoins, $thirdRelationAlias, $fourthRelationAlias);
    }
}
