<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\ActionOverlay;
use dry\admin\component\SortHandle;
use dry\media\Pick;
use dry\media\Thumbnail;
use dry\orm\action;
use dry\orm;
use Tnt\Blog\Model\BlogPostPhoto;

class BlogPostPhotoManager extends orm\Manager
{
    public function __construct()
    {
        parent::__construct(BlogPostPhoto::class, [
            'title' => 'Photos',
        ] );

        $this->actions[] = $pick = new Pick( [
            'media_field' => 'photo',
            'v8n_mimetype' => [
                'image/jpeg',
                'image/png',
            ],
        ] );

        $this->actions[] = $delete = new action\Delete();

        $this->header[] = $pick->create_link( 'Add photo' );

        $this->index = new orm\Index( [
            new Thumbnail( 'photo' ),
            new ActionOverlay( [
                new SortHandle(),
                $delete->create_link(),
            ] ),
        ], [
            'layout' => orm\Index::GRID,
        ] );

        $this->index->sorter = new orm\sort\DragSorter( 'sort_index' );
    }
}