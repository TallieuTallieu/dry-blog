<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\I18nSwitcher;
use dry\admin\component\Popout;
use dry\http\Response;
use dry\orm\action\Execute;
use dry\route\Router;
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
use Tnt\Blog\Model\BlogPostBlock;
use Tnt\Blog\Model\BlogPostPhoto;

class BlogPostManager extends Manager
{
    public $edit;

    public $tabbedContent;

    public $requiredFields = [];

    public function __construct(array $kwargs = [])
    {
        $categories = true;
        $authors = true;
        $photos = true;
        $advancedLayout = true;
        $isPrivate = false;
        $isFeatured = false;
        $blockTypes = [];
        $languages = [];
        $requiredFields = [];

        extract( $kwargs, EXTR_IF_EXISTS );

        $this->requiredFields = $requiredFields;

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
                    'v8n_required' => $this->isRequired('title'),
                    'suggest_slug' => 'slug_'.$language,
                    'label' => 'title',
                ]),
                new StringEdit('slug_'.$language, [
                    'v8n_required' => $this->isRequired('slug'),
                    'handle_duplicate' => TRUE,
                    'slugify_on_blur' => TRUE,
                    'label' => 'slug',
                ]),
            ];

            $contentComponents[$language] = [
                new StringEdit('intro_text_'.$language, [
                    'v8n_required' => $this->isRequired('intro_text'),
                    'multiline' => TRUE,
                    'height' => 60,
                    'label' => 'intro text',
                ]),
                new StringEdit('badge_text_'.$language, [
                    'v8n_required' => $this->isRequired('badge_text'),
                    'label' => 'badge text',
                ]),
                new Stack(Stack::HORIZONTAL, [
                    new StringEdit('cta_title_'.$language, [
                        'v8n_required' => $this->isRequired('cta_title'),
                        'label' => 'title'
                    ]),
                    new StringEdit('cta_url_'.$language, [
                        'v8n_required' => $this->isRequired('cta_url'),
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

        $this->tabbedContent = new TabbedContent([
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
                'v8n_required' => $this->isRequired('publication_date'),
            ]),
            new Picker('photo', [
                'v8n_required' => $this->isRequired('photo'),
            ]),
            new BooleanEdit('is_visible'),
        ];

        if ($advancedLayout) {
            array_unshift($sidebarContent, new EnumEdit('layout', BlogPost::get_layout_enum()));
        }

        if ($isPrivate) {
            $sidebarContent[] = new BooleanEdit('is_private');
        }

        if ($isFeatured) {
            $sidebarContent[] = new BooleanEdit('is_featured');
        }

        $this->actions[] = $create = new Create([
            new Stack(Stack::VERTICAL, $sidebarContent),
        ], [
            'popup' => TRUE,
        ]);

        $this->actions[] = $this->edit = new Edit([
            new Stack(Stack::HORIZONTAL, [
                $this->tabbedContent,
                new Stack(Stack::VERTICAL, $sidebarContent, [
                    'title' => 'General information'
                ]),
            ], [
                'grid' => [5, 2],
            ]),
        ]);

        if ($categories) {
            $this->tabbedContent->tabs[] = [ 'Categories', [
                new ForeignKeySelect('category', [
                    'to_string' => 'title_'.$languages[0],
                ]),
            ] ];
        }

        if ($authors) {
            $this->tabbedContent->tabs[] = [ 'Author', [
                new ForeignKeySelect('author', [
                    'null' => TRUE
                ]),
            ] ];
        }

        if ($photos) {
            $this->tabbedContent->tabs[] = [ 'Media', [
               new InlineManager(new BlogPostPhotoManager(), [
                   'restrict_by_foreign_key' => 'blog_post',
               ])
            ] ];
        }

        $this->actions[] = $duplicate = new Execute(function ($row) {

            $blogPost = new BlogPost();
            $blogPost->created = time();
            $blogPost->updated = time();
            $blogPost->title_nl = $row->title_nl.' (duplicate)';
            $blogPost->title_fr = $row->title_fr.' (duplicate)';
            $blogPost->title_en = $row->title_en.' (duplicate)';
            $blogPost->slug_nl = $row->slug_nl.'-duplicate';
            $blogPost->slug_fr = $row->slug_fr.'-duplicate';
            $blogPost->slug_en = $row->slug_en.'-duplicate';
            $blogPost->intro_text_nl = $row->intro_text_nl;
            $blogPost->intro_text_fr = $row->intro_text_fr;
            $blogPost->intro_text_en = $row->intro_text_en;
            $blogPost->badge_text_nl = $row->badge_text_nl;
            $blogPost->badge_text_fr = $row->badge_text_fr;
            $blogPost->badge_text_en = $row->badge_text_en;
            $blogPost->cta_title_nl = $row->cta_title_nl;
            $blogPost->cta_title_fr = $row->cta_title_fr;
            $blogPost->cta_title_en = $row->cta_title_en;
            $blogPost->cta_url_nl = $row->cta_url_nl;
            $blogPost->cta_url_fr = $row->cta_url_fr;
            $blogPost->cta_url_en = $row->cta_url_en;
            $blogPost->sort_index = $row->sort_index++;
            $blogPost->publication_date = $row->publication_date;
            $blogPost->is_visible = false;
            $blogPost->layout = $row->layout;
            $blogPost->photo = $row->photo;
            $blogPost->category = $row->category;
            $blogPost->author = $row->author;
            $blogPost->save();

            foreach ($row->blocks as $block) {

                $blogPostBlock = new BlogPostBlock();
                $blogPostBlock->created = time();
                $blogPostBlock->updated = time();
                $blogPostBlock->blog_post = $blogPost;
                $blogPostBlock->type = $block->type;
                $blogPostBlock->sort_index = $block->sort_index;
                $blogPostBlock->photo = $block->photo;
                $blogPostBlock->title_nl = $block->title_nl;
                $blogPostBlock->title_fr = $block->title_fr;
                $blogPostBlock->title_en = $block->title_en;
                $blogPostBlock->body_nl = $block->body_nl;
                $blogPostBlock->body_fr = $block->body_fr;
                $blogPostBlock->body_en = $block->body_en;
                $blogPostBlock->quote_nl = $block->quote_nl;
                $blogPostBlock->quote_fr = $block->quote_fr;
                $blogPostBlock->quote_en = $block->quote_en;
                $blogPostBlock->save();
            }

            foreach ($row->photos as $photo) {

                $blogPostPhoto = new BlogPostPhoto();
                $blogPostPhoto->created = time();
                $blogPostPhoto->updated = time();
                $blogPostPhoto->blog_post = $blogPost;
                $blogPostPhoto->sort_index = $photo->sort_index;
                $blogPostPhoto->photo = $photo->photo;
                $blogPostPhoto->save();
            }

            return 'Blog post duplicated';

        }, [
            'id' => 'duplicate',
            'icon' => Execute::ICON_DUPLICATE,
        ]);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add post');

        $this->footer[] = new Pagination();

        $this->index = new Index([
            new StringView('title_'.$languages[0], [
                'link' => function ($row) use ($languages) {

                    Response::$language = $languages[0];

                    if (isset(Router::$urls[Response::$language]['blog::view'])) {
                        return \dry\url('blog::view', $row);
                    }

                    return '#';
                }
            ]),
            new DateView('publication_date'),
            $this->edit->create_link(),
            $delete->create_link(),
            new Popout([
                $duplicate->create_link(),
            ])
        ],[
            'field_to_row_class' => [
                'is_visible', NULL, IndexRow::STYLE_DISABLED
            ],
        ]);

        $this->index->sorter = new StaticSorter('publication_date', StaticSorter::DESC);

        $this->index->paginator = new Paginator(10);
    }

    /**
     * @param $field
     * @return bool
     */
    private function isRequired($field) {
        return in_array($field, $this->requiredFields);
    }
}