<?php

namespace Automad\Auth\Session;

use PHPUnit\Framework\TestCase;

class PasswordResetCodeTest extends TestCase {
	protected function tearDown(): void {
		PasswordResetCode::reset('test-user');
	}

	public function testIsThrottledRightAfterCodeWasCreated(): void {
		/** @disregard */
		$this->assertFalse(PasswordResetCode::isThrottled('test-user'));

		new PasswordResetCode('test-user', PasswordResetCode::generate());

		/** @disregard */
		$this->assertTrue(PasswordResetCode::isThrottled('test-user'));
	}
}
