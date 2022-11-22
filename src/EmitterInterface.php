<?php

declare(strict_types=1);

namespace ResponseEmitter;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    /**
     * Emits an HTTP response, that including status line, headers and message body, according to the environment.
     *
     * When implementing this method, MAY use `header()` and the output buffer. Also, implementations MAY throw
     * exceptions if it cannot emit a response, e.g., if headers already sent or output has been emitted previously.
     */
    public function emit(ResponseInterface $response, bool $withoutBody = false): void;
}
