<?php

namespace Automad\Ai\Mcp\Validator;

use Mcp\Exception\ToolCallException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageStructureValidatorTest extends TestCase {
	public static function dataForTestAssertMovablePasses() {
		return array(
			'to another page' => array('/project', '/project/', '/', '/work', '/work/'),
			'to the homepage' => array('/work/project', '/work/project/', '/work', '/', '/'),
			'to a page with a similar name' => array('/media', '/media/', '/', '/media-copy', '/media-copy/'),
			'to a sibling' => array('/work/a', '/work/a/', '/work', '/work/b', '/work/b/')
		);
	}

	public static function dataForTestAssertMovableThrows() {
		return array(
			'homepage' => array('/', '/', '', '/work', '/work/', 'homepage'),
			'into itself' => array('/work', '/work/', '/', '/work', '/work/', 'into itself or one of its own sub-pages'),
			'into a child' => array('/work', '/work/', '/', '/work/project', '/work/project/', 'into itself or one of its own sub-pages'),
			'into a deep descendant' => array('/work', '/work/', '/', '/work/a/b/c', '/work/a/b/c/', 'into itself or one of its own sub-pages'),
			'below the current parent' => array('/work/project', '/work/project/', '/work', '/work', '/work/', 'already located below'),
			'below the homepage as first level page' => array('/work', '/work/', '/', '/', '/', 'already located below')
		);
	}

	public static function dataForTestResolveOrder() {
		return array(
			'full list in new order' => array(array('a', 'b', 'c'), array('c', 'a', 'b'), array('c', 'a', 'b')),
			'partial list appends the rest in the current order' => array(array('a', 'b', 'c', 'd'), array('c', 'a'), array('c', 'a', 'b', 'd')),
			'duplicates are ignored' => array(array('a', 'b', 'c'), array('b', 'b', 'a', 'b'), array('b', 'a', 'c')),
			'full IDs are reduced to their basename' => array(array('a', 'b'), array('/parent/b', 'a'), array('b', 'a')),
			'empty list keeps the current order' => array(array('a', 'b'), array(), array('a', 'b')),
			'no children' => array(array(), array(), array())
		);
	}

	#[DataProvider('dataForTestAssertMovablePasses')]
	public function testAssertMovablePasses(string $id, string $path, string $parentId, string $targetId, string $targetPath) {
		(new PageStructureValidator())->assertMovable($id, $path, $parentId, $targetId, $targetPath);

		/** @disregard */
		$this->addToAssertionCount(1);
	}

	#[DataProvider('dataForTestAssertMovableThrows')]
	public function testAssertMovableThrows(string $id, string $path, string $parentId, string $targetId, string $targetPath, string $message) {
		/** @disregard */
		$this->expectException(ToolCallException::class);
		/** @disregard */
		$this->expectExceptionMessage($message);

		(new PageStructureValidator())->assertMovable($id, $path, $parentId, $targetId, $targetPath);
	}

	#[DataProvider('dataForTestResolveOrder')]
	public function testResolveOrder(array $children, array $order, array $expected) {
		/** @disregard */
		$this->assertSame($expected, (new PageStructureValidator())->resolveOrder('/parent', $children, $order));
	}

	public function testResolveOrderThrowsForUnknownPages() {
		try {
			(new PageStructureValidator())->resolveOrder('/parent', array('a', 'b'), array('ghost', 'a', 'other'));
			/** @disregard */
			$this->fail('Expected exception was not thrown.');
		} catch (ToolCallException $e) {
			/** @disregard */
			$this->assertStringContainsString('`ghost`, `other`', $e->getMessage());
			/** @disregard */
			$this->assertStringContainsString('Existing sub-pages: `a`, `b`', $e->getMessage());
			/** @disregard */
			$this->assertStringContainsString('[/parent]', $e->getMessage());
		}
	}
}
