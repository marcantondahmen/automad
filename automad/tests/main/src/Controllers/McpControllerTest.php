<?php

namespace Automad\Controllers;

use Automad\Core\Str;
use PHPUnit\Framework\TestCase;

class McpControllerTest extends TestCase {
	public function testServerCardAuthentication() {
		$card = McpController::getServerCard();

		$this->assertSame(false, $card['authentication']['required']);
		$this->assertSame(array('bearer'), $card['authentication']['schemes']);
	}

	public function testServerCardCapabilities() {
		$card = McpController::getServerCard();

		$this->assertSame(array('tools', 'resources'), array_keys($card['capabilities']));
		$this->assertSame(array('dynamic'), $card['tools']);
		$this->assertSame(array('dynamic'), $card['resources']);
		$this->assertArrayNotHasKey('prompts', $card);
	}

	public function testServerCardIsJsonSerializable() {
		$json = json_encode(McpController::getServerCard());
		$data = json_decode(strval($json), true);

		$this->assertIsArray($data);
		$this->assertSame(array(), $data['capabilities']['tools']);
	}

	public function testServerCardServerInfo() {
		$card = McpController::getServerCard();
		$host = preg_replace('#^https?://#i', '', AM_SERVER);

		$this->assertSame('automad-' . Str::sanitize($host, true), $card['serverInfo']['name']);
		$this->assertSame("Automad ($host)", $card['serverInfo']['title']);
		$this->assertSame(AM_VERSION, $card['serverInfo']['version']);
	}

	public function testServerCardTransport() {
		$card = McpController::getServerCard();

		$this->assertSame('streamable-http', $card['transport']['type']);
		$this->assertSame(AM_BASE_URL . AM_MCP_SERVER_URL, $card['transport']['endpoint']);
	}
}
