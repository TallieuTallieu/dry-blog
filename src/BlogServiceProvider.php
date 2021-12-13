<?php

namespace Tnt\Blog;

use dry\admin\Portal;
use Oak\Contracts\Config\RepositoryInterface;
use Oak\Contracts\Container\ContainerInterface;
use Oak\Migration\MigrationManager;
use Oak\Migration\Migrator;
use Oak\ServiceProvider;
use Tnt\Blog\Admin\BlogCategoryManager;
use Tnt\Blog\Admin\BlogAuthorManager;
use Tnt\Blog\Admin\BlogPostManager;
use Tnt\Blog\Contracts\BlogPortalInterface;
use Tnt\Blog\Contracts\BlogCategoryRepositoryInterface;
use Tnt\Blog\Contracts\BlogPostRepositoryInterface;
use Tnt\Blog\Revisions\CreateBlogCategoryTable;
use Tnt\Blog\Revisions\CreateBlogAuthorTable;
use Tnt\Blog\Revisions\CreateBlogPostBlockTable;
use Tnt\Blog\Revisions\CreateBlogPostPhotoTable;
use Tnt\Blog\Revisions\CreateBlogPostTable;
use Tnt\Blog\Revisions\UpdateBlogPostAddIsFeatured;
use Tnt\Blog\Revisions\UpdateBlogPostAddIsPrivate;
use Tnt\Blog\Revisions\UpdateBlogPostBlockAddEmbed;
use Tnt\Blog\Revisions\UpdateBlogPostBlockAddQuote;
use Tnt\Blog\Revisions\UpdateBlogPostBlockAddVideo;

class BlogServiceProvider extends ServiceProvider
{

    /**
     * @param ContainerInterface $app
     * @return mixed|void
     */
    public function register(ContainerInterface $app)
    {
        $app->set(BlogPostRepositoryInterface::class, BlogPostRepository::class);
        $app->set(BlogCategoryRepositoryInterface::class, BlogCategoryRepository::class);

        $app->set(BlogPortalInterface::class, function() use ($app) {
            return $this->registerPortal($app);
        });
    }

    /**
     * @param ContainerInterface $app
     * @return mixed|void
     */
    public function boot(ContainerInterface $app)
    {
        if ($app->isRunningInConsole()) {

            $migrator = $app->getWith(Migrator::class, [
                'name' => 'blog',
            ]);

            $migrator->setRevisions([
                CreateBlogCategoryTable::class,
                CreateBlogAuthorTable::class,
                CreateBlogPostTable::class,
                CreateBlogPostBlockTable::class,
                CreateBlogPostPhotoTable::class,
                UpdateBlogPostBlockAddQuote::class,
                UpdateBlogPostAddIsPrivate::class,
                UpdateBlogPostBlockAddVideo::class,
                UpdateBlogPostBlockAddEmbed::class,
                UpdateBlogPostAddIsFeatured::class
            ]);

            $app->get(MigrationManager::class)
                ->addMigrator($migrator);
        }

        $this->registerPortal($app);
    }

    public function provides(): array
    {
        return [BlogPortalInterface::class];
    }

    /**
     * @param ContainerInterface $app
     * @return Portal
     */
    private function registerPortal(ContainerInterface $app)
    {
        $hasCategories = $app->get(RepositoryInterface::class)->get('blog.categories', true);
        $hasAuthors = $app->get(RepositoryInterface::class)->get('blog.authors', true);
        $hasPhotos = $app->get(RepositoryInterface::class)->get('blog.photos', true);
        $advancedLayout = $app->get(RepositoryInterface::class)->get('blog.advanced-layout', true);
        $isPrivate = $app->get(RepositoryInterface::class)->get('blog.private', false);
        $isFeatured = $app->get(RepositoryInterface::class)->get('blog.featured', false);
        $blockTypes = $app->get(RepositoryInterface::class)->get('blog.types', [
            'text-photo',
            'photo-text',
            'text',
            'quote',
        ]);

        $languages = $app->get(RepositoryInterface::class)->get('blog.languages', [
            'nl',
            'en',
            'fr',
        ]);

        $requiredFields = $app->get(RepositoryInterface::class)->get('blog.required-fields', [
            'title',
            'slug',
            'photo',
            'publication_date'
        ]);

        $croppers = $app->get(RepositoryInterface::class)->get('blog.croppers', []);

        $publicationTimestamp = $app->get(RepositoryInterface::class)->get('blog.publication_timestamp', false);

        $modules = [
            new BlogPostManager([
                'categories' => $hasCategories,
                'authors' => $hasAuthors,
                'photos' => $hasPhotos,
                'advancedLayout' => $advancedLayout,
                'isPrivate' => $isPrivate,
                'isFeatured' => $isFeatured,
                'blockTypes' => $blockTypes,
                'languages' => $languages,
                'requiredFields' => $requiredFields,
                'croppers' => $croppers,
                'publicationTimestamp' => $publicationTimestamp,
            ]),
        ];

        if ($hasCategories) {
            $modules['categories'] = new BlogCategoryManager($languages);
        }

        if ($hasAuthors) {
            $modules['authors'] = new BlogAuthorManager($languages);
        }

        return new Portal('blog', 'Blog', $modules);
    }
}
