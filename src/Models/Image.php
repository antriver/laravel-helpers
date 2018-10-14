<?php

namespace Tmd\LaravelHelpers\Models;

use Config;
use Tmd\LaravelHelpers\Models\Base\AbstractModel;
use Tmd\LaravelHelpers\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelHelpers\Models\Traits\BelongsToUserTrait;

class Image extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;



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
}
