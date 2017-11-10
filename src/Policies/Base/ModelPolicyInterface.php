<?php

namespace Tmd\LaravelSite\Policies\Base;

use Tmd\LaravelSite\Models\Base\AbstractModel;
use Tmd\LaravelSite\Models\User;

interface ModelPolicyInterface
{
    /**
     * Can the current user create a new model?
     *
     * @param User $user
     *
     * @return mixed
     */
    public function create(User $user);

    /**
     * Can the current user create a new model belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function createForUser(User $user, User $forUser);

    /**
     * Can the current user view an existing model?
     *
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function view(User $user, AbstractModel $model);

    /**
     * Can the current user view existing models belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function viewForUser(User $user, User $forUser);

    /**
     * Can the current user edit an existing model?
     *
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function update(User $user, AbstractModel $model);

    /**
     * Can the current user edit existing models belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function updateForUser(User $user, User $forUser);

    /**
     * Can the current user delete an existing model?
     *
     * @param User          $user
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function destroy(User $user, AbstractModel $model);

    /**
     * Can the current user delete existing models belonging to the specified user?
     *
     * @param User $user
     * @param User $forUser
     *
     * @return mixed
     */
    public function destroyForUser(User $user, User $forUser);
}
