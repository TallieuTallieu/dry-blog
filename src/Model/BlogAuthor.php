<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;
use dry\orm\relationship\HasMany;
use dry\orm\special\Boolean;

class BlogAuthor extends Model
{
    const TABLE = 'blog_author';

    public static $special_fields = [
        'photo' => File::class,
        'is_visible' => Boolean::class,
    ];

    /**
     * @return \dry\orm\relationship\HasMany
     */
    public function get_posts(): HasMany
    {
        return $this->has_many(BlogPost::class, 'author');
    }

    /**
     *
     */
    public function delete(): void
    {
        foreach($this->posts as $p) {
            $p->author = NULL;
            $p->save();
        }

        parent::delete();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->first_name.' '.$this->last_name;
    }
}