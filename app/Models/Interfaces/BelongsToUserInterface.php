<?php

namespace Tmd\LaravelSite\Models\Interfaces;

use Tmd\LaravelSite\Models\User;

interface BelongsToUserInterface
{
    /**
     * Return the userId that created this model.
     *
     * @return int|null
     */
    public function getUserId();

    public function getUser(): ?User;
}
