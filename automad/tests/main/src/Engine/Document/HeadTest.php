<?php

namespace Automad\Engine\Document;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\Document\Head
 */
class HeadTest extends TestCase {
	public function dataForTestAppendIsEqual() {
		return array(
			array(
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						Not a "<head></head>" tag.
					</head>
					<body>
						Not a "<head></head>" tag.
					</body>
				</html>
				HTML,
				'<style></style>',
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						Not a "<head></head>" tag.
					<style></style></head>
					<body>
						Not a "<head></head>" tag.
					</body>
				</html>
				HTML,
			),
			array(
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						<title>Test</title>
					</head>
					<!-- Comment -->
					<body>
					</body>
				</html>
				HTML,
				'<style></style>',
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						<title>Test</title>
					<style></style></head>
					<!-- Comment -->
					<body>
					</body>
				</html>
				HTML,
			)
		);
	}

	public function dataForTestPrependIsEqual() {
		return array(
			array(
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						Not a "<head></head>" tag.
					</head>
					<body>
						Not a "<head></head>" tag.
					</body>
				</html>
				HTML,
				'<style></style>',
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head><style></style>
						Not a "<head></head>" tag.
					</head>
					<body>
						Not a "<head></head>" tag.
					</body>
				</html>
				HTML,
			),
			array(
				<<< HTML
				<!DOCTYPE html>
				<html>
					<!-- Comment -->
					<head>
						<title>Test</title>
					</head>
					<body>
					</body>
				</html>
				HTML,
				'<style></style>',
				<<< HTML
				<!DOCTYPE html>
				<html>
					<!-- Comment -->
					<head><style></style>
						<title>Test</title>
					</head>
					<body>
					</body>
				</html>
				HTML,
			)
		);
	}

	/**
	 * @dataProvider dataForTestAppendIsEqual
	 * @param mixed $doc
	 * @param mixed $tag
	 * @param mixed $expected
	 */
	public function testAppendIsEqual($doc, $tag, $expected) {
		$this->assertEquals(Head::append($doc, $tag), $expected);
	}

	/**
	 * @dataProvider dataForTestPrependIsEqual
	 * @param mixed $doc
	 * @param mixed $tag
	 * @param mixed $expected
	 */
	public function testPrependIsEqual($doc, $tag, $expected) {
		$this->assertEquals(Head::prepend($doc, $tag), $expected);
	}
}
