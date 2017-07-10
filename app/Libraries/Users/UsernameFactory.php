<?php

namespace Tmd\LaravelSite\Libraries\Users;

use Laravel\Socialite\AbstractUser;
use Tmd\LaravelSite\Http\Traits\ValidatesUserCredentialsTrait;
use Tmd\LaravelSite\Repositories\UserRepository;

class UsernameFactory
{
    use ValidatesUserCredentialsTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UsernameFactory constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function makeUsername()
    {
        $latestUserId = $this->userRepository->getMaxId();

        return 'Member'.($latestUserId + 1);
    }

    public function makeUsernameFromSocialUser(AbstractUser $socialUser)
    {
        $username = null;

        if ($nickname = $socialUser->getNickname()) {
            $username = $nickname;
        } elseif ($fullName = $socialUser->getName()) {
            $username = $fullName;
        } else {
            $username = $this->makeUsername();
        }

        return $this->makeValid($username);
    }

    /**
     * Appends a number to the end of a username until there is no other user with that username.
     *
     * @param string $username
     *
     * @return string
     */
    private function makeValid($username)
    {
        $username = preg_replace('/[^A-Za-z0-9_-]/i', '', $username);

        $salt = 1;
        $originalUsername = $username;
        while ($this->userRepository->findOneBy('username', $username)) {
            ++$salt;
            $username = $originalUsername.$salt;
        }

        return $username;
    }
}
