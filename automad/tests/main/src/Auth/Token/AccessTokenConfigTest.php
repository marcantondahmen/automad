<?php

namespace Automad\Auth\Token;

use PHPUnit\Framework\TestCase;

class AccessTokenConfigTest extends TestCase {
	private function tokenFixture(string $id, string $tokenHash): array {
		return array(
			'id' => $id,
			'name' => 'Test Token',
			'tokenHash' => $tokenHash,
			'createdAt' => time()
		);
	}

	public function testAddAndFindTokenByHash() {
		$AccessTokenConfig = new AccessTokenConfig();
		$AccessTokenConfig->addToken($this->tokenFixture('token-1', 'hash-1'));

		$token = $AccessTokenConfig->findTokenByHash('hash-1');

		$this->assertNotNull($token);
		$this->assertEquals('token-1', $token['id']);
		$this->assertNull($AccessTokenConfig->findTokenByHash('unknown-hash'));
	}

	public function testRemoveToken() {
		$AccessTokenConfig = new AccessTokenConfig();
		$AccessTokenConfig->addToken($this->tokenFixture('token-1', 'hash-1'));
		$AccessTokenConfig->addToken($this->tokenFixture('token-2', 'hash-2'));
		$AccessTokenConfig->removeToken('token-1');

		$this->assertNull($AccessTokenConfig->findTokenByHash('hash-1'));
		$this->assertNotNull($AccessTokenConfig->findTokenByHash('hash-2'));
	}
}
