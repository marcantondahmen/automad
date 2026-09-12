<?php

namespace Automad\System\Ai;

use PHPUnit\Framework\TestCase;

class McpConfigTest extends TestCase {
	private function tokenFixture(string $id, string $tokenHash): array {
		return array(
			'id' => $id,
			'name' => 'Test Token',
			'tokenHash' => $tokenHash,
			'createdAt' => time()
		);
	}

	public function testAddAndFindTokenByHash() {
		$McpConfig = new McpConfig();
		$McpConfig->addToken($this->tokenFixture('token-1', 'hash-1'));

		$token = $McpConfig->findTokenByHash('hash-1');

		$this->assertNotNull($token);
		$this->assertEquals('token-1', $token['id']);
		$this->assertNull($McpConfig->findTokenByHash('unknown-hash'));
	}

	public function testRemoveToken() {
		$McpConfig = new McpConfig();
		$McpConfig->addToken($this->tokenFixture('token-1', 'hash-1'));
		$McpConfig->addToken($this->tokenFixture('token-2', 'hash-2'));
		$McpConfig->removeToken('token-1');

		$this->assertNull($McpConfig->findTokenByHash('hash-1'));
		$this->assertNotNull($McpConfig->findTokenByHash('hash-2'));
	}
}
