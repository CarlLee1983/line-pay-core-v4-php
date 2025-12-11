<?php

declare(strict_types=1);

namespace LinePay\Core\Errors;

use Exception;

/**
 * LINE Pay API Error.
 *
 * Custom exception class for handling LINE Pay API response errors.
 * This error is thrown when the LINE Pay API returns an error response
 * (either HTTP error or business logic error with returnCode !== '0000').
 *
 * @example
 * ```php
 * try {
 *     $client->confirm($transactionId, $body);
 * } catch (LinePayError $e) {
 *     echo $e->getReturnCode();     // e.g., '1104'
 *     echo $e->getReturnMessage();  // e.g., 'Invalid Channel ID'
 *     echo $e->isAuthError();       // true (for 1xxx codes)
 * }
 * ```
 *
 * @see https://pay.line.me/documents/online_v4.html LINE Pay API Documentation
 */
class LinePayError extends Exception
{
    /**
     * LINE Pay API error code.
     */
    protected string $returnCode;

    /**
     * LINE Pay API error message.
     */
    protected string $returnMessage;

    /**
     * HTTP status code from the response.
     */
    protected int $httpStatus;

    /**
     * Raw response body for debugging purposes.
     */
    protected ?string $rawResponse;

    /**
     * Creates a new LinePayError instance.
     *
     * @param string      $returnCode    LINE Pay API error code (e.g., '1104', '2101', '9000')
     * @param string      $returnMessage LINE Pay API error message description
     * @param int         $httpStatus    HTTP status code from the response
     * @param string|null $rawResponse   Optional raw response body for debugging purposes
     */
    public function __construct(
        string $returnCode,
        string $returnMessage,
        int $httpStatus = 0,
        ?string $rawResponse = null
    ) {
        $this->returnCode = $returnCode;
        $this->returnMessage = $returnMessage;
        $this->httpStatus = $httpStatus;
        $this->rawResponse = $rawResponse;

        parent::__construct("LINE Pay API Error [{$returnCode}]: {$returnMessage}");
    }

    /**
     * Get the LINE Pay return code.
     */
    public function getReturnCode(): string
    {
        return $this->returnCode;
    }

    /**
     * Get the LINE Pay return message.
     */
    public function getReturnMessage(): string
    {
        return $this->returnMessage;
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * Get the raw response body.
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * Checks if this is an authentication/authorization related error.
     *
     * Authentication errors have return codes starting with '1' (1xxx series).
     */
    public function isAuthError(): bool
    {
        return str_starts_with($this->returnCode, '1');
    }

    /**
     * Checks if this is a payment-related error.
     *
     * Payment errors have return codes starting with '2' (2xxx series).
     */
    public function isPaymentError(): bool
    {
        return str_starts_with($this->returnCode, '2');
    }

    /**
     * Checks if this is an internal server error.
     *
     * Internal errors have return codes starting with '9' (9xxx series).
     */
    public function isInternalError(): bool
    {
        return str_starts_with($this->returnCode, '9');
    }

    /**
     * Converts the error to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => 'LinePayError',
            'message' => $this->getMessage(),
            'returnCode' => $this->returnCode,
            'returnMessage' => $this->returnMessage,
            'httpStatus' => $this->httpStatus,
            'rawResponse' => $this->rawResponse,
        ];
    }
}
