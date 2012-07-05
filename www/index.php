<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
$header = realpath(dirname(__FILE__) . '/../app/app-header.php');
if (! file_exists($header)) {
    $err = "appfuel's app-header script is required but not found -($header)";
    throw new LogicException($err);
}

$ctrl['app-type'] = 'web';
$handler = require $header;

$uri = $handler->getRequestUri();
$method = $handler->getRequestMethod(); 
$route = $handler->findRoute($uri, $method);
if (! $handler->isMatchedRoute($route)) {
    $err = "your request to this application could not be resolved for $uri";
    throw new DomainException($err, 404);
}
$context = $handler->createWebContext($route, $method);

$handler->runStartupTasks($context);
$context = $handler->runAction($context);
$content = $handler->composeView($context); 
$headers = $context->get('http-headers', array());
if (! is_array($headers) || empty($headers)) {
    $headers = null;
}

$status = $context->getExitCode();
$handler->outputHttp($content, $status, $headers);
exit(0);
