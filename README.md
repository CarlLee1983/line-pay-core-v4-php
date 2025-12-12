# LINE Pay Core V4 PHP

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)

Core library for LINE Pay API V4 SDK - Provides shared utilities, base client, types, and error handling for building LINE Pay integrations.

**🌐 Language / 語言 / 言語 / ภาษา:**
[English](./README.md) | [繁體中文](./README_ZH.md) | [日本語](./README_JA.md) | [ภาษาไทย](./README_TH.md)

## Overview

This package provides the foundational components for building LINE Pay V4 integrations in PHP:

- **LinePayBaseClient**: Abstract base class with authentication, HTTP handling, and error management
- **LinePayUtils**: Utility class for signature generation, validation, and query string handling
- **Error Classes**: Comprehensive error handling with specific exception types
- **Configuration**: Type-safe configuration management

## Requirements

- PHP 8.1 or higher
- ext-json
- ext-openssl
- Guzzle HTTP Client 7.0+

## Installation

```bash
composer require carllee/line-pay-core-v4
```

## Usage

This is a core library meant to be extended by specific LINE Pay implementations (Online/Offline).

### Creating a Custom Client

```php
use LinePay\Core\LinePayBaseClient;
use LinePay\Core\Config\LinePayConfig;

class MyLinePayClient extends LinePayBaseClient
{
    public function requestPayment(array $body): array
    {
        return $this->sendRequest('POST', '/v3/payments/request', $body);
    }

    public function confirmPayment(string $transactionId, array $body): array
    {
        return $this->sendRequest(
            'POST',
            "/v3/payments/{$transactionId}/confirm",
            $body
        );
    }
}

// Usage
$config = new LinePayConfig(
    channelId: getenv('LINE_PAY_CHANNEL_ID'),
    channelSecret: getenv('LINE_PAY_CHANNEL_SECRET'),
    env: 'sandbox', // or 'production'
    timeout: 30
);

$client = new MyLinePayClient($config);
```

### Using Utilities

```php
use LinePay\Core\LinePayUtils;

// Generate signature
$signature = LinePayUtils::generateSignature(
    $channelSecret,
    '/v3/payments/request',
    json_encode($requestBody),
    $nonce
);

// Verify signature (timing-safe)
$isValid = LinePayUtils::verifySignature($secret, $data, $receivedSignature);

// Validate transaction ID
if (LinePayUtils::isValidTransactionId($transactionId)) {
    // Process transaction
}

// Parse callback query
$result = LinePayUtils::parseConfirmQuery($_GET);
// $result['transactionId'], $result['orderId']
```

### Error Handling

```php
use LinePay\Core\Errors\LinePayError;
use LinePay\Core\Errors\LinePayTimeoutError;
use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\Errors\LinePayValidationError;

try {
    $response = $client->requestPayment($body);
} catch (LinePayTimeoutError $e) {
    // Handle timeout
    echo "Request timed out after {$e->getTimeout()} seconds";
} catch (LinePayError $e) {
    // Handle API errors
    echo "Error [{$e->getReturnCode()}]: {$e->getReturnMessage()}";
    
    if ($e->isAuthError()) {
        // Handle authentication errors (1xxx codes)
    } elseif ($e->isPaymentError()) {
        // Handle payment errors (2xxx codes)
    } elseif ($e->isInternalError()) {
        // Handle internal errors (9xxx codes)
    }
}
```

## Configuration

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | string | Yes | - | Channel ID from LINE Pay Merchant Center |
| `channelSecret` | string | Yes | - | Channel Secret from LINE Pay Merchant Center |
| `env` | string | No | `'sandbox'` | Environment: `'production'` or `'sandbox'` |
| `timeout` | int | No | `20` | Request timeout in seconds |

## Related Packages

- [`carllee/line-pay-online-v4`](https://github.com/CarlLee1983/line-pay-online-v4-php) - LINE Pay Online API V4 client
- [`carllee/line-pay-offline-v4`](https://github.com/CarlLee1983/line-pay-offline-v4-php) - LINE Pay Offline API V4 client

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test:coverage

# Run static analysis
composer analyze

# Fix code style
composer lint:fix
```

## License

MIT License - see the [LICENSE](LICENSE) file for details.

## Author

Carl Lee - [GitHub](https://github.com/CarlLee1983)
