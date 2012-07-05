<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace TestFuel\Unit\Regex;

use StdClass,
    Appfuel\Regex\RegexFilter,
    TestFuel\TestCase\BaseTestCase;

class RegexFilterTest extends BaseTestCase
{
    /**
     * @return  RegexFilter
     */
    public function createRegexFilter()
    {
        return new RegexFilter();
    }

    /**
     * @test
     * @return  RegexFilter
     */
    public function interfaceForRegexFilter()
    {
        $filter = $this->createRegexFilter();
        $this->assertInstanceOf('Appfuel\Regex\RegexFilterInterface', $filter);

        return $filter;
    }

    /**
     * @test
     * @depends interfaceForRegexFilter
     * @return  RegexFilter
     */
    public function convertEmptyPattern(RegexFilter $filter)
    {
        $raw = '';
        $result = $filter->convert($raw);
        $this->assertEquals('//', $result);
        
        $result = $filter->convert($raw, 'i');
        $this->assertEquals('//i', $result);

        $result = $filter->convert($raw, 'is');
        $this->assertEquals('//is', $result);
    }

   /**
     * @test
     * @depends interfaceForRegexFilter
     * @return  RegexFilter
     */
    public function convertWithForwardSlash(RegexFilter $filter)
    {
        $raw = '/';
        $result = $filter->convert($raw);
        $this->assertEquals('/\//', $result);

        $raw = '\\/';
        $result = $filter->convert($raw);
        $this->assertEquals('/\\\\\//', $result);
       
        $raw = '^project/folder/file/99';
        $result = $filter->convert($raw);
        echo "\n", print_r('^\\\\\.',1), "\n";exit; 
    }
}
