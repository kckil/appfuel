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
namespace Appfuel\View;

use SplFileInfo,
	Appfuel\Framework\Exception,
	Appfuel\Framework\View\ScopeInterface,
	Appfuel\Framework\View\TemplateInterface,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\File\FrameworkFileInterface;

/**
 * A view template is associated with one and only one template file. The 
 * template files are located in the clientside directory and the Clientside
 * File object is used to abstract that way so you only to worry about the
 * the relative path from your namespace to the template file. The template
 * assigns values into scope (template file visiblity through $this) and 
 * builds the template file into a string.
 */
class Template extends Dictionary implements TemplateInterface
{
	/**
	 * A file or object or string that represents the path to a file
	 * @var array
	 */
	protected $file = null;

	/**
	 * Used to bind the file to the data in scope
	 * @var	Scope
	 */
	protected $scope = null;

	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct($file = null, $data = null)
	{
		if ($file !== null) {
			$this->setFile($file);
		}

		if ($data !== null) {
			if (! $data instanceof ScopeInterface) {
				$data = new Scope($data);
			}

			$this->setScope($data);
		}
	}

	/**
	 * Alias for add. Will add a name/value pair into this templates dictionary
	 *
	 * @param	scalar	$name
	 * @param	mixed	$value
	 * @return	Template
	 */
	public function assign($name, $value)
	{
		$this->add($name, $value);
		return $this;
	}

	/**
	 * Determines if a file has been set 
	 * @return bool
	 */
    public function fileExists()
	{
		return $this->file instanceof FrameworkFileInterface ||
			   $this->file instanceof SplFileInfo;
	}

	/**
	 * Store a path string for file interface in an associative array that
	 * can be retrieved by the key
	 *
	 * @param	scalar	$key
	 * @param	string	$path
	 * @return	Template
	 */
    public function setFile($path, $isViewFile = true)
	{
		$err = "setFile failed: path";
		if (empty($path)) {
			throw new Exception("$err can not be empty");
		}
		
		/* we can assign directly because it is already the required object */
		if ($path instanceof FrameworkFileInterface || 
			$path instanceof SplFileInfo) {
			$this->file = $path;
			return $this;
		}

		/* from this point it can only be a string which will convert into
		 * a file object that the build expects 
		 */
		if (! is_string($path)) {
			$err .= " must of one of the following";
			$err .= " (string|FrameworkFileInterface|SplFileInfo)";
			throw new Exception($err); 
		} 
		
		$this->file = $this->createViewFile($path, $isViewFile);
		return $this;
	}

	/**
	 * @return false | File
	 */
    public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param	string	$path
	 * @return	ClientsideFile
	 */
	public function createViewFile($path, $isViewFile = true)
	{
		if (true === $isViewFile) {
			$file = new ViewFile($path);
		}
		else {
			$file = new FrameworkFile($path);
		}
		
		return $file;
	}

	/**
	 * @return ScopeInterface
	 */
	public function getScope()
	{
		return $this->scope;
	}

	/**
	 * @param	ScopeInterface $scope
	 * @return	FileTemplate
	 */
	public function setScope(ScopeInterface $scope)
	{
		$this->scope = $scope;
		return $this;
	}

	/**
	 * @param	array	$data 
	 * @return	Scope
	 */
	public function createScope(array $data = array())
	{
		return new Scope($data);
	}

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function build(array $data = array(), $isPrivate = false)
	{
		$err = "Template::build failed: ";
		if (! $this->fileExists()) {
			$err .= "file does not exist path is empty";
			throw new Exception($err);
		}

		$file = $this->getFile();
		if (! $file->isFile()) {
			$path = $file->getFullPath();
			$err .= "file does not exist at $path";
			throw new Exception($err);
		}
			
		$isPrivate = (bool) $isPrivate;
	
		/*
		 * When private use only data in the second parameter. 
		 * When not private and data in second parameter then merge
		 * When not private and no data then use only data in dictionary
		 */
		if (true === $isPrivate) {
			$scopeData = $data;
		}
		else if (! empty($data)) {
			$scopeData = array_merge($this->getAll(), $data);
		}
		else {
			$scopeData = $this->getAll();
		}
	
		$scope = $this->getScope();
		if (! $scope instanceof ScopeInterface) {
			$scope = $this->createScope($scopeData);
		}
		else {
			$scope->load($scopeData);
		}
	
		return $scope->build($file->getRealPath());
	}
}
