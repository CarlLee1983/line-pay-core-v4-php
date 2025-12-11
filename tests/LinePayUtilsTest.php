<?php

declare(strict_types=1);

namespace LinePay\Core\Tests;

use InvalidArgumentException;
use LinePay\Core\LinePayUtils;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for LinePayUtils.
 */
class LinePayUtilsTest extends TestCase
{
    public function testGenerateSignature(): void
    {
        $secret = 'test-secret';
        $uri = '/v3/payments/request';
        $body = '{"amount":1000}';
        $nonce = 'test-nonce-123';

        $signature = LinePayUtils::generateSignature($secret, $uri, $body, $nonce);

        $this->assertNotEmpty($signature);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/]+=*$/', $signature);
    }

    public function testGenerateSignatureWithQueryString(): void
    {
        $secret = 'test-secret';
        $uri = '/v3/payments/request';
        $body = '{"amount":1000}';
        $nonce = 'test-nonce-123';
        $queryString = '?foo=bar';

        $signatureWithQuery = LinePayUtils::generateSignature($secret, $uri, $body, $nonce, $queryString);
        $signatureWithoutQuery = LinePayUtils::generateSignature($secret, $uri, $body, $nonce);

        $this->assertNotEquals($signatureWithQuery, $signatureWithoutQuery);
    }

    public function testVerifySignatureValid(): void
    {
        $secret = 'test-secret';
        $data = 'test-data-to-sign';

        $signature = LinePayUtils::generateSignature($secret, $data, '', '');
        $isValid = LinePayUtils::verifySignature($secret, $secret . $data, $signature);

        $this->assertTrue($isValid);
    }

    public function testVerifySignatureInvalid(): void
    {
        $secret = 'test-secret';
        $data = 'test-data';

        $isValid = LinePayUtils::verifySignature($secret, $data, 'invalid-signature');

        $this->assertFalse($isValid);
    }

    public function testIsValidTransactionIdValid(): void
    {
        $transactionId = '1234567890123456789';

        $this->assertTrue(LinePayUtils::isValidTransactionId($transactionId));
    }

    public function testIsValidTransactionIdInvalid(): void
    {
        $this->assertFalse(LinePayUtils::isValidTransactionId('12345'));
        $this->assertFalse(LinePayUtils::isValidTransactionId('12345678901234567890'));
        $this->assertFalse(LinePayUtils::isValidTransactionId('123456789012345678a'));
        $this->assertFalse(LinePayUtils::isValidTransactionId(''));
    }

    public function testValidateTransactionIdThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transactionId format');

        LinePayUtils::validateTransactionId('12345');
    }

    public function testValidateTransactionIdValid(): void
    {
        LinePayUtils::validateTransactionId('1234567890123456789');

        $this->assertTrue(true);
    }

    public function testBuildQueryStringWithParams(): void
    {
        $params = [
            'foo' => 'bar',
            'baz' => 'qux',
        ];

        $queryString = LinePayUtils::buildQueryString($params);

        $this->assertStringStartsWith('?', $queryString);
        $this->assertStringContainsString('foo=bar', $queryString);
        $this->assertStringContainsString('baz=qux', $queryString);
    }

    public function testBuildQueryStringEmpty(): void
    {
        $queryString = LinePayUtils::buildQueryString([]);

        $this->assertEquals('', $queryString);
    }

    public function testBuildQueryStringNull(): void
    {
        $queryString = LinePayUtils::buildQueryString(null);

        $this->assertEquals('', $queryString);
    }

    public function testParseConfirmQueryValid(): void
    {
        $query = [
            'transactionId' => '1234567890123456789',
            'orderId' => 'ORDER-123',
        ];

        $result = LinePayUtils::parseConfirmQuery($query);

        $this->assertEquals('1234567890123456789', $result['transactionId']);
        $this->assertArrayHasKey('orderId', $result);
        if (isset($result['orderId'])) {
            $this->assertEquals('ORDER-123', $result['orderId']);
        }
    }

    public function testParseConfirmQueryWithoutOrderId(): void
    {
        $query = [
            'transactionId' => '1234567890123456789',
        ];

        $result = LinePayUtils::parseConfirmQuery($query);

        $this->assertEquals('1234567890123456789', $result['transactionId']);
        $this->assertArrayNotHasKey('orderId', $result);
    }

    public function testParseConfirmQueryWithArrayValues(): void
    {
        $query = [
            'transactionId' => ['1234567890123456789'],
            'orderId' => ['ORDER-123'],
        ];

        $result = LinePayUtils::parseConfirmQuery($query);

        $this->assertEquals('1234567890123456789', $result['transactionId']);
        $this->assertArrayHasKey('orderId', $result);
        if (isset($result['orderId'])) {
            $this->assertEquals('ORDER-123', $result['orderId']);
        }
    }

    public function testParseConfirmQueryMissingTransactionId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing transactionId in callback query');

        LinePayUtils::parseConfirmQuery([]);
    }

    public function testParseConfirmQueryEmptyTransactionId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing transactionId in callback query');

        LinePayUtils::parseConfirmQuery(['transactionId' => '']);
    }

    public function testGenerateNonce(): void
    {
        $nonce = LinePayUtils::generateNonce();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $nonce
        );
    }

    public function testGenerateNonceUnique(): void
    {
        $nonces = [];

        for ($i = 0; $i < 100; $i++) {
            $nonces[] = LinePayUtils::generateNonce();
        }

        $this->assertCount(100, array_unique($nonces));
    }
}
