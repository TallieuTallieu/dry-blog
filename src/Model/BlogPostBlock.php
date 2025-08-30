<?php

namespace Tnt\Blog\Model;

use dry\media\File;
use dry\orm\Model;

/**
 * @property string|null $video_type
 * @property string|null $video_id
 * @property File|null $video
 */
class BlogPostBlock extends Model
{
    const TABLE = 'blog_post_block';

    const TYPE_TEXT_PHOTO = 'text_photo';
    const TYPE_PHOTO_TEXT = 'photo_text';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTFRAME = 'textframe';
    const TYPE_PHOTO = 'photo';
    const TYPE_QUOTE = 'quote';
    const TYPE_TEXT_QUOTE = 'text_quote';
    const TYPE_QUOTE_TEXT = 'quote_text';
    const TYPE_VIDEO = 'video';
    const TYPE_TEXT_VIDEO = 'text_video';
    const TYPE_VIDEO_TEXT = 'video_text';
    const TYPE_EMBED = 'embed';

    const VIDEO_TYPE_FILE = 'file';
    const VIDEO_TYPE_VIMEO = 'vimeo';
    const VIDEO_TYPE_YOUTUBE = 'youtube';

    public static $special_fields = [
        'photo' => File::class,
        'video' => File::class,
        'blog_post' => BlogPost::class,
    ];

    public static function get_type_enum(): array
    {
        return [
            [self::TYPE_TEXT_PHOTO, 'Text & photo',],
            [self::TYPE_PHOTO_TEXT, 'Photo & text',],
            [self::TYPE_TEXT, 'Text',],
            [self::TYPE_PHOTO, 'Photo',],
            [self::TYPE_TEXTFRAME, 'Text with frame',],
            [self::TYPE_QUOTE, 'Quote',],
            [self::TYPE_TEXT_QUOTE, 'Text & quote',],
            [self::TYPE_QUOTE_TEXT, 'Quote & text',],
            [self::TYPE_VIDEO, 'Video',],
            [self::TYPE_TEXT_VIDEO, 'Text & video',],
            [self::TYPE_VIDEO_TEXT, 'Video & text',],
            [self::TYPE_EMBED, 'Embed',],
        ];
    }

    public function getVideoUrl(): ?string
    {
        if ($this->video_type === self::VIDEO_TYPE_YOUTUBE) {
            return 'https://www.youtube.com/embed/' . $this->video_id . '?rel=0&autoplay=0';
        }

        if ($this->video_type === self::VIDEO_TYPE_VIMEO) {
            return 'https://player.vimeo.com/video/' . $this->video_id;
        }

        if ($this->video_type === self::VIDEO_TYPE_FILE) {
            return \app\MEDIA_ROOT . $this->video;
        }

        return null;
    }
}
