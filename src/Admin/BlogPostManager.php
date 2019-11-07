<?php

namespace Tnt\Blog\Admin;

use Tnt\Blog\Model\BlogPost;

use dry\admin\component\BooleanEdit;
use dry\admin\component\DateEdit;
use dry\admin\component\DateView;
use dry\admin\component\EnumEdit;
use dry\admin\component\EnumView;
use dry\admin\component\Stack;
use dry\admin\component\StringEdit;
use dry\admin\component\StringView;
use dry\admin\component\TabbedContent;
use dry\admin\Module;
use dry\media\Picker;
use dry\orm\action\Create;
use dry\orm\action\Delete;
use dry\orm\action\Edit;
use dry\orm\component\ForeignKeySelect;
use dry\orm\component\InlineManager;
use dry\orm\component\Pagination;
use dry\orm\Index;
use dry\orm\IndexRow;
use dry\orm\Manager;
use dry\orm\paginate\Paginator;
use dry\orm\sort\StaticSorter;

class BlogPostManager extends Manager
{
    public function __construct(array $kwargs = [])
    {
        $categories = false;
        $authors = false;

        extract( $kwargs, EXTR_IF_EXISTS );

        parent::__construct(BlogPost::class, [
            'icon' => Module::ICON_DOCUMENT,
            'singular' => 'post',
            'plural' => 'posts',
        ]);

        $this->actions[] = $create = new Create([
            new EnumEdit('layout', BlogPost::get_layout_enum()),
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
            new DateEdit('publication_date', [
                'v8n_required' => TRUE,
            ]),
            new Picker('photo', [
                'v8n_required' => TRUE,
                'v8n_mimetype' => [
                    'image/jpeg',
                    'image/png',
                ],
            ]),
        ], [
            'mode' => Create::MODE_POPUP,
        ]);

        if ($categories) {
            $create->components[] = new ForeignKeySelect('category');
        }

        $content = new TabbedContent([
            ['General', [
                new StringEdit('intro_text', [
                    'multiline' => TRUE,
                    'height' => 60,
                ]),
                new StringEdit('badge_text'),
                new InlineManager(new BlogPostBlockManager(), [
                    'restrict_by_foreign_key' => 'blog_post',
                ]),
            ],],
        ]);

        $this->actions[] = $edit = new Edit([
            new Stack(Stack::HORIZONTAL, [
                $content,
                new Stack(Stack::VERTICAL, [
                    new StringEdit('title', [
                        'v8n_required' => TRUE,
                        'suggest_slug' => 'slug',
                    ]),
                    new StringEdit('slug', [
                        'v8n_required' => TRUE,
                        'handle_duplicate' => TRUE,
                        'slugify_on_blur' => TRUE,
                    ]),
                    new DateEdit('publication_date', [
                        'v8n_required' => TRUE,
                    ]),
                    new EnumEdit('layout', BlogPost::get_layout_enum()),
                    new BooleanEdit('is_visible'),
                    new Picker('photo', [
                        'v8n_required' => TRUE,
                    ]),
                ]),
            ], [
                'grid' => [5, 2],
            ]),
        ], [
            'fixed_footer' => TRUE,
        ]);

        if ($categories) {
            $content->tabs[] = [ 'Categories', [
                new ForeignKeySelect('category'),
            ] ];
        }

        if ($authors) {
            $content->tabs[] = [ 'Author', [
                new ForeignKeySelect('author', [
                    'null' => TRUE
                ]),
            ] ];
        }

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add post');

        $this->footer[] = new Pagination();

        $this->index = new Index([
            new StringView('title'),
            new DateView('publication_date'),
            new EnumView('layout', BlogPost::get_layout_enum()),
            $edit->create_link(),
            $delete->create_link(),
        ],[
            'field_to_row_class' => [
                'is_visible', NULL, IndexRow::STYLE_DISABLED
            ],
        ]);

        $this->index->sorter = new StaticSorter('publication_date', StaticSorter::DESC);

        $this->index->paginator = new Paginator(10);
    }
}