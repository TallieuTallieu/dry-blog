<?php

namespace Tnt\Blog\Admin;

use dry\admin\component\EnumSwitcher;
use dry\admin\component\I18nSwitcher;
use app\container\Application;
use Oak\Contracts\Config\RepositoryInterface;
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
            'plural' => 'content',
        ]);

        $blockContent = [];

        $app = Application::get();
        $config = $app->get(RepositoryInterface::class);
        $multiLang = count($languages) > 1;

        $videoTypes = $this->getVideoTypes(
            $languages,
            $config->get('blog.video_types', [])
        );

        if (in_array('text-photo', $blockTypes)) {
            $textPhotoBlocks = [];

            foreach ($languages as $language) {
                $textPhotoBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new RichtextEdit2('body_' . $language, [
                            'label' => 'body',
                        ]),
                        new Picker('photo'),
                    ]),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_TEXT_PHOTO,
                'Text & photo',
                $this->wrapWithI18nSwitcher($textPhotoBlocks, $multiLang),
            ];
        }

        if (in_array('photo-text', $blockTypes)) {
            $photoTextBlocks = [];

            foreach ($languages as $language) {
                $photoTextBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(Stack::HORIZONTAL, [
                        new Picker('photo'),
                        new RichtextEdit2('body_' . $language, [
                            'label' => 'body',
                        ]),
                    ]),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_PHOTO_TEXT,
                'Photo & text',
                $this->wrapWithI18nSwitcher($photoTextBlocks, $multiLang),
            ];
        }

        if (in_array('photo', $blockTypes)) {
            $blockContent[] = [
                BlogPostBlock::TYPE_PHOTO,
                'Photo',
                [
                    new Picker('photo', [
                        'v8n_required' => true,
                    ]),
                ],
            ];
        }

        if (in_array('text', $blockTypes)) {
            $textBlocks = [];

            foreach ($languages as $language) {
                $textBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new RichtextEdit2('body_' . $language, [
                        'label' => 'body',
                    ]),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_TEXT,
                'Text',
                $this->wrapWithI18nSwitcher($textBlocks, $multiLang),
            ];
        }

        if (in_array('textframe', $blockTypes)) {
            $textframeBlocks = [];

            foreach ($languages as $language) {
                $textframeBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new RichtextEdit2('body_' . $language, [
                        'label' => 'body',
                    ]),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_TEXTFRAME,
                'Textframe',
                $this->wrapWithI18nSwitcher($textframeBlocks, $multiLang),
            ];
        }

        if (in_array('quote', $blockTypes)) {
            $quoteBlocks = [];

            foreach ($languages as $language) {
                $quoteBlocks[$language] = [
                    new StringEdit('quote_' . $language, [
                        'label' => 'quote',
                    ]),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_QUOTE,
                'Quote',
                $this->wrapWithI18nSwitcher($quoteBlocks, $multiLang),
            ];
        }

        if (in_array('quote-text', $blockTypes)) {
            $quoteTextBlocks = [];

            foreach ($languages as $language) {
                $quoteTextBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(
                        Stack::HORIZONTAL,
                        [
                            new StringEdit('quote_' . $language, [
                                'label' => 'quote',
                                'multiline' => true,
                                'height' => 75,
                            ]),
                            new RichtextEdit2('body_' . $language, [
                                'label' => 'body',
                            ]),
                        ],
                        [
                            'grid' => [2, 3],
                        ]
                    ),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_QUOTE_TEXT,
                'Quote & text',
                $this->wrapWithI18nSwitcher($quoteTextBlocks, $multiLang),
            ];
        }

        if (in_array('text-quote', $blockTypes)) {
            $textQuoteBlocks = [];

            foreach ($languages as $language) {
                $textQuoteBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new Stack(
                        Stack::HORIZONTAL,
                        [
                            new RichtextEdit2('body_' . $language, [
                                'label' => 'body',
                            ]),
                            new StringEdit('quote_' . $language, [
                                'label' => 'quote',
                                'multiline' => true,
                                'height' => 75,
                            ]),
                        ],
                        [
                            'grid' => [3, 2],
                        ]
                    ),
                ];
            }

            $blockContent[] = [
                BlogPostBlock::TYPE_TEXT_QUOTE,
                'Text & quote',
                $this->wrapWithI18nSwitcher($textQuoteBlocks, $multiLang),
            ];
        }

        if (in_array('video', $blockTypes)) {
            $videoBlocks = [];

            foreach ($languages as $language) {
                $videoBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                ];
            }

            $videoBlocks = $this->wrapWithI18nSwitcher(
                $videoBlocks,
                $multiLang
            );

            $videoBlocks[] = new EnumSwitcher('video_type', $videoTypes);

            $blockContent[] = [
                BlogPostBlock::TYPE_VIDEO,
                'Video',
                $videoBlocks,
            ];
        }

        if (in_array('text-video', $blockTypes)) {
            $textVideoBlocks = [];

            foreach ($languages as $language) {
                $textVideoBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new RichtextEdit2('body_' . $language, [
                        'label' => 'body',
                    ]),
                ];
            }

            $textVideoBlocks = $this->wrapWithI18nSwitcher(
                $textVideoBlocks,
                $multiLang
            );

            $blockContent[] = [
                BlogPostBlock::TYPE_TEXT_VIDEO,
                'Text & video',
                [
                    new Stack(Stack::HORIZONTAL, [
                        $textVideoBlocks,
                        new EnumSwitcher('video_type', $videoTypes),
                    ]),
                ],
            ];
        }

        if (in_array('video-text', $blockTypes)) {
            $videoTextBlocks = [];

            foreach ($languages as $language) {
                $videoTextBlocks[$language] = [
                    new StringEdit('title_' . $language, [
                        'label' => 'title',
                    ]),
                    new RichtextEdit2('body_' . $language, [
                        'label' => 'body',
                    ]),
                ];
            }

            $videoTextBlocks = $this->wrapWithI18nSwitcher(
                $videoTextBlocks,
                $multiLang
            );

            $blockContent[] = [
                BlogPostBlock::TYPE_VIDEO_TEXT,
                'Video & text',
                [
                    new Stack(Stack::HORIZONTAL, [
                        new EnumSwitcher('video_type', $videoTypes),
                        $textVideoBlocks,
                    ]),
                ],
            ];
        }

        if (in_array('embed', $blockTypes)) {
            $blockContent[] = [
                BlogPostBlock::TYPE_EMBED,
                'Embed',
                [
                    new StringEdit('embed_id', [
                        'v8n_required' => true,
                    ]),
                ],
            ];
        }

        $enumswitcher = new EnumSwitcher('type', $blockContent, [
            'mode' => EnumSwitcher::TABS,
        ]);

        $this->actions[] = $create = new Create([$enumswitcher]);

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

    private function getVideoTypes(array $languages, array $types): array
    {
        $multiLang = count($languages) > 1;
        $mediaCreditBlock = [];

        foreach ($languages as $index => $language) {
            $key = $multiLang ? $language : $index;

            $mediaCreditBlock[$key] = new StringEdit(
                'media_credit_' . $language,
                [
                    'label' => 'video credit',
                ]
            );
        }

        if ($multiLang) {
            $mediaCreditBlock = new I18nSwitcher($mediaCreditBlock);
        } else {
            $mediaCreditBlock = reset($mediaCreditBlock);
        }

        $videoTypes = [
            'file' => [
                BlogPostBlock::VIDEO_TYPE_FILE,
                'File',
                [
                    new Picker('video', [
                        'v8n_required' => true,
                        'v8n_mimetype' => ['video/mp4'],
                    ]),
                    new Picker('photo', ['label' => 'video thumbnail']),
                    $mediaCreditBlock,
                ],
            ],
            'vimeo' => [
                BlogPostBlock::VIDEO_TYPE_VIMEO,
                'Vimeo',
                [
                    new StringEdit('video_id', [
                        'v8n_required' => true,
                    ]),
                    new Picker('photo', ['label' => 'video thumbnail']),
                    $mediaCreditBlock,
                ],
            ],
            'youtube' => [
                BlogPostBlock::VIDEO_TYPE_YOUTUBE,
                'Youtube',
                [
                    new StringEdit('video_id', [
                        'v8n_required' => true,
                    ]),
                    new Picker('photo', ['label' => 'video thumbnail']),
                    $mediaCreditBlock,
                ],
            ],
        ];

        return array_filter(
            $videoTypes,
            function ($type, $key) use ($types) {
                return in_array($key, $types);
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    public function wrapWithI18nSwitcher($blocks, bool $multiLang)
    {
        if ($multiLang) {
            return [new I18nSwitcher($blocks)];
        }
        return array_values($blocks);
    }
}
