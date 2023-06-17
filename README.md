<h2>Query Sorting Bundle</h2>

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bugloos/query-sorting-bundle/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/bugloos/query-sorting-bundle/?branch=main)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/bugloos/query-sorting-bundle/test)](https://github.com/bugloos/query-sorting-bundle/actions)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/bugloos/query-sorting-bundle/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence)

<h2>What does it do? :)</h2>
The query sorting bundle allows you to sort data from QueryBuilder and the Database. you can sort multiple columns at the same time and also you can sort relation fields with two-level deep and without any join in your query builder.

<h2>Installation:</h2>

```bash
composer require bugloos/query-sorting-bundle
```

<h2>Compatibility</h2>

* PHP v8.1 or above
* Symfony v4.4 or above

<h2>Usage</h2>
Suppose our database has the following tables with the following relations

![Service running preview](./tests/Fixtures/db/diagram.png)

<strong>Now we want to show the Book entity by sorting</strong>

We want sort Book entity by price column, so we can send sort data by Querystring or make inline with array like this:

```php
/*
 * Sort book by price ascending
*/
//Get api/book/index?order[price]=ASC
OR
$orders = [
    'price' => SortType::ASC,
];

/*
 * Sort book by price descending
*/
//Get api/book/index?order[price]=DESC
OR
$orders = [
    'price' => SortType::DESC,
];
```

You just need to add Sorting class in controller the call sort method:

```php
use Bugloos\QuerySortingBundle\Service\QuerySorting;
```

The following code is in Book controller.
<p>As you see, At first you should call for() method and pass QueryBuilder as parameter to this method</p>
<p>Then call parameters() method and pass orders request items</p>
<p>At the end you should call sort() method to run sorting</p>
The return of sort method is Query Builder, so you can add anything else to Query Builder after sorting.

```php
public function index(
    Request $request,
    BookRepository $bookRepository,
    QuerySorting $querySorting
): Response {
    $queryBuilder = $bookRepository->createQueryBuilder('b');
    
    $queryBuilder = $querySorting->for($queryBuilder)
        ->parameters($request->get('order'))
        ->sort()
    ;
    
    return $queryBuilder->getQuery()->getResult();
}
```

<p>If you want to sort the ManyToOne relation field or one level deep relation, you should add mapper.</p>
<p>To add a mapper, you call addMapper() method to add single mapper or call mappers() method to send multiple mappers with array</p>
<p>First parameter of addMapper() method is parameter name and second parameter is relation name and its field name, which separate by " . " sign</p>

```php
$mappers = [
    'country' => 'country.name',
];
```

For example we want to sort Book entity by its Country name. Book has ManyToOne relation with Country entity

```php
/*
 * Sort book by Country name ascending
*/
//Get api/book/index?order[country]=ASC
OR
$orders = [
    'country' => SortType::ASC,
];

/*
 * Sort book by Country name descending
*/
//Get api/book/index?order[country]=DESC
OR
$orders = [
    'country' => SortType::DESC,
];
```

The following code is in Book controller.

```php
public function index(
    Request $request,
    BookRepository $bookRepository,
    QuerySorting $querySorting
): Response {
    $queryBuilder = $bookRepository->createQueryBuilder('b');
    
    $queryBuilder = $querySorting->for($queryBuilder)
        ->parameters($request->get('order'))
        ->addMapper('country', 'country.name')
        ->sort()
    ;
    
    return $queryBuilder->getQuery()->getResult();
}
```

**NOTE**: There is no need to add your relationship join in Query builder because if join is not added, I will add it automatically. ;)

```php
$queryBuilder = $bookRepository->createQueryBuilder('b');

OR

$queryBuilder = $bookRepository->createQueryBuilder('b')
    ->addSelect('country')   
    ->leftJoin('b.country', 'country')      
;
```

<p>If you want to sort the ManyToMany relation field or two level deep relation, you should again add mapper</p>

```php
$mapper = [
    'age' => 'bookUsers.user.age',
];
```

For example we want to sort Book entity by its Writer age. Book has ManyToMany relation with User entity

```php
/*
 * Sort book by Writer age ascending
*/
//Get api/book/index?order[age]=ASC
OR
$orders = [
    'age' => SortType::ASC,
];

/*
 * Sort book by Writer age descending
*/
//Get api/book/index?order[age]=DESC
OR
$orders = [
    'age' => SortType::DESC,
];
```

The following code is in Book controller.

```php
public function index(
    Request $request,
    BookRepository $bookRepository,
    QuerySorting $querySorting
): Response {
    $queryBuilder = $bookRepository->createQueryBuilder('b');
    
    $queryBuilder = $querySorting->for($queryBuilder)
        ->parameters($request->get('order'))
        ->addMapper('age', 'bookUsers.user.age')
        ->sort()
    ;
    
    return $queryBuilder->getQuery()->getResult();
}
```

**NOTE**: You should know that you can sort data with multiple columns too, you just need to send multiple order data with a Query string like this:

```php
/*
 * Sort book by title ascending and price descending
*/
//Get api/book/index?order[title]=ASC&order[price]=DESC
OR
$orders = [
    'title' => SortType::ASC,
    'price' => SortType::DESC,
];
```

<h2>Suggestion</h2>

You can change two parameters with the config file, just make a yaml file in config/packages/ directory then you can change default cache time for queries and default relation separator as follows:

```yaml
query_sorting:
  default_cache_time: 3600
  separator: '.'
```

**NOTE**: You can set the cache time for each query separately and if you don't set any cache time, it uses default cache time in your config file

```php
$queryBuilder = $querySorting->for($queryBuilder)
    ->parameters($request->get('order'))
    ->cacheTime(120)
    ->sort()
;
```
<h2>Contributing <img class="emoji" alt="v" height="20" width="20" src="https://github.githubassets.com/images/icons/emoji/unicode/270c.png"> <img class="emoji" alt="beer" height="20" width="20" src="https://github.githubassets.com/images/icons/emoji/unicode/1f37a.png"></h2>

If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.