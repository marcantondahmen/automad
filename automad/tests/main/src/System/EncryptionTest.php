<?php

namespace Automad\System;

use PHPUnit\Framework\TestCase;

class EncryptionTest extends TestCase {
	private array $env;

	private array $files = array();

	protected function setUp(): void {
		$this->env = array();

		foreach (array(Encryption::ENV_KEY, Encryption::ENV_KEY_FILE) as $name) {
			$this->env[$name] = array(getenv($name), $_SERVER[$name] ?? null);
			putenv($name);
			unset($_SERVER[$name]);
		}

		Encryption::loadKey();
	}

	protected function tearDown(): void {
		array_map('unlink', array_filter($this->files, 'is_file'));

		foreach ($this->env as $name => list($env, $server)) {
			putenv($env === false ? $name : "$name=$env");

			if ($server !== null) {
				$_SERVER[$name] = $server;
			}
		}

		Encryption::loadKey();
	}

	public function testDisabledWithoutKey() {
		/** @disregard */
		$this->assertFalse(Encryption::isEnabled());
		/** @disregard */
		$this->assertNull(Encryption::encrypt('secret'));
	}

	public function testDisabledWithShortKey() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH - 1));
		Encryption::loadKey();

		/** @disregard */
		$this->assertFalse(Encryption::isEnabled());
		/** @disregard */
		$this->assertNull(Encryption::encrypt('secret'));
	}

	public function testEncryptAndDecrypt() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();

		foreach (array('O:*:"~":1:{s:6:"apiKey";s:6:"secret";}', '{"AM_MAIL_SMTP_PASSWORD": "secret"}', '') as $value) {
			$encrypted = strval(Encryption::encrypt($value));

			/** @disregard */
			$this->assertStringStartsWith(Encryption::PREFIX, $encrypted);
			/** @disregard */
			$this->assertTrue(Encryption::isEncrypted($encrypted));
			/** @disregard */
			$this->assertSame($value, Encryption::decrypt($encrypted));
		}

		/** @disregard */
		$this->assertNotSame(Encryption::encrypt('secret'), Encryption::encrypt('secret'));
	}

	public function testDecryptWithWrongOrMissingKey() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();
		$encrypted = strval(Encryption::encrypt('secret'));

		putenv(Encryption::ENV_KEY . '=' . str_repeat('b', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();
		/** @disregard */
		$this->assertNull(Encryption::decrypt($encrypted));

		putenv(Encryption::ENV_KEY);
		Encryption::loadKey();
		/** @disregard */
		$this->assertNull(Encryption::decrypt($encrypted));
	}

	public function testDecryptTamperedData() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();
		$encrypted = strval(Encryption::encrypt('secret'));
		$raw = base64_decode(substr($encrypted, strlen(Encryption::PREFIX)));
		$raw[strlen($raw) - 1] = chr(ord($raw[strlen($raw) - 1]) ^ 1);

		/** @disregard */
		$this->assertNull(Encryption::decrypt(Encryption::PREFIX . base64_encode($raw)));
		/** @disregard */
		$this->assertNull(Encryption::decrypt(Encryption::PREFIX . 'invalid'));
		/** @disregard */
		$this->assertNull(Encryption::decrypt('amenc:v2:' . substr($encrypted, strlen(Encryption::PREFIX))));
	}

	public function testIsEncrypted() {
		/** @disregard */
		$this->assertFalse(Encryption::isEncrypted('{"AM_MAIL_SMTP_PASSWORD": "secret"}'));
		/** @disregard */
		$this->assertFalse(Encryption::isEncrypted('O:*:"~":1:{s:6:"apiKey";s:6:"secret";}'));
		/** @disregard */
		$this->assertFalse(Encryption::isEncrypted('a:0:{}'));
		/** @disregard */
		$this->assertFalse(Encryption::isEncrypted(''));
	}

	public function testKeyFileAbsolutePath() {
		$file = $this->createKeyFile(FileSystem::getTmpDir(), str_repeat('a', Encryption::KEY_MIN_LENGTH) . "\n");
		putenv(Encryption::ENV_KEY_FILE . "=$file");
		Encryption::loadKey();

		/** @disregard */
		$this->assertTrue(Encryption::isEnabled());
		/** @disregard */
		$this->assertSame('secret', Encryption::decrypt(strval(Encryption::encrypt('secret'))));
	}

	public function testKeyFileRelativePath() {
		$file = $this->createKeyFile(AM_BASE_DIR . '/automad/tests/main', str_repeat('a', Encryption::KEY_MIN_LENGTH));
		putenv(Encryption::ENV_KEY_FILE . '=automad/tests/main/' . basename($file));
		Encryption::loadKey();

		/** @disregard */
		$this->assertTrue(Encryption::isEnabled());

		$encrypted = strval(Encryption::encrypt('secret'));

		putenv(Encryption::ENV_KEY_FILE);
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();

		/** @disregard */
		$this->assertSame('secret', Encryption::decrypt($encrypted));
	}

	public function testKeyFileMissingOrTooShort() {
		putenv(Encryption::ENV_KEY_FILE . '=' . FileSystem::getTmpDir() . '/missing-' . uniqid() . '.key');
		Encryption::loadKey();

		/** @disregard */
		$this->assertFalse(Encryption::isEnabled());

		$file = $this->createKeyFile(FileSystem::getTmpDir(), str_repeat('a', Encryption::KEY_MIN_LENGTH - 1) . "\n");
		putenv(Encryption::ENV_KEY_FILE . "=$file");
		Encryption::loadKey();

		/** @disregard */
		$this->assertFalse(Encryption::isEnabled());
	}

	public function testKeyFileIsReadOnce() {
		$file = $this->createKeyFile(FileSystem::getTmpDir(), str_repeat('a', Encryption::KEY_MIN_LENGTH));
		putenv(Encryption::ENV_KEY_FILE . "=$file");
		Encryption::loadKey();

		$encrypted = strval(Encryption::encrypt('secret'));
		unlink($file);

		/** @disregard */
		$this->assertTrue(Encryption::isEnabled());
		/** @disregard */
		$this->assertSame('secret', Encryption::decrypt($encrypted));

		Encryption::loadKey();

		/** @disregard */
		$this->assertFalse(Encryption::isEnabled());
	}

	public function testKeyFromServer() {
		$_SERVER['REDIRECT_' . Encryption::ENV_KEY_FILE] = $this->createKeyFile(FileSystem::getTmpDir(), str_repeat('a', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();

		try {
			/** @disregard */
			$this->assertTrue(Encryption::isEnabled());
		} finally {
			unset($_SERVER['REDIRECT_' . Encryption::ENV_KEY_FILE]);
		}
	}

	public function testKeyTakesPrecedenceOverKeyFile() {
		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));
		Encryption::loadKey();
		$encrypted = strval(Encryption::encrypt('secret'));

		$file = $this->createKeyFile(FileSystem::getTmpDir(), str_repeat('b', Encryption::KEY_MIN_LENGTH));
		putenv(Encryption::ENV_KEY_FILE . "=$file");
		Encryption::loadKey();

		/** @disregard */
		$this->assertSame('secret', Encryption::decrypt($encrypted));

		putenv(Encryption::ENV_KEY);
		Encryption::loadKey();

		/** @disregard */
		$this->assertNull(Encryption::decrypt($encrypted));
	}

	private function createKeyFile(string $dir, string $key): string {
		$file = "$dir/encryption-test-" . uniqid() . '.key';
		file_put_contents($file, $key);
		$this->files[] = $file;

		return $file;
	}
}
