<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;

class BlogPostPhoto extends Model
{
    const TABLE = 'blog_post_photo';

    public static $special_fields = [
        'photo' => File::class,
        'blog_post' => BlogPost::class,
    ];
}