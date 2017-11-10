<?php

namespace Tmd\LaravelSite\Exceptions;

use Exception;
use Tmd\LaravelSite\Models\UserBan;

class BannedUserException extends Exception
{
    public function __construct(UserBan $ban)
    {
        $message = 'This account is disabled';
        if ($ban->to) {
            $message .= ' until <strong>'.date('Y-m-d H:i:s', strtotime($ban->to)).'</strong>';
        }
        if ($ban->reason) {
            $message .= ' due to: <strong>'.$ban->reason.'</strong>';
        } else {
            $message .= '.';
        }

        $this->message = $message;
    }
}
