<?php

namespace Automad\Ai\Mcp;

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase {
	public function testPrivateIsNeverPublic() {
		$publicTools = array_map(fn ($Tool) => $Tool::class, Provider::getTools(false));

		foreach (Provider::getTools(true) as $Tool) {
			if ($Tool::requiresAuth()) {
				/** @disregard */
				$this->assertNotContains($Tool::class, $publicTools);
			}
		}
	}

	public function testPrivateIsSame() {
		/** @disregard */
		$this->assertSame(
			array_values(array_map(fn ($Tool) => $Tool->getName(), Provider::getTools(true))),
			array(
				'component_list',
				'file_import',
				'filelist_template_list',
				'page_create',
				'page_delete',
				'page_duplicate',
				'page_move',
				'page_publish',
				'page_read',
				'page_reorder_children',
				'page_search',
				'page_template_list',
				'page_tree',
				'page_update',
				'pagelist_template_list',
				'snippet_list'
			)
		);
	}

	public function testPublicIsSame() {
		/** @disregard */
		$this->assertSame(
			array_values(array_map(fn ($Tool) => $Tool->getName(), Provider::getTools(false))),
			array('page_read', 'page_search', 'page_tree')
		);
	}
}
