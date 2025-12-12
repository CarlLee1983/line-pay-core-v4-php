# LINE Pay Core V4 PHP

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)

LINE Pay API V4 SDK コアライブラリ - LINE Pay統合を構築するための共有ユーティリティ、ベースクライアント、型定義、エラー処理を提供します。

**🌐 Language / 語言 / 言語 / ภาษา:**
[English](./README.md) | [繁體中文](./README_ZH.md) | [日本語](./README_JA.md) | [ภาษาไทย](./README_TH.md)

## 概要

このパッケージは、PHPでLINE Pay V4統合を構築するための基礎コンポーネントを提供します：

- **LinePayBaseClient**: 認証、HTTP処理、エラー管理を含む抽象ベースクラス
- **LinePayUtils**: 署名生成、検証、クエリ文字列処理のユーティリティクラス
- **Error Classes**: 特定の例外タイプを持つ包括的なエラー処理
- **Configuration**: タイプセーフな設定管理

## 要件

- PHP 8.1以上
- ext-json
- ext-openssl
- Guzzle HTTP Client 7.0+

## インストール

```bash
composer require carllee/line-pay-core-v4
```

## 使用方法

これはコアライブラリであり、特定のLINE Pay実装（Online/Offline）によって継承されることを意図しています。

### カスタムクライアントの作成

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

// 使用方法
$config = new LinePayConfig(
    channelId: getenv('LINE_PAY_CHANNEL_ID'),
    channelSecret: getenv('LINE_PAY_CHANNEL_SECRET'),
    env: 'sandbox', // または 'production'
    timeout: 30
);

$client = new MyLinePayClient($config);
```

### ユーティリティの使用

```php
use LinePay\Core\LinePayUtils;

// 署名を生成
$signature = LinePayUtils::generateSignature(
    $channelSecret,
    '/v3/payments/request',
    json_encode($requestBody),
    $nonce
);

// 署名を検証（タイミング攻撃対策）
$isValid = LinePayUtils::verifySignature($secret, $data, $receivedSignature);

// 取引IDを検証
if (LinePayUtils::isValidTransactionId($transactionId)) {
    // 取引を処理
}

// コールバッククエリを解析
$result = LinePayUtils::parseConfirmQuery($_GET);
// $result['transactionId'], $result['orderId']
```

### エラーハンドリング

```php
use LinePay\Core\Errors\LinePayError;
use LinePay\Core\Errors\LinePayTimeoutError;
use LinePay\Core\Errors\LinePayConfigError;
use LinePay\Core\Errors\LinePayValidationError;

try {
    $response = $client->requestPayment($body);
} catch (LinePayTimeoutError $e) {
    // タイムアウトを処理
    echo "リクエストが {$e->getTimeout()} 秒後にタイムアウトしました";
} catch (LinePayError $e) {
    // APIエラーを処理
    echo "エラー [{$e->getReturnCode()}]: {$e->getReturnMessage()}";
    
    if ($e->isAuthError()) {
        // 認証エラーを処理（1xxxコード）
    } elseif ($e->isPaymentError()) {
        // 決済エラーを処理（2xxxコード）
    } elseif ($e->isInternalError()) {
        // 内部エラーを処理（9xxxコード）
    }
}
```

## 設定パラメータ

| パラメータ | 型 | 必須 | デフォルト | 説明 |
|------------|------|------|------------|------|
| `channelId` | string | はい | - | LINE Pay加盟店センターのChannel ID |
| `channelSecret` | string | はい | - | LINE Pay加盟店センターのChannel Secret |
| `env` | string | いいえ | `'sandbox'` | 環境：`'production'` または `'sandbox'` |
| `timeout` | int | いいえ | `20` | リクエストタイムアウト（秒） |

## 関連パッケージ

- [`carllee/line-pay-online-v4`](https://github.com/CarlLee1983/line-pay-online-v4-php) - LINE Pay Online API V4クライアント
- [`carllee/line-pay-offline-v4`](https://github.com/CarlLee1983/line-pay-offline-v4-php) - LINE Pay Offline API V4クライアント

## 開発

```bash
# 依存関係をインストール
composer install

# テストを実行
composer test

# カバレッジ付きでテストを実行
composer test:coverage

# 静的解析を実行
composer analyze

# コードスタイルを修正
composer lint:fix
```

## ライセンス

MIT License - 詳細は [LICENSE](LICENSE) ファイルをご覧ください。

## 作者

Carl Lee - [GitHub](https://github.com/CarlLee1983)
