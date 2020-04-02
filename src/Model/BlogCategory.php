<?php

namespace Tnt\Blog\Model;

use dry\orm\Model;

class BlogCategory extends Model
{
    const TABLE = 'blog_category';

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title_en ? $this->title_en : ( $this->title_nl ? $this->title_nl : $this->title_fr );
    }
}