<?php

namespace Bugloos\QuerySortingBundle\Tests\Functional;

use Exception;
use Bugloos\QuerySortingBundle\Enum\SortType;
use Bugloos\QuerySortingBundle\Tests\Fixtures\Story\BookUserCollectionStory;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @author Milad Ghofrani <milad.ghofrani@gmail.com>
 */
class ExampleControllerTest extends WebTestCase
{
    use Factories;
    use HttpClientTrait;
    use MailerAssertionsTrait;
    use ResetDatabase;
    use WebTestAssertionsTrait;

    /**
     * @throws Exception
     */
    public function test_no_order_send_to_query_builder(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $client->request('GET', '/api/sort/books');
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);
    }

    /**
     * @throws Exception
     */
    public function test_it_can_sort_native_field_with_no_relation(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'price' => SortType::ASC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);

        foreach ($bookCollection as $i => $book) {
            if ($i < \count($bookCollection) - 1) {
                self::assertGreaterThanOrEqual($book['price'], $bookCollection[$i + 1]['price']);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function test_it_can_sort_relation_field_with_one_level_relation(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'country' => SortType::ASC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);

        foreach ($bookCollection as $i => $book) {
            if ($i < \count($bookCollection) - 1) {
                self::assertLessThanOrEqual(0, strcmp($book['country']['name'], $bookCollection[$i + 1]['country']['name']));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function test_it_can_sort_relation_field_with_two_level_relation(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'userAge' => SortType::ASC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);

        foreach ($bookCollection as $i => $book) {
            if ($i < \count($bookCollection) - 1 && \count($book['bookUsers'])) {
                self::assertGreaterThanOrEqual($book['bookUsers'][0]['user']['age'], $bookCollection[$i + 1]['bookUsers'][0]['user']['age']);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function test_it_can_sort_relation_field_with_two_level_relation_with_multiple_mappers(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'userAge' => SortType::ASC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books/multiple-mappers', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);

        foreach ($bookCollection as $i => $book) {
            if ($i < \count($bookCollection) - 1 && \count($book['bookUsers'])) {
                self::assertGreaterThanOrEqual($book['bookUsers'][0]['user']['age'], $bookCollection[$i + 1]['bookUsers'][0]['user']['age']);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function test_it_can_sort_multiple_fields(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'title' => SortType::ASC,
                'price' => SortType::ASC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);

        foreach ($bookCollection as $i => $book) {
            if ($i < \count($bookCollection) - 1) {
                self::assertLessThanOrEqual(0, strcmp($book['title'], $bookCollection[$i + 1]['title']));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function test_it_can_sort_multiple_fields_and_one_field_sort_type_is_empty(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'title' => '',
                'price' => SortType::ASC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);

        foreach ($bookCollection as $i => $book) {
            if ($i < \count($bookCollection) - 1) {
                self::assertGreaterThanOrEqual($book['price'], $bookCollection[$i + 1]['price']);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function test_it_is_working_if_send_empty_order_data(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'title' => '',
                'price' => '',
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);
        $bookCollection = json_decode($client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertNotEmpty($bookCollection);
    }

    /**
     * @throws Exception
     */
    public function test_sort_native_wrong_field_with_no_relation(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'wrongField' => SortType::DESC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
        self::assertTrue($client->getResponse()->isServerError());
        $this->throwException(new \InvalidArgumentException('You have selected the wrong field for sorting'));
    }

    /**
     * @throws Exception
     */
    public function test_sort_with_wrong_relation(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => [
                'wrongRelation' => SortType::DESC,
            ],
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
        self::assertTrue($client->getResponse()->isServerError());
        $this->throwException(new \InvalidArgumentException('You have selected the wrong relation for sorting'));
    }

    /**
     * @throws Exception
     */
    public function test_if_send_a_non_array_sort_parameters(): void
    {
        BookUserCollectionStory::load();
        static::ensureKernelShutdown();

        $client = static::createClient();

        $queryParams = [
            'order' => 'simple_string',
            'no_cache' => random_int(0, 999999),
        ];
        $client->request('GET', '/api/sort/books', $queryParams);

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
        self::assertTrue($client->getResponse()->isServerError());
        $this->throwException(new \InvalidArgumentException('Order parameters should be an array type'));
    }

    public static function _resetSchema(): void
    {
    }
}
