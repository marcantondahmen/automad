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

	public static function dataForTestHtmlIsSame() {
		$numbered = array_map(fn (int $i): string => "<pre> $i </pre>", range(0, 11));

		return array(
			'whitespace between tags is collapsed and trimmed' => array(
				<<< HTML

				<html>
					<head>
						<title>Test</title>
					</head>
					<body>
						<p>Text</p>
					</body>
				</html>

				HTML,
				'<html> <head> <title>Test</title> </head> <body> <p>Text</p> </body> </html>'
			),
			'whitespace inside text is kept' => array(
				"<p>\n\ta  b\n</p>",
				'<p> a  b </p>'
			),
			'comments are removed' => array(
				"<div><!-- comment --></div>\n<!--\n\tmulti\n\tline\n-->\n<p>Text</p>",
				'<div></div> <p>Text</p>'
			),
			'pre, textarea, script and style are kept' => array(
				"<div>\n\t<PRE class=\"code\">  a\n\t  b  </PRE>\n\t<textarea name=\"t\">\n  x  y\n</textarea>\n\t<script type=\"module\">\n\t\tconst a = '<b>  c</b>'; // <!-- kept -->\n\t</script>\n\t<style media=\"all\">  body  {  }  </style>\n</div>",
				"<div> <PRE class=\"code\">  a\n\t  b  </PRE> <textarea name=\"t\">\n  x  y\n</textarea> <script type=\"module\">\n\t\tconst a = '<b>  c</b>'; // <!-- kept -->\n\t</script> <style media=\"all\">  body  {  }  </style> </div>"
			),
			'more than ten protected blocks are restored in order' => array(
				implode("\n", $numbered),
				implode(' ', $numbered)
			),
			'multi-line opening tag is collapsed' => array(
				"<div\n\tclass=\"a\"\n\tid=\"b\"\n>Text</div>",
				'<div class="a" id="b">Text</div>'
			),
			'quoted attribute values are kept' => array(
				"<a title=\"a   b\" data-x='c\n d'   href=\"#\">Link</a>",
				"<a title=\"a   b\" data-x='c\n d' href=\"#\">Link</a>"
			),
			'attribute value containing a closing bracket' => array(
				'<a title="x > y"  href="z">Link</a>',
				'<a title="x > y" href="z">Link</a>'
			),
			'whitespace next to brackets inside attribute values is kept' => array(
				'<a title="x >   y" data-y="a   <b">Link</a>',
				'<a title="x >   y" data-y="a   <b">Link</a>'
			),
			'space before closing bracket after quoted value is removed' => array(
				"<a href=\"x\" >Link</a>\n<img src=\"x\" />",
				'<a href="x">Link</a> <img src="x"/>'
			),
			'space before closing bracket after unquoted token is kept' => array(
				"<p>a<br />b</p>\n<input disabled >",
				'<p>a<br />b</p> <input disabled >'
			)
		);
	}

	#[DataProvider('dataForTestCssIsEqual')]
	public function testCssIsEqual($css, $expected) {
		/** @disregard */
		$this->assertEquals(Minify::css($css), $expected);
	}

	#[DataProvider('dataForTestHtmlIsSame')]
	public function testHtmlIsSame($html, $expected) {
		/** @disregard */
		$this->assertSame($expected, Minify::html($html));
	}
}
