<?php

namespace Automad\Engine\Processors;

use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomizationProcessorTest extends TestCase {
	public static function dataForTestAddIsEqual() {
		return array(
			array(
				array(
					'customHTMLHead' => '<title>Test</title>',
				),
				array(
					'customHTMLBodyEnd' => 'end'
				),
				<<< HTML
				<html>
				<head><title>Test</title></head>
				<body>end</body>
				</html>	
				HTML
			),
			array(
				array(
					'customCSS' => <<< CSS
					body {
						color: red;	
					}
					CSS,
					'customJSHead' => <<< JS
					const test2 = "test page";

					console.log(test2);
					JS
				),
				array(
					'customCSS' => <<< CSS
					.shared {
						color: blue;	
					}
					CSS,
					'customJSHead' => <<< JS
					const test1 = "shared"; // Comment
					JS
				),
				<<< HTML
				<html>
				<head><script>const test1 = "shared"; // Comment
				const test2 = "test page";
				
				console.log(test2);</script><style>.shared{color:blue;}
				body{color:red;}</style></head>
				<body></body>
				</html>	
				HTML
			)
		);
	}

	#[DataProvider('dataForTestAddIsEqual')]
	public function testAddIsEqual($customPage, $customShared, $expected) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$CustomizationProcessor = new CustomizationProcessor($Automad);
		$Shared = $Automad->Shared;
		$Page = $Automad->Context->get();

		$template = <<< HTML
		<html>
		<head></head>
		<body></body>
		</html>	
		HTML;

		$Shared->data = $customShared;
		$Page->data = $customPage;

		/** @disregard */
		$this->assertEquals($CustomizationProcessor->addCustomizations($template), $expected);
	}
}
