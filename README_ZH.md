# LINE Pay Core V4 PHP

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)

LINE Pay API V4 SDK 核心庫 - 提供共用工具、基礎客戶端、型別定義和錯誤處理，用於建立 LINE Pay 整合。

繁體中文 | [English](./README.md)

## 概述

此套件提供在 PHP 中建立 LINE Pay V4 整合的基礎元件：

- **LinePayBaseClient**: 抽象基礎類別，包含認證、HTTP 處理和錯誤管理
- **LinePayUtils**: 工具類別，用於簽章生成、驗證和查詢字串處理
- **Error Classes**: 完整的錯誤處理，包含特定的例外類型
- **Configuration**: 型別安全的設定管理

## 需求

- PHP 8.1 或更高版本
- ext-json
- ext-openssl
- Guzzle HTTP Client 7.0+

## 安裝

```bash
composer require carllee/line-pay-core-v4
```

## 使用方式

這是一個核心庫，旨在被特定的 LINE Pay 實作（Online/Offline）繼承使用。

### 建立自訂客戶端

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

// 使用方式
$config = new LinePayConfig(
    channelId: getenv('LINE_PAY_CHANNEL_ID'),
    channelSecret: getenv('LINE_PAY_CHANNEL_SECRET'),
    env: 'sandbox', // 或 'production'
    timeout: 30
);

$client = new MyLinePayClient($config);
```

### 使用工具類別

```php
use LinePay\Core\LinePayUtils;

// 生成簽章
$signature = LinePayUtils::generateSignature(
    $channelSecret,
    '/v3/payments/request',
    json_encode($requestBody),
    $nonce
);

// 驗證簽章（防時序攻擊）
$isValid = LinePayUtils::verifySignature($secret, $data, $receivedSignature);

// 驗證交易 ID
if (LinePayUtils::isValidTransactionId($transactionId)) {
    // 處理交易
}

// 解析回調查詢
$result = LinePayUtils::parseConfirmQuery($_GET);
// $result['transactionId'], $result['orderId']
```

### 錯誤處理

```php
use LinePay\Core\Errors\LinePayError;
use LinePay\Core\Errors\LinePayTimeoutError;
use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\Errors\LinePayValidationError;

try {
    $response = $client->requestPayment($body);
} catch (LinePayTimeoutError $e) {
    // 處理逾時
    echo "請求在 {$e->getTimeout()} 秒後逾時";
} catch (LinePayError $e) {
    // 處理 API 錯誤
    echo "錯誤 [{$e->getReturnCode()}]: {$e->getReturnMessage()}";
    
    if ($e->isAuthError()) {
        // 處理認證錯誤（1xxx 代碼）
    } elseif ($e->isPaymentError()) {
        // 處理支付錯誤（2xxx 代碼）
    } elseif ($e->isInternalError()) {
        // 處理內部錯誤（9xxx 代碼）
    }
}
```

## 設定參數

| 參數 | 類型 | 必填 | 預設值 | 說明 |
|------|------|------|--------|------|
| `channelId` | string | 是 | - | LINE Pay 商家後台的 Channel ID |
| `channelSecret` | string | 是 | - | LINE Pay 商家後台的 Channel Secret |
| `env` | string | 否 | `'sandbox'` | 環境：`'production'` 或 `'sandbox'` |
| `timeout` | int | 否 | `20` | 請求逾時時間（秒） |

## 相關套件

- `carllee/line-pay-online-v4` - LINE Pay Online API V4 客戶端（即將推出）
- `carllee/line-pay-offline-v4` - LINE Pay Offline API V4 客戶端（即將推出）

## 開發

```bash
# 安裝依賴
composer install

# 執行測試
composer test

# 執行測試並產生覆蓋率報告
composer test:coverage

# 執行靜態分析
composer analyze

# 修正程式碼風格
composer lint:fix
```

## 授權

MIT License - 詳見 [LICENSE](LICENSE) 文件。

## 作者

Carl Lee - [GitHub](https://github.com/CarlLee1983)
