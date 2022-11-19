<?php

declare(strict_types=1);

namespace Emitter\Tests\Utils;

class MockData
{
    public static array $headers = [];
    public static int $statusCode = 200;
    public static string $statusLine = '';
    public static bool $isHeadersSent  = false;
    public static array $contentSplitByBytes = [];

    public static function create(): void
    {
        self::$headers = [];
        self::$statusCode = 200;
        self::$statusLine = '';
        self::$isHeadersSent = false;
        self::$contentSplitByBytes = [];
    }
}
