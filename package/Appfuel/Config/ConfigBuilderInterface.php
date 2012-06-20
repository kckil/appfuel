<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * For complete copywrite and license details see the LICENSE file distributed   
 * with this source code.                                                        
 */
namespace Appfuel\Config;

/**
 * Build a config file from merging two enviroment specific config files 
 * togather
 */
interface ConfigBuilderInterface
{

	/**
	 * @return	string
	 */
	public function getMergeEnv();

	/**
	 * @return string
	 */
	public function getCurrentEnv();

	/**
	 * @param	string	$char
	 * @return	ConfigBuilder
	 */
	public function setCurrentEnv($env);

	/**
	 * @return	FileFinderInterface
	 */
	public function getFileFinder();

	/**
	 * @return	FileReaderInterface
	 */
	public function getFileReader();

	/**
	 * @return	FileWriterInterface
	 */
	public function getFileWriter();

	/**
	 * @throws	RunTimeException
	 * @return	array
	 */
	public function getCurrentEnvData();

	/**
	 * @throws	RunTimeException
	 * @return	array
	 */
	public function getProductionData();

	/**
	 * @return	array
	 */
	public function mergeConfigurations();

	/**
	 * @return	string
	 */
	public function generateConfigFile();

	/**
	 * @param	array	$array
	 * @return	string
	 */
	public function printArray(array $array);

	/**
	 * @param	array	$array
	 * @param	int		$level
	 * @return	string
	 */	
	public function printArrayBody(array $array, $level = 0);
}
