<?php

namespace Automad\System;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileSystemPhpStringTest extends TestCase {
	private string $file;

	protected function setUp(): void {
		$this->file = FileSystem::getTmpDir() . '/php-string-test-' . uniqid() . '.php';
	}

	protected function tearDown(): void {
		if (is_file($this->file)) {
			unlink($this->file);
		}
	}

	public static function dataForTestWriteAndRead() {
		return array(
			array('O:*:"~":1:{s:6:"apiKey";s:6:"secret";}'),
			array("single ' and double \" quotes"),
			array('back\\slash and trailing backslash\\'),
			array("null \0 byte and\nnew line"),
			array("'; echo 'injected'; '"),
			array('')
		);
	}

	public function testReadLegacyFileMigratesToPhp() {
		$serialized = 'O:*:"~":1:{s:6:"apiKey";s:6:"secret";}';
		file_put_contents($this->file, $serialized);

		ob_start();
		$data = FileSystem::readPhpString($this->file);
		$output = ob_get_clean();

		/** @disregard */
		$this->assertSame($serialized, $data);
		/** @disregard */
		$this->assertSame('', $output);
		/** @disregard */
		$this->assertStringStartsWith("<?php\ndefined('AUTOMAD') or die();", strval(file_get_contents($this->file)));
		/** @disregard */
		$this->assertSame($serialized, FileSystem::readPhpString($this->file));
	}

	public function testReadMissingFileReturnsNull() {
		/** @disregard */
		$this->assertNull(FileSystem::readPhpString($this->file));
	}

	#[DataProvider('dataForTestWriteAndRead')]
	public function testWriteAndRead(string $value) {
		/** @disregard */
		$this->assertTrue(FileSystem::writePhpString($this->file, $value));

		$contents = strval(file_get_contents($this->file));

		/** @disregard */
		$this->assertStringStartsWith("<?php\ndefined('AUTOMAD') or die();", $contents);

		ob_start();
		$data = FileSystem::readPhpString($this->file);
		$output = ob_get_clean();

		/** @disregard */
		$this->assertSame($value, $data);
		/** @disregard */
		$this->assertSame('', $output);
	}
}
