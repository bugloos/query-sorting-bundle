<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\AbstractSortingHandler;
use Bugloos\QuerySortingBundle\SortingHandler\Contract\WithRelationInterface;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class OneLevelRelationHandler extends AbstractSortingHandler implements WithRelationInterface
{
    public function getSortProperty($rootAlias, $rootClass, $relationsAndFieldName): string
    {
        [$relationAlias, $relationField] = $relationsAndFieldName;

        $this->validateRelationNames($relationAlias, $rootClass);

        $relationClass = $this->getRelationClass($rootClass, $relationAlias);
        $this->validateFieldNames($relationField, $relationClass);

        return $this->createOrderBySyntax($relationAlias, $relationField);
    }

    public function getRelationJoin($relationJoins, $rootAlias, $rootClass, $relationsAndFieldName): array
    {
        [$relationAlias] = $relationsAndFieldName;

        return $this->addRelationJoin($relationJoins, $rootAlias, $relationAlias);
    }
}
