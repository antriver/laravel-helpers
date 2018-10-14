<?php

namespace Tmd\LaravelHelpers\Libraries\Laravel\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;
use Illuminate\Support\Facades\Cache;
use Tmd\LaravelHelpers\Models\User;
use Tmd\LaravelPasswordUpdater\PasswordHasher;
use Tmd\LaravelHelpers\Repositories\Interfaces\UserRepositoryInterface;

class RepositoryUserProvider implements UserProviderInterface
{
    /**
     * @var PasswordHasher
     */
    protected $hasher;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($id)
    {
        if ($user = $this->userRepository->find($id)) {
            /** @var Authenticatable $user */
            return $user;
        }

        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return Cache::remember(
            'user-id-token:'.$identifier.$token,
            60,
            function () use ($identifier, $token) {
                $user = $this->userRepository->find($identifier);

                if ($user) {
                    /** @var Authenticatable $user */
                    if ($user->getRememberToken() === $token) {
                        return $user;
                    }
                }

                return null;
            }
        );
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|User $user
     * @param  string                                          $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $this->userRepository->persist($user);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|User|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = User::query();

        foreach ($credentials as $key => $value) {
            if (!str_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        /** @var User|null $user */
        $user = $query->first();

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|User $user
     * @param  array                                      $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->verify($plain, $user, 'password');
    }
}
