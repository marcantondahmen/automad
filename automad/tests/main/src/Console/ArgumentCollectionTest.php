<?php

namespace Automad\Console;

use Automad\Console\Commands\ConfigSet;
use Automad\Console\Commands\UserCreate;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Console\ArgumentCollection
 */
class ArgumentCollectionTest extends TestCase {
	public function dataForTestParseArgvIsEqual() {
		return array(
			array(
				ConfigSet::class,
				array('automad/console', 'config:set', '--key', 'AM_KEY', '--value', 'value'),
				array('key=AM_KEY', 'value=value'),
				true
			),
			array(
				ConfigSet::class,
				array('automad/console', 'config:set', '--key', 'AM_KEY'),
				array('key=', 'value='),
				false
			),
			array(
				UserCreate::class,
				array('automad/console', 'user:create', '--email', 'test@test.local'),
				array('username=', 'email=test@test.local', 'password='),
				true
			)
		);
	}

	public function dataForTestValidateArgvIsEqual() {
		return array(
			array(
				ConfigSet::class,
				array('automad/console', 'config:set', '--key', 'AM_KEY', '--value', 'value'),
				true,
			),
			array(
				ConfigSet::class,
				array('automad/console', 'config:set', '--key', 'AM_KEY'),
				false
			),
			array(
				UserCreate::class,
				array('automad/console', 'user:create'),
				true
			),
		);
	}

	/**
	 * @dataProvider dataForTestParseArgvIsEqual
	 * @testdox parseArgv($argv) equals $expected
	 * @param string $cls
	 * @param array $argv
	 * @param array $expected
	 * @param bool $expectedSuccess
	 */
	public function testParseArgvIsEqual($cls, $argv, $expected, $expectedSuccess) {
		$command = new $cls();

		ob_start();
		$success = $command->ArgumentCollection->parseArgv($argv);
		ob_end_clean();

		$result = array_map(function ($Argument) {
			return "$Argument->name=$Argument->value";
		}, $command->ArgumentCollection->args);

		/** @disregard */
		$this->assertEquals($result, $expected);

		/** @disregard */
		$this->assertEquals($success, $expectedSuccess);
	}

	/**
	 * @dataProvider dataForTestValidateArgvIsEqual
	 * @testdox validateArgv($argv) equals $expected
	 * @param string $cls
	 * @param array $argv
	 * @param bool $expected
	 */
	public function testValidateArgvIsEqual($cls, $argv, $expected) {
		$command = new $cls();

		ob_start();

		/** @disregard */
		$this->assertEquals($command->ArgumentCollection->validateArgv($argv), $expected);

		ob_end_clean();
	}
}
