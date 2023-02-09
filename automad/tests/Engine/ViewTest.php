<?php

namespace Automad\Engine;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\View
 */
class ViewTest extends TestCase {
	public function dataForTestInPageRenderIsEqual() {
		$data = array();
		$templates = array(
			'email_01' =>   '<a href="#">test</a>' .
							"<a href='#' onclick='this.href=`mailto:` + this.innerHTML.split(``).reverse().join(``)' style='unicode-bidi:bidi-override;direction:rtl'>moc.tset-tset.tset@tset-tset.tset</a>&#x200E;" .
							'<a href="#">test</a>',
			'email_02' => 	'<a href="mailto:test@test.com"><span></span>test@test.com</a>',
			'resolve_01' => '<img src="' . AM_DIR_PAGES . '/page-slug/image.jpg" srcset="' . AM_DIR_PAGES . '/page-slug/image.jpg 500w, ' . AM_DIR_PAGES . '/page-slug/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>',
			'resolve_02' => '<img src="' . AM_DIR_PAGES . '/page-slug/image.jpg" srcset="' . AM_DIR_PAGES . '/page-slug/image.jpg 500w, ' . AM_DIR_PAGES . '/page-slug/image_large.jpg 1200w">' .
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
			'comments_01' => 'Page',
			'email_01' => '<a href="#">test</a>' .
						  "<a href='#' onclick='this.href=`mailto:` + this.innerHTML.split(``).reverse().join(``)' style='unicode-bidi:bidi-override;direction:rtl'>moc.tset-tset.tset@tset-tset.tset</a>&#x200E;" .
						  '<a href="#">test</a>',
			'email_02' => '<a href="mailto:test@test.com"><span></span>test@test.com</a>',
			'extension_01' => 'Test',
			'extension_02' => 	'<head>' .
								'<meta name="Generator" content="Automad ' . AM_VERSION . '">' .
								'<link href="' . AM_BASE_URL . '/automad/dist/blocks/blocks.min.css?m=' .
								filemtime(AM_BASE_DIR . '/automad/dist/blocks/blocks.min.css') .
								'" rel="stylesheet">' .
								'<script src="' . AM_BASE_URL . '/automad/dist/blocks/blocks.min.js?m=' .
								filemtime(AM_BASE_DIR . '/automad/dist/blocks/blocks.min.js') .
								'" type="text/javascript"></script>' .
								'<link rel="stylesheet" href="' .
								AM_BASE_URL . '/automad/tests/packages/vendor/extension/styles.css?m=' .
								filemtime(AM_BASE_DIR . '/automad/tests/packages/vendor/extension/styles.css') .
								'" />' .
								'<script type="text/javascript" src="' .
								AM_BASE_URL . '/automad/tests/packages/vendor/extension/script.js?m=' .
								filemtime(AM_BASE_DIR . '/automad/tests/packages/vendor/extension/script.js') .
								'"></script>' .
								'</head>Asset Test',
			'for_01' => '1, 2, 3, 4, 5',
			'if_01' => 'True',
			'if_02' => 'False',
			'if_03' => 'True',
			'if_04' => 'True',
			'inheritance_01' => 'derived',
			'inheritance_02' => 'derived by user',
			'inheritance_03' => 'nested derived',
			'inheritance_04' => 'nested derived override',
			'pagelist_01' => 'Text Subpage Page Home Blocks',
			'pagelist_02' => 'Blocks Text',
			'pagelist_03' => 'Home Subpage',
			'pagelist_04' => '[/page]: includes not only the word find but also the word me. [/blocks]: Some text containing the word me and the word find nested in the list',
			'pipe_dateformat_01' => '2019',
			'pipe_dateformat_02' => 'Samstag, 21. Juli 2018',
			'pipe_dateformat_03' => 'Sat, 21 Jul 2018',
			'pipe_def_01' => 'Test String',
			'pipe_def_02' => 'This is a "Test String"',
			'pipe_def_03' => 'This is a "Test String"',
			'pipe_def_04' => 'Test String',
			'pipe_def_05' => 'Some text with a "key": "value", pair.',
			'pipe_def_06' => '"Quoted" "Test" "String"',
			'pipe_empty' => '',
			'pipe_markdown_01' => '<p>A paragraph with <strong>bold</strong> text.</p>',
			'pipe_math_01' => '15',
			'pipe_math_02' => '50',
			'pipe_math_03' => '10',
			'pipe_math_04' => '17',
			'pipe_replace_01' => 'Some <div class="test">test</div> string',
			'pipe_replace_02' => '<div class="test"><p>Test</p></div>',
			'pipe_sanatize_01' => 'some-very-long-quoted-string-all-do',
			'pipe_shorten_01' => 'This is ...',
			'pipe_shorten_02' => 'This is another very >>>',
			'querystringmerge_01' => 'source=0&key1=test-string&key2=another-test-value&key3=15',
			'querystringmerge_02' => 'source=0&key1=some-key-value-pair.',
			'resolve_01' => '<img src="' . AM_DIR_PAGES . '/page-slug/image.jpg" srcset="' . AM_DIR_PAGES . '/page-slug/image.jpg 500w, ' . AM_DIR_PAGES . '/page-slug/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>',
			'resolve_02' => '<img src="' . AM_DIR_PAGES . '/page-slug/image.jpg" srcset="' . AM_DIR_PAGES . '/page-slug/image.jpg 500w, ' . AM_DIR_PAGES . '/page-slug/image_large.jpg 1200w">' .
							'<a href="/index.php/page/test">Test</a>',
			'session_get_01' => 'Session Test',
			'set_01' => 'Test 1, Test 2',
			'snippet_01' => 'Snippet Test / Snippet Test',
			'toolbox_breadcrumbs_01' => '<ul><li><a href="/">Home</a></li> <li><a href="/index.php/page">Page</a></li> <li><a href="/index.php/page/subpage">Subpage</a></li> </ul>',
			'toolbox_nav_01' => '<ul><li><a href="/">Home</a></li><li><a href="/index.php/page">Page</a></li><li><a href="/index.php/text">Text</a></li><li><a href="/index.php/blocks">Blocks</a></li></ul>',
			'toolbox_nav_02' => '<ul><li><a href="/index.php/page">Page</a></li><li><a href="/index.php/text">Text</a></li><li><a href="/index.php/blocks">Blocks</a></li></ul>',
			'with_01' => 'Text, Blocks'
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
