<?php

namespace Automad\Ai\Mcp;

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase {
	public function testPrivateIsNeverPublic() {
		$publicTools = array_map(fn ($Tool) => $Tool::class, Provider::getTools(false));
		$publicResources = array_map(fn ($Resource) => $Resource::class, Provider::getResources(false));

		foreach (Provider::getTools(true) as $Tool) {
			if ($Tool::requiresAuth()) {
				/** @disregard */
				$this->assertNotContains($Tool::class, $publicTools);
			}
		}

		foreach (Provider::getResources(true) as $Resource) {
			if ($Resource::requiresAuth()) {
				/** @disregard */
				$this->assertNotContains($Resource::class, $publicResources);
			}
		}
	}

	public function testPrivateIsSame() {
		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Tool) => $Tool->getName(), Provider::getTools(true))),
			'page_create, page_delete, page_move, page_read, page_reorder_children, page_search, page_tree, page_update'
		);

		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Resource) => $Resource->getName(), Provider::getResources(true))),
			'components, snippets, templates/filelist, templates/page, templates/pagelist'
		);
	}

	public function testPublicIsSame() {
		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Tool) => $Tool->getName(), Provider::getTools(false))),
			'page_read, page_search, page_tree'
		);

		/** @disregard */
		$this->assertSame(
			join(', ', array_map(fn ($Resource) => $Resource->getName(), Provider::getResources(false))),
			''
		);
	}
}
