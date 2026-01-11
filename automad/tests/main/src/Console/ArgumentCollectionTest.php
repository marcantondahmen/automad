<?php

namespace Automad\Console;

use Automad\Console\Commands\ConfigSet;
use Automad\Console\Commands\LogPath;
use Automad\Console\Commands\UserCreate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArgumentCollectionTest extends TestCase {
	public static function dataForTestParseArgvIsEqual() {
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
				array('email=test@test.local', 'username=', 'password='),
				true
			)
		);
	}

	public static function dataForTestValidateArgvIsEqual() {
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

	public static function dataForTestValueIsEqual() {
		return array(
			array(
				ConfigSet::class,
				array('automad/console', 'config:set', '--key', 'AM_KEY', '--value', 'value'),
				'key',
				'AM_KEY'
			),
			array(
				LogPath::class,
				array('automad/console', 'log:path', '--help'),
				'help',
				''
			),
			array(
				LogPath::class,
				array('automad/console', 'log:path'),
				'help',
				null
			),
		);
	}

	#[DataProvider('dataForTestParseArgvIsEqual')]
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

	#[DataProvider('dataForTestValidateArgvIsEqual')]
	public function testValidateArgvIsEqual($cls, $argv, $expected) {
		$command = new $cls();

		ob_start();

		/** @disregard */
		$this->assertEquals($command->ArgumentCollection->validateArgv($argv), $expected);

		ob_end_clean();
	}

	#[DataProvider('dataForTestValueIsEqual')]
	public function testValueIsEqual($cls, $argv, $arg, $expected) {
		$command = new $cls();
		$command->ArgumentCollection->parseArgv($argv);

		/** @disregard */
		$this->assertEquals($command->ArgumentCollection->get($arg)->value, $expected);
	}
}
