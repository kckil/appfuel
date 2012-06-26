<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    RunTimeException,
    InvalidArgumentException,
    Appfuel\View\ViewInterface,
    Appfuel\ClassLoader\ManualClassLoader,
    Appfuel\Config\ConfigRegistry,
    Appfuel\Kernel\TaskHandlerInterface,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface,
    Appfuel\Kernel\Mvc\MvcFactoryInterface;

/**
 * 
 */
class WebHandler extends AppHandler implements WebHandlerInterface
{
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
