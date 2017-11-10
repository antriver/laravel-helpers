<?php

namespace Tmd\LaravelSite\Http\Validators;

class HexColorValidator
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
        return preg_match('/^#?[a-fA-F0-9]{6}$/u', $value);
    }
}
