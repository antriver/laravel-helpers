<?php

namespace Tmd\LaravelSite\Providers;

use Validator;

class ValidationServiceProvider extends \Illuminate\Validation\ValidationServiceProvider
{
    public function register()
    {
        parent::register();

        Validator::extend('email_valid', 'Tmd\LaravelSite\Http\Validators\EmailMXValidator@validate');
        Validator::extend('hex_color', 'Tmd\LaravelSite\Http\Validators\HexColorValidator@validate');
        Validator::extend('recaptcha', 'Tmd\LaravelSite\Http\Validators\RecaptchaValidator@validate');
        Validator::extend('user_image', 'Tmd\LaravelSite\Http\Validators\UserImageValidator@validate');
        Validator::extend('i_in', 'Tmd\LaravelSite\Http\Validators\InCaseInsensitiveValidator@validateIn');
        Validator::extend('i_not_in', 'Tmd\LaravelSite\Http\Validators\InCaseInsensitiveValidator@validateNotIn');
    }
}
