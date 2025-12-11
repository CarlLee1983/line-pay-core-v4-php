# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-12-11

### Added

- Initial release of `line-pay-core-v4-php`
- **LinePayBaseClient**: Abstract base class for LINE Pay API integration
  - HMAC-SHA256 signature generation for API authentication
  - HTTP request handling with Guzzle
  - Configurable timeout support
  - Comprehensive error handling
- **LinePayUtils**: Utility class with static helper methods
  - `generateSignature()`: Generate HMAC-SHA256 signatures
  - `verifySignature()`: Timing-safe signature verification
  - `validateTransactionId()`: Validate 19-digit transaction IDs
  - `isValidTransactionId()`: Non-throwing transaction ID validation
  - `buildQueryString()`: Build URL query strings from arrays
  - `parseConfirmQuery()`: Parse LINE Pay callback query parameters
  - `generateNonce()`: Generate UUID v4 nonces
- **Configuration Classes**:
  - `LinePayConfig`: Type-safe configuration management
  - `Env`: Environment constants and base URLs
- **Error Classes**:
  - `LinePayError`: API error with return code categorization
  - `LinePayTimeoutError`: Request timeout errors
  - `LinePayConfigError`: Configuration validation errors
  - `LinePayValidationError`: Input validation errors
- Full PHPDoc documentation for all public APIs
- Comprehensive test suite with 43 tests and 89 assertions
- PHPStan Level Max static analysis support
- PHP-CS-Fixer for code style enforcement
- Multi-language README (English and Traditional Chinese)
- Security policy and contributing guidelines

### Security

- Timing-safe signature verification using `hash_equals()`
- Input validation for transaction IDs
- Configuration parameter validation
- Secure HMAC-SHA256 signature generation

[1.0.0]: https://github.com/CarlLee1983/line-pay-core-v4-php/releases/tag/v1.0.0
