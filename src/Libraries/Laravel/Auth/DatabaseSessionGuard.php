<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Auth;

use Cache;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DatabaseSessionGuard implements StatefulGuard
{
    use GuardHelpers;

    /**
     * How long (in minutes) to cache the user ID for a session ID?
     *
     * Default is 7 days.
     *
     * @var int
     */
    protected $cacheLifetime = 10080;

    /**
     * @var string
     */
    protected $inputKey = 'token';

    /**
     * @var string
     */
    protected $table = 'user_sessions';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string|null
     */
    protected $sessionId;

    public function __construct(
        UserProvider $provider,
        Request $request
    ) {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * @return null|string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if ($this->user !== null) {
            return $this->user ?: null;
        }

        $this->sessionId = $this->getTokenForRequest();

        if (!empty($this->sessionId)) {
            $userId = $this->findUserIdBySessionId($this->sessionId);
            if ($userId) {
                $user = $this->provider->retrieveById($userId);

                $this->user = $user ?: false;
            }
        }

        return $this->user ?: null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        // Not sure when this is used!
        return false;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest()
    {
        $token = $this->request->query($this->inputKey);

        if (empty($token)) {
            $token = $this->request->input($this->inputKey);
        }

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        if (empty($token)) {
            $token = $this->request->getPassword();
        }

        return $token;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array $credentials
     * @param  bool $remember
     *
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        // TODO: Implement attempt() method.
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array $credentials
     *
     * @return bool
     * @throws Exception
     */
    public function once(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);
        if (!$user) {
            throw new Exception("User not found.");
        }

        $this->user = $user;
    }

    /**
     * Log a user into the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  bool $remember
     */
    public function login(Authenticatable $user, $remember = false)
    {
        $this->sessionId = $this->findOrCreateSessionForUser($user);
        $this->user = $user;
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed $id
     * @param  bool $remember
     *
     * @return Authenticatable
     * @throws Exception
     */
    public function loginUsingId($id, $remember = false)
    {
        $user = $this->provider->retrieveById($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        $this->login($user, $remember);

        return $user;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed $id
     *
     * @return bool
     * @throws Exception
     */
    public function onceUsingId($id)
    {
        $user = $this->provider->retrieveById($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        $this->once($user);

        return true;
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return false;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        if ($this->sessionId) {
            $this->logoutSession($this->sessionId);
        }
    }

    /**
     * @param string $sessionId
     *
     * @return int|null
     */
    protected function findUserIdBySessionId(string $sessionId): ?int
    {
        $cacheKey = $this->getSessionIdUserIdCacheKey($sessionId);

        if (($userId = Cache::get($cacheKey)) !== null) {
            return $userId ?: null;
        }

        $result = DB::selectOne(
            "SELECT `userId` FROM `{$this->table}` WHERE `id` = ? AND `loggedOutAt` IS NULL",
            [
                $sessionId,
            ]
        );

        $userId = $result ? $result->userId : null;

        // Cache false if the session was not found so we remember this session does not exist.
        Cache::put($cacheKey, $userId ?: false, $this->cacheLifetime);

        return $userId;
    }

    /**
     * @param string $sessionId
     */
    protected function logoutSession(string $sessionId)
    {
        DB::update(
            "UPDATE `{$this->table}` SET `loggedOutAt` = ? WHERE `id` = ?",
            [
                (new Carbon())->toDateTimeString(),
                $sessionId,
            ]
        );

        $cacheKey = $this->getSessionIdUserIdCacheKey($sessionId);
        Cache::forget($cacheKey);
    }

    /**
     * Create (or re-use) a session ID for a user.
     *
     * @param Authenticatable $user
     *
     * @return string Returns the session ID.
     */
    public function findOrCreateSessionForUser(Authenticatable $user)
    {
        $ip = (string) $this->request->getClientIp();

        if ($existingSessionId = $this->findReusableSession($user, $ip)) {
            $sessionId = $existingSessionId;
        } else {
            $sessionId = $this->createSession($user, $ip);
        }

        $cacheKey = $this->getSessionIdUserIdCacheKey($sessionId);
        Cache::put($cacheKey, $user->getAuthIdentifier(), $this->cacheLifetime);

        return $sessionId;
    }

    /**
     * @param Authenticatable $user
     * @param string $ip
     *
     * @return null|string
     */
    protected function findReusableSession(Authenticatable $user, string $ip): ?string
    {
        $existingSession = DB::selectOne(
            "SELECT `id` FROM `{$this->table}` WHERE `userId` = ? AND `ip` = ? AND `loggedOutAt` IS NULL",
            [
                $user->getAuthIdentifier(),
                $ip,
            ]
        );

        if ($existingSession) {
            DB::update(
                "UPDATE `{$this->table}` SET `loggedInAt` = ? WHERE `id` = ?",
                [
                    (new Carbon())->toDateTimeString(),
                    $existingSession->id,
                ]
            );

            return $existingSession->id;
        }

        return null;
    }

    /**
     * @param Authenticatable $user
     * @param string $ip
     *
     * @return string
     */
    protected function createSession(Authenticatable $user, string $ip): string
    {
        $newSessionId = $this->generateSessionId();

        DB::insert(
            "INSERT INTO `{$this->table}` (`id`,`userId`, `ip`) VALUES (?, ?, ?)",
            [
                $newSessionId,
                $user->getAuthIdentifier(),
                $ip,
            ]
        );

        return $newSessionId;
    }

    /**
     * @return string
     */
    protected function generateSessionId(): string
    {
        return Str::random(50);
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    protected function getSessionIdUserIdCacheKey(string $sessionId): string
    {
        // suid = session user ID.
        return 'suid:'.$sessionId;
    }
}
