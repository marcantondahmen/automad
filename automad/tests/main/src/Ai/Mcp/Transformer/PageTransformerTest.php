<?php

namespace Automad\Ai\Mcp\Transformer;

use Automad\Models\Page;
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
}
