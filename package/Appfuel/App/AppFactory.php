<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use RunTimeException,
    Appfuel\Http\HttpInput,
    Appfuel\Http\HttpOutput,
    Appfuel\Http\HttpOutputInterface,
    Appfuel\Http\HttpResponse,
    Appfuel\Console\ArgParser,
    Appfuel\Console\ConsoleInput,
    Appfuel\Console\ConsoleOutput,
    Appfuel\Console\ConsoleOutputInterface,
    Appfuel\Kernel\Task\TaskHandler,
    Appfuel\Kernel\Mvc\FrontController,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\InterceptChain,
    Appfuel\Kernel\Mvc\InterceptChainInterface;

/**
 * Create all object required to implement appfuels take on the mvc pattern
 */
class AppFactory implements AppFactoryInterface
{
    /**
     * @param   array   $paths
     * @return  AppPath
     */
    public function createAppPath(array $paths)
    {
        return new AppPath($paths);
    }

    /**
     * @return ConfigHandler
     */    
    public function createConfigHandler()
    {
        return new ConfigHandler();
    }

    /**
     * @return  ConfigBuilder
     */
    public function createConfigBuilder()
    {
        return new ConfigBuilder();
    }

    /**
     * @param   ConsoleOutputInterface  $output
     * @return  ConsoleHandlerInterface
     */ 
    public function createConsoleHandler(ConsoleOutputInterface $output = null)
    {
        return new ConsoleHandler($output);
    }

    /**
     * @param   HttpOutputInterface
     * @return  WebHandlerInterface
     */
    public function createWebHandler(HttpOutputInterface $output = null)
    {
        return new WebHandler($output);
    }

    /**
     * @param   string  $method
     * @param   array   $params
     * @return  HttpInput
     */
    public function createHttpInput($method, array $params = array())
    {
        return new HttpInput($method, $params);
    }

    /**
     * @return  ConsoleInput
     */
    public function createConsoleInput(array $data)
    {
        return new ConsoleInput($data);
    }

    /**
     * @param   array   $spec
     * @return  AppView
     */
    public function createAppView(array $spec = null)
    {
        return new AppView($spec);
    }

    /**
     * @return  CliParser
     */
    public function createCliArgParser()
    {
        return new ArgParser();
    }

    /**
     * @param   string  $key
     * @param   AppInputInterface $input
     * @return  AppContext
     */
    public function createContext($key, $type, $input, $acl = null)
    {
        return new AppContext($key, $type, $input, $acl);
    }

    /**
     * @param   DispatcherInterface $dispatcher
     * @param   InterceptChainInterface $preChain
     * @param   InterceptChainInterface $postChain
     * @return  FrontController
     */
    public function createFrontController(InterceptChainInterface $pre  = null,
                                          InterceptChainInterface $post = null)
    {
        if (null === $pre) {
            $pre = $this->createInterceptChain();
        }

        if (null === $post) {
            $post = $this->createInterceptChain();
        }

        return new FrontController($pre, $post);
    }

    /**
     * @return  InterceptChain
     */
    public function createInterceptChain()
    {
        return new InterceptChain();
    }

    /**    
     * @return    TaskHandler
     */
    public function createTaskHandler()
    {
        return new TaskHandler();
    }

    public function createHttpResponse($data, 
                                       $status,
                                       $version = null,
                                       array $headers = null)
    {
        return new HttpResponse($data, $status, $version, $headers);
    }

    /**
     * @return    HttpOutputInterface
     */
    public function createHttpOutput()
    {
        return new HttpOutput();
    }

    public function createConsoleOutput()
    {
        return new ConsoleOutput();
    }
}
