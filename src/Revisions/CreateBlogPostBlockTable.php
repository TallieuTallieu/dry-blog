<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class CreateBlogPostBlockTable implements RevisionInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * CreateBlogPostBlockTable constructor.
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
        $this->queryBuilder->table('blog_post_block')->create(function (TableBuilder $table) {

            $table->addColumn('id', 'int')->length(11)->primaryKey();
            $table->addColumn('created', 'int')->length(11);
            $table->addColumn('updated', 'int')->length(11);
            $table->addColumn('sort_index', 'int')->length(11);
            $table->addColumn('type', 'varchar')->length(255);
            $table->addColumn('title', 'varchar')->length(255);
            $table->addColumn('body', 'text');
            $table->addColumn('photo', 'int')->length(11);
            $table->addColumn('blog_post', 'int')->length(11);

            $table->addForeignKey('photo', 'dry_media_file');
            $table->addForeignKey('blog_post', 'blog_post');

        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     *
     */
    public function down()
    {
        $this->queryBuilder->table('blog_post_block')->drop();

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Create blog_post_block table';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Drop blog_post_block table';
    }
}
