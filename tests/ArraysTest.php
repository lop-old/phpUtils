<?php
/*
 * PoiXson phpUtils - Website Utilities Library
*
* @copyright 2004-2015
* @license GPL-3
* @author lorenzo at poixson.com
* @link http://poixson.com/
*/
namespace pxn\phpUtils\tests;

use pxn\phpUtils\Arrays;


/**
 * @coversDefaultClass \pxn\phpUtils\Arrays
 */
class ArraysTest extends \PHPUnit_Framework_TestCase {



	const CLEAN_ARRAY = [ 'a', 'b', 'c' ];



	/**
	 * @covers ::TrimFlat
	 */
	public function testTrimFlat() {
		// clean
		$array = self::CLEAN_ARRAY;
		Arrays::TrimFlat($array);
		$this->assertEquals(
				\print_r(self::CLEAN_ARRAY, TRUE),
				\print_r($array,            TRUE)
		);
		// dirty
		$array = [ 'a', [ 'b', [ 'c' ] ] ];
		Arrays::TrimFlat($array);
		$this->assertEquals(
				\print_r(self::CLEAN_ARRAY, TRUE),
				\print_r($array,            TRUE)
		);
		// null
		$array = NULL;
		Arrays::TrimFlat($array);
		$this->assertEquals(
				NULL,
				$array
		);
		// blank string
		$array = '';
		Arrays::TrimFlat($array);
		$this->assertEquals(
				\print_r([],     TRUE),
				\print_r($array, TRUE)
		);
		// zero
		$array = 0;
		Arrays::TrimFlat($array);
		$this->assertEquals(
				\print_r([0],    TRUE),
				\print_r($array, TRUE)
		);
		// string
		$array = 'abc';
		Arrays::TrimFlat($array);
		$this->assertEquals(
				\print_r(['abc'],  TRUE),
				\print_r($array,   TRUE)
		);
	}



	/**
	 * @covers ::Flatten
	 */
	public function testFlatten() {
		// clean
		$array = self::CLEAN_ARRAY;
		$this->assertEquals(
				\print_r(self::CLEAN_ARRAY,       TRUE),
				\print_r(Arrays::Flatten($array), TRUE)
		);
		// dirty
		$array = [ 'a', [ 'b', [ 'c' ] ] ];
		$this->assertEquals(
				\print_r(self::CLEAN_ARRAY,       TRUE),
				\print_r(Arrays::Flatten($array), TRUE)
		);
		unset($array);
		// null
		$this->assertEquals(
				NULL,
				Arrays::Flatten(NULL)
		);
		// blank string
		$this->assertEquals(
				\print_r( [''],               TRUE),
				\print_r(Arrays::Flatten(''), TRUE)
		);
		// zero
		$this->assertEquals(
				\print_r( [0],               TRUE),
				\print_r(Arrays::Flatten(0), TRUE)
		);
		// string
		$this->assertEquals(
				\print_r( ['abc'],               TRUE),
				\print_r(Arrays::Flatten('abc'), TRUE)
		);
	}



	/**
	 * @covers ::Trim
	 */
	public function testTrim() {
		// clean
		$array = self::CLEAN_ARRAY;
		Arrays::Trim($array);
		$this->assertEquals(
				\print_r(self::CLEAN_ARRAY, TRUE),
				\print_r($array,            TRUE)
		);
		// dirty
		$array = [
				'a',
				'b',
				'c',
				NULL,
				'',
				'D' => NULL,
				'E' => ''
		];
		Arrays::Trim($array);
		$this->assertEquals(
				\print_r(self::CLEAN_ARRAY, TRUE),
				\print_r($array,            TRUE)
		);
		// null
		$array = NULL;
		$this->assertEquals(
				NULL,
				Arrays::Trim($array)
		);
		// blank string
		$array = '';
		Arrays::Trim($array);
		$this->assertEquals(
				\print_r([''],   TRUE),
				\print_r($array, TRUE)
		);
		// zero
		$array = 0;
		Arrays::Trim($array);
		$this->assertEquals(
				\print_r([0],    TRUE),
				\print_r($array, TRUE)
		);
		// string
		$array = 'abc';
		Arrays::Trim($array);
		$this->assertEquals(
				\print_r(['abc'], TRUE),
				\print_r($array,  TRUE)
		);
		unset($array);
	}



}
