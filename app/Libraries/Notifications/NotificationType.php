<?php

namespace Tmd\LaravelSite\Libraries\Notifications;

use ReflectionClass;

class NotificationType
{
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
        return array_flip(static::getAll());
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
        $names = static::getAllNames();

        return array_key_exists($int, $names) ? $names[$int] : null;
    }

    /**
     * These types can never be disabled for on-site notifications.
     *
     * @return int[]
     */
    public static function getEnforcedInts()
    {
        return [];
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
        return [];
    }

    /**
     * What notifications should be enabled by default?
     *
     * @return int
     */
    public static function getDefaultSum()
    {
        return array_sum(static::getAll());
    }

    /**
     * What notifications should be enabled by default for push notifications?
     *
     * @return int
     */
    public static function getDefaultPushSum()
    {
        return array_sum(static::getAll());
    }

    /**
     * What notifications should be enabled by default for email notifications?
     *
     * @return int
     */
    public static function getDefaultEmailSum()
    {
        return 0;
    }
}
