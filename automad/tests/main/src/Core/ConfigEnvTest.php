<?php

namespace Automad\Core;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigEnvTest extends TestCase {
	public static function dataForTestCastEnvValue() {
		return array(
			array('true', true),
			array('FALSE', false),
			array('8000', 8000.0),
			array('1.5', 1.5),
			array('Yes', 'Yes'),
			array('off', 'off'),
			array('', ''),
			array('Some text', 'Some text')
		);
	}

	public static function dataForTestParseEnv() {
		return array(
			array('K=abc+/def=', array('K' => 'abc+/def=')),
			array('K=a!b&c', array('K' => 'a!b&c')),
			array('K=PHP_VERSION', array('K' => 'PHP_VERSION')),
			array('K=${HOME}', array('K' => '${HOME}')),
			array('K="abc="', array('K' => 'abc=')),
			array("K='abc='", array('K' => 'abc=')),
			array("K='", array('K' => "'")),
			array("# comment\n; comment\nA=1\nB=two", array('A' => '1', 'B' => 'two')),
			array('[', array())
		);
	}

	#[DataProvider('dataForTestCastEnvValue')]
	public function testCastEnvValue(string $value, bool|float|string $expected) {
		/** @disregard */
		$this->assertSame($expected, Config::castEnvValue($value));
	}

	#[DataProvider('dataForTestParseEnv')]
	public function testParseEnv(string $content, array $expected) {
		/** @disregard */
		$this->assertSame($expected, Config::parseEnv($content));
	}
}
