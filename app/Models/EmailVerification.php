<?php

namespace Tmd\LaravelSite\Models;

use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelSite\Models\Traits\BelongsToUserTrait;

/**
 * Stickable\Models\EmailVerification
 *
 * @property int $id
 * @property int $userId
 * @property string $email
 * @property string $token
 * @property int|null $isChange Is this the initial verification, or changing an existing user?
 * @property string $createdAt
 * @property string|null $resentAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereIsChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereResentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Stickable\Models\EmailVerification whereUserId($value)
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
