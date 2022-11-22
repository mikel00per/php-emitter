<?php

namespace ResponseEmitter\Tests\Utils;

use Fig\Http\Message\ReasonPhrasesInterface;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase as UnitTestCase;
use Psr\Http\Message\ResponseInterface;

class TestCase extends UnitTestCase
{
    protected function createResponse(
        int $statusCode = 200,
        array $headers = [],
        string $contents = '',
        string $protocol = '1.1'
    ): ResponseInterface {
        $response = new Response(
            $statusCode,
            $headers,
            '',
            $protocol,
            ReasonPhrasesInterface::REASON_PHRASES[$statusCode]
        );

        $response->getBody()->write($contents);

        return $response;
    }
}
