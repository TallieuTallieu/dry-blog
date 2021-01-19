<?php

namespace Tnt\Blog\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class UpdateBlogPostBlockAddVideo implements RevisionInterface
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
            $table->addColumn('media_credit_nl', 'varchar')->length(255);
            $table->addColumn('media_credit_fr', 'varchar')->length(255);
            $table->addColumn('media_credit_en', 'varchar')->length(255);

            $table->addColumn('video_type', 'varchar')->length(255);
            $table->addColumn('video_id', 'varchar')->length(255);

            $table->addColumn('video', 'int')->length(11)->null();
            $table->addForeignKey('video', 'dry_media_file');
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
        return 'Update blog_post_block table add video';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Update blog_post_block table drop video';
    }
}
