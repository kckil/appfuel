<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use Appfuel\Filesystem\FileHandlerInterface,
    Symfony\Component\Yaml\ParseException,
    Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Turns an application yaml file into a php array after merging the data
 * data from the files that the settings file imports.
 */
class SettingsHandler implements SettingsHandlerInterface
{
    /**
     * @var FileHandlerInterface
     */
    protected $fileHandler = null;

    /**
     * @var YamlParser
     */
    protected $parser = null;

    /**
     * @var string
     */
    protected $error = null;

    /**
     * @param   FileHandlerInterface $fileHandler
     * @return  SettingsResolver
     */
    public function __construct(FileHandlerInterface $fileHandler, 
                                YamlParser $parser)
    {
        $this->fileHandler = $fileHandler;
        $this->parser = $parser;
    }

    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

    /**
     * @return  YamlParser
     */
    public function getYamlParser()
    {
        return $this->parser;
    }

    /**
     * @param   string  
     * @return  array
     */
    public function resolve($file)
    {
        $fileHandler = $this->getFileHandler();
        $fileHandler->disableExceptionsOnFailure();

        $content = $fileHandler->read($file);
        if (! $content) {
            $this->setError("could not read settings file at -($file)");
            return false;
        }

        $parser = $this->getYamlParser();
        try {
            $data = $parser->parse($content);
        } catch (ParseException $e) {
            $this->setError("Unable to parse YAML string {$e->getMessage()}");
            return false;
        }

        $dirPath = $fileHandler->getDirPath($file);
        if (isset($data['import'])) {
            $import = $data['import'];
            if (is_string($import)) {
                $import = array($import);
            }
            else if (! is_array($import)) {
                $err = "import setting must be a string or an array of strings";
                $this->setError($msg);
                return false;
            }

            unset($data['import']);

            $result = array();
            foreach ($import as $importFile) {
                if (! is_string($importFile) || empty($importFile)) {
                    $err = "import file must be a non empty string";
                    $this->setError($err);
                    return false;
                }
                $full = "$dirPath/$importFile";
                $importContent = $fileHandler->read($full);
                if (! $importContent) {
                    $err = "could not read settings file at -($importFile)";
                    $this->setError($err);
                    return false;
                }
                
                try {
                    $importData = $parser->parse($importContent);
                } catch (ParseException $e) {
                    $err = "Unable to parse YAML string {$e->getMessage()}";
                    $this->setError($err);
                    return false;
                }
                $result = array_replace_recursive($importData, $result, $data);
            }

            $data = $result;
        }

        return $data;
    }

    /**
     * @param   array   $data
     * @return  string
     */
    public function convert(array $data)
    {
        return "<?php \n /* appfuel generated config file */ \n return " .
               $this->printArray($data) . "\n"; 
    }

    /**
     * @param   array   $data
     * @return  string
     */
    public function printArray(array $data)
    {
        return "array(\n {$this->printArrayBody($data)}\n);";
    }

    /**
     * @param   array   $data
     * @return  string
     */
    public function printArrayBody(array $data, $level = 0)
    {
        $tab = str_repeat("\t", $level);
        $body = '';
        foreach ($data as $key => $value) {
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

    /**
     * @param   string  $content
     * @param   string  $file
     * @return  bool
     */
    public function write($file, $content)
    {
        $fileHandler = $this->getFileHandler();
        if (! $fileHandler->write($file, $content)) {
            return false;
        }

        return true;
    }

    /**
     * @return  string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return  bool
     */
    public function isError()
    {
        return is_string($this->error) && ! empty($this->error);
    }

    /**
     * @param   string  $msg
     * @return  SettingsHandler
     */
    protected function setError($msg)
    {
        $this->error = $msg;
        return $this;
    }
}
