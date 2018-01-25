<?php

use Tmd\LaravelSite\Libraries\Date\DateFormat;

/**
 * Include this in AppServiceProvider::boot()
 */

if (!function_exists('display_datetime')) {
    /**
     * @param DateTime $dateTime
     *
     * @return null|string Nov 10th 2017 10:51 AM
     */
    function display_datetime(DateTime $dateTime = null)
    {
        if (!$dateTime) {
            return null;
        }

        return $dateTime->format(DateFormat::DATE_TIME);
    }
}

if (!function_exists('display_date')) {
    /**
     * @param DateTime $dateTime
     *
     * @return string|null Nov 10th 2017
     */
    function display_date(DateTime $dateTime = null)
    {
        if (!$dateTime) {
            return null;
        }

        return $dateTime->format(DateFormat::DATE_ONLY);
    }
}

if (!function_exists('view_path')) {
    /**
     * @return string
     */
    function view_path()
    {
        return resource_path('views');
    }
}

if (!function_exists('asset_url')) {
    /**
     * @param  string $path
     * @param bool $external
     *
     * @return string
     */
    function asset_url($path, $external = false)
    {
        return ($external ? config('app.assets_url') : config('app.assets_url')).'/'.$path;
    }
}

if (!function_exists('data_url')) {
    /**
     * Generate an absolute url to an item stored in remote storage.
     *
     * @param  string $path
     *
     * @return string
     */
    function data_url($path)
    {
        return config('app.data_url').'/'.$path;
    }
}

if (!function_exists('format_score')) {
    /**
     * @param int $score
     * @param string $classes
     * @param bool $colorize
     *
     * @return string
     */
    function format_score($score, $classes = '', $colorize = true)
    {
        if ($score > 0) {
            return '<span class="score '.($colorize ? 'green' : '').' '.$classes.'">+'.number_format($score).'</span>';
        } elseif ($score < 0) {
            return '<span class="score '.($colorize ? 'red' : '').' '.$classes.'">'.number_format($score).'</span>';
        }

        return '<span class="score '.$classes.'">'.$score.'</span>';
    }
}


if (!function_exists('get_remote_addr')) {
    /**
     * @return string|null
     */
    function get_remote_addr(): ?string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return null;
    }
}

if (!function_exists('get_http_status_text')) {

    function getHttpStatusText(int $code): ?string
    {
        $codes = [
            // INFORMATIONAL CODES
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            // SUCCESS CODES
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-status',
            208 => 'Already Reported',
            // REDIRECTION CODES
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy', // Deprecated
            307 => 'Temporary Redirect',
            // CLIENT ERROR
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            // SERVER ERROR
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            511 => 'Network Authentication Required',
        ];

        return isset($codes[$code]) ? $codes[$code] : null;
    }
}
