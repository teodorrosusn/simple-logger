<?php

namespace Myprojects\Logger\exceptions;

use Throwable;

class FileNotFoundException extends \Exception
{
    /**
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
