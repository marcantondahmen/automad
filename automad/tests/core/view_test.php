<?php 

namespace Automad\Core;

use Automad\Tests\Mock;
use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\Core\View
 */

class View_Test extends TestCase {
	
	
	/**
	 *	@dataProvider dataForTestRenderIsEqual
	 *	@testdox render $template: $expected
	 */
	
	public function testRenderIsEqual($template, $expected) {
		
		$Mock = new Mock();
		$View = new View($Mock->createAutomad($template));
		$rendered = $View->render();
		$rendered = trim(str_replace('\n', '', $rendered));
		
		$this->assertEquals($rendered, $expected);
		
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
			'pipe_markdown_01' => '<p>A paragraph with <strong>bold</strong> text.</p>',
			'pipe_dateformat_01' => '2019',
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
			'email_01' => '<a href="#">test</a><a href="#" onclick="this.href=\'mailto:\'+ this.innerHTML.split(\'\').reverse().join(\'\')" style="unicode-bidi:bidi-override;direction:rtl">moc.tset-tset.tset@tset-tset.tset</a>&#x200E;<a href="#">test</a>',
			'email_02' => '<a href="mailto:test@test.com"><span></span>test@test.com</a>',
			'resolve_01' => '<img src="/pages/image.jpg" srcset="/pages/image.jpg 500w, /pages/image_large.jpg 1200w"><a href="/index.php/test">Test</a>'
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
	 *	@dataProvider dataForTestHeadlessRenderIsEqual
	 *	@testdox render $template: $expected
	 */

	public function testHeadlessRenderIsEqual($template, $expected) {
		
		$Mock = new Mock();
		$View = new View($Mock->createAutomad(), true, AM_BASE_DIR . '/automad/tests/templates/' . $template . '.php');
		$rendered = $View->render();
		
		$this->assertEquals($rendered, $expected);
		
	}
	
	
	public function dataForTestHeadlessRenderIsEqual() {
		
		$data = array();
		$templates = array(
			'headless_01' => '{ "quoted": "\"Quoted\" \"Test\" \"String\"" }',
			'headless_02' => '{ "text": "<img src=\"/pages/image.jpg\" srcset=\"/pages/image.jpg 500w, /pages/image_large.jpg 1200w\"><a href=\"/index.php/test\">Test</a>" }',
			'headless_03' => '{ "text": "This is a\\\\nmultiline test." }'
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
	 *	@dataProvider dataForTestHeadlessRenderArrayIsEqual
	 *	@testdox render $template: $expected
	 */

	public function testHeadlessRenderArrayIsEqual($template, $expected) {
		
		$Mock = new Mock();
		$View = new View($Mock->createAutomad(), true, AM_BASE_DIR . '/automad/tests/templates/' . $template . '.php');
		$rendered = $View->render();
		
		$this->assertEquals(json_decode($rendered, true), $expected);
		
	}
	
	
	public function dataForTestHeadlessRenderArrayIsEqual() {
		
		$data = array();
		$templates = array(
			'headless_01' => array('quoted' => '"Quoted" "Test" "String"'),
			'headless_02' => array('text' => '<img src="/pages/image.jpg" srcset="/pages/image.jpg 500w, /pages/image_large.jpg 1200w"><a href="/index.php/test">Test</a>'),
			'headless_03' => array('text' => 'This is a\nmultiline test.')
		);
		
		foreach ($templates as $template => $expected) {
			
			$data[] = array(
				$template,
				$expected
			);
			
		}
		
		return $data;
		
	}
	
	
}
