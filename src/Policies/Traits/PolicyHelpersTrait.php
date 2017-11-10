<?php

namespace Tmd\LaravelSite\Policies\Traits;

use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\Interfaces\BelongsToUserInterface;
use Tmd\LaravelSite\Models\User;

trait PolicyHelpersTrait
{
    /**
     * Return true if the authenticated user is a level that can modify this item.
     * Moderators can edit most objects, so by default this returns if the user is a moderator.
     * Override this method to restrict access to admins.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isPrivileged(User $user)
    {
        return $user->isModerator();
    }

    /**
     * Returns true if the authenticated user is the user that created this object.
     *
     * @param User                                 $user
     * @param AbstractModel|BelongsToUserInterface $model
     *
     * @return bool
     */
    public function isOwner(User $user, AbstractModel $model)
    {
        return $user && $user->id === $model->getUserId();
    }

    /**
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function isOwnerOrPrivileged(User $user, AbstractModel $model)
    {
        return $user && ($this->isOwner($user, $model) || $this->isPrivileged($user));
    }

    /**
     * Returns true if the user is a moderator.
     *
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function isModerator(User $user)
    {
        if (!$user instanceof \Stickable\Models\User) {
            throw new \Exception('The current user must be an instance of \Stickable\Models\User');
        }

        return $user->isModerator();
    }

    /**
     * Returns true if the user is an admin.
     *
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function isAdmin(User $user)
    {
        if (!$user instanceof \Stickable\Models\User) {
            throw new \Exception('The current user must be an instance of \Stickable\Models\User');
        }

        return $user->isAdmin();
    }
}
