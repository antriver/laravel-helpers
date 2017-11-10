<?php

namespace Tmd\LaravelSite\Policies\Traits;

use Stickable\Models\Interfaces\FeaturableInterface;
use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\User;
use Tmd\LaravelSite\Policies\Base\AbstractPolicy;

trait DefaultModelPolicyTrait
{
    use PolicyHelpersTrait;

    /**
     * Can the current user create a new model?
     *
     * @param User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        // By default, everybody can create a model.
        return true;
    }

    /**
     * Can the current user create a new model belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function createForUser(User $user, User $forUser)
    {
        return $user->id === $forUser->id;
    }

    /**
     * Can the current user view an existing model?
     *
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function view(User $user, AbstractModel $model)
    {
        // By default, everybody can view a model.
        return true;
    }

    /**
     * Can the current user view existing models belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function viewForUser(User $user, User $forUser)
    {
        return true;
    }

    /**
     * Can the current user edit an existing model?
     *
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function update(User $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can edit a model.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    /**
     * Can the current user edit existing models belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function updateForUser(User $user, User $forUser)
    {
        return $user->id === $forUser->id;
    }

    /**
     * Can the current user delete an existing model?
     *
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function destroy(User $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can delete a model.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    /**
     * Can the current user delete existing models belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function destroyForUser(User $user, User $forUser)
    {
        return $user->id === $forUser->id;
    }

    public function restore(User $user, AbstractModel $model)
    {
        // By default, a moderator can restore a model.

        /** @var AbstractPolicy|self $this */
        return $this->isModerator($user);
    }

    public function viewTrashed(User $user, AbstractModel $model)
    {
        // By default, the creator or a moderator can view a model if it has been deleted.

        /** @var AbstractPolicy|self $this */
        return $this->isOwnerOrPrivileged($user, $model);
    }

    public function feature(User $user, FeaturableInterface $model)
    {
        return $this->isModerator($user);
    }

    public function unFeature(User $user, FeaturableInterface $model)
    {
        return $this->isModerator($user);
    }
}
