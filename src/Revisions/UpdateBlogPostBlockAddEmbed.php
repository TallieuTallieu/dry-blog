<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class UpdateBlogPostBlockAddEmbed implements RevisionInterface
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
        $this->queryBuilder->table('blog_post_block')->alter(function (TableBuilder $table) {
            $table->addColumn('embed_id', 'varchar')->length(255);
        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }   

    /**
     *
     */
    public function down()
    {
        $this->queryBuilder->table('blog_post_block')->alter(function (TableBuilder $table) {
            $table->dropColumn('embed_id');

        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Update blog_post_block table add embed_id';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Update blog_post_block table drop embed_id';
    }
}
