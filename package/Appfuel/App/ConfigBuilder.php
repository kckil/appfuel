<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use DomainException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileWriter,
    Appfuel\Filesystem\FileReaderInterface,
    Appfuel\Filesystem\FileWriterInterface;

/**
 */
class ConfigBuilder implements ConfigBuilderInterface
{
    /**
     * Used to read the config file data
     * @var FileReaderInterface
     */
    protected $fileReader = null;

    /**
     * Used to write the final config file
     * @var FileWriterInterface 
     */
    protected $fileWriter = null;

    /**
     * List of environments to merge
     * @var array
     */
    protected $envList = array();

    /**
     * List of config files that will be processed and turned into a single
     * config file
     * @var array
     */
    protected $files = array();

    /**
     * Flag used to determine that only a single config file will be processed
     * @var bool
     */
    protected $isSingleFile = true;

    /**
     * Flag used to determine if a config file has sections to be processed
     * @var bool
     */
    protected $isSections = false;

    /**
     * @param    FileReaderInterface $reader
     * @param    FileWriterInterface $writer
     * @return  ConfigBuilder
     */
    public function __construct(FileReaderInterface $reader = null,
                                FileWriterInterface $writer = null)
    {

        $finder = new FileFinder(null, false);
        if (null === $reader) {
            $reader = new FileReader($finder);
        }
        $this->reader = $reader;

        if (null === $writer) {
            $writer = new FileWriter($finder);
        }
        $this->writer = $writer;
    }

    /**
     * @return  FileReaderInterface
     */
    public function getFileReader()
    {
        return $this->reader;
    }

    /**
     * @return  FileWriterInterface
     */
    public function getFileWriter()
    {
        return $this->writer;
    }

    /**
     * @return  ConfigBuilder
     */
    public function useSingleFile()
    {
        $this->isSingleFile = true;
        return $this;
    }

