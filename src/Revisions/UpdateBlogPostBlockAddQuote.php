<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class UpdateBlogPostBlockAddQuote implements RevisionInterface
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

            $table->addColumn('quote_nl', 'varchar')->length(510);
            $table->addColumn('quote_fr', 'varchar')->length(510);
            $table->addColumn('quote_en', 'varchar')->length(510);

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

            $table->dropColumn('quote_nl');
            $table->dropColumn('quote_fr');
            $table->dropColumn('quote_en');

        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Update blog_post_block table add quote';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Update blog_post_block table drop quote';
    }
}
