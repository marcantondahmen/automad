<?php

namespace Automad\Ai\Mcp\Validator;

use Automad\Ai\Mcp\Schema\PageSchema;
use PHPUnit\Framework\TestCase;

class InputValidatorTest extends TestCase {
	public function testBlockWithMissingRequiredDataReportsOnlyThatBlock() {
		$errors = $this->validate(array(
			'id' => '/test',
			'content' => array('+main' => array(array('type' => 'header', 'data' => array('text' => 'No level'))))
		));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/content/+main/0/data', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Missing required properties: `level`.', $errors[0]['message']);
	}

	public function testDataOfMissingBlockTypeReportsMissingType() {
		$errors = $this->validate(array(
			'id' => '/test',
			'content' => array('+main' => array(array('data' => array('text' => 'No type'))))
		));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/content/+main/0', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Missing required properties: `type`.', $errors[0]['message']);
	}

	public function testInvalidDateIsReportedOnce() {
		$errors = $this->validate(array('id' => '/test', 'date' => 'not-a-date'));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/date', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Value does not match the required format: `date-time`.', $errors[0]['message']);
	}

	public function testInvalidEnumValueIsReportedForTheMeantBlock() {
		$errors = $this->validate(array(
			'id' => '/test',
			'content' => array('+main' => array(array('type' => 'header', 'data' => array('level' => 9, 'text' => 'Level 9'))))
		));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/content/+main/0/data/level', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Value must be one of the allowed values: 1, 2, 3, 4, 5, 6.', $errors[0]['message']);
	}

	public function testInvalidTypeOfNullablePropertyListsAllTypes() {
		$errors = $this->validate(array('id' => '/test', 'title' => 5));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/title', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Invalid type. Expected `string|null`, but received `integer`.', $errors[0]['message']);
	}

	public function testMissingIdIsReported() {
		$errors = $this->validate(array('title' => 'No ID'));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('Missing required properties: `id`.', $errors[0]['message']);
	}

	public function testNestedBlockErrorsAreReportedForTheMeantBlock() {
		$errors = $this->validate(array(
			'id' => '/test',
			'content' => array('+main' => array(array(
				'type' => 'layoutSection',
				'data' => array('content' => array(
					array('type' => 'paragraph', 'data' => array('text' => 'Fine')),
					array('type' => 'image', 'data' => array('url' => 'image.jpg'))
				))
			)))
		));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/content/+main/0/data/content/1/data', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Missing required properties: `alt`.', $errors[0]['message']);
	}

	public function testNullIsValidForOptionalProperties() {
		/** @disregard */
		$this->assertSame(array(), $this->validate(array(
			'id' => '/test',
			'title' => null,
			'template' => null,
			'theme' => null,
			'date' => null,
			'private' => null,
			'hidden' => null,
			'tags' => null,
			'content' => null
		)));
	}

	public function testUnexpectedPropertyOfBlockIsReported() {
		$errors = $this->validate(array(
			'id' => '/test',
			'content' => array('+main' => array(array('type' => 'paragraph', 'data' => array('text' => 'Text', 'foo' => 'bar'))))
		));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/content/+main/0/data', $errors[0]['pointer']);
		/** @disregard */
		$this->assertSame('Unexpected additional properties: `foo`.', $errors[0]['message']);
	}

	public function testUnknownBlockTypeListsAllowedTypes() {
		$errors = $this->validate(array(
			'id' => '/test',
			'content' => array('+main' => array(array('type' => 'bogus', 'data' => array('x' => 1))))
		));

		/** @disregard */
		$this->assertCount(1, $errors);
		/** @disregard */
		$this->assertSame('/content/+main/0/type', $errors[0]['pointer']);
		/** @disregard */
		$this->assertStringContainsString('Invalid block type "bogus"', $errors[0]['message']);
		/** @disregard */
		$this->assertStringContainsString('"paragraph"', $errors[0]['message']);
		/** @disregard */
		$this->assertStringContainsString('"layoutSection"', $errors[0]['message']);
	}

	public function testValidDataHasNoErrors() {
		/** @disregard */
		$this->assertSame(array(), $this->validate(array(
			'id' => '/test',
			'title' => 'Title',
			'date' => '2026-10-02T08:03:25+00:00',
			'tags' => array('a', 'b'),
			'content' => array('+main' => array(
				array('type' => 'header', 'data' => array('level' => 2, 'text' => 'Header')),
				array('type' => 'tableOfContents')
			))
		)));
	}

	/**
	 * Validate the data against the update schema.
	 *
	 * @param array $data
	 * @return array
	 */
	private function validate(array $data): array {
		return (new InputValidator())->validateAgainstJsonSchema($data, PageSchema::update());
	}
}
