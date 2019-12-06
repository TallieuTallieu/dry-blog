<?php

namespace Tnt\Blog\Facade;

use Oak\Facade;
use Tnt\Blog\Contracts\BlogPostRepositoryInterface;

class BlogPosts extends Facade
{
    protected static function getContract(): string
    {
        return BlogPostRepositoryInterface::class;
    }
}