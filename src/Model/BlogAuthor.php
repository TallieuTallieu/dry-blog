<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;
use dry\orm\special\Boolean;

class BlogAuthor extends Model
{
	const TABLE = 'blog_author';

	public static $special_fields = [
		'photo' => File::class,
		'is_visible' => Boolean::class,
	];

	public function get_posts()
	{
	    return $this->has_many(BlogPost::class, 'author');
	}

	public function delete()
	{
		foreach( $this->posts as $p )
		{
			$p->author = NULL;
			$p->save();
		}

		parent::delete();
	}

	public function __toString()
	{
		return $this->first_name . ' ' . $this->last_name . ' (' . $this->function . ')';
	}
}