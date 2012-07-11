<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate\Filter;

class EmailFilter extends ValidationFilter
{
    /**
     * @param   mixed   $raw
     * @return  mixed 
     */    
    public function filter($raw)
    {
        $options = array('options' => array());
        if ($this->isDefault()) {
            $options['options']['default'] = $this->getDefault();
        }
        
        $result = filter_var($raw, FILTER_VALIDATE_EMAIL, $options);

        if (! $result) {
            $result = $this->getFailure();
        }

        return $result;
    }
}
