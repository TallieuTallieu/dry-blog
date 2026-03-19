<?php

namespace Tnt\Blog\Model;

use dry\orm\Model;
use Tnt\Blog\Traits\HasTranslation;

class BlogCategory extends Model
{
    use HasTranslation;

    protected $translation_fields = ['title', 'slug'];

    const TABLE = 'blog_category';

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title_en ? $this->title_en : ( $this->title_nl ? $this->title_nl : $this->title_fr );
    }
}