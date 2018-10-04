<?php

namespace DevMob\Postcodes\Providers\Tests;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * @param  int $status Http status
     * @param  string $file File name to return contents of
     * @param  array $headers
     * @return \GuzzleHttp\ClientInterface
     */
    protected function createClientMock(int $status, string $file, array $headers = []): ClientInterface
    {
        $body = file_get_contents($file);
        $handler = HandlerStack::create(new MockHandler([
            new Response($status, $headers, $body),
        ]));

        return new GuzzleClient(['handler' => $handler]);
    }
}
