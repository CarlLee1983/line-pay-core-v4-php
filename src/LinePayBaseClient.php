<?php

declare(strict_types=1);

namespace LinePay\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use LinePay\Core\Config\LinePayConfig;
use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\Errors\LinePayError;
use LinePay\Core\Errors\LinePayTimeoutError;

/**
 * LINE Pay Base Client.
 *
 * Abstract base class for LINE Pay API integration (Online and Offline).
 * Provides core functionality for:
 * - API authentication with HMAC-SHA256 signatures
 * - HTTP request handling with timeout support
 * - Response parsing and error handling
 * - Configuration validation
 *
 * This class should be extended by specific API clients (e.g., `LinePayOnlineClient`).
 */
abstract class LinePayBaseClient
{
    /**
     * LINE Pay Channel ID.
     */
    protected readonly string $channelId;

    /**
     * LINE Pay Channel Secret.
     */
    protected readonly string $channelSecret;

    /**
     * BASE URL for LINE Pay API.
     */
    protected readonly string $baseUrl;

    /**
     * Request timeout in seconds.
     */
    protected readonly int $timeout;

    /**
     * HTTP client instance.
     */
    protected Client $httpClient;

    /**
     * Creates a new LinePayBaseClient instance.
     *
     * @param LinePayConfig $config LINE Pay configuration object
     *
     * @throws LinePayConfigError If channelId or channelSecret is empty
     * @throws LinePayConfigError If timeout is not a positive number
     */
    public function __construct(LinePayConfig $config)
    {
        if ($config->channelId === '') {
            throw new LinePayConfigError('channelId is required and cannot be empty');
        }
        if ($config->channelSecret === '') {
            throw new LinePayConfigError('channelSecret is required and cannot be empty');
        }
        if ($config->timeout <= 0) {
            throw new LinePayConfigError('timeout must be a positive number');
        }

        $this->channelId = $config->channelId;
        $this->channelSecret = $config->channelSecret;
        $this->baseUrl = $config->getBaseUrl();
        $this->timeout = $config->timeout;

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout,
            'http_errors' => false,
        ]);
    }

    /**
     * Sends an HTTP request to LINE Pay API with authentication.
     *
     * @param string                     $method            HTTP method ('GET' or 'POST')
     * @param string                     $path              API endpoint path
     * @param array<string, mixed>|null  $body              Optional request body
     * @param array<string, string>|null $params            Optional query parameters
     * @param array<string, string>|null $additionalHeaders Optional additional HTTP headers
     *
     * @return array<string, mixed> LINE Pay response data
     *
     * @throws LinePayTimeoutError If request exceeds configured timeout
     * @throws LinePayError        If API returns an error or response is invalid
     */
    protected function sendRequest(
        string $method,
        string $path,
        ?array $body = null,
        ?array $params = null,
        ?array $additionalHeaders = null
    ): array {
        $nonce = LinePayUtils::generateNonce();
        $queryString = LinePayUtils::buildQueryString($params);
        $url = $path . $queryString;
        $bodyString = $body !== null ? json_encode($body, JSON_UNESCAPED_UNICODE) : '';

        $signature = LinePayUtils::generateSignature(
            $this->channelSecret,
            $path,
            $bodyString ?: '',
            $nonce,
            $queryString
        );

        $headers = [
            'Content-Type' => 'application/json',
            'X-LINE-ChannelId' => $this->channelId,
            'X-LINE-Authorization-Nonce' => $nonce,
            'X-LINE-Authorization' => $signature,
        ];

        if ($additionalHeaders !== null) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        $options = [
            'headers' => $headers,
        ];

        if ($method === 'POST' && $bodyString !== '') {
            $options['body'] = $bodyString;
        }

        try {
            $response = $this->httpClient->request($method, $url, $options);
            $responseText = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            /** @var array<string, mixed>|null $jsonResponse */
            $jsonResponse = json_decode($responseText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($jsonResponse)) {
                throw new LinePayError(
                    'PARSE_ERROR',
                    'Failed to parse response as JSON',
                    $statusCode,
                    $responseText
                );
            }

            $returnCode = isset($jsonResponse['returnCode']) && is_string($jsonResponse['returnCode'])
                ? $jsonResponse['returnCode']
                : '';
            $returnMessage = isset($jsonResponse['returnMessage']) && is_string($jsonResponse['returnMessage'])
                ? $jsonResponse['returnMessage']
                : '';

            if ($statusCode >= 400) {
                throw new LinePayError(
                    $returnCode !== '' ? $returnCode : 'HTTP_ERROR',
                    $returnMessage !== '' ? $returnMessage : $response->getReasonPhrase(),
                    $statusCode,
                    $responseText
                );
            }

            if ($returnCode !== '0000') {
                throw new LinePayError(
                    $returnCode !== '' ? $returnCode : 'UNKNOWN_ERROR',
                    $returnMessage !== '' ? $returnMessage : 'Unknown error',
                    $statusCode,
                    $responseText
                );
            }

            return $jsonResponse;
        } catch (ConnectException $e) {
            if (str_contains($e->getMessage(), 'timed out') || str_contains($e->getMessage(), 'timeout')) {
                throw new LinePayTimeoutError($this->timeout, $this->baseUrl . $url);
            }
            throw $e;
        } catch (RequestException $e) {
            if ($e->getPrevious() instanceof LinePayError) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

    /**
     * Get the configured channel ID.
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * Get the API base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the configured timeout.
     *
     * @return int Timeout in seconds
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
