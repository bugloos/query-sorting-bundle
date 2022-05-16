<?php

declare(strict_types=1);

namespace Bugloos\QuerySortingBundle\Tests;

use Bugloos\QuerySortingBundle\QuerySortingBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

class QuerySortingTestKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new ZenstruckFoundryBundle(),
            new DoctrineBundle(),
            new FrameworkBundle(),
            new QuerySortingBundle(),
        ];
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/../src/Resources/config/services.xml');
        $loader->load(__DIR__.'/Fixtures/Config/services.yaml');
        $loader->load(__DIR__.'/Fixtures/Config/config.yaml');
    }
}
