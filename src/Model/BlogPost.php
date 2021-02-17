<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;
use dry\orm\relationship\HasMany;
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
        'is_private' => Boolean::class,
    ];

    /**
     * @return array
     */
    public static function get_layout_enum(): array
    {
        return [
            [self::LAYOUT_SIDEBAR, 'With sidebar'],
            [self::LAYOUT_CLEAN, 'Clean'],
        ];
    }

    /**
     * @return \dry\orm\relationship\HasMany
     */
    public function get_blocks(): HasMany
    {
        return $this->has_many(BlogPostBlock::class, 'blog_post', 'ORDER BY sort_index');
    }

    /**
     * @return \dry\orm\relationship\HasMany
     */
    public function get_photos(): HasMany
    {
        return $this->has_many(BlogPostPhoto::class, 'blog_post', 'ORDER BY sort_index');
    }

    /**
     * Delete item
     */
    public function delete(): void
    {
        foreach($this->blocks as $b) {
            $b->delete();
        }

        foreach($this->photos as $p) {
            $p->delete();
        }

        parent::delete();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title_en ? $this->title_en : ( $this->title_nl ? $this->title_nl : $this->title_fr );
    }
}