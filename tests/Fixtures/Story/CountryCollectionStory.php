<?php

namespace Bugloos\QuerySortingBundle\Tests\Fixtures\Story;

use Bugloos\QuerySortingBundle\Tests\Fixtures\Factory\CountryFactory;
use Zenstruck\Foundry\Story;

final class CountryCollectionStory extends Story
{
    public function build(): void
    {
        CountryFactory::createMany(31);
    }
}
