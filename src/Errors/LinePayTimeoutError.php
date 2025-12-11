<?php

declare(strict_types=1);

namespace LinePay\Core\Errors;

use Exception;

/**
 * LINE Pay Request Timeout Error.
 *
 * Thrown when an API request exceeds the configured timeout duration.
 * This typically indicates network issues or slow server response.
 */
class LinePayTimeoutError extends Exception
{
    /**
     * The timeout duration in seconds that was exceeded.
     */
    protected int $timeout;

    /**
     * The URL of the request that timed out.
     */
    protected ?string $url;

    /**
     * Creates a new LinePayTimeoutError instance.
     *
     * @param int         $timeout The timeout duration in seconds that was exceeded
     * @param string|null $url     Optional URL of the request that timed out
     */
    public function __construct(int $timeout, ?string $url = null)
    {
        $this->timeout = $timeout;
        $this->url = $url;

        parent::__construct("Request timeout after {$timeout}s");
    }

    /**
     * Get the timeout duration.
     *
     * @return int Timeout in seconds
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get the URL that timed out.
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}
