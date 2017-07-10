<?php

namespace Tmd\LaravelSite\Http\Validators;

class EmailMXValidator
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return bool
     */
    public function validate($attribute, $value, $parameters)
    {
        return $this->checkDns($this->getHost($value));
    }

    /**
     * @param string $email
     *
     * @return string
     */
    private function getHost($email)
    {
        // Use the input to check DNS if we cannot extract something similar to a domain.
        $host = $email;

        // Attempt to extract the domain (everything after the @)
        if (($atPos = strrpos($email, '@')) !== false) {
            $host = substr($email, $atPos + 1);
        }

        return $host;
    }

    /**
     * @param $host
     *
     * @return bool
     */
    private function checkDns($host)
    {
        return checkdnsrr($host) === true;
    }
}
