<?php

namespace Automad\System;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase {
	public static function dataForTestIsValidPackageNameRejectsShellAndOptionInput(): array {
		return array(
			array('automad/standard-lite', true),
			array('vendor/my-package.name', true),
			array('a/b; rm -rf /', false),
			array('a/b && x', false),
			array('--help', false),
			array('a/b --dev', false),
			array('a b/c', false),
			array('$(x)/y', false),
			array('vendor', false)
		);
	}

	#[DataProvider('dataForTestIsValidPackageNameRejectsShellAndOptionInput')]
	public function testIsValidPackageNameRejectsShellAndOptionInput(string $name, bool $expected): void {
		/** @disregard */
		$this->assertSame($expected, Package::isValidPackageName($name));
	}
}
