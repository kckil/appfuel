<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\DataStructure;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface;

class ArrayDataTest extends FrameworkTestCase 
{
    protected $resource = null;

    public function setUp()
    {
        $this->resource = fopen('php://stdout', 'r');
    }

    public function tearDown()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    /**
     * @return  array
     */
    public function provideValidKeyValues()
    {
        return array(
            array(1, 0),
            array(2, -1),
            array(3, 100),
            array(4, new StdClass),
            array('key-a', 'value-a'),
            array('key-b', 1),
            array('key-c', null),
            array('key-d', true),
            array(9, false),
            array(11, array(1,2,3)),
            array('key-e', array(1,2,3)),
            array('', 'some-value')
        );
    }

    /**
     * @param   array   $list 
     * @return  ArrayData
     */
    public function createArrayData($data = array(), $type = 'any')
    {
        return new ArrayData($data, $type);
    }

    /**
     * @return  array
     */
    public function getKeyAsTypeExample()
    {
        return array(
            'string'     => 'this is a string',
            'array'      => array(1,2,3),
            'bool'       => true,
            'int'        => 12345,
            'scalar'     => 'A123B',
            'numeric'    => '1234',
            'float'      => 1.23,
            'resource'   => $this->resource,
            'callable'   => 'is_file',
            'null'       => null,
            'empty'      => '',
            'bool-true'  => true,
            'bool-false' => false,
            'non-empty-string' => 'non empty string',
            'StdClass'   => new StdClass,
        );
    }

    /**
     * @return  array
     */ 
    public function getIndexExample()
    {
        return array('a', 'b', 'c', 'd');
    }

