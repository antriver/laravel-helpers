<?php

namespace Tmd\LaravelHelpers\Models;

use Tmd\LaravelHelpers\Models\Base\AbstractModel;

/**
 * Tmd\LaravelHelpers\Models\UserSocialAccount
 *
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel
{
    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
