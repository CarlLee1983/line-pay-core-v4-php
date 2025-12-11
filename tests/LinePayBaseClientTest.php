<?php

declare(strict_types=1);

namespace LinePay\Core\Tests;

use LinePay\Core\Config\LinePayConfig;
use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\LinePayBaseClient;
use PHPUnit\Framework\TestCase;

/**
 * Concrete implementation for testing abstract LinePayBaseClient.
 */
class TestLinePayClient extends LinePayBaseClient
{
    /**
     * Make sendRequest public for testing.
     *
     * @param string                     $method
     * @param string                     $path
     * @param array<string, mixed>|null  $body
     * @param array<string, string>|null $params
     * @param array<string, string>|null $additionalHeaders
     *
     * @return array<string, mixed>
     */
    public function testSendRequest(
        string $method,
        string $path,
        ?array $body = null,
        ?array $params = null,
        ?array $additionalHeaders = null
    ): array {
        return $this->sendRequest($method, $path, $body, $params, $additionalHeaders);
    }
}

/**
 * Test cases for LinePayBaseClient.
 */
class LinePayBaseClientTest extends TestCase
{
    public function testClientCreation(): void
    {
        $config = new LinePayConfig(
            channelId: 'test-channel-id',
            channelSecret: 'test-channel-secret',
            env: 'sandbox',
            timeout: 30
        );

        $client = new TestLinePayClient($config);

        $this->assertEquals('test-channel-id', $client->getChannelId());
        $this->assertEquals('https://sandbox-api-pay.line.me', $client->getBaseUrl());
        $this->assertEquals(30, $client->getTimeout());
    }

    public function testClientCreationProduction(): void
    {
        $config = new LinePayConfig(
            channelId: 'test-id',
            channelSecret: 'test-secret',
            env: 'production'
        );

        $client = new TestLinePayClient($config);

        $this->assertEquals('https://api-pay.line.me', $client->getBaseUrl());
    }

    public function testClientThrowsErrorForEmptyChannelId(): void
    {
        $this->expectException(LinePayConfigError::class);
        $this->expectExceptionMessage('channelId is required');

        $config = new LinePayConfig(
            channelId: '',
            channelSecret: 'test-secret'
        );

        new TestLinePayClient($config);
    }

    public function testClientThrowsErrorForWhitespaceChannelId(): void
    {
        $this->expectException(LinePayConfigError::class);
        $this->expectExceptionMessage('channelId is required');

        $config = new LinePayConfig(
            channelId: '   ',
            channelSecret: 'test-secret'
        );

        new TestLinePayClient($config);
    }

    public function testClientThrowsErrorForEmptyChannelSecret(): void
    {
        $this->expectException(LinePayConfigError::class);
        $this->expectExceptionMessage('channelSecret is required');

        $config = new LinePayConfig(
            channelId: 'test-id',
            channelSecret: ''
        );

        new TestLinePayClient($config);
    }

    public function testClientThrowsErrorForInvalidTimeout(): void
    {
        $this->expectException(LinePayConfigError::class);
        $this->expectExceptionMessage('timeout must be a positive number');

        $config = new LinePayConfig(
            channelId: 'test-id',
            channelSecret: 'test-secret',
            timeout: 0
        );

        new TestLinePayClient($config);
    }

    public function testClientThrowsErrorForNegativeTimeout(): void
    {
        $this->expectException(LinePayConfigError::class);
        $this->expectExceptionMessage('timeout must be a positive number');

        $config = new LinePayConfig(
            channelId: 'test-id',
            channelSecret: 'test-secret',
            timeout: -1
        );

        new TestLinePayClient($config);
    }
}
