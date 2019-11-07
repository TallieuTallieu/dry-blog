<?php

namespace Tnt\Blog\Model;

use dry\orm\Model;

class BlogCategory extends Model
{
	const TABLE = 'blog_category';

	public function __toString(): String
	{
		return $this->title;
	}
}