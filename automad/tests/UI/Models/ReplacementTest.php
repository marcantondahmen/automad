<?php

namespace Automad\UI\Models;

use Automad\Core\Parse;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\UI\Models\ReplacementModel
 */
class ReplacementTest extends TestCase {
	public function dataForTestReplaceInDataIsSame() {
		return array(
			// Blocks, no regex, not case sensitive.
			array(
				'simple',
				'replaced',
				false,
				false,
				array('+main'),
				Parse::dataFile(__DIR__ . '/../../data/blocks.txt'),
				Parse::dataFile(__DIR__ . '/../../data/blocks_replaced.txt')
			),
			// Blocks, regex, not case sensitive.
			array(
				'si.p.e',
				'replaced',
				true,
				false,
				array('+main'),
				Parse::dataFile(__DIR__ . '/../../data/blocks.txt'),
				Parse::dataFile(__DIR__ . '/../../data/blocks_replaced.txt')
			),
			// Text, no regex, not case sensitive.
			array(
				'/Url/to/Page',
				'/replaced/url/to/page',
				false,
				false,
				array('text'),
				Parse::dataFile(__DIR__ . '/../../data/text.txt'),
				Parse::dataFile(__DIR__ . '/../../data/text_replaced.txt')
			),
			// Text, no regex, case sensitive.
			array(
				'/url/To/page',
				'/replaced/url/to/page',
				false,
				true,
				array('text'),
				Parse::dataFile(__DIR__ . '/../../data/text.txt'),
				Parse::dataFile(__DIR__ . '/../../data/text.txt')
			),
			// Text, regex, case sensitive.
			array(
				'/url/.*/page',
				'/replaced/url/to/page',
				true,
				true,
				array('text'),
				Parse::dataFile(__DIR__ . '/../../data/text.txt'),
				Parse::dataFile(__DIR__ . '/../../data/text_replaced.txt')
			)
		);
	}

	/**
	 * @dataProvider dataForTestReplaceInDataIsSame
	 * @testdox replaceInData()
	 * @param mixed $searchValue
	 * @param mixed $replaceValue
	 * @param mixed $isRegex
	 * @param mixed $isCaseSensitive
	 * @param mixed $keys
	 * @param mixed $data
	 * @param mixed $expected
	 */
	public function testReplaceInDataIsSame($searchValue, $replaceValue, $isRegex, $isCaseSensitive, $keys, $data, $expected) {
		$ReplacementReflection = new \ReflectionClass('\Automad\UI\Models\ReplacementModel');

		$replaceInData = $ReplacementReflection->getMethod('replaceInData');
		$replaceInData->setAccessible(true);

		$ReplacementModel = new ReplacementModel(
			$searchValue,
			$replaceValue,
			$isRegex,
			$isCaseSensitive
		);

		$replacedData = $replaceInData->invokeArgs($ReplacementModel, array($data, $keys));

		$this->assertSame(
			json_encode($replacedData, JSON_PRETTY_PRINT),
			json_encode($expected, JSON_PRETTY_PRINT)
		);
	}
}
