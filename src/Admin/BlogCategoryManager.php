<?php

namespace Tnt\Blog\Admin;

use Tnt\Blog\Model\BlogCategory;

use dry\admin\component\BooleanEdit;
use dry\admin\component\SortHandle;
use dry\admin\component\Stack;
use dry\admin\component\StringEdit;
use dry\admin\component\StringView;
use dry\admin\Module;
use dry\orm\action\Create;
use dry\orm\action\Delete;
use dry\orm\action\Edit;
use dry\orm\Index;
use dry\orm\IndexRow;
use dry\orm\Manager;
use dry\orm\sort\DragSorter;

class BlogCategoryManager extends Manager
{
    public function __construct()
    {
        parent::__construct(BlogCategory::class, [
            'icon' => Module::ICON_NEWS,
            'singular' => 'category',
            'plural' => 'categories',
        ]);

        $this->actions[] = $create = new Create([
            new Stack(Stack::HORIZONTAL, [
                new StringEdit('title', [
                    'v8n_required' => TRUE,
                    'suggest_slug' => 'slug',
                ]),
                new StringEdit('slug', [
                    'v8n_required' => TRUE,
                    'handle_duplicate' => TRUE,
                    'slugify_on_blur' => TRUE,
                ]),
            ]),
            new BooleanEdit('is_visible'),
        ], [
            'mode' => Create::MODE_POPUP,
        ] );

        $this->actions[] = $edit = new Edit($create->components, [
            'mode' => Create::MODE_POPUP,
        ]);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add category');

        $this->index = new Index([
            new SortHandle(),
            new StringView('title'),
            $edit->create_link(),
            $delete->create_link(),
        ], [
            'field_to_row_class' => [
                'is_visible', NULL, IndexRow::STYLE_DISABLED
            ],
        ] );

        $this->index->sorter = new DragSorter('sort_index');
    }
}