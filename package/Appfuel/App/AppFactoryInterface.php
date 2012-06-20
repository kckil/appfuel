<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * For complete copywrite and license details see the LICENSE file distributed   
 * with this source code.                                                        
 */
namespace Appfuel\App;

use RunTimeException,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\MvcDispatcherInterface,
    Appfuel\Kernel\Mvc\InterceptChainInterface;

/**
 * Encapsulates the create of the frameworks most critical objects so that
 * may be easily replaced.
 */
interface AppFactoryInterface
{    

    /**
     * We look for query string first because the path info in the request uri
     * gets lost with rewrite rules.
     * 
     * @return  RequestUri
     */
    public function createUriFromServerSuperGlobal();

    /**
     * @return    RequestUri
     */
    public function createUri($str);

    /**
     * @param    string    $method
     * @param    array    $params
     * @return    AppInput
     */
    public function createInput($method, array $params = array());

    /**
     * @param    RequestUriInterface $uri 
     * @return    AppInput
     */
    public function createRestInputFromBrowser(RequestUriInterface $uri=null);
    
    /**
     * @return    AppInput
     */
    public function createEmptyInput();

    /**
     * @param    string    $key
     * @return    MvcRouteDetailInterface
     */
    public function createRouteDetail($key);

    /**
     * @param    string    $key
     * @param    AppInputInterface $input
     * @param    mixed    $view
     * @return    MvcContext
     */
    public function createContext($key, AppInputInterface $input, $view=null);

    /**
     * @param    string    $key
     * @return    MvcContext
     */
    public function createEmptyContext($key);

    /**
     * @return    MvcViewBuilderInterface
     */
    public function createViewBuilder();

    /**
     * @param    MvcDispatcherInterface $dispatcher
     * @param    InterceptChainInterface $preChain
     * @param    InterceptChainInterface $postChain
     * @return    MvcFront
     */
    public function createFront(MvcDispatcherInterface $dispatcher = null,
                                InterceptChainInterface $preChain  = null,
                                InterceptChainInterface $postChain = null);
    /**
     * @return    MvcDispatcher
     */
    public function createDispatcher();

    /**    
     * @return    TaskHandler
     */
    public function createTaskHandler();
}
