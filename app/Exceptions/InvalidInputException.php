<?php

namespace Tmd\LaravelSite\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * InputExceptions have an additional "messages" property that says
 * which input field an error relates to.
 */
class InvalidInputException extends BadRequestHttpException
{
    private $inputMessages = [];

    public function __construct(array $messages)
    {
        $this->inputMessages = $messages;

        $flattenedMessages = [];
        array_walk_recursive(
            $messages,
            function ($a) use (&$flattenedMessages) {
                $flattenedMessages[] = $a;
            }
        );
        $message = implode(' ', $flattenedMessages);

        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function getInputMessages()
    {
        return $this->inputMessages;
    }
}
