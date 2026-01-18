<?php

namespace Automad\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase {
	public static function dataForTestDateFormatEqual() {
		return array(
			array('2025-04-05T14:39:57+00:00', 'l, j. F Y', 'de', 'Samstag, 5. April 2025'),
			array('2025-04-05T14:39:57+00:00', 'l, j. F Y', 'fr', 'samedi, 5. avril 2025'),
			array('2025-04-05T14:39:57+00:00', 'l, j. F Y', 'nl', 'zaterdag, 5. april 2025'),
			array('2025-04-05T14:39:57+00:00', 'l, j. F Y', null, 'Saturday, 5. April 2025'),
			array('2025-04-05T14:39:57+00:00', 'Y-m-d H:i:s', null, '2025-04-05 14:39:57'),
			array('2025-04-05T14:39:57+00:00', 'F j, Y, g:i a', 'en', 'April 5, 2025, 2:39 pm'),
		);
	}

	public static function dataForTestDefIsEqual() {
		return array(
			array('', 'Some string.', 'Some string.'),
			array('Not empty.', 'Some string.', 'Not empty.')
		);
	}

	public static function dataForTestFindFirstImageIsEqual() {
		return array(
			array(<<<HTML
				Test
				<img src="test-01.png" />
				Test
				HTML,
				'test-01.png'
			),
			array(<<<HTML
				Test
				<am-img-loader src="test-01.png" width="2670" height="1780" image="xxxxx" preload="xxxxx"></am-img-loader>
				Test
				HTML,
				'test-01.png'
			),
			array(<<<HTML
				Test
				<am-gallery first="gallery-01.png" data="xxxxx"></am-gallery>
				Test
				HTML,
				'gallery-01.png'
			),
			array(<<<HTML
				Test
				<am-image-slideshow first="slideshow-01.png" data="xxxxx"></am-image-slideshow>
				Test
				HTML,
				'slideshow-01.png'
			)
		);
	}

	public static function dataForTestSanitizeIsEqual() {
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

	public static function dataForTestShortenIsEqual() {
		return array(
			array('Some long string.', 3, ' ...', 'Som ...'),
			array('Some long string.', 8, ' ...', 'Some ...'),
			array('Some long string.', 9, ' (more)', 'Some long (more)'),
			array('Some long string.', 16, ' ...', 'Some long ...'),
			array('Some long string.', 17, ' ...', 'Some long string.'),
			array('Some long string.', 18, ' ...', 'Some long string.')
		);
	}

	public static function dataForTestStripEndIsEqual() {
		return array(
			array('some test test test', 'test', 'some test test ')
		);
	}

	public static function dataForTestStripStartIsEqual() {
		return array(
			array('test test test string', 'test', ' test test string')
		);
	}

	public static function dataForTestStripTagsIsEqual() {
		return array(
			array('	<div>Test</div>', 'Test')
		);
	}

	#[DataProvider('dataForTestDateFormatEqual')]
	public function testDateFormatIsEqual($date, $format, $locale, $expected) {
		/** @disregard */
		$this->assertEquals(Str::dateFormat($date, $format, $locale), $expected);
	}

	#[DataProvider('dataForTestDefIsEqual')]
	public function testDefIsEqual($str, $defaultValue, $expected) {
		/** @disregard */
		$this->assertEquals(Str::def($str, $defaultValue), $expected);
	}

	#[DataProvider('dataForTestFindFirstImageIsEqual')]
	public function testFindFirstImageIsEqual($str, $expected) {
		/** @disregard */
		$this->assertEquals(Str::findFirstImage($str), $expected);
	}

	#[DataProvider('dataForTestSanitizeIsEqual')]
	public function testSanitizeIsEqual($str, $removeDots, $maxChars, $expected) {
		/** @disregard */
		$this->assertEquals(Str::sanitize($str, $removeDots, $maxChars), $expected);
	}

	#[DataProvider('dataForTestShortenIsEqual')]
	public function testShortenIsEqual($str, $maxChars, $ellipsis, $expected) {
		/** @disregard */
		$this->assertEquals(Str::shorten($str, $maxChars, $ellipsis), $expected);
	}

	#[DataProvider('dataForTestStripEndIsEqual')]
	public function testStripEndIsEqual($str, $end, $expected) {
		/** @disregard */
		$this->assertEquals(Str::stripEnd($str, $end), $expected);
	}

	#[DataProvider('dataForTestStripStartIsEqual')]
	public function testStripStartIsEqual($str, $start, $expected) {
		/** @disregard */
		$this->assertEquals(Str::stripStart($str, $start), $expected);
	}

	#[DataProvider('dataForTestStripTagsIsEqual')]
	public function testStripTagsIsEqual($str, $expected) {
		/** @disregard */
		$this->assertEquals(Str::stripTags($str), $expected);
	}
}
