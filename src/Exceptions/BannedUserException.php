<?php

namespace Tmd\LaravelHelpers\Exceptions;

use Exception;
use Tmd\LaravelHelpers\Models\UserBan;

class BannedUserException extends Exception
{
    public function __construct(UserBan $ban)
    {
        $message = 'This account is disabled';
        if ($ban->to) {
            $message .= ' until <strong>'.display_datetime($ban->to).'</strong>';
        }
        if ($ban->reason) {
            $message .= ' due to: <strong>'.$ban->reason.'</strong>';
        } else {
            $message .= '.';
        }

        $this->message = $message;
    }
}
