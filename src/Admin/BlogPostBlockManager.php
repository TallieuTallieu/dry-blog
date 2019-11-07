<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\EnumSwitcher;
use Tnt\Blog\Model\BlogPostBlock;

use dry\admin\component\EnumView;
use dry\admin\component\RichtextEdit2;
use dry\admin\component\SortHandle;
use dry\admin\component\Stack;
use dry\admin\component\StringEdit;
use dry\media\Picker;
use dry\orm\action\Create;
use dry\orm\action\Delete;
use dry\orm\action\Edit;
use dry\orm\Index;
use dry\orm\Manager;
use dry\orm\sort\DragSorter;

class BlogPostBlockManager extends Manager
{
    public function __construct()
    {
        parent::__construct(BlogPostBlock::class, [
            'singular' => 'content block',
        ]);

        $this->actions[] = $create = new Create([
            new EnumSwitcher('type', [
                [BlogPostBlock::TYPE_PHOTO_TEXT, 'Text & photo', [
                    new StringEdit('title'),
                    new Stack( Stack::HORIZONTAL, [
                        new Picker( 'photo' ),
                        new RichtextEdit2('text'),
                    ]),
                ]],
                [BlogPostBlock::TYPE_PHOTO_TEXT, 'Photo & text', [
                    new StringEdit('title'),
                    new Stack( Stack::HORIZONTAL, [
                        new RichtextEdit2('text'),
                        new Picker( 'photo' ),
                    ]),
                ]],
                [BlogPostBlock::TYPE_PHOTO, 'Photo', [
                    new Picker( 'photo', [
                        'v8n_required' => TRUE,
                    ]),
                ]],
                [BlogPostBlock::TYPE_TEXT, 'Text', [
                    new StringEdit('title'),
                    new RichtextEdit2('text'),
                ]],
                [BlogPostBlock::TYPE_TEXTFRAME, 'Textframe', [
                    new StringEdit('title'),
                    new RichtextEdit2('text'),
                ]],
            ], [
                'mode' => EnumSwitcher::TABS,
            ]),
        ]);

        $this->actions[] = $edit = new Edit($create->components);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add block');

        $this->index = new Index([
            new SortHandle(),
            new EnumView('type', BlogPostBlock::get_type_enum()),
            $edit->create_link(),
            $delete->create_link(),
        ]);

        $this->index->sorter = new DragSorter('sort_index');
    }
}