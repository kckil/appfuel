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

/*
 * Appfuel works with both pretty urls (using regex) and old style urls where
 * it looks for two keys 1) routekey 2) viewformat. Either route methods will
 * throw an exception when a route can not be found.
 */
$method = $handler->getRequestMethod(); 
if ($handler->isQueryString()) {
    $route = $handler->lookupRouteInQueryString();
}
else {
    $route = $handler->matchRoute($handler->getRequestUri(), $method);
}
$routeKey = $route->getRouteKey();

/*
 * The app view is an object that holds all content, formatting info and 
 * data assignments used to constuct a view 
 */
$view = $handler->createAppView($route);

/*
 * The routing system will capture parameters from the request uri or even
 * duplicate GET params if the old school urls are used. These params need to
 * be added to the app input
 */
$captures = array('route' => $route->getCaptures());
$input = $handler->createWebInput($method, $captures);

/*
 * The context holds all the objects needed by the action controller
 */
$context = $handler->createWebContext($routeKey, $input, $view);

/*
 * Application specific logic applied before the action is dispatched. Note:
 * the routing system has the ability to add, remove or skip startup tasks
 * for any given route
 */
$handler->runStartupTasks($context);

/*
 * Action is dispatched. Note: pre and post filters are applied by the front
 * controller
 */
$context = $handler->runAction($context);

/*
 * Ensure the view is a string
 */
$content = $handler->composeView($context); 

/*
 * Collect any http headers added to the context
 */
$headers = $context->get('http-headers', array());
if (! is_array($headers) || empty($headers)) {
    $headers = null;
}

$handler->outputHttp($content, $context->getExitCode(), $headers);
exit(0);
