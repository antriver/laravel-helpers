<?php

namespace Tmd\LaravelHelpers\Providers;

use Validator;

class ValidationServiceProvider extends \Illuminate\Validation\ValidationServiceProvider
{
    public function register()
    {
        parent::register();

        Validator::extend('email_valid', 'Tmd\LaravelHelpers\Http\Validators\EmailMXValidator@validate');
        Validator::extend('hex_color', 'Tmd\LaravelHelpers\Http\Validators\HexColorValidator@validate');
        Validator::extend('recaptcha', 'Tmd\LaravelHelpers\Http\Validators\RecaptchaValidator@validate');
        Validator::extend('user_image', 'Tmd\LaravelHelpers\Http\Validators\UserImageValidator@validate');
        Validator::extend('i_in', 'Tmd\LaravelHelpers\Http\Validators\InCaseInsensitiveValidator@validateIn');
        Validator::extend('i_not_in', 'Tmd\LaravelHelpers\Http\Validators\InCaseInsensitiveValidator@validateNotIn');
    }
}
