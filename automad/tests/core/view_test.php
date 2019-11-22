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
	 *	@dataProvider dataForTestHeadlessValueIsEqual
	 *	@testdox render $template: $expected
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
		
		$this->assertEquals($array->test, $expected);
		
	}

	public function dataForTestHeadlessValueIsEqual() {
		
		return array(
			array(
				'<img src="image.jpg" srcset="image.jpg 500w, image_large.jpg 1200w"><a href="test">Test</a>',
				'<img src="/pages/image.jpg" srcset="/pages/image.jpg 500w, /pages/image_large.jpg 1200w"><a href="/index.php/test">Test</a>'
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


	/**
	 *	@dataProvider dataForTestHeadlessJSONIsEqual
	 *	@testdox render $template: $expected
	 */

	public function testHeadlessJSONIsEqual($value, $expected) {
		
		$Mock = new Mock();
		$AutomadMock = $Mock->createAutomad();
		$Page = $AutomadMock->Context->get();
		// Set test to $value.
		$Page->data['test'] = $value;
		// Render view in headless mode.
		$View = new View($AutomadMock, true);
		
		$this->assertEquals($View->render(), $expected);
		
	}

	public function dataForTestHeadlessJSONIsEqual() {
		
		return array(
			array(
				'<img src="image.jpg" srcset="image.jpg 500w, image_large.jpg 1200w"><a href="test">Test</a>',
				'{"test": "<img src=\"/pages/image.jpg\" srcset=\"/pages/image.jpg 500w, /pages/image_large.jpg 1200w\"><a href=\"/index.php/test\">Test</a>"}'
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
	
}
