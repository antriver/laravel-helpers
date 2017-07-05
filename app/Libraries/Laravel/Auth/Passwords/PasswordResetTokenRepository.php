<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Auth\Passwords;

use Carbon\Carbon;
use DB;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Tmd\LaravelSite\Libraries\Traits\GeneratesTokensTrait;
use Tmd\LaravelSite\Models\User;

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
     * @var string
     */
    protected $hashKey;

    /**
     * Create a new token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword|User $user
     *
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert(
            [
                'token' => $token,
                'userId' => $user->id,
                'createdAt' => new Carbon,
            ]
        );

        return $token;
    }

    /**
     * @param string $token
     *
     * @return object|null
     */
    public function find($token)
    {
        return $this->getTable()->where('token', $token)->first();
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword|User $user
     *
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where('userId', $user->id)->delete();
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword|User $user
     * @param  string                                           $token
     *
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $token = $this->getTable()->where('userId', $user->id)->where('token', $token)->first();

        return $token && !$this->tokenExpired($token);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  object $token
     *
     * @return bool
     */
    protected function tokenExpired($token)
    {
        $expiresAt = Carbon::parse($token->createdAt)->addSeconds($this->expires);

        return $expiresAt->isPast();
    }

    /**
     * Delete a token record.
     *
     * @param CanResetPasswordContract|User $user
     */
    public function delete(CanResetPasswordContract $user)
    {
        $this->getTable()->where('userId', $user->id)->delete();
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('createdAt', '<', $expiredAt)->delete();
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return DB::table('password_reset_tokens');
    }
}
