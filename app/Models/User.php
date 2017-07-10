<?php

namespace Tmd\LaravelSite\Models;

use Abibidu\Bit\Mask;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Mail;
use Tmd\LaravelSite\Libraries\LanguageHelpers;
use Tmd\LaravelSite\Libraries\Role;
use Tmd\LaravelSite\Mail\ForgotDetailsMail;
use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\Interfaces\UserInterface;
use Tmd\LaravelSite\Repositories\ImageRepository;

/**
 * Tmd\LaravelSite\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property int $roles
 * @property int $emailVerified
 * @property int $imageId
 * @property \Carbon\Carbon|null $deletedAt
 * @property \Carbon\Carbon|null $deactivatedAt
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Tmd\LaravelSite\Models\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereDeactivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelSite\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\Tmd\LaravelSite\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Tmd\LaravelSite\Models\User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends AbstractModel implements AuthenticatableContract, CanResetPasswordContract, UserInterface
{
    use Authenticatable;
    use CanResetPassword;
    use SoftDeletes;
    use Notifiable;

    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'admin' => 'bool',
        'imageId' => 'int',
    ];

    protected $visible = [
        'id',
        'username',
        'toDo'
    ];

    protected $dates = [
        self::DELETED_AT,
        'deactivatedAt',
    ];

    protected $rolesMask = null;

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array['image'] = app(ImageRepository::class)->find($this->imageId);
        $array['possessiveName'] = LanguageHelpers::possessive($this->username);

        return $array;
    }

    public function toFullArray()
    {
        $array = $this->toArray();

        // Add additional information such as email ddress.
        $array['email'] = $this->email;

        return $array;
    }

    /**
     * For AuthenticatableContract
     *
     * @return null
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->queue(
            new ForgotDetailsMail($token, $this)
        );
    }

    public function getRoles(): Mask
    {
        if (is_null($this->rolesMask)) {
            $this->rolesMask = new Mask($this->roles);
        }

        return $this->rolesMask;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getRoles()->has(Role::ADMIN);
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->getRoles()->has(Role::MODERATOR);
    }

    public function isDeactivated(): bool
    {
        return $this->deactivatedAt !== null;
    }
}
