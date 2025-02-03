<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Core\Str
 */
class StrTest extends TestCase {
	public function dataForTestDefIsEqual() {
		return array(
			array('', 'Some string.', 'Some string.'),
			array('Not empty.', 'Some string.', 'Not empty.')
		);
	}

	public function dataForTestSanitizeIsEqual() {
		return array(
			array('Some string.', true, 100, 'some-string'),
			array('Some very long sentence, with a comma.', true, 10, 'some-very'),
			array('Filename with space.jpg', false, 100, 'filename-with-space.jpg'),
			array('Some & word', true, 100, 'some-and-word'),
			array('Hello@world', true, 100, 'hello-at-world'),
			array('10+10', true, 100, '10-plus-10'),
			array('10*10', true, 100, '10-x-10'),
			array('Hello &mdash; world', true, 100, 'hello-world'),
			array('Hello &ndash; world', true, 100, 'hello-world'),
			array('Hello / world', true, 100, 'hello-world'),
			array('<h1>Hello</h1>', true, 100, 'hello'),
			array('abc defgh', true, 4, 'abc')
		);
	}

	public function dataForTestShortenIsEqual() {
		return array(
			array('Some long string.', 3, ' ...', 'Som ...'),
			array('Some long string.', 8, ' ...', 'Some ...'),
			array('Some long string.', 9, ' (more)', 'Some long (more)'),
			array('Some long string.', 16, ' ...', 'Some long ...'),
			array('Some long string.', 17, ' ...', 'Some long string.'),
			array('Some long string.', 18, ' ...', 'Some long string.')
		);
	}

	public function dataForTestStripEndIsEqual() {
		return array(
			array('some test test test', 'test', 'some test test ')
		);
	}

	public function dataForTestStripStartIsEqual() {
		return array(
			array('test test test string', 'test', ' test test string')
		);
	}

	public function dataForTestStripTagsIsEqual() {
		return array(
			array('	<div>Test</div>', 'Test')
		);
	}

	/**
	 * @dataProvider dataForTestDefIsEqual
	 * @testdox def("$str", "$defaultValue") equals "$expected"
	 * @param mixed $str
	 * @param mixed $defaultValue
	 * @param mixed $expected
	 */
	public function testDefIsEqual($str, $defaultValue, $expected) {
		/** @disregard */
		$this->assertEquals(Str::def($str, $defaultValue), $expected);
	}

	/**
	 * @dataProvider dataForTestSanitizeIsEqual
	 * @testdox sanitize("$str", $removeDots, $maxChars) equals "$expected"
	 * @param mixed $str
	 * @param mixed $removeDots
	 * @param mixed $maxChars
	 * @param mixed $expected
	 */
	public function testSanitizeIsEqual($str, $removeDots, $maxChars, $expected) {
		/** @disregard */
		$this->assertEquals(Str::sanitize($str, $removeDots, $maxChars), $expected);
	}

	/**
	 * @dataProvider dataForTestShortenIsEqual
	 * @testdox shorten("$str", $maxChars, "$ellipsis") equals "$expected"
	 * @param mixed $str
	 * @param mixed $maxChars
	 * @param mixed $ellipsis
	 * @param mixed $expected
	 */
	public function testShortenIsEqual($str, $maxChars, $ellipsis, $expected) {
		/** @disregard */
		$this->assertEquals(Str::shorten($str, $maxChars, $ellipsis), $expected);
	}

	/**
	 * @dataProvider dataForTestStripEndIsEqual
	 * @testdox stripEnd("$str", "$end") equals "$expected"
	 * @param mixed $str
	 * @param mixed $end
	 * @param mixed $expected
	 */
	public function testStripEndIsEqual($str, $end, $expected) {
		/** @disregard */
		$this->assertEquals(Str::stripEnd($str, $end), $expected);
	}

	/**
	 * @dataProvider dataForTestStripStartIsEqual
	 * @testdox stripStart("$str", "$start") equals "$expected"
	 * @param mixed $str
	 * @param mixed $start
	 * @param mixed $expected
	 */
	public function testStripStartIsEqual($str, $start, $expected) {
		/** @disregard */
		$this->assertEquals(Str::stripStart($str, $start), $expected);
	}

	/**
	 * @dataProvider dataForTestStripTagsIsEqual
	 * @testdox stripTags("$str") equals "$expected"
	 * @param mixed $str
	 * @param mixed $expected
	 */
	public function testStripTagsIsEqual($str, $expected) {
		/** @disregard */
		$this->assertEquals(Str::stripTags($str), $expected);
	}
}
