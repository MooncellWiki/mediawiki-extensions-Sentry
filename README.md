[sentry doc](https://docs.sentry.io/platforms/php/configuration/)
[about before send](https://docs.sentry.io/platforms/php/configuration/filtering/)
```
wfLoadExtension('Sentry');
$wgSentryDsn = 'https://examplePublicKey@o0.ingest.sentry.io/0';
$wgSentrySampleRate = 0.1;
$wgSentryBeforeSend = function($event, $hint) {
        // Ignore the event if the original exception is an instance of MyException
        if ($hint !== null && $hint->exception instanceof MyException) {
          return null;
        }
        return $event;
};
```
