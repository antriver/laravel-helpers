<?php

namespace Tmd\LaravelSite\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ForbiddenHttpException extends HttpException
{
    /**
     * @param null            $message
     * @param \Exception|null $previous
     * @param array           $headers
     * @param int             $code
     */
    public function __construct($message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(403, $message, $previous, $headers);
    }
}