    /**
     * @return  ConfigBuilder
     */
    public function useMultipleFiles()
    {
        $this->isSingleFile = false;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isSingleFile()
    {
        return $this->isSingleFile;
    }

    /**
     * @return  bool
     */
    public function isSections()
    {
        return $this->isSections;
    }

    /**
     * @return ConfigBuilder
     */
    public function enableSections()
    {
        $this->isSections = true;
        return $this;
    }

    /**
     * @return ConfigBuilder
     */
    public function disableSections()
    {
        $this->isSections = false;
        return $this;
    }

    /**
     * @return  array
     */
    public function getEnvList()
    {
        return $this->envList;
    }

    /**
     * The list can be a single string (one env) or a space separated string
     * (many envs) or and array of envs
     *
     * @param   mixed string | array
     * @return  ConfigBuilder
     */
    public function setEnvList($list)
    {
        if (is_string($list) && ! empty($list)) {
            $list = explode(' ', trim($list));
        }
        else if (! is_array($list)) {
            $err  = "the evn list most be a string (space separated for multi ";
            $err .= "envs) or an array";
            throw new DomainException($err);
        }

        foreach ($list as $env) {
            if (! is_string($env) || empty($env)) {
                $err = "each env must be a non empty string";
                throw new DomainException($err);
            }
        }

        $this->envList = $list;
        return $this;
    }

    /**
     * @return  array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return  array   $files
     * @return  ConfigBuilder
     */
    public function setFiles(array $files)
    {
        foreach ($files as $file) {
            if (! is_string($file) || empty($file)) {
                $err = "path to config file must be a non empty string";
                throw new DomainException($err);
            }
        }

        $this->files = $files;
        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        if ($this->isSingleFile()) {
            $settings = $this->buildSingleFile();
        }
        else {
            $settings = $this->buildMultipleFiles();
        }

        return $settings;    
    }

    /**
     * @param   string  $file
     * @param   array   $envList
     * @return  array
     */
    public function buildSingleFile()
    {
        $files = $this->getFiles();
        if (empty($files)) {
            $err = "must set a file before you can build it";
            throw new DomainException($err);
        }
        $file = current($files);
        $reader = $this->getFileReader();
        if (false !== strpos('.json', $file)) {
            $data = $reader->decodeJsonAt($file);
        }
        else {
            $data = $reader->import($file);
        }

        $envList = $this->getEnvList();
        if (empty($envList)) {
            $err = "must have at least one env specified";
            throw new DomainException($err);
        }

        $settings = array();
        foreach ($envList as $env) {
            if (! isset($data[$env])) {
                $err = "env -($env) could not be found in -($file)";
                throw new DomainException($err);
            }

            $envSettings = $data[$env];
            if (! is_array($envSettings)) {
                $err = "env -($env) config data must be an array ";
                throw new DomainException($err);
            }
            $settings = array_replace_recursive($settings, $envSettings);
        }

        return $settings;
    }

    /**
     * @param   array   $settings
     * @param   string  $file
     * @return  
     */
    public function writeJsonData(array $settings, $file)
    {
        if (! is_string($file) || empty($file)) {
            $err = "path to build file must be a non empty string";
            throw new DomainException($err);
        }

        $data = json_encode($settings);
        $writer = $this->getFileWriter();
        return $writer->putContent($data, $file);
    }

   /**
     * @throws  RunTimeException
     * @return  array
     */
    public function getCurrentEnvData()
    {
        $reader  = $this->getFileReader();
        $env     = $this->getCurrentEnv();
        $envFile = "$env.php";
        
        $isThrow = true;
        return $reader->import($envFile, $isThrow);
    }

    /**
     * @throws  RunTimeException
     * @return  array
     */
    public function getProductionData()
    {
        $reader  = $this->getFileReader();
        
        $isThrow = true;
        return $reader->import('production.php', $isThrow);
    }

    /**
     * @return    array
     */
    public function mergeConfigurations()
    {
        $prod = $this->getProductionData();
        $env  = $this->getCurrentEnvData();
        return array_replace_recursive($prod, $env);
    }

    /**
     * @return    string
     */
    public function generateConfigFile()
    {
        $env = $this->getCurrentEnv();
        if ('production' === $env) {
            $data = $this->getProductionData();
        }
        $data = $this->mergeConfigurations();
        $type = $this->getFileType();
        switch ($type) {
            case 'json':
                $fileName   = 'config.json';
                $content    = $this->processJson($data);
                break;
            case 'php':
                $fileName   = 'config.php';
                $content    = $this->processPhp($data);
                break;
            default:
                throw new RuntimeException(
                    "Unexpected file type found during generate -($type)"
                );
        }

        $this->setFileName($fileName);
        $writer = $this->getFileWriter();
        return $writer->putContent($content, $fileName);
    }

    /**
     * Write the contents of the data array into a file as a php
     * formatted array.
     *
     * @param   array   $data
     * @return  bool
     */
    protected function processPhp(array $data)
    {
        $content = "<?php \n /* generated config file */ \n return ";
        $content .= $this->printArray($data);
        $content .= "\n?>";

        return $content;
    }

    /**
     * Write the contents of the data array into a file as a json formatted
     * object.
     *
     * @param   arary   $data
     * @return  bool
     */
    protected function processJson(array $data)
    {
        return json_encode($data);
    }

    /**
     * @param    array    $array
     * @return    string
     */
    public function printArray(array $array)
    {
        $str  = "array(\n";
        $str .= $this->printArrayBody($array);
        $str .= ");";
        return $str;
    }

    /**
     * @param    array    $array
     * @param    int        $level
     * @return  string
     */    
    public function printArrayBody(array $array, $level = 0)
    {
        $tab = str_repeat("\t", $level);
        $body = '';
        foreach ($array as $key => $value) {
            
            $type = gettype($value);
            switch ($type) {
                case 'object':
                    continue 2;
                    break;
                case 'boolean':
                    $vline = (true ===$value) ? "true" : "false";
                    $vline .= ",\n";
                    break;
                case 'integer':
                case 'double':
                    $vline = "$value,\n";
                    break;
                case 'string':
                    $vline = "'{$value}', \n";
                    break;
                case 'array':
                    $vline = "array(\n" . 
                             $this->printArrayBody($value, $level+1) .
                             "$tab),\n";
                    break;
            }
            $kline = (is_string($key)) ? "{$tab}'{$key}'" : $tab . $key;
            $body .= $kline . ' => ' . $vline;    
        }

        return $body;
    }
}
