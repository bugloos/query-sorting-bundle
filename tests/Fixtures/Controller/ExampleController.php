<?php

namespace Bugloos\QuerySortingBundle\Tests\Fixtures\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Bugloos\QuerySortingBundle\Service\QuerySorting;
use Bugloos\QuerySortingBundle\Tests\Fixtures\Entity\Book;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class ExampleController extends AbstractController
{
    private QuerySorting $querySorting;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        QuerySorting $querySorting
    ) {
        $this->entityManager = $entityManager;
        $this->querySorting = $querySorting;
    }

    /**
     * @Route("/api/sort/books", methods={"GET"})
     *
     * @param Request $request
     *
     * @throws Exception
     * @throws InvalidArgumentException
     *
     * @return JsonResponse
     */
    public function sortBooks(Request $request): JsonResponse
    {
        $queryBuilder = $this->entityManager->getRepository(Book::class)->createQueryBuilder('b');

        $queryBuilder = $this->querySorting->for($queryBuilder)
            ->parameters($request->get('order'))
            ->addMapper('country', 'country.name')
            ->addMapper('userAge', 'bookUsers.user.age')
            ->addMapper('wrongRelation', 'wrong.name')
            ->mappers([])
            ->cacheTime(100)
            ->sort()
        ;

        return new JsonResponse($queryBuilder->getQuery()->getArrayResult());
    }

    /**
     * @Route("/api/sort/books/multiple-mappers", methods={"GET"})
     *
     * @param Request $request
     *
     * @throws Exception
     * @throws InvalidArgumentException
     *
     * @return JsonResponse
     */
    public function sortBooksWithMultipleMappers(Request $request): JsonResponse
    {
        $queryBuilder = $this->entityManager->getRepository(Book::class)->createQueryBuilder('b');

        $queryBuilder = $this->querySorting->for($queryBuilder)
            ->parameters($request->get('order'))
            ->mappers([
                'country' => 'country.name',
                'userAge' => 'bookUsers.user.age',
                'wrongRelation' => 'wrong.name',
                'emptyValue' => '',
            ])
            ->cacheTime(100)
            ->sort()
        ;

        return new JsonResponse($queryBuilder->getQuery()->getArrayResult());
    }
}
