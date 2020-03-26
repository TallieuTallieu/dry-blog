<?php

namespace Tnt\Blog;

use Tnt\Blog\Contracts\BlogCategoryRepositoryInterface;
use Tnt\Blog\Model\BlogCategory;
use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Criteria\IsTrue;
use Tnt\Dbi\Criteria\OrderBy;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    protected $model = BlogCategory::class;

    /**
     * @return BlogCategoryRepositoryInterface
     */
    public function sorted(): BlogCategoryRepositoryInterface
    {
        $this->addCriteria(new OrderBy('sort_index'));

        return $this;
    }

    /**
     * @return BlogCategoryRepositoryInterface
     */
    public function published(): BlogCategoryRepositoryInterface
    {
        $this->addCriteria(new IsTrue('is_visible'));

        return $this;
    }
}