    /**
     * @return  array
     */
    public function getAssocExample()
    {
        return array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4);
    }

    /**
     * @param   mixed   $object
     * @return  bool
     */
    public function assertArrayData($object)
    {
        $interface = 'Appfuel\\DataStructure\\ArrayDataInterface';
        $this->assertInstanceOf($interface, $object);
        $this->assertInstanceOf('ArrayAccess', $object);
        $this->assertInstanceOf('Countable', $object);
        $this->assertInstanceOf('Serializable', $object);
    }

    /**
     * @test
     * @return  ArrayData
     */
    public function creatingArrayDataEmpty()
    {
        $array = $this->createArrayData();
        $this->assertArrayData($array);

        return $array;
    }

    /**
     * @test
     * @return  ArrayData
     */
    public function creatingIndexExample()
    {
        $data = $this->getIndexExample();
        $type = 'int';
        $array = $this->createArrayData($data, $type);
        $this->assertArrayData($array);
       
        return $array;
    }

    /**
     * @test
     * @return  ArrayData
     */
    public function creatingAssocExample()
    {
        $array = $this->createArrayData($this->getAssocExample());
        
        return $array;    
    }

    /**
     * @test
     */
    public function indexType()
    {
        $data = array(1,2,3);
        $array = $this->createArrayData($data);
        $this->assertEquals('any', $array->getIndexType());

        $array = $this->createArrayData($data, 'any');
        $this->assertEquals('any', $array->getIndexType());

        $array = $this->createArrayData($data, 'int');
        $this->assertEquals('int', $array->getIndexType());

        $array = $this->createArrayData($data, 'non-empty-string');
        $this->assertEquals('non-empty-string', $array->getIndexType());

        $array = $this->createArrayData($data, 'string');
        $this->assertEquals('string', $array->getIndexType());
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function indexTypeNotStringFailure($badType)
    {
        $msg = 'index type must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $array = $this->createArrayData(array(1,2), $badType);
    }

    /**
     * @test
     */
    public function indexTypeNotAValidTypeFailure()
    {
        $msg = 'valid typ must be -(any, int, string, non-empty-string)';
        $this->setExpectedException('DomainException', $msg);

        $array = $this->createArrayData(array(1,2), 'not-valid');
    }

    /**
     * @test
     * @dataProvider    provideValidKeyValues
     */
    public function usingTheOffsets($key, $value)
    {
        $array = $this->createArrayData(array());
        $this->assertFalse($array->offsetExists($key));
        $this->assertNull($array->offsetGet($key));
        $this->assertNull($array->offsetUnset($key));

        $this->assertNull($array->offsetSet($key, $value));
        $this->assertTrue($array->offsetExists($key));
        $this->assertEquals($value, $array->offsetGet($key));
        
        $this->assertNull($array->offsetUnset($key));
        $this->assertFalse($array->offsetExists($key));
        $this->assertNull($array->offsetGet($key));
    }

    /**
     * @test
     * @dataProvider    provideValidKeyValues
     */
    public function usingTheOffsetsWithSetAssignAlias($key, $value)
    {
        $array = $this->createArrayData(array());
        $this->assertFalse($array->offsetExists($key));
        $this->assertNull($array->offsetGet($key));

        // set
        $this->assertSame($array, $array->set($key, $value));
        $this->assertTrue($array->offsetExists($key));
        $this->assertEquals($value, $array->offsetGet($key));
        
        $this->assertNull($array->offsetUnset($key));

        // assign
        $this->assertSame($array, $array->assign($key, $value));
        $this->assertTrue($array->offsetExists($key));
        $this->assertEquals($value, $array->offsetGet($key));
        
        $this->assertNull($array->offsetUnset($key));
        
        // add
        $this->assertSame($array, $array->add($key, $value));
        $this->assertTrue($array->offsetExists($key));
        $this->assertEquals($value, $array->offsetGet($key));
        
    }

    /**
     * @test
     * @depends usingTheOffsets
     * @dataProvider    provideInvalidInts
     */
    public function usingOffsetSetWhenIntSetAndNotInt($badKey)
    {
        $msg = 'offset key in not valid for -(int)';
        $this->setExpectedException('DomainException', $msg);

        $array = $this->createArrayData(null, 'int');
        $array->offsetSet($badKey, 'my-value');
        
    }

    /**
     * @test
     * @depends usingTheOffsets
     */
    public function countingTheArray()
    {
        $data = array(1,2,3,4,5,6,7,8,9,10);
        $array = $this->createArrayData($data);
        $this->assertEquals(count($data), $array->count());
    
        $data = array('a' => 1, 'b' => 2, 4, 5, 7, 'abc');
        $array = $this->createArrayData($data);
        $this->assertEquals(count($data), $array->count());
    }

    /**
     * @test
     */
    public function gettingAllItems()
    {
        $data = $this->getIndexExample();
        $array = $this->createArrayData($data);
        $this->assertEquals($data, $array->getAll());
    }

    /**
     * Load does not clear out any previous items
     *
     * @test
     * @depends gettingAllItems
     */
    public function loadingData()
    {
        $data = $this->getIndexExample();
        $array = $this->createArrayData($data);

        $more = array(4 => 'e', 'f', 'g');
        $this->assertSame($array, $array->load($more));

        $expected = array('a', 'b', 'c', 'd', 'e', 'f', 'g');
        $this->assertEquals($expected, $array->getAll());
    }

    /**
     * Settings all data will clear any existing items
     *
     * @test
     * @depends gettingAllItems
     */
    public function clearingData()
    {
        $data = $this->getIndexExample();
        $array = $this->createArrayData($data);

        $this->assertSame($array, $array->clear(0));

        $expected = array(1 => 'b', 'c', 'd');
        $this->assertEquals($expected, $array->getAll());

        $expected = array(1 => 'b', 3 => 'd'); 
        $this->assertSame($array, $array->clear(2));

        $this->assertSame($array, $array->clear());
        $this->assertEquals(array(), $array->getAll());
    }


    /**
     * Settings all data will clear any existing items
     *
     * @test
     * @depends clearingData
     */
    public function settingsAllData()
    {
        $data = $this->getIndexExample();
        $array = $this->createArrayData($data);

        $more = array('e', 'f', 'g');
        $this->assertSame($array, $array->setAll($more));

        $this->assertEquals($more, $array->getAll());
    }

    /**
     * @test
     */
    public function gettingItems()
    {
        $data = array(
            'key-a' => 'value-a',
            'key-b' => 'value-b',
            '' => 'value-c',
            0  => 'value-d',
        );

        $array = $this->createArrayData($data);
        $this->assertEquals('value-a', $array->get('key-a'));
        $this->assertEquals('value-b', $array->get('key-b'));
        $this->assertEquals('value-c', $array->get(''));
        $this->assertEquals('value-d', $array->get(0));

        $this->assertNull($array->get('key-z'));

        $default = 'some-default';
        $this->assertEquals($default, $array->get('key-z', $default));
        
        $default = 999;
        $this->assertEquals($default, $array->get('key-z', $default));

        $default = new StdClass;
        $this->assertEquals($default, $array->get('key-z', $default));
    }

    /**
     * @test
     */
    public function collectingItems()
    {
        $data = array(
            'key-a' => 'value-a',
            'key-b' => 'value-b',
            'key-c' => 'value-c',
            'key-d' => 'value-d'
        );
        $array = $this->createArrayData($data);
    
        $result = $array->collect(array('key-a', 'key-c'));
        $expected = array('key-a' => 'value-a', 'key-c' => 'value-c');
        $this->assertEquals($expected, $result);

        $result = $array->collect(array('key-a', 'key-z', 'key-c'));
        $this->assertEquals($expected, $result);

        $expected = $this->createArrayData($expected);
        $result = $array->collect(array('key-a', 'key-c'), false);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function checkTheExistanceAs()
    {
        $data = $this->getKeyAsTypeExample();
        $array = $this->createArrayData($data);

        foreach ($data as $key => $value) {
            $this->assertTrue($array->existsAs($key, $key));
        }
    
        $this->assertTrue($array->existsAs('null', 'empty'));       
        $this->assertTrue($array->existsAs('bool-true', 'bool'));       
        $this->assertTrue($array->existsAs('bool-false', 'bool'));       

        /* type can not be empty */
        $this->assertFalse($array->existsAs('string', null));
        $this->assertFalse($array->existsAs('string', ''));
        $this->assertFalse($array->existsAs('string', 0));
      
        /* key does not exist */
        $this->assertFalse($array->existsAs('not-there', 'string'));

        /* types are wrong */  
        $this->assertFalse($array->existsAs('string', 'int'));
        $this->assertFalse($array->existsAs('int', 'string'));
        $this->assertFalse($array->existsAs('array', 'int'));
        $this->assertFalse($array->existsAs('bool', 'int'));
        $this->assertFalse($array->existsAs('scalar', 'array'));
        $this->assertFalse($array->existsAs('numeric', 'array'));
        $this->assertFalse($array->existsAs('float', 'array'));
        $this->assertFalse($array->existsAs('resource', 'array'));
        $this->assertFalse($array->existsAs('callable', 'array'));
        $this->assertFalse($array->existsAs('null', 'array'));
        $this->assertFalse($array->existsAs('empty', 'null'));
        $this->assertFalse($array->existsAs('bool-false', 'int'));
        $this->assertFalse($array->existsAs('bool-false', 'bool-true'));
        $this->assertFalse($array->existsAs('bool-true', 'int'));
        $this->assertFalse($array->existsAs('bool-true', 'bool-false'));
        $this->assertFalse($array->existsAs('non-empty-string', 'int'));
        $this->assertFalse($array->existsAs('non-empty-string', 'empty'));
        $this->assertFalse($array->existsAs('StdClass', 'int'));
    }

    /**
     * Each key is the name of the type for the value it holds. The example
     * array has all types supported by ArrayData.
     *
     * @test
     */
    public function gettingAnItemAs()
    {
        $data = $this->getKeyAsTypeExample();
        $array = $this->createArrayData($data);
        
        $default = 'default value';
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $array->getAs($key, $key));
            $this->assertNull($array->getAs($key, 'no-known-type'));
            $this->assertEquals(
                $default,
                $array->getAs($key, 'no-known-type', $default)
            );
        }

        $this->assertNull($array->getAs('nokey', 'array'));
        $this->assertEquals(
            $default, 
            $array->getAs('nokey', 'array', $default)
        );
    }

    /**
     * @test
     */
    public function collectionAsASingleType()
    {
        $data = array(
            'key-a' => 'value-a',
            'key-b' => 1234,
            'key-c' => 'value-c'
        );
        $array = $this->createArrayData($data);
        
        $keys = array_keys($data);
        $result = $array->collectAs($keys, 'string');
        $expected = array(
            'key-a' => 'value-a',
            'key-c' => 'value-c'
        );
        $this->assertEquals($expected, $result);
    }
} 
