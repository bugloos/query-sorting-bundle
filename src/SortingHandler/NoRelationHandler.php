<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler;

use Bugloos\QuerySortingBundle\SortingHandler\Contract\AbstractSortingHandler;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class NoRelationHandler extends AbstractSortingHandler
{
    public function getSortProperty($rootAlias, $rootClass, $relationsAndFieldName): string
    {
        $alias = $rootAlias;
        $field = $relationsAndFieldName[0];

        $this->validateFieldNames($field, $rootClass);

        return $this->createOrderBySyntax($alias, $field);
    }
}
