<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\View;

/**
 * Convert data assignments into a comma separted list
 */
class CsvTemplate extends ViewTemplate
{
    /**
     * @return  string
     */
    public function build()
    {
        return ViewCompositor::composeCsv($this->getAll());
    }
}
