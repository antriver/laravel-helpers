<?php

namespace Tmd\LaravelHelpers\Models;

use Tmd\LaravelHelpers\Models\Base\AbstractModel;
use Tmd\LaravelHelpers\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelHelpers\Models\Traits\BelongsToUserTrait;

/**
 * Tmd\LaravelHelpers\Models\EmailVerification
 *
 * @property int $id
 * @property int $userId
 * @property string $email
 * @property string $token
 * @property int|null $isChange Is this the initial verification, or changing an existing user?
 * @property string $createdAt
 * @property string|null $resentAt
 * @mixin \Eloquent
 */
class EmailVerification extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;

    protected $table = 'email_verifications';

    public $timestamps = false;

    public function getUrl()
    {
        return url('verify-email').'?id='.$this->id.'&token='.$this->token;
    }
}
