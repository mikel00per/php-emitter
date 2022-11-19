<?php

namespace Emitter\Tests\Utils;

use Emitter\ReasonPhrasesInterface;
use HttpSoft\Message\Response;
use PHPUnit\Framework\TestCase as UnitTestCase;
use Psr\Http\Message\ResponseInterface;

class TestCase extends UnitTestCase
{
    /**
     * @param int $statusCode
     * @param array $headers
     * @param string $contents
     * @param string $protocol
     * @return Response
     */
    protected function createResponse(
        int $statusCode = 200,
        array $headers = [],
        string $contents = '',
        string $protocol = '1.1'
    ): ResponseInterface {
        $response = new Response(
            $statusCode,
            $headers,
            'php://temp',
            $protocol,
            ReasonPhrasesInterface::REASON_PHRASES[$statusCode]
        );

        $response->getBody()->write($contents);

        return $response;
    }
}
