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
use Tnt\Blog\Revisions\CreateBlogCategoryTable;
use Tnt\Blog\Revisions\CreateBlogAuthorTable;
use Tnt\Blog\Revisions\CreateBlogPostBlockTable;
use Tnt\Blog\Revisions\CreateBlogPostTable;

class BlogServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $app)
    {
        //
    }

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
            ]);

            $app->get(MigrationManager::class)
                ->addMigrator($migrator);
        }

        $this->registerAdminModules($app);
    }

    private function registerAdminModules(ContainerInterface $app)
    {
        $hasCategories = $app->get(RepositoryInterface::class)->get('blog.categories', true);
        $hasAuthors = $app->get(RepositoryInterface::class)->get('blog.members', true);

        $modules = [
            new BlogPostManager([
                'categories' => $hasCategories,
                'authors' => $hasAuthors,
            ]),
        ];

        if ($hasCategories) {
            $modules[] = new BlogCategoryManager();
        }

        if ($hasAuthors) {
            $modules[] = new BlogAuthorManager();
        }

        \dry\admin\Router::$modules[] = new Portal( 'blog', 'Blog', $modules );
    }
}