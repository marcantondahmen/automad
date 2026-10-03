<?php

namespace Automad\Ai\Mcp\Transformer;

use Automad\Models\Page;
use Automad\System\Fields;
use Automad\System\FileSystem;
use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageTransformerTest extends TestCase {
	public static function dataForTestToAgentEquals() {
		return array(
			array(
				'page',
				'agentStructured',
				false
			),
			array(
				'page',
				'agentText',
				true
			),
		);
	}

	public static function dataForTestUpdateFromAgentEquals() {
		return array(
			array(
				'page',
				'agent',
				'updated'
			),
		);
	}

	#[DataProvider('dataForTestToAgentEquals')]
	public function testToAgentEquals(string $filePage, string $fileAgent, bool $toString) {
		$dir = __DIR__ . '/PageTransformer/toAgent';
		$Automad = (new Mock())->createAutomad();
		$data = FileSystem::readJson($dir . "/$filePage.json", true);
		$Page = new Page($data, $Automad->Shared);
		$PageTransformer = new PageTransformer($Automad);

		/** @disregard */
		$this->assertEquals(
			json_encode($PageTransformer->toAgent($Page, $toString), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
			json_encode(FileSystem::readJson($dir . "/$fileAgent.json", true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);
	}

	public function testUpdateFromAgentClearsExplicitEmptyValues() {
		$PageTransformer = new PageTransformer((new Mock())->createAutomad());
		$Page = $this->createPageWithMetaData();

		$updated = $PageTransformer->updateFromAgent(
			$Page,
			date: '',
			private: false,
			hidden: false,
			tags: array()
		);

		/** @disregard */
		$this->assertArrayNotHasKey(Fields::DATE, $updated);
		/** @disregard */
		$this->assertArrayNotHasKey(Fields::PRIVATE, $updated);
		/** @disregard */
		$this->assertArrayNotHasKey(Fields::HIDDEN, $updated);
		/** @disregard */
		$this->assertArrayNotHasKey(Fields::TAGS, $updated);
		/** @disregard */
		$this->assertSame('MCP Test', $updated[Fields::TITLE]);
		/** @disregard */
		$this->assertSame($Page->data['+main'], $updated['+main']);
		/** @disregard */
		$this->assertSame($Page->data['+hero'], $updated['+hero']);
	}

	public function testUpdateFromAgentClearsOnlyListedContentField() {
		$PageTransformer = new PageTransformer((new Mock())->createAutomad());
		$Page = $this->createPageWithMetaData();

		$updated = $PageTransformer->updateFromAgent($Page, content: array('+hero' => array()));

		/** @disregard */
		$this->assertSame(array(), $updated['+hero']['blocks']);
		/** @disregard */
		$this->assertSame($Page->data['+main'], $updated['+main']);
	}

	#[DataProvider('dataForTestUpdateFromAgentEquals')]
	public function testUpdateFromAgentEquals(string $filePage, string $fileAgent, string $fileUpdated) {
		$dir = __DIR__ . '/PageTransformer/updateFromAgent';
		$Automad = (new Mock())->createAutomad();
		$Page = new Page(FileSystem::readJson($dir . "/$filePage.json", true), $Automad->Shared);
		$PageTransformer = new PageTransformer($Automad);
		$agentData = FileSystem::readJson($dir . "/$fileAgent.json", true);

		$updated = $PageTransformer->updateFromAgent(
			$Page,
			$agentData['title'],
			$agentData['template'],
			$agentData['theme'],
			$agentData['date'],
			$agentData['private'],
			$agentData['hidden'],
			$agentData['tags'],
			$agentData['content']
		);

		/** @disregard */
		$this->assertEquals(
			json_encode(FileSystem::readJson($dir . "/$fileUpdated.json", true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
			json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
		);
	}

	public function testUpdateFromAgentIgnoresEmptyTitle() {
		$PageTransformer = new PageTransformer((new Mock())->createAutomad());
		$Page = $this->createPageWithMetaData();

		/** @disregard */
		$this->assertSame('MCP Test', $PageTransformer->updateFromAgent($Page, title: '')[Fields::TITLE]);
		/** @disregard */
		$this->assertSame('New Title', $PageTransformer->updateFromAgent($Page, title: 'New Title')[Fields::TITLE]);
	}

	public function testUpdateFromAgentKeepsOmittedFields() {
		$PageTransformer = new PageTransformer((new Mock())->createAutomad());
		$Page = $this->createPageWithMetaData();

		$updated = $PageTransformer->updateFromAgent($Page);

		/** @disregard */
		$this->assertTrue($updated[Fields::PRIVATE]);
		/** @disregard */
		$this->assertTrue($updated[Fields::HIDDEN]);
		/** @disregard */
		$this->assertSame('2026-09-30T15:13:59+00:00', $updated[Fields::DATE]);
		/** @disregard */
		$this->assertSame('alpha, beta', $updated[Fields::TAGS]);
		/** @disregard */
		$this->assertSame('MCP Test', $updated[Fields::TITLE]);
		/** @disregard */
		$this->assertSame('page_full_width_centered', $updated[Fields::TEMPLATE]);
		/** @disregard */
		$this->assertSame($Page->data['+main'], $updated['+main']);
		/** @disregard */
		$this->assertSame($Page->data['+hero'], $updated['+hero']);
	}

	public function testUpdateFromAgentPartialOverride() {
		$PageTransformer = new PageTransformer((new Mock())->createAutomad());
		$Page = $this->createPageWithMetaData();

		$updated = $PageTransformer->updateFromAgent($Page, tags: array('gamma', 'delta'));

		/** @disregard */
		$this->assertSame('gamma, delta', $updated[Fields::TAGS]);
		/** @disregard */
		$this->assertTrue($updated[Fields::PRIVATE]);
		/** @disregard */
		$this->assertTrue($updated[Fields::HIDDEN]);
		/** @disregard */
		$this->assertSame('2026-09-30T15:13:59+00:00', $updated[Fields::DATE]);
	}

	/**
	 * Create a page from the update fixture that is private, hidden, dated, tagged
	 * and has an additional `+hero` content field.
	 *
	 * @return Page
	 */
	private function createPageWithMetaData(): Page {
		$Automad = (new Mock())->createAutomad();
		$data = FileSystem::readJson(__DIR__ . '/PageTransformer/updateFromAgent/page.json', true);
		$data[Fields::TAGS] = 'alpha, beta';
		$data['+hero'] = array(
			'blocks' => array(
				array(
					'id' => 'heroBlock001',
					'type' => 'paragraph',
					'data' => array('text' => 'Hero text'),
					'tunes' => array()
				)
			)
		);

		return new Page($data, $Automad->Shared);
	}
}
