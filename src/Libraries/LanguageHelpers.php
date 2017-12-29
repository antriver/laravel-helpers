<?php

namespace Tmd\LaravelSite\Libraries;

class LanguageHelpers
{
    public static function s($num)
    {
        return $num == 1 ? '' : 's';
    }

    public static function are($num)
    {
        return $num == 1 ? 'is' : 'are';
    }

    public static function have($num)
    {
        return $num == 1 ? 'has' : 'have';
    }

    /**
     * Takes a number and adds "th, st, nd, rd, th" after it. Example 10 to 10th.
     *
     * @see http://phpsnips.com/snippet.php?id=37
     *
     * @param int $num
     *
     * @return string
     */
    public static function ordinal($num)
    {
        $test_c = abs($num) % 10;
        $ext = ((abs($num) % 100 < 21 && abs($num) % 100 > 4) ? 'th'
            : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1)
                ? 'th' : 'st' : 'nd' : 'rd' : 'th'));

        return number_format($num).$ext;
    }

    /**
     * Add 's to the end of a string to make it possessive.
     * If the string already ends in s, adds an apostrophe to the end.
     * e.g.
     * Jim -> Jim's
     * Jesus -> Jesus'
     *
     * @param string $string
     *
     * @return string
     */
    public static function possessive($string)
    {
        if (strtolower(substr($string, -1)) === 's') {
            return $string.'\'';
        }

        return $string.'\'s';
    }

    /**
     * Truncate a string but break at the end of the last word instead of middle of a word.
     * e.g.
     * "Hello world this is my string."
     * truncated to 15 chars becomes
     * "Hello world..."
     * instead of
     * "Hello world thi"
     *
     * @param string $string
     * @param int $limit
     * @param string $cutter
     * @param bool $returnArray If true will return an array to show where it was cut.
     *                          If false just returns the new string.
     *
     * @return array|string
     */
    public static function wordTruncate($string, $limit, $cutter = '...', $returnArray = false)
    {
        if (strlen($string) <= $limit) {
            return $string;
        }

        $limit -= strlen($cutter);

        $string = substr($string, 0, $limit);
        $string = trim($string, ' ,.');

        // Find last space in truncated string
        $breakpoint = strrpos($string, ' ');

        if ($breakpoint === false) {
            return $string.$cutter;
        } else {
            $string = substr($string, 0, $breakpoint);
            $string = trim($string, ' ,.');
            $string .= $cutter;
            if ($returnArray) {
                return [
                    'breakpoint' => $breakpoint,
                    'string' => $string,
                ];
            } else {
                return $string;
            }
        }
    }

    public static function sluggify($source, $separator = '-')
    {
        $source = preg_replace("/[^A-Za-z0-9 ]/", '', $source);

        $slugEngine = new \Cocur\Slugify\Slugify();
        $slug = $slugEngine->slugify($source, $separator);

        return $slug;
    }

    public static function expressNumberAsWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion',
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative.self::expressNumberAsWords(abs($number));
        }

        $string = null;
        $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.self::expressNumberAsWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::expressNumberAsWords($numBaseUnits).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::expressNumberAsWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    /**
     * @see http://stackoverflow.com/questions/2690504/php-producing-relative-date-time-from-timestamps
     *
     * @param int $timestamp
     * @param string $word
     * @param bool $yesterday
     * @param bool $longYears
     * @param bool $short
     *
     * @return string
     */
    public static function relativeTime(
        $timestamp,
        $word = 'ago',
        $yesterday = true,
        $longYears = false,
        $short = false
    ) {
        $delta = time() - $timestamp;

        $minute = 60;
        $hour = 60 * $minute;
        $day = 24 * $hour;
        $month = 30 * $day;
        $year = 365 * $day;

        if ($delta < 1 * $minute) {
            if ($delta == 1) {
                return 'one '.($short ? 'sec' : 'second').' '.$word;
            }

            return $delta.' '.($short ? 'secs' : 'seconds').' '.$word;
        }
        if ($delta < 2 * $minute) {
            return '1 '.($short ? 'min' : 'minute').' '.$word;
        }
        if ($delta < 45 * $minute) {
            return floor($delta / $minute).' '.($short ? 'mins' : 'minutes').' '.$word;
        }
        if ($delta < 120 * $minute) {
            return '1 '.($short ? 'hr' : 'hour').' '.$word;
        }
        if ($delta < 24 * $hour) {
            return floor($delta / $hour).' '.($short ? 'hrs' : 'hours').' '.$word;
        }
        if ($yesterday && $delta < 48 * $hour) {
            return 'yesterday';
        }
        if ($delta < 48 * $hour) {
            return '1 day '.$word;
        }
        if ($delta < 30 * $day) {
            return floor($delta / $day).' days '.$word;
        }

        if ($delta < 12 * $month) {
            $months = floor($delta / $day / 30);
            if ($months <= 1) {
                return '1 month '.$word;
            } else {
                return $months.' months '.$word;
            }
        }

        $years = floor($delta / $year);

        if ($longYears) {
            $months = floor(($delta % $year) / $month);

            if ($months == 12) {
                $months = '';
                $years++;
                //$months = 11;
            } elseif ($months > 0) {
                $months = 'and '.$months.' month'.($months > 1 ? 's' : '').' ';
            } else {
                $months = false;
            }

            if ($years <= 1) {
                return '1 year '.$months.$word;
            }

            return $years.' years '.$months.$word;
        } else {
            if ($years <= 1) {
                return '1 year '.$word;
            }

            return $years.' years '.$word;
        }
    }

    /**
     * Join a string with a natural language conjunction at the end.
     *
     * @see http://stackoverflow.com/a/25057951/710630
     *
     * @param array $array Array to join
     * @param string $glue Glue for regular items
     * @param string $conjunction Glue for the last item
     * @param string $twoConjunction Glue if there are only two items
     *
     * @return mixed|string
     */
    public static function naturalLanguageImplode(
        array $array,
        $glue = ', ',
        $conjunction = ', and ',
        $twoConjunction = ' and '
    ) {
        if (count($array) === 2) {
            return implode($twoConjunction, $array);
        }

        $last = array_pop($array);
        if ($array) {
            return implode($glue, $array).$conjunction.$last;
        }

        return $last;
    }
}
