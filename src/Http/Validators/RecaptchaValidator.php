<?php

namespace Tmd\LaravelSite\Http\Validators;

use Config;
use ReCaptcha\ReCaptcha;
use Request;

class RecaptchaValidator
{
    /**
     * Validate an hexadecimal color
     *
     * @param $attribute
     * @param $value
     *
     * @return int
     */
    public function validate($attribute, $value)
    {
        $clientIp = Request::getClientIp();
        $recaptcha = new ReCaptcha(Config::get('services.recaptcha.secret'));

        $response = $recaptcha->verify($value, $clientIp);

        return $response->isSuccess();
    }
}
