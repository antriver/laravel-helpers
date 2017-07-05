<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Passport;

use DateInterval;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\PasswordGrant;

class PassportPasswordGrant extends PasswordGrant
{
    /**
     * Provides public access to the protected issueAccessToken method.
     *
     * @param DateInterval          $accessTokenTTL
     * @param ClientEntityInterface $client
     * @param                       $userIdentifier
     * @param array                 $scopes
     *
     * @return \League\OAuth2\Server\Entities\AccessTokenEntityInterface
     */
    public function generateAccessToken(
        DateInterval $accessTokenTTL,
        ClientEntityInterface $client,
        $userIdentifier,
        array $scopes = []
    ) {
        return $this->issueAccessToken($accessTokenTTL, $client, $userIdentifier, $scopes);
    }
}
