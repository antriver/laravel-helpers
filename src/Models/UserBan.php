<?php

namespace Tmd\LaravelHelpers\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tmd\LaravelHelpers\Models\Base\AbstractModel;
use Tmd\LaravelHelpers\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelHelpers\Models\Traits\BelongsToUserTrait;
use Tmd\LaravelHelpers\Repositories\UserRepository;

/**
 * Tmd\LaravelHelpers\Models\UserBan
 *
 * @property int $id
 * @property int|null $userId
 * @property string|null $ip
 * @property int|null $byUserId
 * @property string $reason
 * @property \Carbon\Carbon $createdAt
 * @property \Carbon\Carbon|null $updatedAt
 * @property \Carbon\Carbon|null $deletedAt
 * @property \Carbon\Carbon|null $expiresAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan current()
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan expired()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Tmd\LaravelHelpers\Models\UserBan onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserBan whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Tmd\LaravelHelpers\Models\UserBan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Tmd\LaravelHelpers\Models\UserBan withoutTrashed()
 * @mixin \Eloquent
 */
class UserBan extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;
    use SoftDeletes;

    protected $table = 'user_bans';

    protected $dates = [self::DELETED_AT, 'expiresAt'];

    public function getMessage()
    {
        $who = null;
        if ($this->userId) {
            $user = $this->getUser();
            $who = $user->username;
        } elseif ($this->ip) {
            $who = $this->ip;
        }

        $str = $who.' is banned';

        if ($this->expiresAt) {
            $str .= ' until '.display_datetime($this->expiresAt, false);
        }
        if ($this->reason) {
            $str .= ' because: '.$this->reason;
        } else {
            $str .= '.';
        }

        return $str;
    }

    public function scopeCurrent($query)
    {
        $query->where(
            function ($query) {
                return $query->whereNull('expiresAt')->orWhere('expiresAt', '>', (new Carbon())->toDateTimeString());
            }
        );
    }

    public function scopeExpired($query)
    {
        $query->where(
            function ($query) {
                return $query->whereNotNull('expiresAt')->where('expiresAt', '<=', (new Carbon())->toDateTimeString());
            }
        );
    }

    public function isExpired()
    {
        return $this->expiresAt !== null && $this->expiresAt <= Carbon::now();
    }

    /**
     * @return User|null
     */
    public function getByUser()
    {
        return $this->getRelationFromRepository('byUserId', UserRepository::class);
    }
}
