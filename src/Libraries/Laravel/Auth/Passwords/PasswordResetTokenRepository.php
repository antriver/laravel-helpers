<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Auth;

use Carbon\Carbon;
use DB;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Tmd\LaravelSite\Libraries\Traits\GeneratesTokensTrait;

class PasswordResetTokenRepository implements TokenRepositoryInterface
{
    use GeneratesTokensTrait;

    /**
     * How long (in seconds) are tokens valid for.
     * (1 hour)
     *
     * @var int
     */
    protected $expires = 3600;

    /**
     * Create a new token.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword|Authenticatable $user
     *
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->generateToken();

        $this->getTable()->insert(
            [
                'token' => $token,
                'userId' => $user->getAuthIdentifier(),
                'createdAt' => new Carbon,
            ]
        );

        return $token;
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword|Authenticatable $user
     * @param string $token
     *
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $token = $this->getTable()->where('userId', $user->getAuthIdentifier())->where('token', $token)->first();

        return $token && !$this->isTokenExpired($token);
    }

    /**
     * Delete a token record.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword|Authenticatable $user
     */
    public function delete(CanResetPasswordContract $user)
    {
        $this->getTable()->where('userId', $user->getAuthIdentifier())->delete();
    }

    /**
     * Delete expired tokens.
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('createdAt', '<', $expiredAt)->delete();
    }

    /**
     * Determine if the token has expired.
     *
     * @param object $tokenData
     *
     * @return bool
     */
    protected function isTokenExpired($tokenData)
    {
        $expiresAt = Carbon::parse($tokenData->createdAt)->addSeconds($this->expires);

        return $expiresAt->isPast();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return DB::table('password_reset_tokens');
    }
}
