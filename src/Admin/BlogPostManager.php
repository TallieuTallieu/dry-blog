<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\I18nSwitcher;
use Tnt\Blog\Model\BlogPost;

use dry\admin\component\BooleanEdit;
use dry\admin\component\DateEdit;
use dry\admin\component\DateView;
use dry\admin\component\EnumEdit;
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
        $categories = true;
        $authors = true;
        $photos = true;
        $advancedLayout = true;
        $blockTypes = [];
        $languages = [];

        extract( $kwargs, EXTR_IF_EXISTS );

        parent::__construct(BlogPost::class, [
            'icon' => Module::ICON_DOCUMENT,
            'singular' => 'post',
            'plural' => 'posts',
        ]);

        $generalComponents = [];
        $contentComponents = [];

        foreach ($languages as $language) {
            $generalComponents[$language] = [
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
            ];

            $contentComponents[$language] = [
                new StringEdit('intro_text_'.$language, [
                    'multiline' => TRUE,
                    'height' => 60,
                    'label' => 'intro text',
                ]),
                new StringEdit('badge_text_'.$language, [
                    'label' => 'badge text',
                ]),
                new Stack(Stack::HORIZONTAL, [
                    new StringEdit('cta_title_'.$language, [
                        'label' => 'title'
                    ]),
                    new StringEdit('cta_url_'.$language, [
                        'label' => 'url'
                    ]),
                ], [
                    'title' => 'Call to action',
                    'grid' => [1,2]
                ]),
            ];
        }

        $generalComponentsContainer = new Stack(Stack::VERTICAL, $generalComponents[$languages[0]]);
        $contentComponentsContainer = new Stack(Stack::VERTICAL, $contentComponents[$languages[0]]);

        if (count($languages) > 1) {
            $generalComponentsContainer = new I18nSwitcher($generalComponents);
            $contentComponentsContainer = new I18nSwitcher($contentComponents);
        }

        $this->actions[] = $create = new Create([
            $generalComponentsContainer,
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

        if ($advancedLayout) {
            array_unshift($create->components, new EnumEdit('layout', BlogPost::get_layout_enum()));
        }

        if ($categories) {
            $create->components[] = new ForeignKeySelect('category', [
                'to_string' => 'title_'.$languages[0],
            ]);
        }

        $content = new TabbedContent([
            ['General', [
                $contentComponentsContainer,
            ]],
            [ 'Content', [
                new InlineManager(new BlogPostBlockManager($blockTypes, $languages), [
                    'restrict_by_foreign_key' => 'blog_post',
                ]),
            ]]
        ]);

        $sidebarContent = [
            $generalComponentsContainer,
            new DateEdit('publication_date', [
                'v8n_required' => TRUE,
            ]),
            new Picker('photo', [
                'v8n_required' => TRUE,
            ]),
            new BooleanEdit('is_visible'),
        ];

        if ($advancedLayout) {
            array_unshift($sidebarContent, new EnumEdit('layout', BlogPost::get_layout_enum()));
        }

        $this->actions[] = $edit = new Edit([
            new Stack(Stack::HORIZONTAL, [
                $content,
                new Stack(Stack::VERTICAL, $sidebarContent, [
                    'title' => 'General information'
                ]),
            ], [
                'grid' => [5, 2],
            ]),
        ], [
            'fixed_footer' => TRUE,
        ]);

        if ($categories) {
            $content->tabs[] = [ 'Categories', [
                new ForeignKeySelect('category', [
                    'to_string' => 'title_'.$languages[0],
                ]),
            ] ];
        }

        if ($authors) {
            $content->tabs[] = [ 'Author', [
                new ForeignKeySelect('author', [
                    'null' => TRUE
                ]),
            ] ];
        }

        if ($photos) {
            $content->tabs[] = [ 'Media', [
               new InlineManager(new BlogPostPhotoManager(), [
                   'restrict_by_foreign_key' => 'blog_post',
               ])
            ] ];
        }

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add post');

        $this->footer[] = new Pagination();

        $this->index = new Index([
            new StringView('title_'.$languages[0]),
            new DateView('publication_date'),
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