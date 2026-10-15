<?php

namespace Automad\Auth\Session;

use Automad\System\FileSystem;
use PHPUnit\Framework\TestCase;

class LoginRateLimiterTest extends TestCase {
	private string $staleFile = AM_LOGIN_RATE_LIMITER_PATH . '/stale.json';

	protected function setUp(): void {
		FileSystem::writeJson($this->staleFile, array('attempts' => array(), 'blockedUntil' => 0));
		touch($this->staleFile, time() - LoginRateLimiter::BLOCK_TIME - 1);
	}

	protected function tearDown(): void {
		LoginRateLimiter::reset('test-user');

		if (file_exists($this->staleFile)) {
			unlink($this->staleFile);
		}
	}

	public function testStaleFilesArePruned(): void {
		LoginRateLimiter::registerFailure('test-user');

		/** @disregard */
		$this->assertFileDoesNotExist($this->staleFile);

		/** @disregard */
		$this->assertFileExists(AM_LOGIN_RATE_LIMITER_PATH . '/' . sha1('test-user') . '.json');
	}
}
