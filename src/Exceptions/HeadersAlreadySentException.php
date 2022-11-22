<?php

declare(strict_types=1);

namespace ResponseEmitter\Exceptions;

use RuntimeException;

class HeadersAlreadySentException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Unable to emit response, the headers already sent.');
    }
}
