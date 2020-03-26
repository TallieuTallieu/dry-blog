<?php

namespace Tnt\Blog\Facade;

use Oak\Facade;
use Tnt\Blog\Contracts\BlogCategoryRepositoryInterface;

class BlogCategory extends Facade
{
    /**
     * @return string
     */
    protected static function getContract(): string
    {
        return BlogCategoryRepositoryInterface::class;
    }
}