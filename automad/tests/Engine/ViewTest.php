<?php

namespace Automad\Engine;

use Automad\Core\Str;
use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\View
 */
class ViewTest extends TestCase {
	public function dataForTestHeadlessJSONIsEqual() {
		return array(
			array(
				'<img src="image.jpg" srcset="image.jpg 500w, image_large.jpg 1200w"><a href="test">Test</a>',
				'{"test": "<img src=\"/pages/01.page/image.jpg\" srcset=\"/pages/01.page/image.jpg 500w, /pages/01.page/image_large.jpg 1200w\"><a href=\"/index.php/page/test\">Test</a>"}'
			),
			array(
				"This is a\n\rmultiline test.",
				'{"test": "This is a\\\\nmultiline test."}'
			),
			array(
				'{"test":""}',
				'{"test": "{\"test\":\"\"}"}'
			)
		);
	}

	public function dataForTestHeadlessValueIsEqual() {
		return array(
			array(
				'<img src="image.jpg" srcset="image.jpg 500w, image_large.jpg 1200w"><a href="test">Test</a>',
				'<img src="/pages/01.page/image.jpg" srcset="/pages/01.page/image.jpg 500w, /pages/01.page/image_large.jpg 1200w"><a href="/index.php/page/test">Test</a>'
			),
			array(
				"This is a\n\rmultiline test.",
				'This is a\nmultiline test.'
			),
			array(
				'{"test":""}',
				'{"test":""}'
			)
		);
	}

	public function dataForTestInPageRenderIsEqual() {
		$data = array();
		$templates = array(
			'email_01' =>   '<a href="#">test</a>' .
							"<a href='#' onclick='this.href=`mailto:` + this.innerHTML.split(``).reverse().join(``)' style='unicode-bidi:bidi-override;direction:rtl'>moc.tset-tset.tset@tset-tset.tset</a>&#x200E;" .
							'<a href="#">test</a>',
			'email_02' => 	'<a href="mailto:test@test.com"><span></span>test@test.com</a>',
			'resolve_01' => '<img src="/pages/01.page/image.jpg" srcset="/pages/01.page/image.jpg 500w, /pages/01.page/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>',
			'resolve_02' => '<img src="/pages/01.page/image.jpg" srcset="/pages/01.page/image.jpg 500w, /pages/01.page/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>'
		);

		foreach ($templates as $template => $expected) {
			$data[] = array(
				$template,
				$expected
			);
		}

		return $data;
	}

