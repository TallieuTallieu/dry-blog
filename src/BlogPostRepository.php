<?php

namespace Tnt\Blog;

use Tnt\Blog\Contracts\BlogPostRepositoryInterface;
use Tnt\Blog\Model\BlogCategory;
use Tnt\Blog\Model\BlogPost;
use Tnt\DataList\Contracts\Paginate\PaginatableInterface;
use Tnt\DataList\Paginate\PaginatableTrait;
use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Criteria\Equals;
use Tnt\Dbi\Criteria\GreaterThan;
use Tnt\Dbi\Criteria\IsFalse;
use Tnt\Dbi\Criteria\IsTrue;
use Tnt\Dbi\Criteria\LessThan;
use Tnt\Dbi\Criteria\LessThanOrEqual;
use Tnt\Dbi\Criteria\NotEquals;
use Tnt\Dbi\Criteria\OrderBy;
use Tnt\Dbi\Raw;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface, PaginatableInterface
{
    use PaginatableTrait;

    protected $model = BlogPost::class;

    /**
     * @return BlogPostRepositoryInterface
     */
    public function recent(): BlogPostRepositoryInterface
    {
        $this->addCriteria(new OrderBy('publication_timestamp', 'DESC'));

        return $this;
    }

    /**
     * @return BlogPostRepositoryInterface
     */
    public function published(): BlogPostRepositoryInterface
    {
        $this->addCriteria(new LessThanOrEqual('publication_timestamp', time()));
        $this->addCriteria(new IsTrue('is_visible'));

        return $this;
    }

    /**
     * @param BlogPost $blogPost
     * @return BlogPostRepositoryInterface
     */
    public function prev(BlogPost $blogPost): BlogPostRepositoryInterface
    {
        $this->addCriteria(new NotEquals('id', $blogPost->id));
        $this->addCriteria(new LessThan('publication_timestamp', $blogPost->publication_timestamp));
        $this->addCriteria(new OrderBy('publication_timestamp', 'DESC'));

        return $this;
    }

    /**
     * @param BlogPost $blogPost
     * @return BlogPostRepositoryInterface
     */
    public function next(BlogPost $blogPost): BlogPostRepositoryInterface
    {
        $this->addCriteria(new NotEquals('id', $blogPost->id));
        $this->addCriteria(new GreaterThan('publication_timestamp', $blogPost->publication_timestamp));
        $this->addCriteria(new OrderBy('publication_timestamp', 'ASC'));

        return $this;
    }

    /**
     * @param BlogCategory $category
     * @return BlogPostRepositoryInterface
     */
    public function categorical(BlogCategory $category): BlogPostRepositoryInterface
    {
        $this->addCriteria(new Equals('category', $category));

        return $this;
    }

    /**
     * @return BlogPostRepositoryInterface
     */
    public function isPrivate(): BlogPostRepositoryInterface
    {
        $this->addCriteria(new IsTrue('is_private'));

        return $this;
    }

    /**
     * @return BlogPostRepositoryInterface
     */
    public function orderByIsFeatured(): BlogPostRepositoryInterface
    {
        $this->addCriteria(new OrderBy('is_featured', 'DESC'));
        $this->addCriteria(new OrderBy('publication_timestamp', 'DESC'));

        return $this;
    }

    /**
     * @return BlogPostRepositoryInterface
     */
    public function isPublic(): BlogPostRepositoryInterface
    {
        $this->addCriteria(new IsFalse('is_private'));

        return $this;
    }

    /**
     * Filter BlogPosts by year.
     *
     * @param $year
     *
     * @return BlogPostRepositoryInterface
     */
    public function yearly($year): BlogPostRepositoryInterface
    {
        if ($year) {
            $this->addCriteria(new Equals(new Raw('FROM_UNIXTIME(publication_timestamp, \'%Y\')'), $year));
        }

        return $this;
    }
}
