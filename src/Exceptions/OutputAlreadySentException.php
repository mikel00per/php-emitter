<?php

declare(strict_types=1);

namespace Emitter\Exceptions;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class OutputAlreadySentException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Unable to emit response, output has been emitted previously.');
    }
}
