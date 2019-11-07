<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;
use dry\orm\special\Boolean;

class BlogPost extends Model
{
    const TABLE = 'blog_post';

    const LAYOUT_CLEAN = 'clean';
    const LAYOUT_SIDEBAR = 'sidebar';

    public static $special_fields = [
        'photo' => File::class,
        'category' => BlogCategory::class,
        'author' => BlogAuthor::class,
        'is_visible' => Boolean::class,
    ];

    public static function get_layout_enum()
    {
        return [
            [self::LAYOUT_SIDEBAR, 'With sidebar'],
            [self::LAYOUT_CLEAN, 'Clean'],
        ];
    }

    public function get_siblings()
    {
        return self::all('
            WHERE category = ?
            AND id != ?
            AND publication_date < UNIX_TIMESTAMP( NOW() )
            ORDER BY publication_date DESC
            LIMIT 4
        ', $this->category ? $this->category->id : NULL, $this->id);
    }

    public function get_blocks()
    {
        return $this->has_many(BlogPostBlock::class, 'blog_post', 'ORDER BY sort_index');
    }

    public function delete()
    {
        foreach($this->blocks as $b) {
            $b->delete();
        }

        parent::delete();
    }
}