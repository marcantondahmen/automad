<?php

namespace Automad\Console;

use Automad\Console\Commands\ConfigSet;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Console\ArgumentCollection
 */
class ArgumentCollectionTest extends TestCase {
	public function dataForTestParseArgvIsEqual() {
		return array(
			array(
				array('automad/console', 'config:set', '--key', 'AM_KEY', '--value', 'value'),
				array('key=AM_KEY', 'value=value'),
			),
			array(
				array('automad/console', 'config:set', '--key', 'AM_KEY'),
				array('key=', 'value='),
			),
		);
	}

	public function dataForTestValidateArgvIsEqual() {
		return array(
			array(
				array('automad/console', 'config:set', '--key', 'AM_KEY', '--value', 'value'),
				true,
			),
			array(
				array('automad/console', 'config:set', '--key', 'AM_KEY'),
				false
			),
		);
	}

	/**
	 * @dataProvider dataForTestParseArgvIsEqual
	 * @testdox parseArgv($argv) equals $expected
	 * @param mixed $argv
	 * @param mixed $expected
	 */
	public function testParseArgvIsEqual($argv, $expected) {
		$command = new ConfigSet();

		ob_start();
		$command->ArgumentCollection->parseArgv($argv);
		ob_end_clean();

		$result = array_map(function ($Argument) {
			return "$Argument->name=$Argument->value";
		}, $command->ArgumentCollection->args);

		/** @disregard */
		$this->assertEquals($result, $expected);
	}

	/**
	 * @dataProvider dataForTestValidateArgvIsEqual
	 * @testdox validateArgv($argv) equals $expected
	 * @param mixed $argv
	 * @param bool $expected
	 */
	public function testValidateArgvIsEqual($argv, $expected) {
		$command = new ConfigSet();

		ob_start();

		/** @disregard */
		$this->assertEquals($command->ArgumentCollection->validateArgv($argv), $expected);

		ob_end_clean();
	}
}
