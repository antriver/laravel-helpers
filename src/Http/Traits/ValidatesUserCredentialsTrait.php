<?php

namespace Tmd\LaravelHelpers\Http\Traits;

use Tmd\LaravelHelpers\Models\User;

trait ValidatesUserCredentialsTrait
{
    protected function getUsernameValidationRules(User $user = null, $required = true)
    {
        $rules = [
            'max:30',
            $user ? 'unique:users,username,'.$user->id : 'unique:users,username',
            'regex:'.$this->getUsernameRegex(),
            'i_not_in:'.implode(',', $this->getReservedUsernames()),
        ];

        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }

    protected function getUsernameRegex()
    {
        return '/^[A-Za-z0-9_-]{1,30}$/';
    }

    protected function getReservedUsernames()
    {
        return [
            'anonymous',
            'admin',

            // Any chat room names need to be reserved, as that would break the /messages/username+room links
            'chat',
            'modchat',
        ];
    }

    protected function getEmailValidationRules(User $user = null, $required = true)
    {
        $rules = [
            'bail',
            'email',
            //'email_valid',
            $user ? 'unique:users,email,'.$user->id : 'unique:users,email',
        ];

        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }

    protected function getPasswordValidationRules($required = true)
    {
        $rules = [
            'min:3',
        ];

        if ($required) {
            $rules[] = 'required';
        }

        return $rules;
    }
}
