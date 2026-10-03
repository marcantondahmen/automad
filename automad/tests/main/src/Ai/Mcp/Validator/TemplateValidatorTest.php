<?php

namespace Automad\Ai\Mcp\Validator;

use Mcp\Exception\ToolCallException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TemplateValidatorTest extends TestCase {
	public static function dataForTestInvalidCombinationThrows() {
		return array(
			'unknown theme' => array('wrong/theme', 'page', 'Theme [wrong/theme] not found'),
			'unknown template' => array('vendor/theme-a', 'nope', 'Template [nope] does not exist in theme [vendor/theme-a]'),
			'template of another theme' => array('vendor/theme-b', 'sidebar', 'Template [sidebar] does not exist in theme [vendor/theme-b]'),
			'empty theme' => array('', 'page', 'Theme [] not found'),
			'empty template' => array('vendor/theme-a', '', 'Template [] does not exist in theme [vendor/theme-a]')
		);
	}

	public static function dataForTestValidCombinationPasses() {
		return array(
			array('vendor/theme-a', 'page'),
			array('vendor/theme-a', 'sidebar'),
			array('vendor/theme-b', 'page')
		);
	}

	#[DataProvider('dataForTestInvalidCombinationThrows')]
	public function testInvalidCombinationThrows(string $theme, string $template, string $message) {
		/** @disregard */
		$this->expectException(ToolCallException::class);
		/** @disregard */
		$this->expectExceptionMessage($message);

		$this->createValidator()->assertValid($theme, $template);
	}

	public function testUnknownTemplateMessageListsAvailableTemplates() {
		try {
			$this->createValidator()->assertValid('vendor/theme-a', 'nope');
			/** @disregard */
			$this->fail('Expected exception was not thrown.');
		} catch (ToolCallException $e) {
			/** @disregard */
			$this->assertStringContainsString('`page`, `sidebar`', $e->getMessage());
			/** @disregard */
			$this->assertStringNotContainsString('theme-b', $e->getMessage());
		}
	}

	public function testUnknownThemeMessageListsAvailableThemes() {
		try {
			$this->createValidator()->assertValid('wrong/theme', 'page');
			/** @disregard */
			$this->fail('Expected exception was not thrown.');
		} catch (ToolCallException $e) {
			/** @disregard */
			$this->assertStringContainsString('`vendor/theme-a`, `vendor/theme-b`', $e->getMessage());
		}
	}

	#[DataProvider('dataForTestValidCombinationPasses')]
	public function testValidCombinationPasses(string $theme, string $template) {
		$this->createValidator()->assertValid($theme, $template);

		/** @disregard */
		$this->addToAssertionCount(1);
	}

	private function createValidator(): TemplateValidator {
		return new TemplateValidator(array(
			'vendor/theme-a' => array('page', 'sidebar'),
			'vendor/theme-b' => array('page')
		));
	}
}
