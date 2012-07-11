<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html;

use LogicException,
    RunTimeException,
    InvalidArgumentException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Html\Resource\PkgName,
    Appfuel\Html\Resource\FileStack,
    Appfuel\Html\Resource\Yui3FileStack,
    Appfuel\Html\Resource\Yui3Manifest,
    Appfuel\Html\Resource\ResourceTreeManager,
    Appfuel\Html\Resource\Yui\Yui3ResourceAdapter,
    Appfuel\Html\Resource\FileStackInterface,
    Appfuel\Html\Resource\AppfuelManifest,
    Appfuel\Html\Resource\AppViewManifest,
    Appfuel\Html\Resource\ResourceTree,
    Appfuel\Html\Tag\GenericTagInterface;

/**
 * Builds and configures an html page using an HtmlPageDetailInterface
 */
class HtmlPageConfiguration implements HtmlPageConfigurationInterface
{
    /**
     * @param    string    
     */
    protected $resourceUrl = '';

    /**
     * @param   bool
     */
    protected $isBuild = true;

    /**
     * @param   string  $url
     * @return  HtmlPageConfiguration
     */
    public function __construct($url = null, $isBuild = true, $default = null)
    {
        if (null !== $url) {
            $this->setResourceUrl($url);
        }
        
        if (! $isBuild) {
            $this->disableBuild();
        }
    }

    /**
     * @return  string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * @param   string  $url
     * @return  HtmlPageConfiguration
     */
    public function setResourceUrl($url)
    {
        if (! is_string($url)) {
            $err = 'resource url must be a string';
            throw new InvalidArgumentException($err);
        }

        $this->resourceUrl = $url;
        return $this;
    }

    /**
     * @return  HtmlPageConfiguration
     */
    public function enableBuild()
    {
        $this->isBuild = true;
    }

    /**
     * @return  HtmlPageConfiguration
     */
    public function disableBuild()
    {
        $this->isBuild = false;
    }

    /**
     * @return  bool
     */
    public function isBuild()
    {
        return $this->isBuild;
    }

    /**
     * @param   string  $pkg    name of the page view to be configured
     * @param   HtmlPageInterface $page
     * @return  null
     */
    public function applyView($pkg, HtmlPageInterface $page)
    {
        if (! is_string($pkg) || empty($pkg)) {
            $err = "view package must be an non empty string";
            throw new InvalidArgumentException($err);
        }
        $page->setViewPkg($pkg);

        $stack = new FileStack();
        if ($this->isBuild()) {
            $htmlPkg = ResourceTreeManager::resolveBuildPage($pkg, $stack);
        } else {
            $htmlPkg = ResourceTreeManager::resolvePage($pkg, $stack);
        }

        $url   = $this->getResourceUrl();
        $js    = $stack->get('js', "$url/resource");

        foreach ($js as $file) {
            $page->addScript($file);
        }

        $css = $stack->get('css', "$url/resource");
        foreach ($css as $file) {
            $page->addCssLink($file);
        }

        $config = $htmlPkg->getHtmlConfig();
        
        $page->setViewPkgName($htmlPkg->getHtmlDocName());
        $this->apply($config, $page);
    }

    /**
     * @param   array   $config
     * @param   HtmlPageInterface $page
     * @return  null
     */
    public function apply(array $config, HtmlPageInterface $page)
    {
        if (isset($config['attrs']) && is_array($config['attrs'])) {
            foreach ($config['attrs'] as $name => $value) {
                $page->addHtmlAttribute($name, $value);
            }
        }

        if (isset($config['head']) && is_array($config['head'])) {
            $this->applyHead($config['head'], $page);
        }

        if (isset($config['body']) && is_array($config['body'])) {
            $this->applyBody($config['body'], $page);
        }
    }

    public function applyHead(array $config, HtmlPageInterface $page)
    {

        if (isset($config['attrs']) && is_array($config['attrs'])) {
            foreach ($config['attrs'] as $name => $value) {
                $page->addHeadAttribute($name, $value);
            }
        }

        if (isset($config['title'])) {
            $this->applyTitle($config['title'], $page);
        }
    
        if (isset($config['base'])) {
            $this->applyBase($config['base'], $page);
        }

        if (isset($config['meta'])) {
            $this->applyMeta($config['meta'], $page);
        }

        if (isset($config['css-links']) && is_array($config['css-links'])) {
            $this->applyCssFiles($config['css-links'], $page);
        }
    }
    
