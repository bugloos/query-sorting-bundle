<?php

namespace Bugloos\QuerySortingBundle\Tests\Unit\DependencyInjection;

use Bugloos\QuerySortingBundle\DependencyInjection\QuerySortingExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
final class QuerySortingExtensionTest extends AbstractExtensionTestCase
{
    public function test_default_config(): void
    {
        $this->load();

        $this->assertTrue($this->container->getDefinition('bugloos_query_sorting.query_sorting')->isPublic());
        $this->assertNotEmpty($this->container->getDefinition('bugloos_query_sorting.query_sorting')->getArguments());

        $this->assertFalse($this->container->getDefinition('bugloos_query_sorting.query_sorting_handler_factory.sorting_factory')->isPublic());
        $this->assertNotEmpty($this->container->getDefinition('bugloos_query_sorting.query_sorting_handler_factory.sorting_factory')->getArguments());
    }

    protected function getContainerExtensions(): array
    {
        return [new QuerySortingExtension()];
    }
}
