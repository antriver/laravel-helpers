<?php

namespace Tmd\LaravelSite\Models;

use Tmd\LaravelSite\Models\Base\AbstractModel;

/**
 * Tmd\LaravelSite\Models\UserSocialAccount
 *
 * @mixin \Eloquent
 */
class UserSocialAccount extends AbstractModel
{
    protected $table = 'user_social_accounts';

    public $timestamps = true;
}
