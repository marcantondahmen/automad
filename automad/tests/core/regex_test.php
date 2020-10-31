<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\Core\Regex
 */

class Regex_Test extends TestCase {
	
	
	/**
	 *	@dataProvider dataForTestCsvIsSame
	 *	@testdox csv() matches: $str
	 */
	
	public function testCsvIsSame($str, $expected) {
		
		$result = array();
		
		preg_match_all('/' . Regex::csv() . '/', $str, $matches, PREG_SET_ORDER);
		
		foreach ($matches as $match) {
			$result[] = $match[1];
		}
		
		$this->assertSame($result, $expected);
		
	}
	
	public function dataForTestCsvIsSame() {
		
		return array(
			array(
				'"String", 10, @{ var | function (parameter, @{ var }) }', 
				array(
					'"String"', 
					'10',
					'@{ var | function (parameter, @{ var }) }'
				)
			),
			array(
				'"Some \"quoted\" string", false',
				array(
					'"Some \"quoted\" string"',
					'false'
				)
			),
			array(
				"'String', 'String \'quoted\'', '@{ var }'",
				array(
					"'String'",
					"'String \'quoted\''",
					"'@{ var }'"
				)
			)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestExpressionHasArraySubset
	 *	@testdox expression("$prefix") matches: $str
	 */
	
	public function testExpressionHasArraySubset($str, $prefix, $expected) {
			
		preg_match_all('/' . Regex::expression($prefix) . '/', $str, $matches, PREG_SET_ORDER);
		$this->assertArraySubset($expected, $matches[0]);
		
	}
	
	public function dataForTestExpressionHasArraySubset() {
		
		return array(
			array(
				'@{ var } > 5', 
				'test', 
				array(
					'testLeftVar' => '@{ var }',
					'testOperator' => '>',
					'testRightNumber' => '5'
				)
			),
			array(
				'not @{ var | 10 }',
				'test',
				array(
					'testNot' => 'not ',
					'testVar' => '@{ var | 10 }'
				)
			),
			array(
				'!@{ var | +2 | *5 }',
				'test',
				array(
					'testNot' => '!',
					'testVar' => '@{ var | +2 | *5 }'
				)
			)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestMarkupHasArraySubset
	 *	@testdox markup() matches: $str
	 */
	
	public function testMarkupHasArraySubset($str, $expected) {
		
		preg_match_all('/' . Regex::markup() . '/is', $str, $matches, PREG_SET_ORDER);
		$this->assertArraySubset($expected, $matches[0]);
		
	}
	
	public function dataForTestMarkupHasArraySubset() {
		
		return array(
			array(
				'<@ path/to/file.php @>',
				array(
					'file' => 'path/to/file.php'
				)
			),
			array(
				'<@ function { key1: "value", key2: @{ var | def("Test") } } @>',
				array(
					'call' => 'function',
					'callOptions' => '{ key1: "value", key2: @{ var | def("Test") } }'
				)
			),
			array(
				'<@# snippet name @>Some snippet.<@# end @>',
				array(
					'snippet' => 'name',
					'snippetSnippet' => 'Some snippet.'
				)
			),
			array(
				'<@# with "*.jpg" { width: 300, height: 200 } @>@{ :file | sanitize }<@# end @>',
				array(
					'with' => '"*.jpg"',
					'withOptions' => '{ width: 300, height: 200 }',
					'withSnippet' => '@{ :file | sanitize }'
				)
			),
			array(
				'<@# for 1 to @{ x } @>@{ :i }<@# end @>',
				array(
					'forStart' => '1',
					'forEnd' => '@{ x }',
					'forSnippet' => '@{ :i }'
				)
			),
			array(
				'<@# foreach in filelist { width: @{ width } } @>@{ :file }<@# end @>',
				array(
					'foreach' => 'filelist',
					'foreachOptions' => '{ width: @{ width } }',
					'foreachSnippet' => '@{ :file }'
				)
			),
			array(
				'<@# if @{ x } > 5 and 10 < @{ y | def(5) } @>True<@# else @>False<@# end @>',
				array(
					'if' => '@{ x } > 5 and 10 < @{ y | def(5) }',
					'ifSnippet' => 'True',
					'ifElseSnippet' => 'False'
				)
			),
			array(
				'@{ var | function (true, "string") | function | 100 }',
				array(
					'var' => '@{ var | function (true, "string") | function | 100 }'
				)
			)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestKeyValueHasArraySubset
	 *	@testdox keyValue() matches: $str
	 */
	
	public function testKeyValueHasArraySubset($str, $expected) {
		
		preg_match_all('/' . Regex::keyValue() . '/is', $str, $matches, PREG_SET_ORDER);
		$this->assertArraySubset($expected, $matches);
		
	}
	
	public function dataForTestKeyValueHasArraySubset() {
		
		return array(
			array(
				'{ "key1": false, key2: @{ var | function ("param") }, key3: "\"Quoted\" Text" }',
				array(
					array(
						'key' => '"key1"',
						'value' => 'false'
					),
					array(
						'key' => 'key2',
						'value' => '@{ var | function ("param") }'
					),array(
						'key' => 'key3',
						'value' => '"\"Quoted\" Text" '
					)
				)
			),
			array(
				"{ key: 'Some text with a @{ variable | function }' }",
				array(
					array(
						'key' => 'key',
						'value' => "'Some text with a @{ variable | function }' "
					)
				)
			)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestPipeHasArraySubset
	 *	@testdox pipe("$prefix") matches: $str
	 */
	
	public function testPipeHasArraySubset($str, $prefix, $expected) {
		
		preg_match_all('/' . Regex::pipe($prefix) . '/i', $str, $matches, PREG_SET_ORDER);
		$this->assertArraySubset($expected, $matches);
		
	}
	
	public function dataForTestPipeHasArraySubset() {
		
		return array(
			array(
				'| function (boolean, "string", 10, @{ var | sanitize }) | +5',
				'test',
				array(
					array(
						'testFunction' => 'function',
						'testParameters' => 'boolean, "string", 10, @{ var | sanitize }'
					),
					array(
						'testOperator' => '+',
						'testNumber' => '5'
					)
				)
			)
		);
		
	}
	
	
	/**
	 *	@dataProvider dataForTestVariableHasArraySubset
	 *	@testdox variable("$prefix") matches: $str
	 */
	
	public function testVariableHasArraySubset($str, $prefix, $expected) {
		
		preg_match_all('/' . Regex::variable($prefix) . '/i', $str, $matches, PREG_SET_ORDER);
		$this->assertArraySubset($expected, $matches[0]);
		
	}
	
	public function dataForTestVariableHasArraySubset() {
		
		return array(
			array(
				'@{ variable | 100 }',
				'test',
				array(
					'testName' => 'variable',
					'testFunctions' => '| 100 '
				)
			),
			array(
				'@{ variable | def (@{ default | sanitize }) | stripTags | 100 }',
				'test',
				array(
					'testName' => 'variable',
					'testFunctions' => '| def (@{ default | sanitize }) | stripTags | 100 '
				)
			)
		);
		
	}

	
}