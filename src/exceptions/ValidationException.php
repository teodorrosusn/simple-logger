<?php

namespace Myprojects\Logger\exceptions;

use Throwable;

class ValidationException extends \Exception
{
    /** @var array */
    private $validationMessage = [];

    /**
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Sets the message for the current validation exception.
     *
     * @param array $message
     * @return void
     */
    public function setValidationMessage(array $message): void
    {
        $this->validationMessage = $message;
    }

    /**
     * Gets the message for the current validation exception.
     *
     * @return array
     */
    public function getValidationMessage(): array
    {
        return $this->validationMessage;
    }
}
