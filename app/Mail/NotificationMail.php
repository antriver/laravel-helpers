<?php

namespace Tmd\LaravelSite\Mail;

use Tmd\LaravelSite\Mail\Base\ExtendedMailable;

class NotificationMail extends ExtendedMailable
{
    public function build()
    {
        // The Notification should have already set the lines and action on the object by this point.

        return $this->view('emails.layouts.default');
    }
}
