<?php

declare(strict_types=1);

namespace LinePay\Core\Errors;

use Exception;

/**
 * LINE Pay Configuration Error.
 *
 * Thrown when the LinePayClient is instantiated with invalid configuration.
 * This includes missing or empty required fields like channelId or channelSecret.
 */
class LinePayConfigError extends Exception
{
    /**
     * Creates a new LinePayConfigError instance.
     *
     * @param string $message Description of the configuration error
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
