<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    Appfuel\Kernel\Mvc\MvcContextInterface;

class WebHandler extends AppHandler implements WebHandlerInterface
{
    /**
     * @return string
     */
    public function createRequestUri()
    {
        if (! isset($_SERVER['REQUEST_URI'])) {
            $err = "The request uri has not be set in the server super global";
            throw new LogicException($err);
        }

        $uri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        if (false === $uri) {
            $err = "The uri given is invalid";
            throw new DomainException($err);
        }

        return urldecode($uri);
    }

    /**
     * @param   MvcRouteDetailInterface $route
     * @param   MvcContextInterface $context
     * @param   bool    $isHttp
     * @return  null
     */
    public function outputHttpContext(MvcRouteDetailInterface $route, 
                                      MvcContextInterface $context,
                                      $version = '1.1')
    {
        $content = $this->composeView($route, $context);
        $status  = $context->getExitCode();
        $headers = $context->get('http-headers', null); 
        if (! is_array($headers) || empty($headers)) {
                $headers = null;
        }
        $factory = $this->getAppFactory();
        $response = $factory->createHttpResponse(
            $content, 
            $status, 
            $version, 
            $headers
        );

        $output = $factory->createHttpOutput();
        $output->render($response);
    }
}
