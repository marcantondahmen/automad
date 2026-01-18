<?php

namespace Automad\Stores;

use Automad\Core\PublicationState;
use Automad\System\Fields;
use PHPUnit\Framework\TestCase;

class ComponentStoreTest extends TestCase {
	private $bak =  AM_BASE_DIR . AM_DIR_SHARED . '/components.bak';

	private $file =  AM_BASE_DIR . AM_DIR_SHARED . '/components';

	protected function setUp(): void {
		if (is_readable($this->bak)) {
			$this->tearDown();
		}

		copy($this->file, $this->bak);
	}

	protected function tearDown(): void {
		unlink($this->file);
		rename($this->bak, $this->file);
	}

	public function testComponentStoreIsEqual() {
		$ComponentStore = new ComponentStore();
		$data = array();
		$data['components'] = array($this->createComponent('1', 'store test'));

		$ComponentStore->setState(PublicationState::DRAFT, $data)->save();
		$ComponentStore->publish();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEmpty($tmp[PublicationState::DRAFT->value]);

		$lastPublished = $ComponentStore->getState(PublicationState::PUBLISHED)[Fields::TIME_LAST_PUBLISHED];

		$data['components'] = array($this->createComponent('1', 'store test 2'));

		$ComponentStore->setState(PublicationState::DRAFT, $data)->save();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::DRAFT->value]['components'][0]['blocks'][0]['data']['text'], 'store test 2');

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::PUBLISHED->value]['components'][0]['blocks'][0]['data']['text'], 'store test');

		$data['components'] = array($this->createComponent('1', 'store test'));

		$ComponentStore->setState(PublicationState::DRAFT, $data)->save();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEmpty($tmp[PublicationState::DRAFT->value]);

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::PUBLISHED->value]['components'][0]['blocks'][0]['data']['text'], 'store test');

		$data['components'] = array(
			$this->createComponent('1', 'store test'),
			$this->createComponent('2', 'new block')
		);

		$ComponentStore->setState(PublicationState::DRAFT, $data)->save();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEquals(count($tmp[PublicationState::DRAFT->value]['components']), 2);

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::PUBLISHED->value]['components'][0]['blocks'][0]['data']['text'], 'store test');

		$data['components'] = array($this->createComponent('1', 'store test'));

		$ComponentStore->setState(PublicationState::DRAFT, $data)->save();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEmpty($tmp[PublicationState::DRAFT->value]);

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::PUBLISHED->value]['components'][0]['blocks'][0]['data']['text'], 'store test');
	}

	private function createComponent(string $id, string $text): array {
		return json_decode(<<<JSON
			{
				"id": "{$id}",
				"name": "Store Test",
				"blocks": [
					{
						"id": "yyy",
						"type": "paragraph",
						"data": {
							"text": "{$text}",
							"large": false
						},
						"tunes": {
							"layout": null,
							"spacing": {
								"top": "",
								"right": "",
								"bottom": "",
								"left": ""
							},
							"className": "",
							"id": ""
						}
					}
				],
				"collapsed": false
			}
			JSON, true);
	}

	private function readStore(): array {
		$contents = file_get_contents($this->file);
		$data = json_decode($contents, true);

		return $data;
	}
}
