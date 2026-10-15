<?php

namespace Automad\Auth;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class UserTest extends TestCase {
	public function testPasswordUsesPasswordHashAndVerifies(): void {
		$User = new User('user', 'Secret#123');
		$hash = $User->getPasswordHash();

		/** @disregard */
		$this->assertFalse(password_needs_rehash($hash, PASSWORD_DEFAULT));

		/** @disregard */
		$this->assertTrue($User->verifyPassword('Secret#123'));

		/** @disregard */
		$this->assertFalse($User->verifyPassword('wrong'));

		// Legacy hashes created with crypt() must still verify.
		(new ReflectionProperty(User::class, 'passwordHash'))->setValue($User, crypt('Legacy#123', '$2y$10$abcdefghijklmnopqrstuv'));

		/** @disregard */
		$this->assertTrue($User->verifyPassword('Legacy#123'));

		/** @disregard */
		$this->assertFalse($User->verifyPassword('wrong'));
	}
}
