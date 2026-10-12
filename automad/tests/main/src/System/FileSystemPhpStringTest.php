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

	public function testMigratePhpStrings() {
		$dir = FileSystem::getTmpDir() . '/php-string-migration-test-' . uniqid();
		$serialized = 'O:*:"~":1:{s:6:"apiKey";s:6:"secret";}';
		$nowdoc = "<?php return <<< 'JSON'\r\n{\r\n    \"AM_MAIL_SMTP_PASSWORD\": \"secret\"\r\n}\r\nJSON;\r\n";

		FileSystem::makeDir($dir);
		file_put_contents("$dir/legacy.php", $serialized);
		file_put_contents("$dir/config.php", $nowdoc);
		FileSystem::writePhpString("$dir/wrapped.php", $serialized);

		$wrapped = file_get_contents("$dir/wrapped.php");

		/** @disregard */
		$this->assertSame(array("$dir/legacy.php"), FileSystem::migratePhpStrings($dir));
		/** @disregard */
		$this->assertStringStartsWith("<?php\ndefined('AUTOMAD') or die();", strval(file_get_contents("$dir/legacy.php")));
		/** @disregard */
		$this->assertSame($serialized, FileSystem::readPhpString("$dir/legacy.php"));
		/** @disregard */
		$this->assertSame($nowdoc, file_get_contents("$dir/config.php"));
		/** @disregard */
		$this->assertSame($wrapped, file_get_contents("$dir/wrapped.php"));
		/** @disregard */
		$this->assertSame(array(), FileSystem::migratePhpStrings($dir));

		array_map('unlink', FileSystem::glob("$dir/*.php"));
		rmdir($dir);
	}

	public function testReadLegacyFileDoesNotWrite() {
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
		$this->assertSame($serialized, file_get_contents($this->file));
	}

	public function testReadLegacyNowdocConfig() {
		$json = "{\r\n    \"AM_MAIL_SMTP_PASSWORD\": \"secret\"\r\n}";
		file_put_contents($this->file, "<?php return <<< 'JSON'\r\n$json\r\nJSON;\r\n");

		ob_start();
		$data = FileSystem::readPhpString($this->file);
		$output = ob_get_clean();

		/** @disregard */
		$this->assertSame('', $output);
		/** @disregard */
		$this->assertSame(array('AM_MAIL_SMTP_PASSWORD' => 'secret'), json_decode(strval($data), true));
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
