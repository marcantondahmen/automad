<?php

namespace Automad\System\Setup\Migrations;

use Automad\System\Encryption;
use Automad\System\FileSystem;
use PHPUnit\Framework\TestCase;

class EncryptConfigFilesTest extends TestCase {
	private string $dir;

	private array $env;

	protected function setUp(): void {
		$this->env = array();

		foreach (array(Encryption::ENV_KEY, Encryption::ENV_KEY_FILE) as $name) {
			$this->env[$name] = array(getenv($name), $_SERVER[$name] ?? null);
			putenv($name);
			unset($_SERVER[$name]);
		}

		$this->dir = FileSystem::getTmpDir() . '/encrypt-config-files-test-' . uniqid();
		FileSystem::makeDir($this->dir);
	}

	protected function tearDown(): void {
		foreach ($this->env as $name => list($env, $server)) {
			putenv($env === false ? $name : "$name=$env");

			if ($server !== null) {
				$_SERVER[$name] = $server;
			}
		}

		array_map('unlink', FileSystem::glob($this->dir . '/*.php'));
		rmdir($this->dir);
	}

	public function testRun() {
		$serialized = 'O:*:"~":1:{s:6:"apiKey";s:6:"secret";}';
		$secrets = array('ai.claude.php', 'ai.openai.php', 'composer.auth.php', 'config.mail.php', 'tokens.auth.php');
		$untouched = array('config.php', 'accounts.php', '_accounts.php');

		foreach (array_merge($secrets, $untouched) as $name) {
			FileSystem::writePhpString("{$this->dir}/$name", $serialized);
		}

		$snapshot = $this->snapshot();
		$Migration = new EncryptConfigFiles($this->dir);
		$Migration->run();

		/** @disregard */
		$this->assertSame(array(), $Migration->getMigrated());
		/** @disregard */
		$this->assertSame($snapshot, $this->snapshot());

		putenv(Encryption::ENV_KEY . '=' . str_repeat('a', Encryption::KEY_MIN_LENGTH));
		$Migration->run();
		$migrated = array_map('basename', $Migration->getMigrated());
		sort($migrated);

		/** @disregard */
		$this->assertSame($secrets, $migrated);

		foreach ($secrets as $name) {
			$contents = strval(file_get_contents("{$this->dir}/$name"));

			/** @disregard */
			$this->assertStringContainsString(Encryption::PREFIX, $contents);
			/** @disregard */
			$this->assertStringNotContainsString('secret', $contents);
			/** @disregard */
			$this->assertSame($serialized, FileSystem::readPhpString("{$this->dir}/$name"));
		}

		foreach ($untouched as $name) {
			/** @disregard */
			$this->assertSame($snapshot[$name], file_get_contents("{$this->dir}/$name"));
		}

		$snapshot = $this->snapshot();
		$Migration->run();

		/** @disregard */
		$this->assertSame(array(), $Migration->getMigrated());
		/** @disregard */
		$this->assertSame($snapshot, $this->snapshot());
	}

	private function snapshot(): array {
		$snapshot = array();

		foreach (FileSystem::glob($this->dir . '/*.php') as $file) {
			$snapshot[basename($file)] = file_get_contents($file);
		}

		return $snapshot;
	}
}