    /**
     * @param   array   $config
     * @param   HtmlPageInterface $page
     * @return  null
     */
    public function applyBody(array $config, HtmlPageInterface $page)
    {
        if (isset($config['attrs']) && is_array($config['attrs'])) {
            foreach ($config['attrs'] as $name => $value) {
                $page->addBodyAttribute($name, $value);
            }
        }

        if (isset($config['js-scripts']) && is_array($config['js-scripts'])) {
            $this->applyJsFiles($config['js-scripts'], $page);
        }
    }

    /**
     * @param   array   $config
     * @param   HtmlPageInterface   $page
     * @return  null
     */
    public function applyTitle($config, HtmlPageInterface $page)
    {
        $sep    = null;
        $text   = '';
        $action = 'replace';
        if (is_string($config)) {
            $text = $config;
        }
        else if (is_array($config)) {
            if (isset($config['sep'])) {
                $sep = $config['sep'];
            }

            if (isset($config['text'])) {
                $text = $config['text'];
            }

            if (isset($config['action'])) {
                $action = $config['action'];
            }
        }

        $title = $page->getHtmlTag()
                      ->getHead()
                      ->getTitle();
        $title->addContent($text, $action);

        if (null !== $sep) {
            $title->setContentSeparator($sep);
        }
    }

    /**
     * @param   mixed   $config
     * @param   HtmlPageInterface $page
     * @return  null
     */
    public function applyBase($config, HtmlPageInterface $page)
    {
        $href = null;
        $target = null;

        if (is_string($config)) {
            $href = $config;
        }
        else if (is_array($config)) {
            if (isset($config['href'])) {
                $href = $config['href'];
            }

            if (isset($config['target'])) {
                $target = $config['target'];
            }
        }

        if (null !== $href || null !== $target) {
            $page->setHeadBase($href, $target);
        }
    }

    /**
     * @param   array   $list
     * @param   HtmlPageInterface $page
     * @return  null
     */
    public function applyMeta(array $list, HtmlPageInterface $page)
    {
        
        foreach ($list as $data) {
            $name    = isset($data['name']) ? $data['name'] : null;
            $content = isset($data['content']) ? $data['content']: null;
            $equiv   = isset($data['http-equiv']) ? $data['http-equiv'] : null;
            $charset = isset($data['charset']) ? $data['charset'] : null;
            $page->addHeadMeta($name, $content, $equiv, $charset);    
        }
        
    }

    /**
     * @param   array   $config
     * @param   HtmlPageInterface
     * @return  null
     */
    public function applyCssFiles(array $data, HtmlPageInterface $page)
    {
        $css  = null;
        $rel  = 'stylesheet';
        $type = 'text/css';
        foreach ($data as $idx => $file) {
            if (is_string($file)) {
                $css = $file;
            }
            else if (is_array($file)) {
                if ($file === array_values($file)) {
                    $css  = current($file);
                    $rel  = next($file);
                    $type = next($file);
                }
                else {
                    if (isset($file['href'])) {
                        $css = $file['href'];
                    }
            
                    if (isset($file['rel'])) {
                        $rel = $file['rel'];
                    }

                    if (isset($file['type'])) {
                        $type = $file['type'];
                    }
                }
            }
            else if ($file instanceof GenericTagInterface) {
                $page->addCssLinkTag($file);
                continue;
            }
            else {
                $err  = 'css file has been set but its format was not ';
                $err .= 'recognized, css file can be a string, indexed array,';
                $err .= 'associative array or an object that implements ';
                $err .= 'Appfuel\View\Html\Tag\GenericTagInterface ';
                $err .= "-($idx)";
                throw new InvalidArgumentException($err);
            }

            if (! is_string($css) || empty($css)) {
                $err  = "can not configure the html page: ";
                $err .= "css file at index -($idx) must be a non empty string";
                throw new InvalidArgumentException($err);
            }

            $page->addCssLink($css, $rel, $type);
        }
    }

    /**
     * @param   mixed   $config
     * @param   HtmlPageInterface $pafge
     * @return  null
     */
    public function applyInlineCss($config, HtmlPageInterface $page)
    {

    }

    /**
     * @param   array   $config
     * @param   HtmlPageInterface
     * @return  null
     */
    public function applyJsFiles(array $data, HtmlPageInterface $page)
    {
        foreach ($data as $index => $file) {
            if (! is_string($file) || empty($file)) {
                $err = "js file at -($index) must be a non empty string";
                throw new InvalidArgumentException($err);
            }

            $page->addScript($file, "text/javascript");
        }
    }

    /**
     * @param   mixed   $config
     * @param   HtmlPageInterface $pafge
     * @return  null
     */
    public function applyInlineJs($config, HtmlPageInterface $page)
    {

    }
}
