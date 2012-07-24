<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Cache;

use OutOfRangeException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\Filesystem\FileHandler,
    Appfuel\Filesystem\FileHandlerInterface;

/**
 */
class CodeCacheLoader implements CodeCacheLoaderInterface
{
    /**
     * List of core classes to cache 
     * @var array
     */
    static protected $classes = array(
        'Appfuel\\DataStructure\\ArrayData',
        'Appfuel\\Filesystem\\FileFinder',
        'Appfuel\\Filesystem\\FileHandler',
        'Appfuel\\Kernel\\FaultHandler',
        'Appfuel\\Kernel\\AppInitializer',
        'Appfuel\\Http\\HttpRequest',
        'Appfuel\\Http\\HttpResponse',
        'Appfuel\\Http\\HttpHeaderList',
        'Appfuel\\Http\\HttpStatus',
    );

    /**
     * @var FileHandler
     */
    protected $fileHandler = null;

    /**
     * @param   FileHandlerInterface    $fileHandler
     * @return  CodeCacheLoader 
     */
    public function __construct(FileHandlerInterface $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

    /**
     * @return  array
     */
    public static function getFrameworkClasses()
    {
        return self::$classes;
    }

    /**
     * @param   array   $classes
     * @return  string
     */
    public function generate(array $classes, array $exclude = array())
    {
        $fHandler = $this->getFileHandler();
        $content = CodeCacheHandler::generate($classes, $exclude, $fHandler);
        return '<?php ' . $content; 
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function write($path, $content)
    {
        $fHandler = $this->getFileHandler();
        $fHandler->disableExceptionsOnFailure();

        return $fHandler->write($path, $content);
    }
}
