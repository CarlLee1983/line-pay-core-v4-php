<?php

declare(strict_types=1);

namespace LinePay\Core\Tests;

use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\Errors\LinePayError;
use LinePay\Core\Errors\LinePayTimeoutError;
use LinePay\Core\Errors\LinePayValidationError;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for Error classes.
 */
class ErrorsTest extends TestCase
{
    public function testLinePayError(): void
    {
        $error = new LinePayError('1104', 'Invalid Channel ID', 400, '{"error": "details"}');

        $this->assertEquals('1104', $error->getReturnCode());
        $this->assertEquals('Invalid Channel ID', $error->getReturnMessage());
        $this->assertEquals(400, $error->getHttpStatus());
        $this->assertEquals('{"error": "details"}', $error->getRawResponse());
        $this->assertStringContainsString('[1104]', $error->getMessage());
        $this->assertStringContainsString('Invalid Channel ID', $error->getMessage());
    }

    public function testLinePayErrorIsAuthError(): void
    {
        $authError = new LinePayError('1104', 'Auth Error', 401);
        $paymentError = new LinePayError('2101', 'Payment Error', 400);

        $this->assertTrue($authError->isAuthError());
        $this->assertFalse($paymentError->isAuthError());
    }

    public function testLinePayErrorIsPaymentError(): void
    {
        $authError = new LinePayError('1104', 'Auth Error', 401);
        $paymentError = new LinePayError('2101', 'Payment Error', 400);

        $this->assertFalse($authError->isPaymentError());
        $this->assertTrue($paymentError->isPaymentError());
    }

    public function testLinePayErrorIsInternalError(): void
    {
        $internalError = new LinePayError('9000', 'Internal Error', 500);
        $authError = new LinePayError('1104', 'Auth Error', 401);

        $this->assertTrue($internalError->isInternalError());
        $this->assertFalse($authError->isInternalError());
    }

    public function testLinePayErrorToArray(): void
    {
        $error = new LinePayError('1104', 'Invalid Channel ID', 400, 'raw');
        $array = $error->toArray();

        $this->assertEquals('LinePayError', $array['name']);
        $this->assertEquals('1104', $array['returnCode']);
        $this->assertEquals('Invalid Channel ID', $array['returnMessage']);
        $this->assertEquals(400, $array['httpStatus']);
        $this->assertEquals('raw', $array['rawResponse']);
    }

    public function testLinePayTimeoutError(): void
    {
        $error = new LinePayTimeoutError(30, 'https://api-pay.line.me/v3/payments');

        $this->assertEquals(30, $error->getTimeout());
        $this->assertEquals('https://api-pay.line.me/v3/payments', $error->getUrl());
        $this->assertStringContainsString('30s', $error->getMessage());
    }

    public function testLinePayTimeoutErrorWithoutUrl(): void
    {
        $error = new LinePayTimeoutError(20);

        $this->assertEquals(20, $error->getTimeout());
        $this->assertNull($error->getUrl());
    }

    public function testLinePayConfigError(): void
    {
        $error = new LinePayConfigError('channelId is required');

        $this->assertEquals('channelId is required', $error->getMessage());
    }

    public function testLinePayValidationError(): void
    {
        $error = new LinePayValidationError('Amount must be positive', 'amount');

        $this->assertEquals('Amount must be positive', $error->getMessage());
        $this->assertEquals('amount', $error->getField());
    }

    public function testLinePayValidationErrorWithoutField(): void
    {
        $error = new LinePayValidationError('Validation failed');

        $this->assertEquals('Validation failed', $error->getMessage());
        $this->assertNull($error->getField());
    }
}
