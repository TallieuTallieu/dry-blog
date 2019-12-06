<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\I18nSwitcher;
use Tnt\Blog\Model\BlogAuthor;

use dry\admin\component\BooleanEdit;
use dry\admin\component\SortHandle;
use dry\admin\component\Stack;
use dry\admin\component\StringEdit;
use dry\admin\component\StringView;
use dry\admin\Module;
use dry\media\Picker;
use dry\media\Thumbnail;
use dry\orm\action\Create;
use dry\orm\action\Delete;
use dry\orm\action\Edit;
use dry\orm\Index;
use dry\orm\IndexRow;
use dry\orm\Manager;
use dry\orm\sort\DragSorter;

class BlogAuthorManager extends Manager
{
    public function __construct(array $languages)
    {
        parent::__construct(BlogAuthor::class, [
            'icon' => Module::ICON_USERS,
            'title' => 'Authors',
            'singular' => 'Author',
        ]);

        $components = [];

        foreach ($languages as $language) {
            $components[$language] = [
                new Stack(Stack::HORIZONTAL, [
                    new StringEdit('function_'.$language, [
                        'v8n_required' => TRUE,
                        'label' => 'function',
                    ]),
                    new StringEdit('short_bio_'.$language, [
                        'v8n_required' => TRUE,
                        'multiline' => TRUE,
                        'label' => 'short bio',
                    ]),
                ]),
            ];
        }

        $componentsContainer = new Stack(Stack::VERTICAL, $components[$languages[0]]);

        if (count($languages) > 1) {
            $componentsContainer = new I18nSwitcher($components);
        }

        $this->actions[] = $create = new Create([
            new Stack(Stack::HORIZONTAL, [
                new Picker('photo', [
                    'v8n_required' => TRUE,
                ]),
                new Stack(Stack::VERTICAL, [
                    new Stack(Stack::HORIZONTAL, [
                        new StringEdit( 'first_name', [
                            'v8n_required' => TRUE,
                        ]),
                        new StringEdit('last_name', [
                            'v8n_required' => TRUE,
                        ]),
                    ]),
                    $componentsContainer,
                    new StringEdit('email', [
                        'v8n_email' => TRUE,
                    ]),
                    new BooleanEdit('is_visible'),
                ]),
            ], [
                'grid' => [3, 5],
            ]),
        ], [
            'fixed_footer' => TRUE,
        ]);

        $this->actions[] = $edit = new Edit($create->components, [
            'fixed_footer' => TRUE,
        ]);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add team member');

        $this->index = new Index([
            new SortHandle(),
            new Thumbnail('photo'),
            new StringView('first_name'),
            new StringView('last_name'),
            new StringView('function_'.$languages[0]),
            $edit->create_link(),
            $delete->create_link(),
        ], [
            'field_to_row_class' => ['is_visible', NULL, IndexRow::STYLE_DISABLED],
        ]);

        $this->index->sorter = new DragSorter('sort_index');
    }
}