<?php

namespace Automad\Engine\Document;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\Document\Body
 */
class BodyTest extends TestCase {
	public function dataForTestAppendIsEqual() {
		return array(
			array(
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						Not a "<body></body>" tag.
					</head>
					<body>
						Not a "<body></body>" tag.
					</body>
				</html>
				HTML,
				'<div>Test</div>',
				<<< HTML
				<!DOCTYPE html>
				<html>
					<head>
						Not a "<body></body>" tag.
					</head>
					<body>
						Not a "<body></body>" tag.
					<div>Test</div></body>
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
		$this->assertEquals(Body::append($doc, $tag), $expected);
	}
}
