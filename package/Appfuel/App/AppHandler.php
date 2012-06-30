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
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Config\ConfigRegistry,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface,
    Appfuel\Kernel\Mvc\MvcFactoryInterface;

/**
 * 
 */
class AppHandler implements AppHandlerInterface
{
    /**
     * @var FileReaderInterface
     */
    protected $reader = null;

    /**
     * @param   FileReaderInterface $reader 
     * @return  AppHandler
     */
    public function __construct(FileReaderInterface $reader = null)
    {
        if (null === $reader) {
            $reader = new FileReader(new FileFinder());
        }

        $this->reader = $reader;
    }

    /**
     * @return  FileReaderInterface
     */
    public function getFileReader()
    {
        return $this->reader;
    }

    /**
     * @return  AppPathInterface
     */
    public function getAppPath()
    {
        return AppRegistry::getAppPath();
    }
    
    /**
     * @return  AppFactoryInterface
     */
    public function getAppFactory()
    {
        return AppRegistry::getAppFactory();
    }

    public function resolveRoute($uri, $urlFile = null)
    {
        $group = $this->resolveRouteGroup($uri, $urlFile);
        $patterns = RouteRegistry::getPatterns($group);
        echo "<pre>", print_r('insert here', 1), "</pre>";exit;
    }

    /**
     * @param   array    $tasks
     * @return  RouteDetailInterface
     */
    public function resolveRouteGroup($uri, $urlFile = null)
    {
        if (! is_string($uri)) {
            throw new DomainException("uri must be a string");
        }
        $uri = ltrim($uri, '/'); 
        if (null === $urlFile) {
            $urlFile = 'app/url-groups.php';
        }
        $reader = $this->getFileReader();
        $finder = $reader->getFileFinder();
        $groups = array();
        if ($finder->fileExists($urlFile)) {
            $groups = $reader->import($urlFile);
        }
        
        if (! is_array($groups)) {
            $err = "url groups must be an array";
        }
        
        $matches = array();
        $group = null;
        foreach($groups as $pattern => $groupName) {
            if (! is_string($pattern) || empty($pattern)) {
                $err = 'group pattern must be a non empty string';
                throw new DomainException($err);
            }
            
            $isMatched = preg_match($pattern, $uri, $matches);
            if ($isMatched) {
                $group = $groupName;
                break;
            }
            $matches = array();
        }

        if (! $isMatched) {
            return false;
        }

        $matched = array_shift($matches);
        $pos = strpos($uri, $matched) + strlen($matched);
        $newUri = ltrim(substr($uri, $pos), '/');
        return array(
            'original-uri' => $uri,
            'matched'      => $matched,
            'group'        => $group,
            'uri'          => $newUri,
            'captured'     => $matches
        );
    }

    /**
     * @param   string $key
     * @param   AppInputInterface   $input
     * @return  MvcContextInterface
     */
    public function createContext($key, AppInputInterface $input)
    {
        return $this->getAppFactory()
                    ->createContext($key, $input);
    }

    /**
     * @param   MvcRouteDetailInterface    $route
     * @param   MvcContextInterface        $context
     * @return  AppHandler
     */
    public function initializeApp(MvcRouteDetailInterface $route, 
                                  MvcContextInterface $context)
    {
        $handler = $this->loadTaskHandler();
        $handler->kernelRunTasks($route, $context);
        return $this;
    }

    /**
     * @param   MvcRouteDetailInterface    $route
     * @param   MvcContextInterface        $context
     * @param   string                    $format
     * @return  AppRunner
     */
    public function setupView(MvcRouteDetailInterface $route, 
                              MvcContextInterface $context, 
                              $format = null)
    {

        if (empty($format)) {
            $format = $route->getFormat();
        }

        $this->getAppFactory()
             ->createViewBuilder()
             ->setupView($context, $route, $format);

        return $this;
    }

    public function composeView(MvcRouteDetailInterface $route,
                                MvcContextInterface $context)
    {
        if ($route->isViewDisabled()) {
            return '';
        }

        $view = $context->getView();
        if (is_string($view)) {
            $result = $view;
        }
        else if ($view instanceof ViewInterface) {
            $result = $view->build();
        }
        else if (is_callable(array($view, '__toString'))) {
            $result =(string) $view;
        }
        else {
            $err  = "view must be a string or an object the implements ";
            $err .= "Appfuel\View\ViewInterface or an object thtat implemnts ";
            $err .= "__toString";
            throw new DomainException($err);
        }
    
        return $result;
    }

    /**
     * @param    MvcContextInterface        $context
     * @return    AppRunner
     */
    public function runAction(MvcContextInterface $context)
    {
        $context = $this->getAppFactory()
                        ->createFront()
                        ->run($context);

        return $context;
    }

    /**
     * @param    array    $tasks
     * @return    AppRunner
     */
    public function runTasks(array $tasks)
    {
        $this->getTaskHandler()
             ->runTasks($tasks);

        return $this;
    }

    /**
     * @return    TaskHandlerInterface
     */
    public function getTaskHandler()
    {
        return $this->taskHandler;
    }

    /**
     * @param   TaskHandlerInterface $handler
     * @return  AppHandler
     */
    public function setTaskHandler(TaskHandlerInterface $handler)
    {
        $this->taskHandler = $handler;
        return $this;    
    }
}
