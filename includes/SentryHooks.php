<?php

namespace MediaWiki\Extension\Sentry;

use MediaWiki\MediaWikiServices;
use MediaWiki\Hook\LogExceptionHook;

class SentryHooks implements LogExceptionHook
{
    function __construct()
    {
        $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig('Sentry');
        $options = [
            'dsn' => $config->get('SentryDsn'),
            'sample_rate' => $config->get('SentrySampleRate'),
            'traces_sample_rate' => $config->get('SentryTraceSampleRate'),
            'profiles_sample_rate' => $config->get('SentryProfileSampleRate'),
        ];
        if ($config->get('SentryBeforeSend')) {
            $options['before_send'] = $config->get('SentryBeforeSend');
        }
        \Sentry\init($options);
    }

    public function onLogException($e, $suppressed)
    {
        // 被 @、AtEase 或 error_reporting 屏蔽的错误，MediaWiki 只以 DEBUG 级别记到 silenced-error 频道，这里同样不上报
        if ($suppressed) {
            return;
        }
        \Sentry\captureException($e);
    }
}
