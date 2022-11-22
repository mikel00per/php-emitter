<?php

declare(strict_types=1);

namespace ResponseEmitter\Exceptions;

use RuntimeException;

class OutputAlreadySentException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Unable to emit response, output has been emitted previously.');
    }
}
