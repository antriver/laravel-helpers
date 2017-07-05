<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Passport;

use Cache;
use Carbon\Carbon;
use DateTime;
use DB;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class PassportAccessTokenRepository extends \Laravel\Passport\Bridge\AccessTokenRepository
{
    /**
     * @param $id
     *
     * @return \Laravel\Passport\Token
     */
    public function find($id)
    {
        return Cache::remember(
            'token:'.$id,
            10080, // 1 week
            function () use ($id) {
                return $this->tokenRepository->find($id);
            }
        );
    }

    public function forget($id)
    {
        Cache::forget('token'.$id);
    }

    /**
     * @param ClientEntityInterface $clientEntity
     * @param array                 $scopes
     * @param null                  $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $newToken = new PassportAccessToken($userIdentifier, $scopes);

        if ($existingToken = $this->findExistingCompatibleToken($clientEntity, $newToken)) {
            $existingToken->isRecycled = true;

            return $existingToken;
        }

        return $newToken;
    }

    /**
     * @param ClientEntityInterface      $clientEntity
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @return AccessTokenEntityInterface|null
     */
    protected function findExistingCompatibleToken(
        ClientEntityInterface $clientEntity,
        AccessTokenEntityInterface $accessTokenEntity
    ) {
        // FIXME: Personal access tokens should be unique per 'name'
        $row = DB::table('oauth_access_tokens')
            ->where('user_id', $accessTokenEntity->getUserIdentifier())
            ->where('client_id', $clientEntity->getIdentifier())
            ->where('scopes', $this->formatScopesForStorage($accessTokenEntity->getScopes()))
            ->where('revoked', false)
            ->where('expires_at', '>=', (new Carbon('+1 MONTH'))->toDateTimeString())
            ->first();

        if ($row) {
            $accessTokenEntity->setIdentifier($row->id);
            $accessTokenEntity->setExpiryDateTime(new DateTime($row->expires_at));

            return $accessTokenEntity;
        }

        return null;
    }

    /**
     * @param AccessTokenEntityInterface|PassportAccessToken $accessTokenEntity
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        if (!property_exists($accessTokenEntity, 'isRecycled') || !$accessTokenEntity->isRecycled) {
            parent::persistNewAccessToken($accessTokenEntity);
        }
    }
}
