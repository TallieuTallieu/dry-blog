<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class CreateBlogPostTable implements RevisionInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * CreateBlogPostTable constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     *
     */
    public function up()
    {
        $this->queryBuilder->table('blog_post')->create(function(TableBuilder $table) {

            $table->addColumn('id', 'int')->length(11)->primaryKey();
            $table->addColumn('created', 'int')->length(11);
            $table->addColumn('updated', 'int')->length(11);
            $table->addColumn('title_nl', 'varchar')->length(255);
            $table->addColumn('title_fr', 'varchar')->length(255);
            $table->addColumn('title_en', 'varchar')->length(255);
            $table->addColumn('slug_nl', 'varchar')->length(255);
            $table->addColumn('slug_fr', 'varchar')->length(255);
            $table->addColumn('slug_en', 'varchar')->length(255);
            $table->addColumn('intro_text_nl', 'varchar')->length(255);
            $table->addColumn('intro_text_fr', 'varchar')->length(255);
            $table->addColumn('intro_text_en', 'varchar')->length(255);
            $table->addColumn('badge_text_nl', 'varchar')->length(255);
            $table->addColumn('badge_text_fr', 'varchar')->length(255);
            $table->addColumn('badge_text_en', 'varchar')->length(255);
            $table->addColumn('sort_index', 'int')->length(11);
            $table->addColumn('publication_date', 'int')->length(11);
            $table->addColumn('is_visible', 'tinyint')->length(1);
            $table->addColumn('layout', 'varchar')->length(255);
            $table->addColumn('photo', 'int')->length(11)->null();
            $table->addColumn('cta_title_nl', 'varchar')->length(255);
            $table->addColumn('cta_title_fr', 'varchar')->length(255);
            $table->addColumn('cta_title_en', 'varchar')->length(255);
            $table->addColumn('cta_url_nl', 'varchar')->length(255);
            $table->addColumn('cta_url_fr', 'varchar')->length(255);
            $table->addColumn('cta_url_en', 'varchar')->length(255);
            $table->addColumn('category', 'int')->length(11)->null();
            $table->addColumn('author', 'int')->length(11)->null();

            $table->addForeignKey('photo', 'dry_media_file');
            $table->addForeignKey('category', 'blog_category');
            $table->addForeignKey('author', 'blog_author');

        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     *
     */
    public function down()
    {
        $this->queryBuilder->table('blog_post')->drop();

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Create blog_post table';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Drop blog_post table';
    }
}
