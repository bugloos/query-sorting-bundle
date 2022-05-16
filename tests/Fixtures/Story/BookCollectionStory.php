<?php

namespace Bugloos\QuerySortingBundle\Tests\Fixtures\Story;

use Bugloos\QuerySortingBundle\Tests\Fixtures\Factory\BookFactory;
use Zenstruck\Foundry\Story;

final class BookCollectionStory extends Story
{
    public function build(): void
    {
        CountryCollectionStory::load();

        BookFactory::createMany(31);
    }
}
