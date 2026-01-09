<?php

namespace Automad\Engine\Document;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MinifyTest extends TestCase {
	public static function dataForTestCssIsEqual() {
		return array(
			array(
				<<< CSS
				
				body {
					font-size: 16px;
					padding: 0 0 0 1rem;
				}

				.test-class {
					color: #010101;
					box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.2);
				}

				CSS,
				'body{font-size:16px;padding:0 0 0 1rem;}.test-class{color:#010101;box-shadow:0 1rem 3rem rgba(0,0,0,0.2);}'
			),
			array(
				<<< CSS
				/**
				 * Comment
				 */					
				@import file.css;

				/* Comment */
				.test-class {
					color: var(--clr);
				}
				CSS,
				'@import file.css;.test-class{color:var(--clr);}'
			)
		);
	}

	#[DataProvider('dataForTestCssIsEqual')]
	public function testCssIsEqual($css, $expected) {
		/** @disregard */
		$this->assertEquals(Minify::css($css), $expected);
	}
}
