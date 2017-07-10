<?php

namespace Tmd\LaravelSite\Models;

use Config;
use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelSite\Models\Traits\BelongsToUserTrait;

class Image extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;

    const DIRECTORY_AVATAR = 'avatar';
    const DIRECTORY_CATEGORY = 'cat';
    const DIRECTORY_CATEGORY_BG = 'cat-bg';
    const DIRECTORY_CONTENT = 'content'; // Misc. user images in comments/messages
    const DIRECTORY_POST = 'post';
    const DIRECTORY_STICKER = 'sticker';
    const DIRECTORY_STICKER_BG = 'sticker-bg';
    const DIRECTORY_STICKER_RAW = 'sticker-raw';
    const DIRECTORY_TASK = 'task';
    const DIRECTORY_TASK_BG = 'task-bg';

    /*
    const DIRECTORY_CONTENT = 'content';
    const DIRECTORY_COVERS = 'covers';
    const DIRECTORY_POSTS = 'posts';
    const DIRECTORY_PROFILES = 'profiles';
    const DIRECTORY_TOPICS = 'topics';*/

    protected $casts = [
        'id' => 'int',
        'userId' => 'int',
        'hasThumbnail' => 'bool',
    ];

    public $dates = [
        'createdAt',
    ];

    protected $visible = [
        'id',
        'width',
        'height'
    ];

    public $timestamps = false;

    public function toArray()
    {
        $array = parent::toArray();

        $array['url'] = $this->getUrl();

        return $array;
    }

    public function getUrl()
    {
        return Config::get('app.upload_url').'/'.$this->getPathname();
    }

    public function getPathname()
    {
        return $this->directory.'/'.$this->filename;
    }

    public static function getUserUploadableTypes()
    {
        return [
            self::DIRECTORY_AVATAR,
            self::DIRECTORY_POST,
        ];
    }
}
