<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate\Filter;

/**
 * Validate floating point numbers with native php filter_var function
 */
class FloatFilter extends ValidationFilter
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

        $decimal = $this->getOption('decimal-sep', false);
        if (is_string($decimal) && ! empty($decimal)) {
            $options['options']['decimal'] = $decimal;
        }

        /*
         * allow the use of thousand marker
         */
        if ($this->getOption('allow-thousands', false)) {
            $options['flags'] = FILTER_FLAG_ALLOW_THOUSAND;
        }

        /* will bitwise or the flag if it exists */
        if ($this->getOption('allow-fractions', false)) {
            if (! isset($options['flags'])) {
                $options['flags'] = FILTER_FLAG_ALLOW_FRACTION;
            }
            else {
                $options['flags'] |= FILTER_FLAG_ALLOW_FRACTION;
            }
        }
        
        $result = filter_var($raw, FILTER_VALIDATE_FLOAT, $options);
        if (false === $result) {
            $result = $this->getFailureToken();
        }

        return $result;
    }
}
