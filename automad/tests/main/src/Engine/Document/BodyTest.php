<?php

namespace Automad\Engine\Document;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BodyTest extends TestCase {
	public static function dataForTestAppendIsEqual() {
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

	#[DataProvider('dataForTestAppendIsEqual')]
	public function testAppendIsEqual($doc, $tag, $expected) {
		/** @disregard */
		$this->assertEquals(Body::append($doc, $tag), $expected);
	}
}
