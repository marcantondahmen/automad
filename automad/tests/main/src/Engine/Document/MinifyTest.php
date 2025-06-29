<?php

namespace Automad\Engine\Document;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\Document\Minify
 */
class MinifyTest extends TestCase {
	public function dataForTestCssIsEqual() {
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

	/**
	 * @dataProvider dataForTestCssIsEqual
	 * @param mixed $css
	 * @param mixed $expected
	 */
	public function testCssIsEqual($css, $expected) {
		/** @disregard */
		$this->assertEquals(Minify::css($css), $expected);
	}
}
