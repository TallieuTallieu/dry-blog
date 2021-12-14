<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class UpdateBlogPostAddPublicationTimestamp implements RevisionInterface
{
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function up()
    {
        $this->queryBuilder->table('blog_post')->alter(function (TableBuilder $table) {
            $table->addColumn('publication_hour', 'varchar')->length(255)->null();
            $table->addColumn('publication_timestamp', 'int')->length(11)->null();
        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    public function down()
    {
        $this->queryBuilder->table('blog_post')->alter(function (TableBuilder $table) {
            $table->dropColumn('publication_hour');
            $table->dropColumn('publication_timestamp');
        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    public function describeUp(): string
    {
        return 'Update blog_post table add publication_hour, publication_timestamp';
    }

    public function describeDown(): string
    {
        return 'Update blog_post table drop publication_hour, publication_timestamp';
    }
}
