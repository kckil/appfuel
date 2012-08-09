<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route\Fixtures\Controller;

use Appfuel\Http\HttpResponse;

class MyController
{
    public function execute()
    {
        return new HttpResponse('goodbye world');
    }
}
