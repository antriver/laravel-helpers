<?php

namespace Tmd\LaravelSite\Models;

use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Stickable\Models\UserEmailChange
 *
 * @property int $id
 * @property int $userId
 * @property string $oldEmail
 * @property string $newEmail
 * @property \Carbon\Carbon $createdAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\UserEmailChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\UserEmailChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\UserEmailChange whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\UserEmailChange whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\UserEmailChange whereUserId($value)
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
