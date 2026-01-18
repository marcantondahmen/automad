<?php

namespace Automad\Stores;

use Automad\Core\PublicationState;
use Automad\System\Fields;
use PHPUnit\Framework\TestCase;

class DataStoreTest extends TestCase {
	private $bak =  AM_BASE_DIR . AM_DIR_SHARED . '/data.bak';

	private $file =  AM_BASE_DIR . AM_DIR_SHARED . '/data';

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

	public function testSharedDataStoreIsEqual() {
		$DataStore = new DataStore();
		$data = $DataStore->getState(PublicationState::DRAFT);

		$data['newField'] = 'Test';

		$DataStore->setState(PublicationState::DRAFT, $data)->save();
		$DataStore->publish();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEmpty($tmp[PublicationState::DRAFT->value]);

		$lastPublished = $DataStore->getState(PublicationState::PUBLISHED)[Fields::TIME_LAST_PUBLISHED];

		$data['newField'] = 'Test Draft';
		$DataStore->setState(PublicationState::DRAFT, $data)->save();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::DRAFT->value]['newField'], 'Test Draft');

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::PUBLISHED->value]['newField'], 'Test');

		$data['newField'] = 'Test';
		$DataStore->setState(PublicationState::DRAFT, $data)->save();

		$tmp = $this->readStore();

		/** @disregard */
		$this->assertEmpty($tmp[PublicationState::DRAFT->value]);

		/** @disregard */
		$this->assertEquals($tmp[PublicationState::PUBLISHED->value]['newField'], 'Test');

		/** @disregard */
		$this->assertEquals($lastPublished, $tmp[PublicationState::PUBLISHED->value][Fields::TIME_LAST_PUBLISHED]);
	}

	private function readStore(): array {
		$contents = file_get_contents($this->file);
		$data = json_decode($contents, true);

		return $data;
	}
}
