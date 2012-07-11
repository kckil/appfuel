<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\View;

use RunTimeException,
    InvalidArgumentException;

/**
 * Deprecated: should not be used. Please use View
 */
class ViewTemplate implements ViewInterface
{
    /**
     * List of other template used by this template
     * @var array
     */
    protected $templates = array();

    /**
     * Strategy used to be build and assign templates
     * @var array
     */
    protected $templateBuild = array();

    /**
     * Holds assignment until build time where they are passed into scope
     * @var array
     */
    protected $assign = array();

    /**
     * @param    mixed    $file 
     * @param    array    $data
     * @return    ViewTemplate
     */
    public function __construct(array $data = null)
    {
        if (null !== $data) {
            $this->load($data);
        }
    }

    /**
     * Determines if template has been added
     *
     * @param   scalar  $key    template identifier
     * @return  bool
     */
    public function isTemplate($key)
    {
        if (! is_string($key)) {
            return false;
        }

        if (isset($this->templates[$key]) &&
            $this->templates[$key] instanceof ViewInterface) {
            return true;
        }

        return false;
    }

    /**
     * @param   scalar              $key
     * @param   TemplateInterface   $template
     * @return  CompositeTemplate
     */
    public function addTemplate($key, ViewInterface $template)
    {
        if (empty($key) || ! is_string($key)) {
            $err = 'addTemplate failed: key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->templates[$key] = $template;
        return $this;
    }

    /**
     * @param   scalar  $key
     * @return  TemplateInterface | false when no template is found
     */
    public function getTemplate($key)
    {
        if (! $this->isTemplate($key)) {
            return false;
        }

        return $this->templates[$key];
    }

    /**
     * @param   scalar  $key    
     * @return  CompositeTemplate
     */
    public function removeTemplate($key)
    {
        if (! $this->isTemplate($key)) {
            return $this;
        }

        unset($this->templates[$key]);
        return $this;
    }

    /**
     * This will add an entry that will tell the build to turn the
     * source template into a string and using the label assign it to 
     * the destination template. When the label is not given it will you 
     * the source key as the assignment label
     *
     * @param    string    $source            template key for source template
     * @param    string  $destination    template key for destination
     * @param    string  $label            assignement label to destination
     * @return    ViewTemplate
     */
    public function assignTemplate($source, $destination = null, $label = null)
    {
        $err = 'failed to assign template: ';
        if (empty($source) || ! is_string($source)) {
            $err .= 'the template key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        if (null === $destination) {
            $destination = 'this';
        }

        if (empty($destination) || ! is_string($destination)) {
            $err = 'destination template key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        /* if no assignment label is given use the source template's key */
        if (null === $label) {
            $label = $source;
        }
    
        if (empty($label) || ! is_string($label)) {
            $err .= 'the assignment label must be a non empty string';
            throw new InvalidArgumentException($err);
        }


        $this->templateBuild[$source] = array($destination, $label);
        return $this;
    }

    /**
     * @return    array
     */
    public function getTemplateAssignments()
    {
        return $this->templateBuild;
    }

    /**
     * @return    int
     */
    public function templateCount()
    {
        return count($this->templates);
    }

    /**
     * @return    int
     */
    public function assignCount()
    {
        return count($this->assign);
    }

    /**
     * @param    array    $data
     * @return    ViewTemplate
     */
    public function setAssignments(array $data)
    {
        $this->clear();
        $this->load($data);
        return $this;
    }

    /**
     * @return    array
     */
    public function getAssignments()
    {
        return $this->getAll();
    }

    /**
     * @return    ViewTemplate
     */
    public function clear()
    {
        $this->assign = array();
    }

    /**
     * @param    array    $data
     * @return    ViewTemplate
     */
    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        return $this;
    }

    /**
     * Assign key value pair into the template. This assignment will not reach
     * the templates scope until the build method has been used to convert it
     * into a string. IsDeep only applies to composite templates not leaves 
     * which searches templates in templates and assigns the last one the
     * key value
     *
     * @param    string    $key
     * @param    mixed    $value
     * @return    ViewTemplate
     */
    public function assign($key, $value)
    {
        if (! is_scalar($key)) {
            $err = "Template assignment keys must be scalar ";
            throw new InvalidArgumentException($err);
        }

        if (false !== strpos($key, '.')) {
            return $this->assignTo($key, $value);
        }

        $this->assign[$key] = $value;
        return $this;
    }

    /**
     * @param    string    $key
     * @param    array    $value 
     * @return    ViewTemplate
     */
    public function assignMerge($key, array $value)
    {
        $target = $this->get($key, null);
        if (empty($target) || ! is_array($target)) {
            return $this->assign($key, $value);
        }

        return $this->assign($key, array_replace_recursive($target, $value));
    }

    /**
     * Assign a key=>value pair to one of this templates template. When is
     * deep is true then the key will be exploded on '.' and the last element
     * treated as the key all other elements are treated as template key and
     * traversed through the template graph to the last template.
     *
     * @param    string    $key
     * @param    mixed    $value
     * @param    bool    $isDeep
     * @return    ViewTemplate
     */
    public function assignTo($key, $value)
    {
        $err = 'assignTo failed: ';
        if (empty($key) && ! is_string($key)) {
            $err .= 'assignTo failed: key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        if (false === strpos($key, '.')) {
            $template = $this->getTemplate($key);
            if (! ($template instanceof ViewInterface)) {
                $err .= "template not found at -($key)";
                throw new RunTimeException($err);
            }
            $template->assign($key, $value);
            return $this;
        }

        $parts = explode('.', $key);
        if (empty($parts)) {
            $err .= "no templates found for -($key)";
            throw new RunTimeException($err);
        }

        $label = array_pop($parts);
        $template = $this->traverseTemplates($parts);
        if (! ($template instanceof ViewInterface)) {
            $err .= "no template found at -($template)";
            throw new RunTimeException($err);
        }

        $template->assign($label, $value);
        return $this;
    }

    /**
     * @param    string    $key
     * @return    mixed | default on failure
     */
    public function get($key, $default = null)
    {    
        if (empty($key) || ! is_string($key)) {
            return $default;
        }

        if (false !== strpos($key, '.')) {
            return $this->getFrom($key, $default);
        }

        if (! $this->isAssigned($key)) {
            return $default;
        }

        return $this->assign[$key];
    }

    /**
     * @param    string
     * @return    bool
     */
    public function isAssigned($key)
    {
        if (empty($key) || ! is_string($key) || ! isset($this->assign[$key])) {
            return false;
        }

        return true;
    }

    /**
     * @param    string    $key
     * @param    mixed    $default
     * @return    null
     */
    public function getFrom($key, $default = null)
    {
        if (empty($key) || ! is_string($key)) {
            return $default;
        }

        if (false === strpos($key, '.')) {
            $template = $this->getTemplate($key);
            if (! ($template instanceof ViewInterface)) {
                return $default;
            }
            return $template->get($key, $default);
        }

        $parts = explode('.', $key);
        if (empty($parts)) {
            $err .= "no templates found for -($key)";
            throw new RunTimeException($err);
        }

        $key = array_pop($parts);
        $template = $this->traverseTemplates($parts);
        if (! ($template instanceof ViewInterface)) {
            return $default;
        }

        return $template->get($key, $default);
    }

    /**
     * @return    array
     */
    public function getAll()
    {
        return $this->assign;
    }

    /**
     * Build the template file indicated by key into string. Use data in
     * the dictionary as scope
     *
     * @param    string    $key    template file identifier
     * @param    array    $data    used for private scope
     * @return    string
     */
    public function build()
    {
        if ($this->templateCount() > 0) {
            $this->buildTemplates();
        }
        
        return ViewCompositor::composeList($this->getAll());
    }

    /**
     * Build template into other templates
     * @return    null
     */
    public function buildTemplates()
    {
        $error  = 'template build failed: ';
        $result = '';
        $assignments = $this->getTemplateAssignments();
        foreach ($assignments as $source => $data) {
            if (! isset($data[0]) || ! isset($data[1])) {
                $error .= "malformed template build -($source)";
                throw new RunTimeException($err);
            }
            $target = $data[0];
            $label  = $data[1];
            
            $sourceTemplate = $this->getTemplate($source);
            if (! $this->isTemplate($sourceTemplate)) {
                $error .= 'source template not found';
                throw new RunTimeException($err);
            }
    
            if (! $this->isTemplate($target)) {
                $error .= 'target template not found';
                throw new RunTimeException($err);
            }

            if ('this' === $target) {
                $this->assign($label, $sourceTemplate->build());
            }
            else {
                $targetTemplate = $this->getTemplate($target);
                $targetTemplate->assign($label, $sourceTemplate->build());
            }
        }
    }

    /**
     * This should not throw any exceptions. Any errors will result is any
     * empty string
     *
     * @return    string
     */
    public function __toString()
    {
        try {
            $result = $this->build();
        } catch (Exception $e) {
            $result = "|<<| EXCEPTION CAUGHT IN VIEW: {$e->getMessage()} |>>|";
        }

        return $result;
    }

    /**
     * Assign the top template to this template then traverse down each
     * template getting the next template until we found the template at
     * the bottom of the tree.
     * 
     * @param    array    $keys
     * @return    ViewTemplateInterface | string of the key not found
     */
    public function traverseTemplates(array $keys) 
    {
        $template = $this;
        foreach ($keys as $templateKey) {
            $template = $template->getTemplate($templateKey);
            if (! ($template instanceof ViewInterface)) {
                return $templateKey;
            }
        }

        return $template;
    }
}
