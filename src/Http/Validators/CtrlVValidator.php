<?php

namespace Tmd\LaravelSite\Http\Validators;

use Validator;

class CtrlVValidator
{
    public function validate($attribute, $value, $parameters)
    {
        // Check it's a valid URL first
        $validator = Validator::make(
            [
                $attribute => $value,
            ],
            [
                $attribute => 'url',
            ]
        );

        if ($validator->fails()) {
            return false;
        }

        $url = parse_url($value);

        if ($url['host'] !== 'img.ctrlv.in') {
            return false;
        }

        if ($url['scheme'] !== 'http' && $url['scheme'] !== 'https') {
            return false;
        }

        if (isset($url['port'])) {
            return false;
        }

        return true;
    }
}
