<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;

class PointerTest extends \PHPUnit_Framework_TestCase
{
	/**
     * Tests that the pointer correctly stores and returns the target
     */
	public function testStoresTarget()
	{
        $target = [
            "foo" => ["bar", "baz"],
            "" => 0,
            "a/b" => 1,
            "c%d" => 2,
            "e^f" => 3,
            "g|h" => 4,
            "i\\j" => 5,
            "k\"l" => 6,
            " " => 7,
            "m~n" => 8
        ];
		$pointer = new Pointer($target);

		$this->assertEquals($pointer->getTarget(), $target);

		return $pointer;
	}

	/**
     * @dataProvider invalidTargetProvider
     * @depends testStoresTarget
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testFailsInvalidTarget($target, Pointer $pointer)
    {
        $pointer->setTarget($target);
    }

    /**
     * @depends testStoresTarget
     * @dataProvider pathProvider
     */
    public function testGetPathValue($path, Pointer $pointer)
    {
        $pointer->get($path);
    }

    /**
     * @depends testStoresTarget
     */
    public function testSetPathValue(Pointer $pointer)
    {
        $value = 'bar';
        $pointer->set('/foo', $value);

        $this->assertEquals($pointer->get('/foo'), $value);
    }

    /**
     * @depends testStoresTarget
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testUnsetPathValue(Pointer $pointer)
    {
        $pointer->remove('/foo');
        $pointer->get('/foo');
    }

    /**
     * @depends testStoresTarget
     * @dataProvider invalidPathProvider
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetUnsetPathValue($path, Pointer $pointer)
    {
        $pointer->get($path);
    }

    /**
     * @depends testStoresTarget
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetInvalidPathValue(Pointer $pointer)
    {
        $pointer->get('/q~ux');
    }

    /**
     * @expectedException \gamringer\JSONPointer\Exception
     */
	public function testGetFromUnsetTarget()
	{
		(new Pointer())->get('');
	}

	public function invalidTargetProvider()
	{
		return [
			[''],
			[12],
			[12.111],
			[true],
		];
	}

    public function pathProvider()
    {
        return [

            //  Regular JSON Paths
            [addslashes("")],
            [addslashes("/foo")],
            [addslashes("/foo/0")],
            [addslashes("/")],
            [addslashes("/a~1b")],
            [addslashes("/c%d")],
            [addslashes("/e^f")],
            [addslashes("/g|h")],
            [addslashes("/i\\j")],
            [addslashes("/k\"l")],
            [addslashes("/ ")],
            [addslashes("/m~0n")],

            //  URI Fragment Paths
            [addslashes('#')],
            [addslashes('#/foo')],
            [addslashes('#/foo/0')],
            [addslashes('#/')],
            [addslashes('#/a~1b')],
            [addslashes('#/c%25d')],
            [addslashes('#/e%5Ef')],
            [addslashes('#/g%7Ch')],
            [addslashes('#/i%5Cj')],
            [addslashes('#/k%22l')],
            [addslashes('#/%20')],
            [addslashes('#/m~0n')],
        ];
    }

	public function invalidPathProvider()
	{
		return [
            [addslashes("qux")],
			[addslashes("/qux")],
			[addslashes("/foo/0")],
		];
	}
}
