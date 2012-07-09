<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Unit\Console;

use StdClass,
    Appfuel\Console\ConsoleInput,
    Testfuel\TestCase\BaseTestCase;

class ConsoleInputTest extends BaseTestCase
{
    /**
     * @return  ArgParser
     */
    public function createInput(array $data)
    {
        return new ConsoleInput($data);
    }

    /**
     * @test
     * @return ConsoleInput
     */
    public function emptyConsoleInput()
    {
        $input = $this->createInput(array());
        $interface = 'Appfuel\Console\ConsoleInputInterface';
        $this->assertInstanceOf($interface, $input);

        $this->assertNull($input->getCmd());
        $this->assertNull($input->getCmd(true));
        $this->assertFalse($input->isArgs());
        $this->assertEquals(array(), $input->getArgs());
        $this->assertEquals(array(), $input->getShortOptions());    
        $this->assertEquals(array(), $input->getLongOptions());    
        return $input;
    }

    /**
     * @test
     * @return  ConsoleInput
     */
    public function cmd()
    {
        $cmd = './my-cmd';
        $input = $this->createInput(array('cmd' => $cmd));
        $this->assertEquals($cmd, $input->getCmd());
    }

    /**
     * @test
     * @return  ConsoleInput
     */
    public function args()
    {
        $args = array('arg1', 'arg2', 'arg3');
        $input = $this->createInput(array('args' => $args));
        $this->assertTrue($input->isArgs());
        $this->assertEquals($args, $input->getArgs());
        foreach ($args as $index => $value) {
            $this->assertEquals($value, $input->getArg($index));

            /* index can be a numeric string */
            $this->assertEquals($value, $input->getArg((string)$index));
        }
       
        $this->assertNull($input->getArg(99));
        $this->assertEquals('custom', $input->getArg(99, 'custom'));

        return $input;
    }

    /**
     * @test
     * @depends args
     * @return  ConsoleInput
     */
    public function getArgNonNumericIndex(ConsoleInput $input)
    {
        $this->assertEquals('custom', $input->getArg(array(1,2,3), 'custom'));
        $this->assertEquals('custom', $input->getArg(true, 'custom'));
        $this->assertEquals('custom', $input->getArg(false, 'custom'));
        $this->assertEquals('custom', $input->getArg(new StdClass(), 'custom'));
    }

    /**
     * @test
     * @return  ConsoleInput
     */
    public function shortOptionsOnlyFlags()
    {
        $opts = array('a' => true, 'b' => true, 'c' => true);
        $input = $this->createInput(array('short' => $opts));
        $this->assertEquals($opts, $input->getShortOptions());
        foreach ($opts as $key => $value) {
            $this->assertTrue($input->isShortOption($key));
            $this->assertTrue($input->isShortOptionFlag($key));
            $this->assertEquals($value, $input->getShortOption($key));
        }

        $this->assertFalse($input->isShortOption('d'));
        $this->assertNull($input->getShortOption('d'));
        $this->assertFalse($input->isShortOptionFlag('d'));

        return $input;
    }

    /**
     * @test
     * @return  ConsoleInput
     */
    public function shortOptionWithParams()
    {
        $opts = array('a' => 'value a', 'b' => 123, 'c' => null);
        $input = $this->createInput(array('short' => $opts));
        $this->assertEquals($opts, $input->getShortOptions());
 
        foreach ($opts as $key => $value) {
            /* none of these are flags */
            $this->assertFalse($input->isShortOptionFlag($key));
            $this->assertTrue($input->isShortOption($key));
            $this->assertEquals($value, $input->getShortOption($key));
        }

        $this->assertFalse($input->isShortOption('d'));
        $this->assertFalse($input->isShortOptionFlag('d'));
        $this->assertEquals('custom', $input->getShortOption('d', 'custom'));

        $this->assertNull($input->getShortOption(array(1,2,3)));
        $this->assertFalse($input->isShortOption(array(1,2,3)));
    }

    /**
     * @test
     * @return  ConsoleInput
     */
    public function longOptionsOnlyFlags()
    {
        $opts = array('opt-a' => true, 'opt-b' => true, 'opt-c' => true);
        $input = $this->createInput(array('long' => $opts));
        $this->assertEquals($opts, $input->getLongOptions());
        foreach ($opts as $key => $value) {
            $this->assertTrue($input->isLongOption($key));
            $this->assertTrue($input->isLongOptionFlag($key));
            $this->assertEquals($value, $input->getLongOption($key));
        }

        $this->assertFalse($input->isLongOption('opt-d'));
        $this->assertNull($input->getLongOption('opt-d'));
        $this->assertFalse($input->isLongOptionFlag('opt-d'));

        return $input;
    }


}
