<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate;

use InvalidArgumentException,
    Appfuel\Validate\Filter\FilterInterface;
/**
 * Validate that a single field will pass successfully through one or more 
 * filters.
 */
class FieldValidator implements FieldValidatorInterface
{
    /**
     * List of fields to be filtered. These fields are located in the 
     * coordinators raw source
     * @var string
     */
    protected $fields = array();

    /**
     * List of filters and sanitizers to run against the field's value
     * @var array
     */
    protected $filters = array();

    /**
     * @return  string
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param   string  $name
     * @return  FieldValidator
     */
    public function addField($name)
    {
        if (! is_string($name) || empty($name)) {
            $err = "field must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (! in_array($name, $this->fields, true)) {
            $this->fields[] = $name;
        }

        return $this;
    }

    /**
     * @return  FieldValidator
     */
    public function clearFields()
    {
        $this->fields = array();
        return $this;
    }

    /**
     * @return  array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param   FilterInterface $filter
     * @return  FieldValidator
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return  FieldValidator
     */
    public function clearFilters()
    {
        $this->filters = array();
        return $this;
    }

    /**
     * @param   FieldSpecInterface $spec
     * @return  FieldValidator
     */
    public function loadSpec(FieldSpecInterface $spec)
    {
        $fields = $spec->getFields();
        foreach($fields as $field) {
            $this->addField($field);
        }
        
        $filters = $spec->getFilters();
        foreach ($filters as $filterSpec) {
            $filter = ValidationFactory::createFilter($filterSpec->getName());
            $filter->loadSpec($filterSpec);
            $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * Run though each filter and pass the raw data into it, reporting back 
     * any errors to the coordinator. We do not bail when a failure is first
     * detected. Instead we continue to feed the raw data into the next filter 
     * until all filters have run. When no errors have occured we add the 
     * clean data into the coordinator
     *
     * @param   mixed   $raw    data used to validate with filters
     * @return  bool
     */
    public function isValid(CoordinatorInterface $coord)
    {
        $fields = $this->getFields();
    
        $isError = false;
        foreach ($fields as $field) {
            if (! $this->isValidField($field, $coord)) {
                $isError = true;
            }
        }

        $result = true;
        if ($isError) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param   string  $field
     * @param   CoordinatorInterface    $coord
     * @return  bool
     */
    public function isValidField($field, CoordinatorInterface $coord)
    {
        $raw = $coord->getRaw($field);
        if (CoordinatorInterface::FIELD_NOT_FOUND === $raw) {
            $coord->addError("could not find field -($field) in source");
            return false;
        }
        
        $isError = false;
        $filters = $this->getFilters();
        foreach ($filters as $filter) {
            $clean = $filter->filter($raw);
            if ($filter->isFailure($clean)) {
                $coord->addError($filter->getError(), $filter->getErrorCode());
                $isError = true;
                continue;
            }
            
            /* 
             * the newly clean data becomes the raw data for the next filter
             */
            $raw = $clean;
        }

        if ($isError) {
            return false;
        }

        $coord->addClean($field, $clean);
        return true;
    }

    /**
     * @return  null
     */
    public function clear()
    {
        $this->clearField();
        $this->clearFilters();
    }
}
