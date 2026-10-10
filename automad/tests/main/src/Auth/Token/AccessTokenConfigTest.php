<?php

namespace Automad\Auth\Token;

use PHPUnit\Framework\TestCase;

class AccessTokenConfigTest extends TestCase {
	public function testAddAndFindTokenByHash() {
		$AccessTokenConfig = new AccessTokenConfig();
		$AccessTokenConfig->addToken($this->tokenFixture('token-1', 'hash-1'));

		$token = $AccessTokenConfig->findTokenByHash('hash-1');

		/** @disregard */
		$this->assertNotNull($token);
		/** @disregard */
		$this->assertEquals('token-1', $token['id']);
		/** @disregard */
		$this->assertEquals('A test token', $token['description']);
		/** @disregard */
		$this->assertEquals('am_abc', $token['preview']);
		/** @disregard */
		$this->assertNull($AccessTokenConfig->findTokenByHash('unknown-hash'));
	}

	public function testRemoveToken() {
		$AccessTokenConfig = new AccessTokenConfig();
		$AccessTokenConfig->addToken($this->tokenFixture('token-1', 'hash-1'));
		$AccessTokenConfig->addToken($this->tokenFixture('token-2', 'hash-2'));
		$AccessTokenConfig->removeToken('token-1');

		/** @disregard */
		$this->assertNull($AccessTokenConfig->findTokenByHash('hash-1'));
		/** @disregard */
		$this->assertNotNull($AccessTokenConfig->findTokenByHash('hash-2'));
	}

	private function tokenFixture(string $id, string $tokenHash): array {
		return array(
			'id' => $id,
			'name' => 'Test Token',
			'description' => 'A test token',
			'preview' => 'am_abc',
			'tokenHash' => $tokenHash,
			'createdAt' => time()
		);
	}
}
