<?php

if (!function_exists('display_datetime')) {
    /**
     * @param DateTime $dateTime
     * @param bool     $secs Include seconds in displayed time?
     *
     * @return null|string Thu, Dec 25, 1975 14:15:23
     */
    function display_datetime(\DateTime $dateTime = null, $secs = true)
    {
        if (!$dateTime) {
            return null;
        }

        if (!$secs) {
            return $dateTime->format('D, M j, Y H:i');
        }

        return $dateTime->format('D, M j, Y H:i:s');
    }
}

if (!function_exists('display_date')) {
    /**
     * @param DateTime $dateTime
     *
     * @return string|null Thu, Dec 25, 1975 14:15:23
     */
    function display_date(\DateTime $dateTime = null)
    {
        if (!$dateTime) {
            return null;

        }

        return $dateTime->format('D, M j, Y');
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
     * @param bool    $external
     *
     * @return string
     */
    function asset_url($path, $external = false)
    {
        return ($external ? config('app.assets_url') : config('app.assets_url')).'/'.$path;
    }
}

if (!function_exists('built_asset_url')) {
    /**
     * @param  string $path
     *
     * @return string
     */
    function built_asset_url($path)
    {
        if (!config('app.assets_use_hash')) {
            return asset_url('build/'.$path);
        }

        $hashes = config('assets.hashes');
        $hashedPath = isset($hashes[$path]) ? $hashes[$path] : $path;

        return asset_url('build/'.$hashedPath);
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

if (!function_exists('bg_pattern_url')) {
    /**
     * @param  string $name
     *
     * @return string
     */
    function bg_pattern_url($name)
    {
        return asset_url('img/bg/patterns/'.$name.'.png');
    }
}

if (!function_exists('format_score')) {
    /**
     * @param int    $score
     * @param string $classes
     * @param bool   $colorize
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
