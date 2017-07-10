<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Passport;

use DateInterval;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\Scope;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Tmd\LaravelSite\Libraries\Laravel\Auth\CachedEloquentUserProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Uses the raw identifier from the oauth_tokens table, passed in as ?token,
 * instead of the full JWT returned by the Passport package.
 */
class PassportTokenAuthGuard implements Guard
{
    use GuardHelpers;

    /**
     * The user we last attempted to retrieve.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $lastAttempted;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var PassportAccessTokenRepository
     */
    private $passportAccessTokenRepository;

    /**
     * @var PassportClientRepository
     */
    private $passportClientRepository = null;

    /**
     * @var PassportPasswordGrant
     */
    private $passportPasswordGrant = null;

    /**
     * Create a new authentication guard.
     *
     * @param CachedEloquentUserProvider    $provider
     * @param \Illuminate\Http\Request      $request
     * @param PassportAccessTokenRepository $accessTokenRepository
     */
    public function __construct(
        CachedEloquentUserProvider $provider,
        Request $request,
        PassportAccessTokenRepository $accessTokenRepository
    ) {
        $this->provider = $provider;
        $this->request = $request;
        $this->passportAccessTokenRepository = $accessTokenRepository;
    }

    /**
     * Get the currently authenticated user - either the already logged in user, or from the token.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if ($tokenId = $this->request->input('token')) {
            if ($user = $this->getUserByToken($tokenId)) {
                $this->setUser($user);

                return $this->user;
            }
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false);
    }

    /**
     * Validate a user's credentials and set the current user if valid.
     *
     * @param array $credentials
     * @param bool  $login
     *
     * @return mixed
     */
    public function attempt(array $credentials = [], $login = true)
    {
        if (!$user = $this->provider->retrieveByCredentials($credentials)) {
            throw new BadRequestHttpException(trans('auth.invalid_username'));
        }

        if (!$this->provider->validateCredentials($user, $credentials)) {
            throw new BadRequestHttpException(trans('auth.invalid_password'));
        }

        if ($login) {
            $this->setUser($user);
        }

        return $user;
    }

    /**
     * Check the current user in the request.
     *
     * @param $tokenId
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    private function getUserByToken($tokenId)
    {
        $token = $this->passportAccessTokenRepository->find($tokenId);
        if (!$token) {
            return null;
        }

        return $this->provider->retrieveById($token->getAttribute('user_id'));
    }

    /**
     * Returns a token used to authenticate again in a subsequent request.
     * Uses the Laravel Passport package to generate the token, but returns the underlying token in the DB,
     * instead of the full JWT.
     *
     * @return null|AccessTokenEntityInterface
     */
    public function getToken()
    {
        if (!$user = $this->user()) {
            return null;
        }

        $passportClient = $this->getPassportClient();

        $grant = $this->getPasswordGrant();
        $token = $grant->generateAccessToken(
            new DateInterval('P100Y'),
            $passportClient,
            $user->getAuthIdentifier(),
            [
                new Scope('*'),
            ]
        );

        return $token;
    }

    /**
     * @return Client
     */
    private function getPassportClient()
    {
        return new Client(1, 'Password Grant Client', null);
    }

    /**
     * @return PassportClientRepository
     */
    private function getPassportClientRepository()
    {
        if ($this->passportClientRepository === null) {
            $this->passportClientRepository = app(PassportClientRepository::class);
        }

        return $this->passportClientRepository;
    }

    /**
     * @return PassportPasswordGrant
     */
    private function getPasswordGrant()
    {
        if ($this->passportPasswordGrant === null) {
            $this->passportPasswordGrant = new PassportPasswordGrant(
                app(PassportUserRepository::class),
                app(RefreshTokenRepository::class)
            );
            $this->passportPasswordGrant->setAccessTokenRepository($this->passportAccessTokenRepository);
        }

        return $this->passportPasswordGrant;
    }
}
