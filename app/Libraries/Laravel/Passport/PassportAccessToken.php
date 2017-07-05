<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Passport;

class PassportAccessToken extends \Laravel\Passport\Bridge\AccessToken
{
    /**
     * Indicates if the token was a previously existing one from the database, instead of a fresh one.
     *
     * @var bool
     */
    public $isRecycled = false;

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        if (!$this->isRecycled) {
            parent::setIdentifier($identifier);
        }
    }

    /**
     * Set the date time when the token expires.
     *
     * @param \DateTime $dateTime
     */
    public function setExpiryDateTime(\DateTime $dateTime)
    {
        if (!$this->isRecycled) {
            parent::setExpiryDateTime($dateTime);
        }
    }
}
