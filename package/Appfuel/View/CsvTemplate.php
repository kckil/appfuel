<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
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
