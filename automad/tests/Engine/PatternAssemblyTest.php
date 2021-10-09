<?php

namespace Automad\Engine;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Engine\PatternAssembly
 */
class PatternAssemblyTest extends TestCase {
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

	/**
	 * @dataProvider dataForTestCsvIsSame
	 * @testdox csv() matches: $str
	 * @param mixed $str
	 * @param mixed $expected
	 */
	public function testCsvIsSame($str, $expected) {
		$result = array();

		preg_match_all('/' . PatternAssembly::csv() . '/', $str, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			$result[] = $match[1];
		}

		for ($i = 0; $i < count($result); $i++) {
			$this->assertSame($result[$i], $expected[$i]);
		}
	}

	/**
	 * @dataProvider dataForTestExpressionHasArraySubset
	 * @testdox expression("$prefix") matches: $str
	 * @param mixed $str
	 * @param mixed $prefix
	 * @param mixed $expected
	 */
	public function testExpressionHasArraySubset($str, $prefix, $expected) {
		preg_match_all('/' . PatternAssembly::expression($prefix) . '/', $str, $matches, PREG_SET_ORDER);

		$result = $matches[0];

		foreach ($expected as $key => $value) {
			$this->assertSame($value, $result[$key]);
		}
	}

	/**
	 * @dataProvider dataForTestKeyValueHasArraySubset
	 * @testdox keyValue() matches: $str
	 * @param mixed $str
	 * @param mixed $expected
	 */
	public function testKeyValueHasArraySubset($str, $expected) {
		preg_match_all('/' . PatternAssembly::keyValue() . '/is', $str, $matches, PREG_SET_ORDER);

		foreach ($expected as $index => $subarray) {
			foreach ($subarray as $key => $value) {
				$this->assertSame($value, $matches[$index][$key]);
			}
		}
	}

	/**
	 * @dataProvider dataForTestMarkupHasArraySubset
	 * @testdox markup() matches: $str
	 * @param mixed $str
	 * @param mixed $expected
	 * @param mixed $prefix
	 */
	public function testMarkupHasArraySubset($str, $expected) {
		preg_match_all('/' . PatternAssembly::template() . '/is', $str, $matches, PREG_SET_ORDER);

		$result = $matches[0];

		foreach ($expected as $key => $value) {
			$this->assertSame($value, $result[$key]);
		}
	}

	/**
	 * @dataProvider dataForTestPipeHasArraySubset
	 * @testdox pipe("$prefix") matches: $str
	 * @param mixed $str
	 * @param mixed $prefix
	 * @param mixed $expected
	 */
	public function testPipeHasArraySubset($str, $prefix, $expected) {
		preg_match_all('/' . PatternAssembly::pipe($prefix) . '/i', $str, $matches, PREG_SET_ORDER);

		foreach ($expected as $index => $subarray) {
			foreach ($subarray as $key => $value) {
				$this->assertSame($value, $matches[$index][$key]);
			}
		}
	}

	/**
	 * @dataProvider dataForTestVariableHasArraySubset
	 * @testdox variable("$prefix") matches: $str
	 * @param mixed $str
	 * @param mixed $prefix
	 * @param mixed $expected
	 */
	public function testVariableHasArraySubset($str, $prefix, $expected) {
		preg_match_all('/' . PatternAssembly::variable($prefix) . '/i', $str, $matches, PREG_SET_ORDER);

		$result = $matches[0];

		foreach ($expected as $key => $value) {
			$this->assertSame($value, $result[$key]);
		}
	}
}
