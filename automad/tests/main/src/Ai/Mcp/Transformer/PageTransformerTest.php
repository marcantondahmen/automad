<?php

namespace Automad\Ai\Mcp\Transformer;

use Automad\Ai\Mcp\Schema\PageSchema;
use Automad\Blocks\AbstractBlock;
use Automad\Core\Blocks;
use Automad\Models\Page;
use Automad\System\Fields;
use Automad\System\FileSystem;
use Automad\Test\Mock;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageTransformerTest extends TestCase {
	/**
	 * Realistic minimum for fields that can't be empty in order to be a meaningful block.
	 */
	const array STORED_DATA = array(
		'filelist' => array('sortOrder' => 'asc'),
		'header' => array('level' => 2),
		'nestedList' => array('style' => 'unordered')
	);

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

	public function testBlockWithoutDataIsValidUpdateInput() {
		$Automad = (new Mock())->createAutomad();

		// Make sure that all block classes are loaded.
		PageSchema::update();

		$classes = array_filter(
			get_declared_classes(),
			fn (string $class): bool => is_subclass_of($class, AbstractBlock::class) && !(new \ReflectionClass($class))->isAbstract()
		);

		/** @disregard */
		$this->assertNotEmpty($classes);

		$invalid = array();

		foreach ($classes as $class) {
			$type = lcfirst(basename(str_replace('\\', '/', $class)));
			$stored = array(
				'id' => 'block-' . $type,
				'type' => $type,
				'data' => self::STORED_DATA[$type] ?? array(),
				'tunes' => array()
			);

			$error = $this->validate(
				array('id' => '/test', 'content' => array('+main' => Blocks::toAgent(array($stored), $Automad->ComponentCollection)))
			);

			if ($error !== null) {
				$invalid[$type] = $error;
			}
		}

		/** @disregard */
		$this->assertSame(array(), $invalid, 'Block types with output that is not valid input for the update schema.');
	}

	public function testPageIsValidUpdateInput() {
		$Automad = (new Mock())->createAutomad();
		$data = FileSystem::readJson(__DIR__ . '/PageTransformer/toAgent/page.json', true);
		$Page = new Page($data, $Automad->Shared);

		/** @disregard */
		$this->assertValidUpdateInput(
			(new PageTransformer($Automad))->toAgent($Page),
			'page fixture'
		);
	}

	public function testPageWithoutDateIsValidUpdateInput() {
		$Automad = (new Mock())->createAutomad();
		$data = FileSystem::readJson(__DIR__ . '/PageTransformer/toAgent/page.json', true);
		$data['date'] = '';
		$Page = new Page($data, $Automad->Shared);

		/** @disregard */
		$this->assertValidUpdateInput(
			(new PageTransformer($Automad))->toAgent($Page),
			'page without date'
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
	 * Validate the data against the update schema.
	 *
	 * @param array $data
	 * @param string $label
	 */
	private function assertValidUpdateInput(array $data, string $label): void {
		/** @disregard */
		$this->assertNull(
			$this->validate($data),
			"The output for the $label is not valid input for the update schema."
		);
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

	/**
	 * Validate the data against the update schema and return the first
	 * detailed errors or null when the data is valid.
	 *
	 * @param array $data
	 * @return string|null
	 */
	private function validate(array $data): ?string {
		$schema = json_decode(json_encode(PageSchema::update(), JSON_UNESCAPED_SLASHES));
		$result = (new Validator())->validate(json_decode(json_encode($data)), $schema);

		if ($result->isValid() || !$result->error()) {
			return null;
		}

		// The leaf errors are the ones that explain what is wrong,
		// all other messages only describe the `oneOf` branches.
		$errors = (new ErrorFormatter())->format($result->error(), true);
		$messages = array();

		foreach ($errors as $path => $list) {
			foreach ($list as $message) {
				if (!str_contains($message, 'must match $ref') && !str_contains($message, 'should match')) {
					$messages[] = "$path: $message";
				}
			}
		}

		return join(' | ', array_slice(array_unique($messages), 0, 6)) ?: 'invalid';
	}
}
