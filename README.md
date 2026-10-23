# Sentry

把 MediaWiki 的异常和 PHP 错误上报到 [Sentry](https://sentry.io/) 的扩展。

> [!WARNING]
> 本扩展已不再积极维护。Mooncell 各站从 MediaWiki 1.43 起改为在站点配置里直接初始化 Sentry SDK 并注册 `LogException` 钩子，已经不再加载本扩展。仓库保留作参考，issue 和 PR 可能不会及时处理。

## 工作方式

- 注册 `LogException` 钩子，把 MediaWiki 记录的异常和 PHP 错误（`MWExceptionHandler` 会把 PHP 错误包装成 `ErrorException`）交给 `\Sentry\captureException()` 上报。
- 被 `@`、AtEase 或 `error_reporting` 屏蔽的错误（钩子参数 `$suppressed` 为 `true`）不上报。这和 MediaWiki core 的处理一致：core 只把这类错误以 DEBUG 级别记到 `silenced-error` 频道。
- Sentry SDK 在钩子处理器构造时初始化，也就是第一次触发 `LogException` 的时候。

## 安装

要求 MediaWiki 1.35 及以上，依赖 `sentry/sentry` ^4.7。

1. 把仓库克隆到 `extensions/Sentry`。
2. 在 MediaWiki 根目录的 `composer.local.json` 里，把 `extensions/Sentry/composer.json` 加进 `extra.merge-plugin.include`，然后运行 `composer update` 安装依赖。
3. 在 `LocalSettings.php` 里启用并填写 DSN：

```php
wfLoadExtension( 'Sentry' );
$wgSentryDsn = 'https://examplePublicKey@o0.ingest.sentry.io/0';
```

## 配置

| 变量 | 默认值 | 对应的 SDK 选项 |
| --- | --- | --- |
| `$wgSentryDsn` | `''` | `dsn` |
| `$wgSentrySampleRate` | `1.0` | `sample_rate` |
| `$wgSentryTraceSampleRate` | `0.1` | `traces_sample_rate` |
| `$wgSentryProfileSampleRate` | `0.1` | `profiles_sample_rate` |
| `$wgSentryBeforeSend` | `null` | `before_send`，设置了才会传给 SDK |

各选项的含义见 [Sentry PHP SDK 的配置文档](https://docs.sentry.io/platforms/php/configuration/options/)。

### 过滤事件

用 `$wgSentryBeforeSend` 丢弃不想上报的事件，回调返回 `null` 即丢弃，详见 [Filtering](https://docs.sentry.io/platforms/php/configuration/filtering/)：

```php
$wgSentryBeforeSend = static function ( \Sentry\Event $event, ?\Sentry\EventHint $hint ): ?\Sentry\Event {
	if ( $hint !== null && $hint->exception instanceof MyException ) {
		return null;
	}
	return $event;
};
```

## 许可证

MIT
