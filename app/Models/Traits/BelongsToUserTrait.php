<?php

namespace Tmd\LaravelSite\Models\Traits;

use Tmd\LaravelSite\Models\User;
use Tmd\LaravelSite\Repositories\UserRepository;

trait BelongsToUserTrait
{
    /**
     * Get the user this model belongs to or was created by.
     *
     * @return User
     */
    public function getUserId()
    {
        return $this->getAttribute('userId');
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->getUserRepository()->find($this->getUserId());
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return app(UserRepository::class);
    }
}
