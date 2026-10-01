<?php

namespace Automad\Ai\Mcp;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class ProviderTest extends TestCase {
	public function testPrivateIsSame() {
		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Tool) => $Tool->getName(), Provider::getTools(true))),
			'page_create, page_delete, page_read, page_search, page_update'
		);

		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Resource) => $Resource->getName(), Provider::getResources(true))),
			'pages, snippets, templates/filelist, templates/page, templates/pagelist'
		);
	}

	public function testPublicIsSame() {
		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Tool) => $Tool->getName(), Provider::getTools(false))),
			'page_read, page_search'
		);

		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Resource) => $Resource->getName(), Provider::getResources(false))),
			'pages'
		);
	}
}
