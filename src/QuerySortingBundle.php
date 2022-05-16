<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle;

use Bugloos\QuerySortingBundle\DependencyInjection\QuerySortingExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class QuerySortingBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new QuerySortingExtension();
        }

        return $this->extension;
    }
}
