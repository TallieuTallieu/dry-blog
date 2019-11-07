<?php

namespace Tnt\Blog\Revisions;

use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;
use dry\db\Connection;

class CreateBlogAuthorTable implements RevisionInterface
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
        $this->queryBuilder->table('blog_author')->create(function(TableBuilder $table) {

            $table->addColumn('id', 'int')->length(11)->primaryKey();
            $table->addColumn('created', 'int')->length(11);
            $table->addColumn('updated', 'int')->length(11);
            $table->addColumn('first_name', 'varchar')->length(255);
            $table->addColumn('last_name', 'varchar')->length(255);
            $table->addColumn('function', 'varchar')->length(255);
            $table->addColumn('short_bio', 'varchar')->length(255);
            $table->addColumn('email', 'varchar')->length(255);
            $table->addColumn('sort_index', 'int')->length(11);
            $table->addColumn('is_visible', 'tinyint')->length(1);
            $table->addColumn('photo', 'int')->length(11);

            $table->addForeignKey('photo', 'dry_media_file');

        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     *
     */
    public function down()
    {
        $this->queryBuilder->table('blog_author')->drop();

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Create blog_author table';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Drop blog_author table';
    }
}