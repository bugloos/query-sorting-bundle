<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\SortingHandler\Contract;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
interface WithRelationInterface
{
    public function getRelationJoin($relationJoins, $rootAlias, $rootClass, $relationsAndFieldName): array;
}
