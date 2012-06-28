<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException;

/**
 * Used when you want a more readable interface for setting php error level
 */
class PHPErrorTask extends StartupTask 
{
    /**
     * @var array
     */
    protected $keys = array(
        'php-display-errors',
        'php-error-level'
    );

    /**
     * @return  bool
     */
    public function execute(array $params = null)
    {
        $isset  = 0; 
        $params = $this->getParamData();
        if ($params->exists('php-display-errors')) {
            $display = $params->get('php-display-errors');
            if (! in_array($display, array('on', 'off'), true)) {
                $err  = 'config setting for display errors can only be ';
                $err .= '-(on, off)';
                throw new DomainException($err);
            }
        
            ini_set('display_errors', $display);
            $isset++;
        }

        if ($params->exists('php-error-level')) {
            $code = $params->get('php-error-level');
            if (! is_string($code) || empty($code)) {
                $err = 'error level must be a non empty string';
                throw new DomainException($err);
            }
            $errorLevel = new PHPErrorLevel();
            $errorLevel->setLevel($code);
            $isset++;
        }

        if ($isset > 0) {
            return true;
        }

        return false;
    }
}
