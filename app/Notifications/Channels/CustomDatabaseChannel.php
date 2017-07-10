<?php

namespace Tmd\LaravelSite\Notifications\Channels;

use Tmd\LaravelSite\Models\User;
use Tmd\LaravelSite\Notifications\Base\AbstractActionLaravelNotification;
use Tmd\LaravelSite\Repositories\NotificationRepository;

/**
 * Used instead of the Laravel's built in database channel, to make things more flexible.
 * e.g. the ability to filter notifications by type.
 * For example, we don't want to JSON encode the notification, and we want to store an action id.
 */
class CustomDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  User                              $notifiable
     * @param  AbstractActionLaravelNotification $notification
     */
    public function send(
        User $notifiable,
        AbstractActionLaravelNotification $notification
    ) {
        $model = $notification->toCustomDatabaseModel($notifiable);
        $model->forUserId = $notifiable->id;
        app(NotificationRepository::class)->persist($model);
    }
}
