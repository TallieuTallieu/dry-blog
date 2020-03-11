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

    public static $special_fields = [
        'photo' => File::class,
        'blog_post' => BlogPost::class,
    ];

    public static function get_type_enum()
    {
        return [
            [ self::TYPE_TEXT_PHOTO, 'Text & photo', ],
            [ self::TYPE_PHOTO_TEXT, 'Photo & text', ],
            [ self::TYPE_TEXT, 'Text', ],
            [ self::TYPE_PHOTO, 'Photo', ],
            [ self::TYPE_TEXTFRAME, 'Text with frame', ],
            [ self::TYPE_QUOTE, 'Quote', ],
        ];
    }
}
