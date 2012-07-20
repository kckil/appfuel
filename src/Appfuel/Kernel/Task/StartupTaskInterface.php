<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use Appfuel\Kernel\Mvc\MvcContextInterface;

interface StartupTaskInterface
{
    /**
     * @param   array | ArrayData   $data
     * @return  StartupTaskInterface
     */
    public function __construct($data = null);

    /**                                                                          
     * @param   array   $data                                                    
     * @param   MvcContextInterface $context                                     
     * @return  bool                                                             
     */                                                                          
    public function executeTask(array $data = null,
                                MvcContextInterface $context = null);
    /**
     * @param   array   $params
     * @param   MvcContextInterface $context 
     * @return  bool
     */
    public function execute();
   
    /**
     * @return  array
     */
    public function getRegistryKeys();

    /**
     * @param   array   $keys
     * @return  StartupTaskInterface
     */
    public function setRegistryKeys(array $keys);

    /**
     * @param   array | ArrayDataInterface  $data
     * @return  StartupTaskInterface
     */
    public function setParamData($data);

    /**
     * @return ArrayDataInterface
     */
    public function getParamData();
    
    /**
     * @return  MvcContextInterface
     */
    public function getContext();

    /**
     * @param   MvcContextInterface $context
     * @return  StartupTaskInterface
     */
    public function setContext(MvcContextInterface $context);

    /**
     * @return  bool
     */
    public function isContext();
}
