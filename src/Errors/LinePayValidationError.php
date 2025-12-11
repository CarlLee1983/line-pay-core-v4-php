<?php

declare(strict_types=1);

namespace LinePay\Core\Errors;

use Exception;

/**
 * LINE Pay Validation Error.
 *
 * Thrown when request parameters fail validation before being sent to the API.
 * This is a client-side validation error that prevents invalid requests
 * from being sent to the LINE Pay servers.
 */
class LinePayValidationError extends Exception
{
    /**
     * The name of the field that failed validation.
     */
    protected ?string $field;

    /**
     * Creates a new LinePayValidationError instance.
     *
     * @param string      $message Description of the validation error
     * @param string|null $field   Optional name of the field that failed validation
     */
    public function __construct(string $message, ?string $field = null)
    {
        $this->field = $field;
        parent::__construct($message);
    }

    /**
     * Get the field name that failed validation.
     */
    public function getField(): ?string
    {
        return $this->field;
    }
}
