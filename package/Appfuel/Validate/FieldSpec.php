<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate;

use DomainException,
    InvalidArgumentException;
/**
 * This is the base specification that all other specs must extend from. 
 * The field details has been left out because it is to be describe by more
 * specific specifications like UnaryFieldSpec or BinaryFieldSpec
 */
class FieldSpec implements FieldSpecInterface
{
    /**     
     * Name of the field to be validated
     * @var array
     */    
    protected $fields = array();    
    
    /**     
     * Location of the field ex) get, post or a method getter or property     
     * @var string     
     */    
    protected $location = null;

    /**
     * List of filter specifications used by the validator
     * @var string
     */
    protected $filters = array();

    /**
     * Key used to create the validator that will execute this specification
     * @var string
     */
    protected $validator = null;

    /**
     * Key used to create the filter specification
     * @var string
     */
    protected $filterSpec = null;

    /**
     * @param   array   $data
     * @return  FieldSpec
     */
    public function __construct(array $data)
    {
        if (isset($data['fields']) && is_array($data['fields'])) {
            $this->setFields($data['fields']);
        }
        else if (isset($data['field']) && is_string($data['field'])) {
            $this->setFields(array($data['field']));
        }
        else {
            $err  = "must use -(field) or -(fields) to indicate fields for ";
            $err .= "the validator";
            throw new DomainException($err);
        }

        if (isset($data['location'])) {
            $this->setLocation($data['location']);
        }

        if (isset($data['validator'])) {
            $this->setValidator($data['validator']);
        }

        if (isset($data['filter-spec'])) {
            $this->setFilterSpec($data['filter-spec']);
        }

        if (! isset($data['filters'])) {
            $err  = "must have one or more filters defined with key -(filters)";
            throw new DomainException($err);
        }
        $this->setFilters($data['filters']);
    }

    /**
     * @return  string
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return  string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return  string
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return  string
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return  string
     */
    public function getFilterSpec()
    {
        return $this->filterSpec;
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setFields(array $list)
    {
        foreach ($list as $name) {
            if (! is_string($name) || empty($name)) {
                $err  = "field must be a non empty string";
                throw new DomainException($err);
            }
        }

        $this->fields = $list;
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setLocation($loc)
    {
        if (! is_string($loc)) {
            $err  = "the location of the field must be a string";
            throw new InvalidArgumentException($err);
        }

        $this->location = $loc;
    }

    /**
     * @param   string  $key
     * @return  null
     */
    protected function setFilterSpec($key)
    {
        if (! is_string($key) || empty($key)) {
            $err  = "filter spec key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->filterSpec = $key;
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setFilters(array $list)
    {
        $result = array();
        foreach ($list as $name => $data) {
            $data['name'] = $name;
            $this->filters[] = $this->createFilterSpec($data);
        }
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setValidator($name)
    {
        if (! is_string($name) || empty($name)) {
            $err  = "the name of the validator must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->validator = $name;
    }

    /**
     * @param   array   $data
     * @return  FilterSpec
     */
    protected function createFilterSpec(array $data)
    {
        $key = $this->getFilterSpec();
        return ValidationFactory::createFilterSpec($data, $key);
    }
}
