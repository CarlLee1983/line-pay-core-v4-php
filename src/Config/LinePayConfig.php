<?php

declare(strict_types=1);

namespace LinePay\Core\Config;

/**
 * LINE Pay Client Configuration.
 *
 * This class holds the configuration for connecting to the LINE Pay API.
 * It validates required fields upon instantiation.
 *
 * @example
 * ```php
 * $config = new LinePayConfig(
 *     channelId: 'your-channel-id',
 *     channelSecret: 'your-channel-secret',
 *     env: 'sandbox',
 *     timeout: 30
 * );
 * ```
 */
final class LinePayConfig
{
    /**
     * Channel ID found in the LINE Pay Merchant Center.
     */
    public readonly string $channelId;

    /**
     * Channel Secret found in the LINE Pay Merchant Center.
     */
    public readonly string $channelSecret;

    /**
     * Environment
     * - 'production': https://api-pay.line.me
     * - 'sandbox': https://sandbox-api-pay.line.me.
     */
    public readonly string $env;

    /**
     * Timeout in seconds.
     */
    public readonly int $timeout;

    /**
     * Create a new LINE Pay configuration.
     *
     * @param string $channelId     Channel ID from LINE Pay Merchant Center
     * @param string $channelSecret Channel Secret from LINE Pay Merchant Center
     * @param string $env           Environment ('production' or 'sandbox'), defaults to 'sandbox'
     * @param int    $timeout       Request timeout in seconds, defaults to 20
     */
    public function __construct(
        string $channelId,
        string $channelSecret,
        string $env = 'sandbox',
        int $timeout = Env::DEFAULT_TIMEOUT
    ) {
        $this->channelId = trim($channelId);
        $this->channelSecret = trim($channelSecret);
        $this->env = $env;
        $this->timeout = $timeout;
    }

    /**
     * Get the API base URL based on the environment.
     *
     * @return string The base URL for API requests
     */
    public function getBaseUrl(): string
    {
        return Env::getBaseUrl($this->env);
    }
}
