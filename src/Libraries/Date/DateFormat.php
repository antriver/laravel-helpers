<?php

namespace Tmd\LaravelSite\Libraries\Date;

class DateFormat
{
    /**
     * Nov 10th 2017
     */
    const DATE_ONLY = 'M jS Y';

    /**
     * Nov 10th 2017 10:51 AM
     */
    const DATE_TIME = 'M jS Y h:i A';

    /**
     * Nov 10th 2017 10:51 AM
     */
    const DATE_TIME_WITH_TZ = 'M jS Y h:i A (e)';

    /**
     * Nov 10th 2017 10:51:55 AM
     */
    const DATE_TIME_SECS = 'M jS Y h:i:s A';

    /**
     * 2017-11-10
     */
    const DB_DATE = 'Y-m-d';

    /**
     * 2017-11-10 10:51:55
     *
     * Can also use Carbon::toDateTimeString()
     */
    const DB_DATE_TIME = 'Y-m-d H:i:s';

    /**
     * Format for an 'Expires' header.
     *
     * Fri, 10 Nov 2017 10:51:55 UTC
     */
    const EXPIRES = 'D, d M Y H:i:s T';

    /**
     * 10:51 AM
     */
    const TIME_ONLY = 'h:i A';

    /**
     * 10:51:55 AM
     */
    const TIME_ONLY_SECS = 'h:i:s A';
}
