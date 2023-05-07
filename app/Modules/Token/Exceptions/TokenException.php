<?php

namespace App\Modules\Token\Exceptions;

class TokenException extends \Exception
{
    public function __construct(string $message = "", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
