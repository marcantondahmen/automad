<?php

namespace Automad\System;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileSystemPhpStringTest extends TestCase {
	private array $env;

	private string $file;

	protected function setUp(): void {
		$this->env = array();

		foreach (array(Encryption::ENV_KEY, Encryption::ENV_KEY_FILE) as $name) {
			$this->env[$name] = array(getenv($name), $_SERVER[$name] ?? null);
			putenv($name);
			unset($_SERVER[$name]);
		}
		$this->file = FileSystem::getTmpDir() . '/php-string-test-' . uniqid() . '.php';
	}

	protected function tearDown(): void {
		foreach ($this->env as $name => list($env, $server)) {
			putenv($env === false ? $name : "$name=$env");

			if ($server !== null) {
				$_SERVER[$name] = $server;
			}
		}

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

	#[DataProvider('dataForTestWriteAndRead')]
	public function testWriteAndReadEncrypted(string $value) {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));

		/** @disregard */
		$this->assertTrue(FileSystem::writePhpString($this->file, $value, true));

		$contents = strval(file_get_contents($this->file));

		/** @disregard */
		$this->assertStringStartsWith("<?php\ndefined('AUTOMAD') or die();\nreturn '" . Encryption::PREFIX, $contents);

		if ($value) {
			/** @disregard */
			$this->assertStringNotContainsString(var_export($value, true), $contents);
		}

		ob_start();
		$data = FileSystem::readPhpString($this->file);
		$output = ob_get_clean();

		/** @disregard */
		$this->assertSame($value, $data);
		/** @disregard */
		$this->assertSame('', $output);

		putenv(Encryption::ENV_KEY);

		/** @disregard */
		$this->assertNull(FileSystem::readPhpString($this->file));
	}

	public function testWriteEncryptedWithoutKeyIsPlainText() {
		$value = 'O:*:"~":1:{s:6:"apiKey";s:6:"secret";}';

		/** @disregard */
		$this->assertTrue(FileSystem::writePhpString($this->file, $value, true));
		/** @disregard */
		$this->assertSame(FileSystem::encodePhpString($value), file_get_contents($this->file));
		/** @disregard */
		$this->assertSame($value, FileSystem::readPhpString($this->file));
	}
}
