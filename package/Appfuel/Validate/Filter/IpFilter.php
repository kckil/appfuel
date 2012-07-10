<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate\Filter;

/**
 * Filters ip values: 
 */
class IpFilter extends ValidationFilter
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
        
        if ($this->getOption('ipv4', false)) {
            $options['flags'] = FILTER_FLAG_IPV4;
        }

        if ($this->getOption('ipv6', false)) {
            $options['flags'] = FILTER_FLAG_IPV6;
        }

        if ($this->getOption('no-private-ranges', false)) {
            $options['flags'] = FILTER_FLAG_NO_PRIV_RANGE;
        }

        if ($this->getOption('no-reserved-ranges', false)) {
            $options['flags'] = FILTER_FLAG_NO_RES_RANGE;
        }

        $result = filter_var($raw, FILTER_VALIDATE_IP, $options);

        if (! $result) {
            $result = $this->getFailureToken();
        }

        return $result;
    }
}
