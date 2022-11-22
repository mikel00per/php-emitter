<?php

declare(strict_types=1);

namespace ResponseEmitter;

use ResponseEmitter\Exceptions\HeadersAlreadySentException;
use ResponseEmitter\Exceptions\OutputAlreadySentException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Emitter implements EmitterInterface
{
    public function __construct(private ?int $bufferLength = null)
    {
        $this->ensureIsValidBufferLength($bufferLength);
    }

    private function ensureIsValidBufferLength(?int $bufferLength): void
    {
        if ($bufferLength !== null && $bufferLength < 1) {
            throw new InvalidArgumentException(sprintf(
                'Buffer length for `%s` must be greater than zero; received `%d`.',
                self::class,
                $bufferLength
            ));
        }
    }

    public function emit(ResponseInterface $response, bool $withoutBody = false): void
    {
        if (headers_sent()) {
            throw HeadersAlreadySentException::create();
        }

        if (ob_get_level() > 0 && ob_get_length() > 0) {
            throw OutputAlreadySentException::create();
        }

        $this->emitHeaders($response);
        $this->emitStatusLine($response);

        if (!$withoutBody && $response->getBody()->isReadable()) {
            $this->emitBody($response);
        }
    }

    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('-', ' ', (string) $name))));
            $firstReplace = !($name === 'Set-Cookie');

            foreach ($values as $value) {
                header("{$name}: {$value}", $firstReplace);
                $firstReplace = false;
            }
        }
    }

    /**
     * Emits the response status line.
     */
    private function emitStatusLine(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();
        $reasonPhrase = trim($response->getReasonPhrase());
        $protocolVersion = trim($response->getProtocolVersion());

        $status = $statusCode . (!$reasonPhrase ? '' : " {$reasonPhrase}");
        header("HTTP/{$protocolVersion} {$status}", true, $statusCode);
    }

    /**
     * Emits the message body.
     */
    private function emitBody(ResponseInterface $response): void
    {
        if ($this->bufferLength === null) {
            echo $response->getBody();
            return;
        }

        flush();
        $body = $response->getBody();
        $range = $this->parseContentRange($response->getHeaderLine('content-range'));

        if (isset($range['unit']) && $range['unit'] === 'bytes') {
            $this->emitBodyRange($body, $range['first'], $range['last']);
            return;
        }

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof()) {
            echo $body->read($this->bufferLength);
        }
    }

    /**
     * Emits a range of the message body.
     *
     * @psalm-suppress PossiblyNullArgument
     */
    private function emitBodyRange(StreamInterface $body, int $first, int $last): void
    {
        $length = $last - $first + 1;

        if ($body->isSeekable()) {
            $body->seek($first);
        }

        while ($length >= $this->bufferLength && !$body->eof()) {
            $contents = $body->read($this->bufferLength);
            $length -= strlen($contents);
            echo $contents;
        }

        if ($length > 0 && !$body->eof()) {
            echo $body->read($length);
        }
    }

    /**
     * Parse Content-Range header.
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
     */
    private function parseContentRange(string $header): ?array
    {
        $contentRange = null;
        $regex = '/(?P<unit>[\w]+)\s+(?P<first>\d+)-(?P<last>\d+)\/(?P<length>\d+|\*)/';

        if (preg_match($regex, $header, $matches)) {
            $contentRange = [
                'unit' => $matches['unit'],
                'first' => (int) $matches['first'],
                'last' => (int) $matches['last'],
                'length' => ($matches['length'] === '*') ? '*' : (int) $matches['length'],
            ];
        }

        return $contentRange;
    }
}
