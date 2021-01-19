<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\EnumSwitcher;
use dry\admin\component\I18nSwitcher;
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
    public function __construct(array $blockTypes, array $languages)
    {
        parent::__construct(BlogPostBlock::class, [
            'singular' => 'content',
            'plural' => 'content'
        ]);

        $blockContent = [];
        $blockContentComponents = [];

        foreach ($languages as $language) {
            if (in_array('text-photo', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_TEXT_PHOTO, 'Text & photo', [
                    new StringEdit('title_'.$language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new RichtextEdit2('body_'.$language, [
                            'label' => 'body',
                        ]),
                        new Picker('photo'),
                    ]),
                ]];
            }

            if (in_array('photo-text', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_PHOTO_TEXT, 'Photo & text', [
                    new StringEdit('title_'.$language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new Picker('photo'),
                        new RichtextEdit2('body_'.$language, [
                            'label' => 'body',
                        ]),
                    ]),
                ]];
            }

            if (in_array('photo', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_PHOTO, 'Photo', [
                    new Picker('photo', [
                        'v8n_required' => TRUE,
                    ]),
                ]];
            }

            if (in_array('text', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_TEXT, 'Text', [
                    new StringEdit('title_'.$language, [
                        'label' => 'title',
                    ]),
                    new RichtextEdit2('body_'.$language, [
                        'label' => 'body',
                    ]),
                ]];
            }

            if (in_array('textframe', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_TEXTFRAME, 'Textframe', [
                    new StringEdit('title_'.$language, [
                        'label' => 'title',
                    ]),
                    new RichtextEdit2('body_'.$language, [
                        'label' => 'body',
                    ]),
                ]];
            }

            if (in_array('quote', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_QUOTE, 'Quote', [
                    new StringEdit('quote_'.$language, [
                        'label' => 'quote',
                    ]),
                ]];
            }

            if (in_array('quote-text', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_QUOTE_TEXT, 'Quote & text', [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new StringEdit('quote_' . $language, [
                            'label' => 'quote',
                            'multiline' => true,
                            'height' => 75,
                        ]),
                        new RichtextEdit2('body_' . $language, [
                            'label' => 'body',
                        ]),
                    ], [
                        'grid' => [2, 3]
                    ]),
                ]];
            }

            if (in_array('text-quote', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_TEXT_QUOTE, 'Text & quote', [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new RichtextEdit2('body_' . $language, [
                            'label' => 'body',
                        ]),
                        new StringEdit('quote_' . $language, [
                            'label' => 'quote',
                            'multiline' => true,
                            'height' => 75,
                        ]),
                    ], [
                        'grid' => [3, 2]
                    ]),
                ]];
            }

            if (in_array('text-video', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_TEXT_VIDEO, 'Text & video', [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new RichtextEdit2('body_' . $language, [
                            'label' => 'body',
                        ]),
                        new Stack(Stack::VERTICAL, [
                            new EnumSwitcher('video_type', [
                                [BlogPostBlock::VIDEO_TYPE_FILE, 'File', [
                                    new Picker('video', [
                                        'v8n_required' => true,
                                        'v8n_mimetype' => [
                                            'video/mp4'
                                        ],
                                    ]),
                                    new StringEdit('media_credit_' . $language, ['label' => 'video credit']),
                                ]],
                                [BlogPostBlock::VIDEO_TYPE_VIMEO, 'Vimeo', [
                                    new StringEdit('video_id', [
                                        'v8n_required' => true
                                    ]),
                                    new Picker('photo', ['label' => 'video thumbnail']),
                                    new StringEdit('media_credit_' . $language, ['label' => 'video credit']),
                                ]],
                                [BlogPostBlock::VIDEO_TYPE_YOUTUBE, 'Youtube', [
                                    new StringEdit('video_id', [
                                        'v8n_required' => true
                                    ]),
                                    new Picker('photo', ['label' => 'video thumbnail']),
                                    new StringEdit('media_credit_' . $language, ['label' => 'video credit']),
                                ]],
                            ]),
                        ]),
                    ]),
                ]];
            }

            if (in_array('video-text', $blockTypes)) {
                $blockContent[] = [BlogPostBlock::TYPE_VIDEO_TEXT, 'Video & text', [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new Stack(Stack::VERTICAL, [
                            new EnumSwitcher('video_type', [
                                [BlogPostBlock::VIDEO_TYPE_FILE, 'File', [
                                    new Picker('video', [
                                        'v8n_required' => true,
                                        'v8n_mimetype' => [
                                            'video/mp4'
                                        ],
                                    ]),
                                    new StringEdit('media_credit_' . $language, ['label' => 'video credit']),
                                ]],
                                [BlogPostBlock::VIDEO_TYPE_VIMEO, 'Vimeo', [
                                    new StringEdit('video_id', [
                                        'v8n_required' => true
                                    ]),
                                    new Picker('photo', ['label' => 'video thumbnail']),
                                    new StringEdit('media_credit_' . $language, ['label' => 'video credit']),
                                ]],
                                [BlogPostBlock::VIDEO_TYPE_YOUTUBE, 'Youtube', [
                                    new StringEdit('video_id', [
                                        'v8n_required' => true
                                    ]),
                                    new Picker('photo', ['label' => 'video thumbnail']),
                                    new StringEdit('media_credit_' . $language, ['label' => 'video credit']),
                                ]],
                            ]),
                        ]),
                        new RichtextEdit2('body_' . $language, [
                            'label' => 'body',
                        ]),
                    ]),
                ]];
            }

            $blockContentComponents[$language] = new EnumSwitcher('type', $blockContent, [
                'mode' => EnumSwitcher::TABS,
            ]);

            $blockContent = [];
        }

        $blockContentContainer = $blockContentComponents[$languages[0]];

        if (count($languages) > 1) {
            $blockContentContainer = new I18nSwitcher($blockContentComponents);
        }

        $this->actions[] = $create = new Create([$blockContentContainer]);

        $this->actions[] = $edit = new Edit($create->components);

        $this->actions[] = $delete = new Delete();

        $this->header[] = $create->create_link('Add content block');

        $this->index = new Index([
            new SortHandle(),
            new EnumView('type', BlogPostBlock::get_type_enum()),
            $edit->create_link(),
            $delete->create_link(),
        ]);

        $this->index->sorter = new DragSorter('sort_index');
    }
}
