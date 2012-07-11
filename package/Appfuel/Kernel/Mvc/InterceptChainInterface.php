<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Mvc;

interface InterceptChainInterface
{
    /**
     * @return  array
     */
    public function getFilters();

    /**
     * @param   array   $filters
     * @return  InterceptChain
     */
    public function setFilters(array $filters);

    /**
     * @param   InterceptFilterInterface
     * @return  InterceptChain
     */
    public function addFilter(InterceptFilterInterface $filter);

    /**
     * @param   array   $filters
     * @return  InterceptChain
     */
    public function loadFilters(array $filters);

    /**
     * @return  InterceptChain
     */
    public function clearFilters();

    /**
     * @return bool
     */
    public function isFilters();

    /**
     * @param   MvcContextInterface  $context
     * @return  MvcContextInterface
     */
    public function applyFilters(MvcContextInterface $context);
}
