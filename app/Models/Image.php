<?php

namespace Stickable\Models;

use Config;
use Stickable\Models\Base\AbstractModel;
use Stickable\Models\Interfaces\BelongsToUserInterface;
use Stickable\Models\Traits\BelongsToUserTrait;

/**
 * Stickable\Models\Image
 *
 * @property int $id
 * @property int $userId
 * @property string $directory
 * @property string|null $filename
 * @property int|null $width
 * @property int|null $height
 * @property int|null $size
 * @property int|null $optimizedSize
 * @property bool $hasThumbnail
 * @property string|null $originalUrl
 * @property \Carbon\Carbon $createdAt
 * @property string|null $updatedAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereHasThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereOptimizedSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereOriginalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\Image whereWidth($value)
 * @mixin \Eloquent
 */
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
