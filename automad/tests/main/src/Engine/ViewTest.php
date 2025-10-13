<?php

namespace Automad\Engine;

use Automad\App;
use Automad\Core\Session;
use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\View
 */
class ViewTest extends TestCase {
	public function dataForTestInPageRenderIsEqual() {
		$data = array();
		$metaTags = $this->getMetaTags();
		$assets = $this->getAssets();
		$custom = $this->getCustomizations();
		$pagesDir = AM_DIR_PAGES;
		$csrf = Session::getCsrfToken();
		$dock = <<< HTML
			<am-inpage-dock
				csrf="$csrf"
				api="/index.php/_api"
				dashboard="/index.php/dashboard"
				url="/page"
				state="draft"
				labels="%7B%22fieldsSettings%22%3A%22Settings%22%2C%22fieldsContent%22%3A%22Content%22%2C%22uploadedFiles%22%3A%22Files%22%2C%22publish%22%3A%22Publish%22%7D"
			></am-inpage-dock>
			HTML;

		$templates = array(
			'email_01' => <<< HTML
						<html lang="en">
							<head>{$metaTags}
							{$assets->mailCSS}{$custom->head}{$assets->inPageCSS}{$assets->inPageJS}</head>
							<body>
								<a href="#">test</a>
								<a href="#" data-eml="N2UzMDA3ZWRDAEBEHkMAF0NIR1VDQyUQUhZHHkRSFhAaEVZDRBkGC1o=" data-key="7e3007ed">test<span class="am-dot"></span>test-test<span class="am-at"></span>test<span class="am-dot"></span>test-test<span class="am-dot"></span>com</a>
								<a href="#">test</a>
							{$assets->mailJS}{$custom->bodyEnd}$dock</body>
						</html>
						HTML,
			'email_02' => <<< HTML
						<html lang="en">
							<head>{$metaTags}
							{$assets->mailCSS}{$custom->head}{$assets->inPageCSS}{$assets->inPageJS}</head>
							<body>
								<a href="#" data-eml="YjY0MmI0MjEWU0dGIkBXQhYYV10P" data-key="b642b421">
									<span></span>
									test<span class="am-at"></span>test<span class="am-dot"></span>com
								</a>
							{$assets->mailJS}{$custom->bodyEnd}$dock</body>
						</html>
						HTML,
			'resolve_01' => <<< HTML
						<html lang="en">
							<head>{$metaTags}{$custom->head}{$assets->inPageCSS}{$assets->inPageJS}</head>
							<body>
								<img src="$pagesDir/page-slug/image.jpg" srcset="$pagesDir/page-slug/image.jpg 500w, $pagesDir/page-slug/image_large.jpg 1200w">
								<a href="/index.php/page/test">Test</a>
							{$custom->bodyEnd}$dock</body>
						</html>
						HTML,
			'resolve_02' => <<< HTML
						<html lang="en">
							<head>{$metaTags}{$custom->head}{$assets->inPageCSS}{$assets->inPageJS}</head>
							<body>
								<img src="$pagesDir/page-slug/image.jpg" srcset="$pagesDir/page-slug/image.jpg 500w, $pagesDir/page-slug/image_large.jpg 1200w">
								<a href="/index.php/page/test">Test</a>
							{$custom->bodyEnd}$dock</body>
						</html>
						HTML
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
		$metaTags = $this->getMetaTags();
		$assets = $this->getAssets();
		$custom = $this->getCustomizations();
		$pagesDir = AM_DIR_PAGES;

		$templates = array(
			'block_assets_01' => <<<HTML
						<html lang="en">
							<head>{$metaTags}{$assets->blocksCSS}{$assets->blocksJS}
							{$custom->head}</head>
							<body>
								<div class="am-block some-class">Test</div>
							{$custom->bodyEnd}</body>
						</html>
						HTML,
			'block_assets_02' => <<<HTML
						<html lang="en">
							<head>{$metaTags}{$assets->blocksCSS}{$assets->blocksJS}
							{$custom->head}</head>
							<body>
								<div class="some-class am-block">Test</div>
							{$custom->bodyEnd}</body>
						</html>
						HTML,
			'comments_01' => 'Page',
			'components_01' => '<div class="am-block"><p class="am-block">Component test</p></div>',
			'consent_check_01' => <<< HTML
						<html lang="en">
							<head>{$metaTags}{$assets->consentCSS}{$assets->consentJS}
							{$custom->head}</head>
							<body>
								<am-consent type="script">Y29uc29sZS5sb2coJ0hlbGxvJyk7</am-consent>
							{$custom->bodyEnd}</body>
						</html>
						HTML,
			'custom_function_01' => '{"text":"Hello"}',
			'custom_function_02' => 'Hello',
			'custom_function_03' => 'Hi there',
			'custom_function_04' => 'derived',
			'custom_function_05' => 'derived by user',
			'email_01' => <<< HTML
						<html lang="en">
							<head>{$metaTags}
							{$assets->mailCSS}{$custom->head}</head>
							<body>
								<a href="#">test</a>
								<a href="#" data-eml="N2UzMDA3ZWRDAEBEHkMAF0NIR1VDQyUQUhZHHkRSFhAaEVZDRBkGC1o=" data-key="7e3007ed">test<span class="am-dot"></span>test-test<span class="am-at"></span>test<span class="am-dot"></span>test-test<span class="am-dot"></span>com</a>
								<a href="#">test</a>
							{$assets->mailJS}{$custom->bodyEnd}</body>
						</html>
						HTML,
			'email_02' => <<< HTML
						<html lang="en">
							<head>{$metaTags}
							{$assets->mailCSS}{$custom->head}</head>
							<body>
								<a href="#" data-eml="YjY0MmI0MjEWU0dGIkBXQhYYV10P" data-key="b642b421">
									<span></span>
									test<span class="am-at"></span>test<span class="am-dot"></span>com
								</a>
							{$assets->mailJS}{$custom->bodyEnd}</body>
						</html>
						HTML,
			'extension_01' => 'Test',
			'extension_02' => <<< HTML
						<html lang="en">
							<head>{$metaTags}
							{$assets->extensionCSS}{$assets->extensionJS}{$custom->head}</head>
							<body>
								Asset Test
							{$custom->bodyEnd}</body>
						</html>
						HTML,
			'falsy' => '0//false/0/1',
			'find_first_image_01' => '/shared/image.png',
			'find_first_image_02' => '/shared/image.png',
			'for_01' => '1, 2, 3, 4, 5',
			'if_01' => 'True',
			'if_02' => 'False',
			'if_03' => 'True',
			'if_04' => 'True',
			'inheritance_01' => 'derived',
			'inheritance_02' => 'derived by user',
			'inheritance_03' => 'nested derived',
			'inheritance_04' => 'nested derived override',
			'invalid' => '//',
			'lang_01' => 'de-DE',
			'pagelist_01' => 'Text Subpage Page Home BreadcrumbsTest Blocks',
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
			'resolve_01' => <<< HTML
						<html lang="en">
							<head>{$metaTags}{$custom->head}</head>
							<body>
								<img src="$pagesDir/page-slug/image.jpg" srcset="$pagesDir/page-slug/image.jpg 500w, $pagesDir/page-slug/image_large.jpg 1200w">
								<a href="/index.php/page/test">Test</a>
							{$custom->bodyEnd}</body>
						</html>
						HTML,
			'resolve_02' => <<< HTML
						<html lang="en">
							<head>{$metaTags}{$custom->head}</head>
							<body>
								<img src="$pagesDir/page-slug/image.jpg" srcset="$pagesDir/page-slug/image.jpg 500w, $pagesDir/page-slug/image_large.jpg 1200w">
								<a href="/index.php/page/test">Test</a>
							{$custom->bodyEnd}</body>
						</html>
						HTML,
			'runtime_01' => '1. /automad/tests/main/data/page-slug/empty.css',
			'session_01' => 'Session Test',
			'session_02' => '1',
			'set_01' => 'Test 1, Test 2',
			'set_02' => 'snippet value',
			'snippet_01' => 'Snippet Test / Snippet Test',
			'snippet_02' => 'Hello',
			'toolbox_breadcrumbs_01' => '<ul><li><a href="/">Home</a></li> <li><a href="/index.php/page">Page</a></li> <li><a href="/index.php/page/subpage">Subpage</a></li> </ul>',
			'toolbox_nav_01' => '<ul><li><a href="/">Home</a></li><li><a href="/index.php/page">Page</a></li><li><a href="/index.php/text">Text</a></li><li><a href="/index.php/blocks">Blocks</a></li></ul>',
			'toolbox_nav_02' => '<ul><li><a href="/index.php/page">Page</a></li><li><a href="/index.php/text">Text</a></li><li><a href="/index.php/blocks">Blocks</a></li></ul>',
			'with_01' => 'Home, Subpage'
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
	 * @runInSeparateProcess
	 * @param mixed $template
	 * @param mixed $expected
	 */
	public function testInPageRenderIsEqual($template, $expected) {
		$_SESSION['username'] = 'test';

		$Mock = new Mock();
		$View = new View($Mock->createAutomad($template));
		$rendered = $View->render();
		$rendered = trim(str_replace('\n', '', $rendered));

		$_SESSION['username'] = false;

		/** @disregard */
		$this->assertEquals($expected, $rendered);
	}

	/**
	 * @dataProvider dataForTestRenderIsEqual
	 * @testdox render $template: $expected
	 * @runInSeparateProcess
	 * @param mixed $template
	 * @param mixed $expected
	 */
	public function testRenderIsEqual($template, $expected) {
		$Mock = new Mock();
		$View = new View($Mock->createAutomad($template));
		$rendered = $View->render();
		$rendered = trim(str_replace('\n', '', $rendered));

		/** @disregard */
		$this->assertEquals($expected, $rendered);
	}

	private function getAssets(): object {
		$asset = function (string $file): string {
			return $file . '?m=' . filemtime(AM_BASE_DIR . $file);
		};

		return (object) array(
			'consentJS' => '<script src="' . $asset('/automad/dist/build/consent/index.js') . '" type="module"></script>',
			'consentCSS' => '<link href="' . $asset('/automad/dist/build/consent/index.css') . '" rel="stylesheet">',
			'blocksJS' => '<script src="' . $asset('/automad/dist/build/blocks/index.js') . '" type="module"></script>',
			'blocksCSS' => '<link href="' . $asset('/automad/dist/build/blocks/index.css') . '" rel="stylesheet">',
			'mailJS' => '<script src="' . $asset('/automad/dist/build/mail/index.js') . '" type="module"></script>',
			'mailCSS' => '<link href="' . $asset('/automad/dist/build/mail/index.css') . '" rel="stylesheet">',
			'inPageJS' => '<script src="' . $asset('/automad/dist/build/inpage/index.js') . '" type="module"></script>',
			'inPageCSS' => '<link href="' . $asset('/automad/dist/build/inpage/index.css') . '" rel="stylesheet">',
			'extensionJS' => '<script type="text/javascript" src="' . $asset('/automad/tests/main/packages/vendor/extension/script.js') . '"></script>',
			'extensionCSS' => '<link href="' . $asset('/automad/tests/main/packages/vendor/extension/styles.css') . '" rel="stylesheet">'
		);
	}

	private function getCustomizations(): object {
		return (object) array(
			'head' => '<title>html head</title><script>javascript head</script><style>custom css</style>',
			'bodyEnd' => '<p>html body end</p><script>javascript body end</script>'
		);
	}

	private function getMetaTags(): string {
		return '<meta name="Generator" content="Automad ' . App::VERSION . '">' .
			'<link rel="canonical" href="' . AM_SERVER . AM_BASE_INDEX . AM_REQUEST . '">' .
			'<meta charset="utf-8">' .
			'<meta http-equiv="X-UA-Compatible" content="IE=edge">' .
			'<meta name="description" content="html body end">' .
			'<meta property="og:title" content="Page | My Test Site">' .
			'<meta property="og:description" content="html body end">' .
			'<meta property="og:image" content="http://localhost/cache/images/og-f6039865b8da0ea1944c4d94eb9777172ecfc176d6145bcb6182139a9aabd4b9.png">' .
			'<meta property="og:type" content="website">' .
			'<meta property="og:url" content="http://localhost/index.php/page">' .
			'<meta name="twitter:card" content="summary_large_image">';
	}
}