	public function dataForTestRenderIsEqual() {
		$data = array();
		$templates = array(
			'pipe_def_01' => 'Test String',
			'pipe_def_02' => 'This is a "Test String"',
			'pipe_def_03' => 'This is a "Test String"',
			'pipe_def_04' => 'Test String',
			'pipe_def_05' => 'Some text with a "key": "value", pair.',
			'pipe_def_06' => '"Quoted" "Test" "String"',
			'pipe_empty' => '',
			'pipe_markdown_01' => '<p>A paragraph with <strong>bold</strong> text.</p>',
			'pipe_dateformat_01' => '2019',
			'pipe_dateformat_02' => 'Samstag, 21. Juli 2018',
			'pipe_dateformat_03' => 'Sat, 21 Jul 2018',
			'pipe_replace_01' => 'Some <div class="test">test</div> string',
			'pipe_replace_02' => '<div class="test"><p>Test</p></div>',
			'pipe_sanatize_01' => 'some-very-long-quoted-string-all-do',
			'pipe_shorten_01' => 'This is ...',
			'pipe_shorten_02' => 'This is another very >>>',
			'pipe_math_01' => '15',
			'pipe_math_02' => '50',
			'pipe_math_03' => '10',
			'pipe_math_04' => '17',
			'for_01' => '1, 2, 3, 4, 5',
			'if_01' => 'True',
			'if_02' => 'False',
			'if_03' => 'True',
			'if_04' => 'True',
			'querystringmerge_01' => 'source=0&key1=test-string&key2=another-test-value&key3=15',
			'querystringmerge_02' => 'source=0&key1=some-key-value-pair.',
			'set_01' => 'Test 1, Test 2',
			'session_get_01' => 'Session Test',
			'email_01' => '<a href="#">test</a>' .
						  "<a href='#' onclick='this.href=`mailto:` + this.innerHTML.split(``).reverse().join(``)' style='unicode-bidi:bidi-override;direction:rtl'>moc.tset-tset.tset@tset-tset.tset</a>&#x200E;" .
						  '<a href="#">test</a>',
			'email_02' => '<a href="mailto:test@test.com"><span></span>test@test.com</a>',
			'resolve_01' => '<img src="/pages/01.page/image.jpg" srcset="/pages/01.page/image.jpg 500w, /pages/01.page/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>',
			'resolve_02' => '<img src="/pages/01.page/image.jpg" srcset="/pages/01.page/image.jpg 500w, /pages/01.page/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>',
			'extension_01' => 'Test',
			'extension_02' => 	'<head>' .
								'<meta name="Generator" content="Automad ' . AM_VERSION . '">' .
								'<link rel="stylesheet" href="' . AM_BASE_URL . '/automad/dist/blocks.min.css?v=' . Str::sanitize(AM_VERSION) . '">' .
								'<script type="text/javascript" src="' . AM_BASE_URL . '/automad/dist/blocks.min.js?v=' . Str::sanitize(AM_VERSION) . '"></script>' .
								'<link rel="stylesheet" href="' .
								AM_BASE_URL . '/automad/tests/packages/vendor/extension/styles.css?m=' .
								filemtime(AM_BASE_DIR . '/automad/tests/packages/vendor/extension/styles.css') .
								'" />' .
								'<script type="text/javascript" src="' .
								AM_BASE_URL . '/automad/tests/packages/vendor/extension/script.js?m=' .
								filemtime(AM_BASE_DIR . '/automad/tests/packages/vendor/extension/script.js') .
								'"></script>' .
								'</head>Asset Test',
			'snippet_01' => 'Snippet Test / Snippet Test',
			'inheritance_01' => 'derived',
			'inheritance_02' => 'derived by user',
			'inheritance_03' => 'nested derived',
			'inheritance_04' => 'nested derived override',
		);

		foreach ($templates as $template => $expected) {
			$data[] = array(
				$template,
				$expected
			);
		}

		return $data;
	}

	/**
	 * @dataProvider dataForTestHeadlessJSONIsEqual
	 * @testdox render $value: $expected
	 * @param mixed $value
	 * @param mixed $expected
	 */
	public function testHeadlessJSONIsEqual($value, $expected) {
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();
		$Page = $AutomadMock->Context->get();
		// Set test to $value.
		$Page->data['test'] = $value;
		// Render view in headless mode.
		$View = new View($AutomadMock, true);

		$this->assertEquals($expected, $View->render());
	}

	/**
	 * @dataProvider dataForTestHeadlessValueIsEqual
	 * @testdox render $value: $expected
	 * @param mixed $value
	 * @param mixed $expected
	 */
	public function testHeadlessValueIsEqual($value, $expected) {
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();
		$Page = $AutomadMock->Context->get();
		// Set test to $value.
		$Page->data['test'] = $value;
		// Render view in headless mode.
		$View = new View($AutomadMock, true);
		// Convert JSON output back into array to check if
		// $value matches $expected.
		$array = json_decode($View->render());

		$this->assertEquals($expected, $array->test);
	}

	/**
	 * @dataProvider dataForTestInPageRenderIsEqual
	 * @testdox render $template: $expected
	 * @param mixed $template
	 * @param mixed $expected
	 */
	public function testInPageRenderIsEqual($template, $expected) {
		$_SESSION['username'] = 'test';

		$Mock = new Mock();
		$View = new View($Mock->createAutomad($template));
		$rendered = $View->render();
		$rendered = trim(str_replace('\n', '', $rendered));

		$this->assertEquals($expected, $rendered);

		$_SESSION['username'] = false;
	}

	/**
	 * @dataProvider dataForTestRenderIsEqual
	 * @testdox render $template: $expected
	 * @param mixed $template
	 * @param mixed $expected
	 */
	public function testRenderIsEqual($template, $expected) {
		$Mock = new Mock();
		$View = new View($Mock->createAutomad($template));
		$rendered = $View->render();
		$rendered = trim(str_replace('\n', '', $rendered));

		$this->assertEquals($expected, $rendered);
	}
}
