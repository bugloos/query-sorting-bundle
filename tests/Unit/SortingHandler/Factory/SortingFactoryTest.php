<?php

namespace Bugloos\QuerySortingBundle\Tests\Unit\SortingHandler\Factory;

use Bugloos\QuerySortingBundle\SortingHandler\Factory\SortingFactory;
use Bugloos\QuerySortingBundle\SortingHandler\NoRelationHandler;
use Bugloos\QuerySortingBundle\SortingHandler\OneLevelRelationHandler;
use Bugloos\QuerySortingBundle\SortingHandler\TwoLevelRelationHandler;
use PHPUnit\Framework\TestCase;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class SortingFactoryTest extends TestCase
{
    public function test_relation_handler_no_relation(): void
    {
        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sortingFactory = new SortingFactory($entityManager);

        $relationHandler = $sortingFactory->createSortingHandler(['title']);

        self::assertInstanceOf(NoRelationHandler::class, $relationHandler);
    }

    public function test_relation_handler_with_one_level_relation(): void
    {
        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sortingFactory = new SortingFactory($entityManager);

        $relationHandler = $sortingFactory->createSortingHandler(['country', 'name']);

        self::assertInstanceOf(OneLevelRelationHandler::class, $relationHandler);
    }

    public function test_relation_handler_with_two_level_relation(): void
    {
        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sortingFactory = new SortingFactory($entityManager);

        $relationHandler = $sortingFactory->createSortingHandler(['bookUsers', 'user', 'age']);

        self::assertInstanceOf(TwoLevelRelationHandler::class, $relationHandler);
    }

    public function test_relation_handler_with_an_exception_when_need_relation_more_than_two_level(): void
    {
        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sortingFactory = new SortingFactory($entityManager);

        $this->expectException(\RuntimeException::class);

        $sortingFactory->createSortingHandler(['bookUsers', 'user', 'country', 'name']);
    }
}
