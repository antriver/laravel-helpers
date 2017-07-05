<?php

namespace Tmd\LaravelSite\Notifications\Base;

use Illuminate\Notifications\Notification;
use Tmd\LaravelSite\Mail\NotificationMail;

/**
 * Note we don't use the ShouldQueue interface here, because all Notification sending happens in
 * queued tasks anyway.
 *
 * @package Stickable\Notifications\Base
 */
abstract class AbstractLaravelNotification extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return \Stickable\Mail\NotificationMail
     */
    public function toMail($notifiable)
    {
        $message = new NotificationMail();

        $message->to($notifiable);
        $message->setRecipient($notifiable);

        return $message;
    }
}
