# LINE Pay Core V4 PHP

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)

ไลบรารีหลักสำหรับ LINE Pay API V4 SDK - ให้บริการยูทิลิตี้ที่ใช้ร่วมกัน, base client, ประเภทข้อมูล และการจัดการข้อผิดพลาดสำหรับการสร้างการผสานรวม LINE Pay

**🌐 Language / 語言 / 言語 / ภาษา:**
[English](./README.md) | [繁體中文](./README_ZH.md) | [日本語](./README_JA.md) | [ภาษาไทย](./README_TH.md)

## ภาพรวม

แพ็คเกจนี้ให้ส่วนประกอบพื้นฐานสำหรับการสร้างการผสานรวม LINE Pay V4 ใน PHP:

- **LinePayBaseClient**: Abstract base class พร้อมการยืนยันตัวตน, การจัดการ HTTP และการจัดการข้อผิดพลาด
- **LinePayUtils**: Utility class สำหรับการสร้างลายเซ็น, การตรวจสอบ และการจัดการ query string
- **Error Classes**: การจัดการข้อผิดพลาดที่ครอบคลุมพร้อม exception types เฉพาะ
- **Configuration**: การจัดการการตั้งค่าที่ปลอดภัยด้านประเภทข้อมูล

## ความต้องการ

- PHP 8.1 หรือสูงกว่า
- ext-json
- ext-openssl
- Guzzle HTTP Client 7.0+

## การติดตั้ง

```bash
composer require carllee/line-pay-core-v4
```

## การใช้งาน

นี่คือไลบรารีหลักที่ออกแบบมาเพื่อให้ implementation เฉพาะของ LINE Pay (Online/Offline) สืบทอด

### สร้าง Custom Client

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

// การใช้งาน
$config = new LinePayConfig(
    channelId: getenv('LINE_PAY_CHANNEL_ID'),
    channelSecret: getenv('LINE_PAY_CHANNEL_SECRET'),
    env: 'sandbox', // หรือ 'production'
    timeout: 30
);

$client = new MyLinePayClient($config);
```

### การใช้ Utilities

```php
use LinePay\Core\LinePayUtils;

// สร้างลายเซ็น
$signature = LinePayUtils::generateSignature(
    $channelSecret,
    '/v3/payments/request',
    json_encode($requestBody),
    $nonce
);

// ตรวจสอบลายเซ็น (ปลอดภัยจาก timing attack)
$isValid = LinePayUtils::verifySignature($secret, $data, $receivedSignature);

// ตรวจสอบ transaction ID
if (LinePayUtils::isValidTransactionId($transactionId)) {
    // ประมวลผลธุรกรรม
}

// แยกวิเคราะห์ callback query
$result = LinePayUtils::parseConfirmQuery($_GET);
// $result['transactionId'], $result['orderId']
```

### การจัดการข้อผิดพลาด

```php
use LinePay\Core\Errors\LinePayError;
use LinePay\Core\Errors\LinePayTimeoutError;
use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\Errors\LinePayValidationError;

try {
    $response = $client->requestPayment($body);
} catch (LinePayTimeoutError $e) {
    // จัดการ timeout
    echo "คำขอหมดเวลาหลังจาก {$e->getTimeout()} วินาที";
} catch (LinePayError $e) {
    // จัดการข้อผิดพลาด API
    echo "ข้อผิดพลาด [{$e->getReturnCode()}]: {$e->getReturnMessage()}";
    
    if ($e->isAuthError()) {
        // จัดการข้อผิดพลาดการยืนยันตัวตน (รหัส 1xxx)
    } elseif ($e->isPaymentError()) {
        // จัดการข้อผิดพลาดการชำระเงิน (รหัส 2xxx)
    } elseif ($e->isInternalError()) {
        // จัดการข้อผิดพลาดภายใน (รหัส 9xxx)
    }
}
```

## พารามิเตอร์การตั้งค่า

| พารามิเตอร์ | ประเภท | จำเป็น | ค่าเริ่มต้น | คำอธิบาย |
|-------------|--------|--------|-------------|----------|
| `channelId` | string | ใช่ | - | Channel ID จาก LINE Pay Merchant Center |
| `channelSecret` | string | ใช่ | - | Channel Secret จาก LINE Pay Merchant Center |
| `env` | string | ไม่ | `'sandbox'` | สภาพแวดล้อม: `'production'` หรือ `'sandbox'` |
| `timeout` | int | ไม่ | `20` | timeout ของคำขอเป็นวินาที |

## แพ็คเกจที่เกี่ยวข้อง

- [`carllee/line-pay-online-v4`](https://github.com/CarlLee1983/line-pay-online-v4-php) - LINE Pay Online API V4 client
- [`carllee/line-pay-offline-v4`](https://github.com/CarlLee1983/line-pay-offline-v4-php) - LINE Pay Offline API V4 client

## การพัฒนา

```bash
# ติดตั้ง dependencies
composer install

# รันการทดสอบ
composer test

# รันการทดสอบพร้อม coverage
composer test:coverage

# รันการวิเคราะห์แบบ static
composer analyze

# แก้ไข code style
composer lint:fix
```

## สัญญาอนุญาต

MIT License - ดูรายละเอียดที่ไฟล์ [LICENSE](LICENSE)

## ผู้เขียน

Carl Lee - [GitHub](https://github.com/CarlLee1983)
