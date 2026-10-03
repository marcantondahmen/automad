<?php

namespace Automad\Ai\Mcp;

use Automad\Core\Str;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase {
	public function testCardAuthentication() {
		$card = Card::get();

		/** @disregard */
		$this->assertSame(false, $card['authentication']['required']);
		/** @disregard */
		$this->assertSame(array('bearer'), $card['authentication']['schemes']);
	}

	public function testCardCapabilities() {
		$card = Card::get();

		/** @disregard */
		$this->assertSame(array('tools'), array_keys($card['capabilities']));
		/** @disregard */
		$this->assertSame(array('dynamic'), $card['tools']);
		/** @disregard */
		$this->assertArrayNotHasKey('resources', $card);
		/** @disregard */
		$this->assertArrayNotHasKey('prompts', $card);
	}

	public function testCardIsJsonSerializable() {
		$json = json_encode(Card::get());
		$data = json_decode(strval($json), true);

		/** @disregard */
		$this->assertIsArray($data);
		/** @disregard */
		$this->assertSame(array(), $data['capabilities']['tools']);
	}

	public function testCardServerInfo() {
		$card = Card::get();
		$host = preg_replace('#^https?://#i', '', AM_SERVER);

		/** @disregard */
		$this->assertSame('automad-' . Str::sanitize($host, true), $card['serverInfo']['name']);
		/** @disregard */
		$this->assertSame("Automad ($host)", $card['serverInfo']['title']);
		/** @disregard */
		$this->assertSame(AM_VERSION, $card['serverInfo']['version']);
	}

	public function testCardTransport() {
		$card = Card::get();

		/** @disregard */
		$this->assertSame('streamable-http', $card['transport']['type']);
		/** @disregard */
		$this->assertSame(AM_BASE_INDEX . AM_MCP_SERVER_URL, $card['transport']['endpoint']);
	}

	public function testHtmlContent() {
		$html = Card::html();
		$card = Card::get();

		/** @disregard */
		$this->assertStringStartsWith('<!DOCTYPE html>', $html);
		/** @disregard */
		$this->assertStringContainsString('<title>' . htmlspecialchars($card['serverInfo']['title'], ENT_QUOTES) . '</title>', $html);
		/** @disregard */
		$this->assertStringContainsString(htmlspecialchars(AM_SERVER . $card['transport']['endpoint'], ENT_QUOTES), $html);
		/** @disregard */
		$this->assertStringContainsString('/.well-known/mcp/server-card.json', $html);
	}

	public function testHtmlIsSelfContained() {
		$html = Card::html();

		/** @disregard */
		$this->assertSame(1, substr_count($html, '<style>'));
		// The favicons are the only external references, all styles are inlined.
		/** @disregard */
		$this->assertSame(2, substr_count($html, '<link'));
		/** @disregard */
		$this->assertStringNotContainsString('rel="stylesheet"', $html);
		/** @disregard */
		$this->assertStringContainsString('rel="icon"', $html);
		/** @disregard */
		$this->assertStringContainsString('rel="alternate icon"', $html);
		/** @disregard */
		$this->assertStringNotContainsString('<script', $html);
		/** @disregard */
		$this->assertStringNotContainsString('<img', $html);
		/** @disregard */
		$this->assertStringContainsString('font-family:ui-monospace', $html);
	}

	public function testJsonMatchesCard() {
		/** @disregard */
		$this->assertSame(json_decode(json_encode(Card::get()), true), json_decode(Card::json(), true));
	}
}
