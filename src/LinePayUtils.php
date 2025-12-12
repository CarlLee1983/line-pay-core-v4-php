<?php

declare(strict_types=1);

namespace LinePay\Core;

/**
 * Utility class for LINE Pay operations.
 *
 * Provides static helper methods for:
 * - HMAC-SHA256 signature generation and verification
 * - Transaction ID validation
 * - Query string building
 * - Callback query parameter parsing
 *
 * All methods are static.
 */
final class LinePayUtils
{
    /**
     * Regular expression for validating LINE Pay transaction IDs.
     * LINE Pay transactionId must be exactly 19 digits.
     */
    private const TRANSACTION_ID_REGEX = '/^\d{19}$/';

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Generates HMAC-SHA256 signature for LINE Pay API authentication.
     *
     * @param string $secret      Channel Secret from LINE Pay Merchant Center
     * @param string $uri         Request URI path (e.g., '/v3/payments/request')
     * @param string $body        Request body as JSON string (empty string for GET requests)
     * @param string $nonce       Unique random string (typically UUID) for this request
     * @param string $queryString Optional query string (without leading '?')
     *
     * @return string Base64-encoded HMAC-SHA256 signature
     */
    public static function generateSignature(
        string $secret,
        string $uri,
        string $body,
        string $nonce,
        string $queryString = ''
    ): string {
        $data = $secret . $uri . $queryString . $body . $nonce;

        return base64_encode(hash_hmac('sha256', $data, $secret, true));
    }

    /**
     * Verifies HMAC-SHA256 signature using timing-safe comparison.
     *
     * @param string $secret    Channel Secret from LINE Pay Merchant Center
     * @param string $data      The data string that was signed
     * @param string $signature The signature to verify (received from LINE Pay)
     *
     * @return bool True if signature is valid, false otherwise
     */
    public static function verifySignature(string $secret, string $data, string $signature): bool
    {
        $expected = base64_encode(hash_hmac('sha256', $data, $secret, true));

        return hash_equals($expected, $signature);
    }

    /**
     * Validates LINE Pay transaction ID format.
     *
     * @param string $transactionId The transaction ID to validate
     *
     * @throws \InvalidArgumentException If transactionId is not a 19-digit number
     */
    public static function validateTransactionId(string $transactionId): void
    {
        if (!self::isValidTransactionId($transactionId)) {
            throw new \InvalidArgumentException(
                "Invalid transactionId format: expected 19-digit number, got \"{$transactionId}\""
            );
        }
    }

    /**
     * Checks if a transaction ID has valid format.
     *
     * @param string $transactionId The transaction ID to check
     *
     * @return bool True if transactionId is a 19-digit number, false otherwise
     */
    public static function isValidTransactionId(string $transactionId): bool
    {
        return preg_match(self::TRANSACTION_ID_REGEX, $transactionId) === 1;
    }

    /**
     * Builds URL query string from parameters array.
     *
     * @param array<string, string>|null $params Optional key-value pairs to convert to query string
     *
     * @return string Query string starting with '?' or empty string
     */
    public static function buildQueryString(?array $params = null): string
    {
        if ($params === null || count($params) === 0) {
            return '';
        }

        return '?' . http_build_query($params);
    }

    /**
     * Parses LINE Pay confirmation callback query parameters.
     *
     * @param array<string, mixed> $query Query parameters array from the callback URL
     *
     * @return array{transactionId: string, orderId?: string} Object containing transactionId and optional orderId
     *
     * @throws \InvalidArgumentException If transactionId is missing or empty
     */
    public static function parseConfirmQuery(array $query): array
    {
        $transactionId = $query['transactionId'] ?? null;

        // Handle array values (some frameworks parse repeated params as arrays)
        if (is_array($transactionId)) {
            $transactionId = $transactionId[0] ?? null;
        }

        // Ensure we have a valid scalar value
        if ($transactionId === null || (is_string($transactionId) && $transactionId === '')) {
            throw new \InvalidArgumentException('Missing transactionId in callback query');
        }

        $result = ['transactionId' => is_scalar($transactionId) ? (string) $transactionId : ''];

        $orderId = $query['orderId'] ?? null;
        if (is_array($orderId)) {
            $orderId = $orderId[0] ?? null;
        }

        if ($orderId !== null && $orderId !== '' && is_scalar($orderId)) {
            $result['orderId'] = (string) $orderId;
        }

        return $result;
    }

    /**
     * Generates a UUID v4 nonce.
     *
     * @return string A UUID v4 string
     */
    public static function generateNonce(): string
    {
        // Generate 16 random bytes
        $data = random_bytes(16);

        // Set version to 0100 (UUID v4)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10 (variant RFC 4122)
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Format as UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
