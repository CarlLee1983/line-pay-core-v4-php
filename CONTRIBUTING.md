# Contributing to line-pay-core-v4-php

Thank you for your interest in contributing to `line-pay-core-v4-php`! We welcome contributions from the community.

[繁體中文](./CONTRIBUTING_ZH.md) | English

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Testing](#testing)
- [Documentation](#documentation)

## Code of Conduct

This project adheres to a Code of Conduct that all contributors are expected to follow. Please be respectful and constructive in all interactions.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When creating a bug report, include:

- **Clear title**: Describe the issue briefly
- **Description**: Detailed description of the problem
- **Steps to reproduce**: Step-by-step instructions
- **Expected behavior**: What you expected to happen
- **Actual behavior**: What actually happened
- **Environment**: OS, PHP version, package version
- **Code samples**: Minimal code to reproduce (if applicable)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Explain why this enhancement would be useful**
- **List any alternatives** you've considered

### Pull Requests

We actively welcome your pull requests:

1. Fork the repo and create your branch from `main`
2. If you've added code that should be tested, add tests
3. If you've changed APIs, update the documentation
4. Ensure the test suite passes
5. Make sure your code follows the coding standards
6. Issue the pull request

## Development Setup

### Prerequisites

- [PHP](https://www.php.net/) 8.1 or later
- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)
- A code editor (VS Code or PhpStorm recommended)

### Setup Steps

1. **Fork and clone the repository**

```bash
git clone https://github.com/YOUR_USERNAME/line-pay-core-v4-php.git
cd line-pay-core-v4-php
```

2. **Install dependencies**

```bash
composer install
```

3. **Run tests to ensure everything works**

```bash
composer test
```

## Development Workflow

### 1. Create a Feature Branch

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/your-bug-fix
```

### 2. Make Your Changes

- Write clean, readable code
- Follow the coding standards
- Add tests for new functionality
- Update documentation as needed

### 3. Test Your Changes

```bash
# Run static analysis
composer analyze

# Run tests
composer test

# Check code style
composer lint
```

### 4. Commit Your Changes

```bash
git add .
git commit -m "type: description"
```

See [Commit Guidelines](#commit-guidelines) for commit message format.

### 5. Push and Create Pull Request

```bash
git push origin feature/your-feature-name
```

Then create a Pull Request on GitHub.

## Coding Standards

### PHP

- Use PHP 8.1+ features
- Enable strict types (`declare(strict_types=1)`)
- Follow PSR-12 coding standard
- Document public APIs with PHPDoc comments

### Code Style

We use PHP-CS-Fixer and follow these conventions:

- **Indentation**: 4 spaces
- **Braces**: Same-line for classes and methods
- **Quotes**: Single quotes for strings
- **Naming Conventions**:
  - `PascalCase` for classes
  - `camelCase` for methods and variables
  - `UPPER_CASE` for constants

### Example

```php
<?php

declare(strict_types=1);

namespace LinePay\Core;

/**
 * Validates a LINE Pay transaction ID.
 *
 * @param string $transactionId The transaction ID to validate
 *
 * @throws \InvalidArgumentException When format is invalid
 */
public function validateTransactionId(string $transactionId): void
{
    if (!preg_match('/^\d{19}$/', $transactionId)) {
        throw new \InvalidArgumentException(
            "Invalid transactionId format: expected 19-digit number, got \"{$transactionId}\""
        );
    }
}
```

## Commit Guidelines

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification.

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Code style changes (formatting, semicolons, etc.)
- **refactor**: Code refactoring (neither fixes a bug nor adds a feature)
- **perf**: Performance improvements
- **test**: Adding or updating tests
- **chore**: Changes to build process or auxiliary tools

### Examples

```bash
# Feature
git commit -m "feat: add signature verification utility"

# Bug fix
git commit -m "fix: correct transaction ID validation regex"

# Documentation
git commit -m "docs: update API reference for LinePayUtils"

# Refactoring
git commit -m "refactor: extract signature generation logic"
```

## Pull Request Process

1. **Update Documentation**: Ensure README and API docs are updated
2. **Add Tests**: All new features must include tests
3. **Pass All Checks**: Ensure static analysis, tests, and lint pass
4. **Clean Commit History**: Squash or rebase if needed
5. **Reference Issues**: Link related issues in PR description
6. **Request Review**: Tag maintainers for review

### PR Title Format

Follow the same format as commit messages:

```
feat: add new utility function for parsing
fix: resolve timing attack vulnerability
docs: improve README examples
```

## Testing

### Writing Tests

- Place tests in `tests/` directory
- Use PHPUnit for testing
- Aim for high code coverage
- Test edge cases and error conditions

### Test Example

```php
<?php

declare(strict_types=1);

namespace LinePay\Core\Tests;

use LinePay\Core\LinePayUtils;
use PHPUnit\Framework\TestCase;

class LinePayUtilsTest extends TestCase
{
    public function testIsValidTransactionIdWithValid19DigitId(): void
    {
        $validId = '1234567890123456789';
        
        $this->assertTrue(LinePayUtils::isValidTransactionId($validId));
    }

    public function testIsValidTransactionIdWithInvalidId(): void
    {
        $invalidId = '123456789012345678'; // Only 18 digits
        
        $this->assertFalse(LinePayUtils::isValidTransactionId($invalidId));
    }
}
```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Run specific test file
./vendor/bin/phpunit tests/LinePayUtilsTest.php
```

## Documentation

### README Updates

- Keep README.md (English) and README_ZH.md (Traditional Chinese) in sync
- Include code examples for new features
- Update API reference section

### Code Documentation

- Use PHPDoc for public APIs
- Document parameters, return types, and exceptions
- Include usage examples in comments

### Example

```php
/**
 * Generates HMAC-SHA256 signature for LINE Pay API requests.
 *
 * @param string $secret      Channel secret key
 * @param string $uri         Request URI path
 * @param string $body        Request body (JSON string)
 * @param string $nonce       Random nonce for this request
 * @param string $queryString Optional query string
 *
 * @return string Base64-encoded HMAC-SHA256 signature
 *
 * @example
 * ```php
 * $signature = LinePayUtils::generateSignature(
 *     'my-secret',
 *     '/v3/payments/request',
 *     json_encode(['amount' => 100]),
 *     'random-nonce'
 * );
 * ```
 */
public static function generateSignature(
    string $secret,
    string $uri,
    string $body,
    string $nonce,
    string $queryString = ''
): string {
    // Implementation
}
```

## Questions?

If you have questions about contributing, feel free to:

- Open a [GitHub Discussion](https://github.com/CarlLee1983/line-pay-core-v4-php/discussions)
- Create an issue with the "question" label

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to `line-pay-core-v4-php`! 🎉
