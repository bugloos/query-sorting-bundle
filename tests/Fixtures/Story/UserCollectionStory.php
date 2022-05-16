<?php

namespace Bugloos\QuerySortingBundle\Tests\Fixtures\Story;

use Bugloos\QuerySortingBundle\Tests\Fixtures\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserCollectionStory extends Story
{
    public function build(): void
    {
        UserFactory::createMany(31);
    }
}
