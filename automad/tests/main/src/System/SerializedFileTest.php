<?php

namespace Automad\System;

use PHPUnit\Framework\TestCase;

class SerializedFileFixture {
	public string $secret;

	public function __construct(string $secret) {
		$this->secret = $secret;
	}
}

class SerializedFileTest extends TestCase {
	private array $env;

	private string $file;

	protected function setUp(): void {
		$this->env = array();

		foreach (array(Encryption::ENV_KEY, Encryption::ENV_KEY_FILE) as $name) {
			$this->env[$name] = array(getenv($name), $_SERVER[$name] ?? null);
			putenv($name);
			unset($_SERVER[$name]);
		}

		$this->file = FileSystem::getTmpDir() . '/serialized-file-test-' . uniqid() . '.php';
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

	public function testBlocksOtherClasses() {
		FileSystem::writePhpString($this->file, serialize(new \ArrayObject(array('secret'))));
		$value = (new SerializedFile($this->file, SerializedFileFixture::class))->read();

		/** @disregard */
		$this->assertInstanceOf(\__PHP_Incomplete_Class::class, $value);
	}

	public function testDelete() {
		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class);
		$SerializedFile->write(new SerializedFileFixture('secret'));

		/** @disregard */
		$this->assertTrue($SerializedFile->delete());
		/** @disregard */
		$this->assertFileDoesNotExist($this->file);
		/** @disregard */
		$this->assertTrue($SerializedFile->delete());
	}

	public function testEncodeMatchesWrite() {
		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class);
		$Fixture = new SerializedFileFixture('secret');
		$SerializedFile->write($Fixture);

		/** @disregard */
		$this->assertSame($SerializedFile->encode($Fixture), file_get_contents($this->file));
	}

	public function testEncrypted() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));

		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class, true);
		$SerializedFile->write(new SerializedFileFixture('secret'));
		$contents = strval(file_get_contents($this->file));

		/** @disregard */
		$this->assertStringContainsString(Encryption::PREFIX, $contents);
		/** @disregard */
		$this->assertStringNotContainsString('secret', $contents);
		/** @disregard */
		$this->assertEquals(new SerializedFileFixture('secret'), $SerializedFile->read());

		putenv(Encryption::ENV_KEY);

		/** @disregard */
		$this->assertNull($SerializedFile->read());
	}

	public function testNotEncryptedWithKey() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));

		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class);
		$SerializedFile->write(new SerializedFileFixture('secret'));

		/** @disregard */
		$this->assertStringNotContainsString(Encryption::PREFIX, strval(file_get_contents($this->file)));
		/** @disregard */
		$this->assertEquals(new SerializedFileFixture('secret'), $SerializedFile->read());
	}

	public function testReadFullClassName() {
		FileSystem::writePhpString($this->file, serialize(new SerializedFileFixture('secret')));

		/** @disregard */
		$this->assertEquals(
			new SerializedFileFixture('secret'),
			(new SerializedFile($this->file, SerializedFileFixture::class))->read()
		);
	}

	public function testReadInvalid() {
		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class);

		/** @disregard */
		$this->assertNull($SerializedFile->read());

		FileSystem::writePhpString($this->file, 'O:*:"~":1:{s:6:"secret";');

		/** @disregard */
		$this->assertNull($SerializedFile->read());
	}

	public function testWriteAndReadArray() {
		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class);
		$fixtures = array(new SerializedFileFixture('a'), new SerializedFileFixture('b'));

		/** @disregard */
		$this->assertTrue($SerializedFile->write($fixtures));
		/** @disregard */
		$this->assertSame(2, substr_count(strval(file_get_contents($this->file)), SerializedFile::PLACEHOLDER));
		/** @disregard */
		$this->assertEquals($fixtures, $SerializedFile->read());
	}

	public function testWriteAndReadObject() {
		$SerializedFile = new SerializedFile($this->file, SerializedFileFixture::class);

		/** @disregard */
		$this->assertTrue($SerializedFile->write(new SerializedFileFixture('secret')));

		$contents = strval(file_get_contents($this->file));

		/** @disregard */
		$this->assertStringContainsString(SerializedFile::PLACEHOLDER, $contents);
		/** @disregard */
		$this->assertStringNotContainsString('SerializedFileFixture', $contents);
		/** @disregard */
		$this->assertEquals(new SerializedFileFixture('secret'), $SerializedFile->read());
	}
}
