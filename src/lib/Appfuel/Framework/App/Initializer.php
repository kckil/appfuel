<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\App;

use Appfuel\Framework\Autoload\AutoloadInterface as LoaderInterface,
	Appfuel\Stdlib\Filesystem\Manager			 as FileManager,
	Appfuel\Registry,
	Appfuel\AppManager;

/**
 * The Initializer is used to put the framework into a known state for the
 * the following areas: include path, config data, error display, error
 * reporting and class autoloading.
 */
class Initializer implements InitializeInterface
{
	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

    /**
     * Used to map and change the php error display_errors and error_reporting
     * @var Stdlib\Error\PHPError
     */
    protected $phpError = NULL;

    /**
     * Autoloader use is loading classes into memory
     * @var Framework\Autoload\AutoloadInterface
     */
    protected $autoloader = NULL;

	/**
	 * Creates objects necessary to Initialize, Bootstrap, Dispatch, and Output
	 * @var FactoryInterface
	 */
	protected $factory = NULL;

	/**
	 * @param	string	$basePath
	 * @return	Initializer
	 */
	public function __construct($basePath)
	{
		if (! is_string($basePath) || empty($basePath)) {
			throw new \Exception("Base path should be non empty string");
		}
		$this->basePath = $basePath;

	}

    /**
     * Initialize the core dependencies needed to begin initalization.  
     * 1) parse config file into an array of data 
     * 2) initialize the registry and load the config data into it
	 * 3) pull out the app factory class and create it then 
	 *	  assign the PHPError and Autoloader classes 
     * 4) initialize the include path
     * 5) initialize error settings
     * 6) register the autoloader
	 *
     * @param   string  $file	file path to config ini
	 * @return	NULL
     */
	public function initialize($file)
	{
		$this->initRegistryConfig($file);

		$defaultFactory = '\Appfuel\Framework\App\Factory';
		$factoryClass   = Registry::get('app_factory', $defaultFactory);
		$factory = $this->createFactory($factoryClass);

		$this->setFactory($factory);
		$this->setPHPError($factory->createPHPError());

		$autoloader = $factory->createAutoloader();
		$this->setAutoloader($autoloader);

        $paths  = Registry::get('include_path', '');
        $action = Registry::get('include_path_action', 'replace');
        $this->includePath($paths, $action);

        $display = Registry::get('display_error',   'off');
        $level   = Registry::get('error_reporting', 'none');
        $this->errorSettings($display, $level);

		$this->registerAutoloader();
	}

	/**
	 * Convert ini file into an array of data and use that to initialize
	 * The application registry
	 *
	 * @param	string	$file	path to config file
	 * @return	NULL
	 */
	public function initRegistryConfig($file)
	{
		$data = $this->getConfigData($file);
		if (! is_array($data)) {
			$data = array();
		}
		$this->initRegistry($data);	
	}

	/**
	 * Initialize the Appfuel\Registry with or without data
	 *
	 * @param	array	$data
	 * @return	NULL
	 */
	public function initRegistry(array $data = array())
	{
		Registry::init($data);
	}

    /**
     * Parse the ini file given into an associative array
     *
	 * @throw	Exception	when file is not found	
     * @param   string	$configFile		path the ini file
     * @param   bool	$useBase		use base path to resolve absolute path
     * @return  mixed	FALSE|array
     */
	public function getConfigData($file)
	{
        $file = $this->getBasePath() . DIRECTORY_SEPARATOR . $file;
        if (! file_exists($file)) {
            throw new Exception("Could not find config file ($file)");
        }

        return FileManager::parseIni($file);
	}

    /**
     * Initialize the php include path. Handles a single string or an
     * array of strings. The action parameter is used to determine how
     * how to deal with the original include path. should we append, prepend,
     * or replace it
     * 
     * @param   mixed   $paths
     * @param   string  $action     how to deal with the original path
     * @return  NULL    
     */
    public function includePath($paths, $action = 'replace')
    {
        /* a single path was passed in */
        if (is_string($paths) && ! empty($paths)) {
            $pathString = $paths;
        } else if (is_array($paths) && ! empty($paths)) {
            $pathString = implode(PATH_SEPARATOR, $paths);
        } else {
            return FALSE;
        }

        /*
         * The default action is to replace the include path. If
         * action is given with either append or prepend the 
         * paths will be concatenated accordingly
         */
        $includePath = get_include_path();
        if ('append' === $action) {
            $pathString = $includePath . PATH_SEPARATOR . $pathString;
        } else if ('prepend' === $action) {
            $pathString .= PATH_SEPARATOR . $includePath;
        }

        return set_include_path($pathString);
    }

    /**
     * Change the php ini setting for display_errors. Also change the
     * error_reporting
     *
     * @param   array   $errors
     * @return  NULL
     */
    public function errorSettings($display, $level)
    {
        $phpError = $this->getPhpError();
        $phpError->setDisplayStatus($display);
        $phpError->setReportingLevel($level);
        return;
    }

	/**
	 * @return NULL
	 */
	public function registerAutoloader()
	{
		$this->getAutoloader()
			 ->register();
	}

    /**
     * @return  FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return  FactoryInterface
     */
    public function setFactory(FactoryInterface $factory)
    {
		$this->factory = $factory;
        return $this->this;
    }

	/**
	 * Create the app factory by converting namespaces into directory paths
	 * locating the file adding it into memory then instantiating the class.
	 * This is done because it generally happens before the autoloader has
	 * been registered.
	 *
	 * @throws	\Exception	when the class file does not exist
	 * @param	string	classnName
	 * @return	FactoryInterface
	 */
	public function createFactory($className)
	{
		$root = $this->getBasePath() . DIRECTORY_SEPARATOR . 'lib';
		$path = FileManager::classNameToFileName($className);
		$file = $root . DIRECTORY_SEPARATOR . $path;
		if (! file_exists($file)) {
			throw new \Exception("could not find app factory file ($file)");
		}
		require_once $file;

		return new $className();
	}

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

    /**
     * @return  AutoloadInterface
     */
    public function getAutoloader()
    {
        return $this->autoloader;
    }

    /**
     * @return  AutoloadInterface
     */
    public function setAutoloader(AutoloadInterface $autoloader)
    {
		$this->autoloader = $autoloader;
        return $this->this;
    }

    /**
     * @return  PHPErrorInterface
     */
    public function getPHPError()
    {
        return $this->phpError;
    }

	/**
	 * @param	PHPErrorInterface	$error
	 * @return	Initializer
	 */
	public function setPHPError(PHPErrorInterface $error)
	{
		$this->phpError = $error;
		return $this;
	}
}
