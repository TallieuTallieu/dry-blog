<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\I18nSwitcher;
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
    public function __construct(array $languages)
    {
        parent::__construct(BlogCategory::class, [
            'icon' => Module::ICON_NEWS,
            'singular' => 'category',
            'plural' => 'categories',
        ]);

        $components = [];

        foreach ($languages as $language) {
            $components[$language] = [
                new Stack(Stack::HORIZONTAL, [
                    new StringEdit('title_'.$language, [
                        'v8n_required' => TRUE,
                        'suggest_slug' => 'slug_'.$language,
                        'label' => 'title',
                    ]),
                    new StringEdit('slug_'.$language, [
                        'v8n_required' => TRUE,
                        'handle_duplicate' => TRUE,
                        'slugify_on_blur' => TRUE,
                        'label' => 'slug',
                    ]),
                ]),
            ];
        }

        $componentsContainer = new Stack(Stack::VERTICAL, $components[$languages[0]]);

        if (count($languages) > 1) {
            $componentsContainer = new I18nSwitcher($components);
        }

        $this->actions[] = $create = new Create([
            $componentsContainer,
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
            new StringView('title_'.$languages[0]),
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