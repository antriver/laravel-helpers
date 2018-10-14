<?php

namespace Tmd\LaravelHelpers\Http\Validators;

class InCaseInsensitiveValidator
{
    /**
     * Validate an attribute is contained within a list of values (case-insensitive).
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    public function validateIn($attribute, $value, $parameters)
    {
        return in_array(strtolower($value), array_map('strtolower', $parameters));
    }

    /**
     * Validate an attribute is not contained within a list of values (case-insensitive).
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     *
     * @return bool
     */
    public function validateNotIn($attribute, $value, $parameters)
    {
        return !$this->validateIn($attribute, $value, $parameters);
    }
}
