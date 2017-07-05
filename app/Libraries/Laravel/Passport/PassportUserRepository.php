<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Passport;

use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface as PassportUserRepositoryInterface;
use Tmd\LaravelSite\Repositories\UserRepository;
use Tmd\LaravelPasswordUpdater\PasswordHasher;

class PassportUserRepository implements PassportUserRepositoryInterface
{
    /**
     * The hasher implementation.
     *
     * @var PasswordHasher
     */
    protected $hasher;

    /**
     * @var \Tmd\LaravelSite\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * Create a new repository instance.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher|PasswordHasher $hasher
     * @param UserRepository                                      $userRepository
     */
    public function __construct(PasswordHasher $hasher, UserRepository $userRepository)
    {
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string                $username
     * @param string                $password
     * @param string                $grantType
     * @param ClientEntityInterface $clientEntity
     *
     * @return bool|User|null
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $user = $this->userRepository->findOneBy('username', $username);
        if (!$user) {
            return false;
        }

        if ($this->hasher->verify($password, $user, 'password')) {
            return new User($user->getAuthIdentifier());
        }

        return null;
    }
}
