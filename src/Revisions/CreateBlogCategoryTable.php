<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class CreateBlogCategoryTable implements RevisionInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * CreateBlogCategoryTable constructor.
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
        $this->queryBuilder->table('blog_category')->create(function (TableBuilder $table) {

           $table->addColumn('id', 'int')->length(11)->primaryKey();
           $table->addColumn('created', 'int')->length(11);
           $table->addColumn('updated', 'int')->length(11);
           $table->addColumn('title', 'varchar')->length(255);
           $table->addColumn('slug', 'varchar')->length(255);
           $table->addColumn('sort_index', 'int')->length(11);
           $table->addColumn('is_visible', 'tinyint')->length(1);

        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     *
     */
    public function down()
    {
        $this->queryBuilder->table('blog_category')->drop();

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Create blog_category table';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Drop blog_category table';
    }
}