<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
$ctrl['app-type'] = 'web';
$handler = require realpath(dirname(__FILE__) . '/../app/app-header.php');

$uri = $handler->createRequestUri();
$key = $handler->resolveRouteGroup($uri);
echo "<pre>", print_r($key, 1), "</pre>";exit;
$context = $handler->createWebContext($key);

$handler->runStartupTasks($key, $context);

$context = $handler->runAction($context);
$content =(string) $context->getView();

$headers = $context->get('http-headers', array());
if (! is_array($headers) || empty($headers)) {
    $headers = null;
}
$handler->outputHttp($content, $headers);
exit($context->getExitCode());
