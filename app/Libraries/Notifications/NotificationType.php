<?php

namespace Tmd\LaravelSite\Libraries\Notifications;

use ReflectionClass;

class NotificationType
{
    const COMMENT_ON_CREATED_POST = 1;
    const COMMENT_REPLY = 2;

    const LIKED_POST = 4;
    const LIKED_COMMENT = 8;

    const LIKED_TASK_TO_DO = 16;
    const LIKED_STICKER_TO_DO = 32;
    const LIKED_STICKER_COMPLETION = 64;
    const LIKED_JOIN = 128;

    const SUBMISSION_APPROVED = 256;
    const SUBMISSION_REJECTED = 512;
    const STICKER_EARNT = 1024;
    const MESSAGE = 2048;

    /**
     * Return all the defined notification types.
     * name => int
     *
     * @return int[]
     */
    public static function getAll()
    {
        $types = (new ReflectionClass(get_called_class()))->getConstants();

        return $types;
    }

    /**
     * Return all the defined notification types with the int as the key and name as the value.
     * int => name
     *
     * @return string[]
     */
    public static function getAllNames()
    {
        return array_flip(self::getAll());
    }

    /**
     * Get the name for the given notification int.
     *
     * @param int $int
     *
     * @return string|null
     */
    public static function getName($int)
    {
        $names = self::getAllNames();

        return array_key_exists($int, $names) ? $names[$int] : null;
    }

    /**
     * These types can never be disabled for on-site notifications.
     *
     * @return int[]
     */
    public static function getEnforcedInts()
    {
        return [
            self::POST_DELETED,
            self::STAFF_ROOM_POST,
        ];
    }

    public static function ensureEnforcedEnabled($value)
    {
        foreach (self::getEnforcedInts() as $int) {
            if (($value & $int) === 0) {
                $value += $int;
            }
        }

        return $value;
    }

    /**
     * Types that should only be displayed (on the notification settings page) to moderators.
     *
     * @return int[]
     */
    public static function getModeratorInts()
    {
        return [
            self::STAFF_ROOM_POST,
        ];
    }

    /**
     * What notifications should be enabled by default?
     *
     * @return int
     */
    public static function getDefaultSum()
    {
        return array_sum(self::getAll());
    }

    /**
     * What notifications should be enabled by default for push notifications?
     *
     * @return int
     */
    public static function getDefaultPushSum()
    {
        return array_sum(self::getAll());
    }

    /**
     * What notifications should be enabled by default for email notifications?
     *
     * @return int
     */
    public static function getDefaultEmailSum()
    {
        return self::MESSAGE + self::ACHIEVEMENT + self::NEW_FOLLOWER + self::POST_APPROVED;
    }
}
