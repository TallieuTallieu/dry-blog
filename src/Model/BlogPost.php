<?php

namespace Tnt\Blog\Model;

use Carbon\Carbon;
use dry\media\File;
use dry\orm\Model;
use dry\orm\relationship\HasMany;
use dry\orm\special\Boolean;

/**
 * @property $blocks
 * @property $photos
 */
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
        'is_featured' => Boolean::class,
    ];

    public function __toString(): string
    {
        return $this->title_en ? $this->title_en : ( $this->title_nl ? $this->title_nl : $this->title_fr );
    }

    public static function get_layout_enum(): array
    {
        return [
            [self::LAYOUT_SIDEBAR, 'With sidebar'],
            [self::LAYOUT_CLEAN, 'Clean'],
        ];
    }

    public function get_blocks(): HasMany
    {
        return $this->has_many(BlogPostBlock::class, 'blog_post', 'ORDER BY sort_index');
    }

    public function get_photos(): HasMany
    {
        return $this->has_many(BlogPostPhoto::class, 'blog_post', 'ORDER BY sort_index');
    }

    public function save(): void
    {
        if ($this->publication_hour) {
            $dateTimestamp = $this->publication_date;
            $hourTimestamp = Carbon::createFromTimeString($this->publication_hour)->timestamp - Carbon::now()->startOfDay()->timestamp;

            $this->publication_timestamp = $dateTimestamp + $hourTimestamp;
        }

        if (! $this->publication_hour) {
            $this->publication_timestamp = $this->publication_date;
        }

        parent::save();
    }

    public function delete(): void
    {
        foreach($this->blocks as $block) {
            $block->delete();
        }

        foreach($this->photos as $photo) {
            $photo->delete();
        }

        parent::delete();
    }
}