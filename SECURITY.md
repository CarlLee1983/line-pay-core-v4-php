# Security Policy

## Supported Versions

We actively support the following versions of `line-pay-core-v4-php`:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |

## Reporting a Vulnerability

We take the security of `line-pay-core-v4-php` seriously. If you discover a security vulnerability, please follow these steps:

### 1. **Do Not** Open a Public Issue

Please do not report security vulnerabilities through public GitHub issues. This helps prevent exploitation before a fix is available.

### 2. Report Privately

Send your vulnerability report to:

- **GitHub Security Advisories**: [Create a security advisory](https://github.com/CarlLee1983/line-pay-core-v4-php/security/advisories/new)
- **Subject**: `[SECURITY] line-pay-core-v4-php: [Brief Description]`

### 3. Include Details

Please include the following information in your report:

- **Description**: A clear description of the vulnerability
- **Impact**: The potential impact and severity
- **Steps to Reproduce**: Detailed steps to reproduce the issue
- **Affected Versions**: Which versions are affected
- **Suggested Fix**: If you have a suggestion for fixing the issue (optional)
- **Proof of Concept**: Any code or examples demonstrating the vulnerability (optional)

### 4. What to Expect

- **Acknowledgment**: We will acknowledge receipt of your vulnerability report within 48 hours
- **Updates**: We will keep you informed about the progress of addressing the vulnerability
- **Timeline**: We aim to release a fix within 30 days for critical vulnerabilities
- **Credit**: We will credit you (if desired) in the security advisory and release notes

### 5. Security Best Practices

When using `line-pay-core-v4-php`, please follow these security best practices:

#### Channel Secret Protection
```php
// ❌ DON'T: Hard-code secrets
$config = new LinePayConfig(
    channelId: '1234567890',
    channelSecret: 'my-secret-key' // Never hard-code!
);

// ✅ DO: Use environment variables
$config = new LinePayConfig(
    channelId: getenv('LINE_PAY_CHANNEL_ID'),
    channelSecret: getenv('LINE_PAY_CHANNEL_SECRET')
);
```

#### Signature Verification
Always verify signatures when receiving webhook notifications from LINE Pay:

```php
use LinePay\Core\LinePayUtils;

// Verify signature using timing-safe comparison
$isValid = LinePayUtils::verifySignature(
    $channelSecret,
    $data,
    $receivedSignature
);

if (!$isValid) {
    throw new Exception('Invalid signature');
}
```

#### HTTPS Only
Always use HTTPS for LINE Pay API communications. The SDK enforces this in production environments.

#### Keep Dependencies Updated
Regularly update `line-pay-core-v4-php` and its dependencies to receive security patches:

```bash
composer update carllee/line-pay-core-v4
```

## Security Features

`line-pay-core-v4-php` includes the following security features:

### 1. Timing-Safe Signature Verification
Uses `hash_equals()` to prevent timing attacks when verifying HMAC signatures.

### 2. Input Validation
- Transaction ID format validation
- Configuration parameter validation
- Type-safe API with PHP 8.1+ strict types

### 3. Error Handling
Custom exception classes that don't leak sensitive information in error messages.

### 4. Minimal Dependencies
Only essential dependencies (Guzzle HTTP) to reduce the attack surface.

## Security Updates

Security updates will be released as patch versions and announced through:

- GitHub Security Advisories
- Release notes
- Packagist package updates

## Scope

This security policy applies to:

- The `carllee/line-pay-core-v4` package
- Security issues in the core library code
- Security issues in the build process and distribution

This policy does **not** cover:

- Security issues in applications built using this library (unless caused by the library itself)
- Issues in LINE Pay's backend services
- Social engineering attacks

## Additional Resources

- [LINE Pay API Documentation](https://pay.line.me/documents/online_v3_en.html)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [Composer Security Advisories](https://packagist.org/advisories)

## Questions?

If you have questions about this security policy, please open a GitHub issue (for non-security questions) or contact the maintainers directly.

---

**Last Updated**: December 11, 2024
