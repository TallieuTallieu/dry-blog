<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;

class BlogPostBlock extends Model
{
    const TABLE = 'blog_post_block';

    const TYPE_TEXT_PHOTO = 'text_photo';
    const TYPE_PHOTO_TEXT = 'photo_text';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTFRAME = 'textframe';
    const TYPE_PHOTO = 'photo';
    const TYPE_QUOTE = 'quote';
    const TYPE_TEXT_QUOTE = 'text_quote';
    const TYPE_QUOTE_TEXT = 'quote_text';

    public static $special_fields = [
        'photo' => File::class,
        'blog_post' => BlogPost::class,
    ];

    /**
     * @return array
     */
    public static function get_type_enum(): array
    {
        return [
            [ self::TYPE_TEXT_PHOTO, 'Text & photo', ],
            [ self::TYPE_PHOTO_TEXT, 'Photo & text', ],
            [ self::TYPE_TEXT, 'Text', ],
            [ self::TYPE_PHOTO, 'Photo', ],
            [ self::TYPE_TEXTFRAME, 'Text with frame', ],
            [ self::TYPE_QUOTE, 'Quote', ],
            [ self::TYPE_TEXT_QUOTE, 'Text & quote', ],
            [ self::TYPE_QUOTE_TEXT, 'Quote & text', ],
        ];
    }
}
