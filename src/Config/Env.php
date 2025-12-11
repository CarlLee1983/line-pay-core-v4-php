<?php

declare(strict_types=1);

namespace LinePay\Core\Config;

/**
 * LINE Pay API Environment Constants.
 *
 * Contains base URLs for production and sandbox environments,
 * as well as default configuration values.
 */
final class Env
{
    /**
     * LINE Pay API Base URL for Production.
     */
    public const BASE_URL_PRODUCTION = 'https://api-pay.line.me';

    /**
     * LINE Pay API Base URL for Sandbox.
     */
    public const BASE_URL_SANDBOX = 'https://sandbox-api-pay.line.me';

    /**
     * Default request timeout in seconds.
     */
    public const DEFAULT_TIMEOUT = 20;

    /**
     * Get API Base URL for the specified environment.
     *
     * @param string $env Environment ('production' or 'sandbox')
     *
     * @return string The base URL
     */
    public static function getBaseUrl(string $env): string
    {
        return match ($env) {
            'production' => self::BASE_URL_PRODUCTION,
            default => self::BASE_URL_SANDBOX,
        };
    }
}
