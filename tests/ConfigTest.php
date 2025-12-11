<?php

declare(strict_types=1);

namespace LinePay\Core\Tests;

use LinePay\Core\Config\Env;
use LinePay\Core\Config\LinePayConfig;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for Config classes.
 */
class ConfigTest extends TestCase
{
    public function testEnvConstants(): void
    {
        $this->assertEquals('https://api-pay.line.me', Env::BASE_URL_PRODUCTION);
        $this->assertEquals('https://sandbox-api-pay.line.me', Env::BASE_URL_SANDBOX);
        $this->assertEquals(20, Env::DEFAULT_TIMEOUT);
    }

    public function testEnvGetBaseUrlProduction(): void
    {
        $url = Env::getBaseUrl('production');

        $this->assertEquals('https://api-pay.line.me', $url);
    }

    public function testEnvGetBaseUrlSandbox(): void
    {
        $url = Env::getBaseUrl('sandbox');

        $this->assertEquals('https://sandbox-api-pay.line.me', $url);
    }

    public function testEnvGetBaseUrlDefault(): void
    {
        $url = Env::getBaseUrl('unknown');

        $this->assertEquals('https://sandbox-api-pay.line.me', $url);
    }

    public function testLinePayConfigCreation(): void
    {
        $config = new LinePayConfig(
            channelId: 'test-channel-id',
            channelSecret: 'test-channel-secret',
            env: 'production',
            timeout: 30
        );

        $this->assertEquals('test-channel-id', $config->channelId);
        $this->assertEquals('test-channel-secret', $config->channelSecret);
        $this->assertEquals('production', $config->env);
        $this->assertEquals(30, $config->timeout);
    }

    public function testLinePayConfigDefaults(): void
    {
        $config = new LinePayConfig(
            channelId: 'test-id',
            channelSecret: 'test-secret'
        );

        $this->assertEquals('sandbox', $config->env);
        $this->assertEquals(20, $config->timeout);
    }

    public function testLinePayConfigTrimsWhitespace(): void
    {
        $config = new LinePayConfig(
            channelId: '  test-id  ',
            channelSecret: '  test-secret  '
        );

        $this->assertEquals('test-id', $config->channelId);
        $this->assertEquals('test-secret', $config->channelSecret);
    }

    public function testLinePayConfigGetBaseUrl(): void
    {
        $sandboxConfig = new LinePayConfig('id', 'secret', 'sandbox');
        $productionConfig = new LinePayConfig('id', 'secret', 'production');

        $this->assertEquals('https://sandbox-api-pay.line.me', $sandboxConfig->getBaseUrl());
        $this->assertEquals('https://api-pay.line.me', $productionConfig->getBaseUrl());
    }
}
