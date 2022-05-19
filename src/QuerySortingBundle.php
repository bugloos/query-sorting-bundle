<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle;

use Bugloos\QuerySortingBundle\DependencyInjection\QuerySortingExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class QuerySortingBundle extends Bundle
{
    /**
     * @return ExtensionInterface|QuerySortingExtension
     *
     * @author Milad Ghofrani <milad.g@bugloos.com>
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new QuerySortingExtension();
        }

        return $this->extension;
    }
}
