<?php

namespace Automad\Models;

use Automad\System\Fields;
use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase {
	private string $revision = '0e24687af5246c807eddacd450da844febc029a5';

	public function testCheckoutAdminIsEqual() {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Page = $Automad->getPage('/history');

		/** @disregard */
		$this->assertEquals($Page->get(Fields::META_TITLE), 'History Changed');

		$_SESSION['username'] = 'test';
		$Page->checkout($this->revision);
		unset($_SESSION['username']);

		/** @disregard */
		$this->assertEquals($Page->get(Fields::META_TITLE), 'History Old Revision');
	}

	public function testCheckoutIsEqual() {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Page = $Automad->getPage('/history');

		/** @disregard */
		$this->assertEquals($Page->get(Fields::META_TITLE), 'History Changed');

		unset($_SESSION['username']);
		$Page->checkout($this->revision);

		/** @disregard */
		$this->assertEquals($Page->get(Fields::META_TITLE), 'History Changed');
	}

	public function testSharedBlocksDefaultIsEqual() {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$Page = $Automad->Context->get();
		$default = $Page->get('+default', true);

		/** @disregard */
		$this->assertEquals($default['blocks'][0]['data']['text'], 'test');
	}
}
