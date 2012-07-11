<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use Exception,
    RunTimeException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileWriter,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileReaderInterface,
    Appfuel\Filesystem\FileWriterInterface;

/**
 * Build javascript and css into concatenated files. Also move theme packages
 * to their build locations
 */
class ResourceTreeDeployer
{
    /**
     * @var string
     */
    protected $error = null;

    /**
     * @var string
     */
    protected $status = null;

    /**
     * @param    MvcContextInterface $context
     * @return    null
     */
    public function deploy()
    {
        if (! ResourceTreeManager::isTree()) {
            ResourceTreeManager::loadTree();
        }

         $finder = new FileFinder('resource');
        $writer = new FileWriter($finder);
        $reader = new FileReader($finder);
        if (! $writer->deleteTree('build', true)) {
            $err = "could not delete -({$finder->getPath('build')})";
            $this->setError($err);
            return false;
        }

         $list = ResourceTreeManager::getAllPageNames();
        if (! is_array($list) || empty($list)) {
            $this->setStatus("no pages in the tree to build");
            return true;
        }

        $status = '';
        foreach ($list as $pageName) {
            $status .= $pageName->getName() . ' ';

            try {
                $pkg = ResourceTreeManager::getPkg($pageName);
            } catch (Exception $e) {
                $this->setError($e->getMessage());
                return false;
            }

            $layers = $pkg->getLayers(); 
            $pageStack = new FileStack();
            foreach ($layers as $layerName) {
                $stack = new FileStack();
                try {
                    $layer = ResourceTreeManager::loadLayer($layerName, $stack);
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                    return false;
                }                

                if (! $this->buildLayer($layer, $reader, $writer, $pageStack)) {
                    return false;
                }
            }

            if (! $this->buildLayer(
                    ResourceTreeManager::createPageLayer($pageName),
                    $reader,
                    $writer,
                    $pageStack)) {
                return false;
            }

            $themeName = $pkg->getThemeName();
            if ($themeName) {
                if (!$this->buildTheme($themeName,$reader,$writer,$pageStack)) {
                    return false;
                }
            }
        }

        $this->setStatus("the following pages have been built: $status");
        return true;
    }

    /**
     * @return    string | null when not set
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return    string | null when not set
     */
    public function getError()
    {
        return $this->error;
    }
 
    /**
     * @param    ResourceLayerInterface $layer
     * @return    int
     */
    protected function buildLayer(ResourceLayerInterface $layer, 
                                  FileReaderInterface $reader,
                                  FileWriterInterface $writer,
                                  FileStackInterface $pageStack)
    {
        $stack        = $layer->getFileStack();
        $vendor          = $layer->getVendor(); 
        $buildDir     = $layer->getBuildDir();
        $buildFile    = $layer->getBuildFile();
        $jsBuildFile  = "$buildFile.js";
        $cssBuildFile = "$buildFile.css";
        $finder       = $reader->getFileFinder();
    
        if (! $finder->isDir($buildDir)) {
            if (! $writer->mkdir($buildDir, 0755, true)) {
                $path = $finder->getPath($buildDir);
                $err = "could not create dir at -({$path})";
                $this->setError($err);
                return false;
            }
        }

        if ($layer->isJs()) {
            $list   = $pageStack->diff('js', $layer->getAllJsSourcePaths());
            $result = $this->makeString('js', $list, $reader, $pageStack);    
            $writer->putContent($result, $jsBuildFile);
        }

        if ($layer->isCss()) {
            $list   = $pageStack->diff('css', $layer->getAllCssSourcePaths());
            $result = $this->makeString('css', $list, $reader, $pageStack);
            $writer->putContent($result, $cssBuildFile);
        }

        return true;
    }

    /**
     * @param    ThemePkgInterface $theme
     * @return    null
     */
    protected function buildTheme(PkgNameInterface $themePkgName,
                                  FileReaderInterface $reader,
                                  FileWriterInterface $writer,
                                  FileStackInterface $pageStack)
    {
        $themeName  = $themePkgName->getName();
        $vendorName = $themePkgName->getVendor();
        $vendor     = ResourceTreeManager::loadVendor($vendorName);
        $version    = $vendor->getVersion();
        $pkgPath    = $vendor->getPackagePath();
        $buildDir   = "build/$vendorName/$version";
        $themeDir   = "$buildDir/theme/$themeName/css";
        $pkg        = ResourceTreeManager::getPkg($themePkgName);
        $finder     = $reader->getFileFinder();

        if (! $finder->isDir($themeDir)) {
            if (! $writer->mkdir($themeDir, 0755, true)) {
                $path = $finder->getPath($themeDir);
                $err = "could not create dir at -({$path})";
                $this->setError($err);
                return false;
            }
        }

        if ($pkg->isCssFiles()) {
            $list = $pkg->getCssFiles($pkgPath);
            $cssFilename = "$themeDir/$themeName.css";
            $result = $this->makeString('css', $list, $reader, $pageStack);
            if (false === $result) {
                $this->setError("could not concatenate $cssFilename");
                return false;
            }
            $writer->putContent($result, "$cssFilename");
        }

        if ($pkg->isAssetFiles()) {
            $list = $pkg->getAssetFiles();
            $assetBuildDir = "$buildDir/{$pkg->getAssetDir()}";
            if (! $finder->isDir($assetBuildDir)) {
                if (! $writer->mkdir($assetBuildDir, 0755, true)) {
                    $path = $finder->getPath($assetBuildDir);
                    $err = "could not create dir at -({$path})";
                    $this->setError($err);
                    return false;
                }
            }

            foreach ($list as $file) {
                $themeDir = "$pkgPath/{$pkg->getPath()}";
                $src  = "$pkgPath/$file";
                $dest = "$buildDir/$file";
                $result = $writer->copy($src, $dest);
            }
        }

        return true;
    }


    /**
     * @param    string    $type
     * @param    array    $list
     * @param    FileReaderInterface    $reader
     * @param    FileStackInterface    $pageStack
     * @return    string
     */
    protected function makeString($type,
                                  array $list,
                                  FileReaderInterface $reader, 
                                  FileStackInterface $pageStack)
    {

        if ('css' !== $type && 'js' !== $type) {
            $err = 'can only convert js or css files';
            $this->setError($err);
            return false;
        }

        $content = new ContentStack();
        foreach ($list as $file) {
            $text = $reader->getContent($file);
            if (false === $text) {
                $err = "could not read contents of file -($file)";
                $this->setError($err);
                return false;
            }
                
            $content->add($text);
            $pageStack->add($type, $file);
        }

        $result = '';
        if ($content->count() > 0) {
            foreach ($content as $data) {
                $result .= $data . PHP_EOL;
            }
        }

        return $result;
    }
    
    /**
     * @param    string    $err
     * @return    null
     */
    protected function setError($msg)
    {
        $this->error = $msg;
    }

    /**
     * @param    string    $msg
     * @return    null
     */
    protected function setStatus($msg)
    {
        $this->status = $msg;
    }
}
