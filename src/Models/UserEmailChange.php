<?php

namespace Tmd\LaravelHelpers\Models;

use Tmd\LaravelHelpers\Models\Base\AbstractModel;
use Tmd\LaravelHelpers\Models\Traits\CreatedAtWithoutUpdatedAtTrait;

/**
 * Tmd\LaravelHelpers\Models\UserEmailChange
 *
 * @property int $id
 * @property int $userId
 * @property string $oldEmail
 * @property string $newEmail
 * @property \Carbon\Carbon $createdAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserEmailChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserEmailChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserEmailChange whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserEmailChange whereOldEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Tmd\LaravelHelpers\Models\UserEmailChange whereUserId($value)
 * @mixin \Eloquent
 */
class UserEmailChange extends AbstractModel
{
    use CreatedAtWithoutUpdatedAtTrait;

    protected $table = 'user_email_changes';
}
