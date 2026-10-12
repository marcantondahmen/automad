<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Ai\Mcp\Validator;

use Mcp\Capability\Discovery\SchemaValidator;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The tool input validator. It validates the arguments of a tool call against the input schema of the tool
 * just like the validator of the MCP SDK, but it reports errors that are useful for agents.
 *
 * The SDK flattens the errors of all branches of a `oneOf` or `anyOf` keyword. For the block schema that
 * means that every error is reported once for each of the more than 20 block types, and since only the first
 * errors are shown to a client, the actual problem is usually not visible. This validator only reports
 * the errors of the branch that was meant by the data, picked by the block `type`, and reports unknown types
 * with a list of all allowed ones.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-type Leaf = array{pointer: string, keyword: string, message: string, args: array, value: mixed}
 */
class InputValidator extends SchemaValidator {
	/**
	 * Validate the data against a JSON schema.
	 *
	 * @param mixed $data
	 * @param array|object $schema
	 * @return list<array{pointer: string, keyword: string, message: string}>
	 */
	public function validateAgainstJsonSchema(mixed $data, array|object $schema): array {
		if (is_array($data) && empty($data)) {
			$data = new \stdClass();
		}

		try {
			$schemaObject = json_decode(json_encode($schema, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
			$result = (new Validator())->validate($this->toObject($data), $schemaObject);
		} catch (\Throwable $e) {
			return array(array('pointer' => '', 'keyword' => 'internal', 'message' => 'Schema validation process failed: ' . $e->getMessage()));
		}

		$error = $result->error();

		if ($result->isValid() || !$error) {
			return array();
		}

		$errors = array();

		foreach ($this->collect($error) as $leaf) {
			$errors[$leaf['pointer'] . '|' . $leaf['message']] = array(
				'pointer' => $leaf['pointer'],
				'keyword' => $leaf['keyword'],
				'message' => $leaf['message']
			);
		}

		return array_values($errors);
	}

	/**
	 * Collect the errors that explain why data is invalid.
	 *
	 * @param ValidationError $error
	 * @return list<Leaf>
	 */
	private function collect(ValidationError $error): array {
		$subErrors = $error->subErrors();

		if (empty($subErrors)) {
			return array($this->createLeaf($error));
		}

		if (in_array($error->keyword(), array('oneOf', 'anyOf'), true)) {
			return $this->collectBranches($error);
		}

		$leaves = array();

		foreach ($subErrors as $subError) {
			$leaves = array_merge($leaves, $this->collect($subError));
		}

		return $leaves;
	}

	/**
	 * Collect the errors of the branch of a `oneOf` or `anyOf` keyword that was meant by the data.
	 *
	 * @param ValidationError $error
	 * @return list<Leaf>
	 */
	private function collectBranches(ValidationError $error): array {
		$pointer = $this->createPointer($error);
		$branches = array_map(fn (ValidationError $branch): array => $this->collect($branch), $error->subErrors());

		// A branch that fails because the data has a different JSON type is a branch that was not meant,
		// for example the `null` branch of a nullable property that was given an object.
		$isWrongType = fn (array $leaves): bool => empty(array_filter(
			$leaves,
			fn (array $leaf): bool => $leaf['keyword'] !== 'type' || $leaf['pointer'] !== $pointer
		));

		$meant = array_values(array_filter($branches, fn (array $leaves): bool => !$isWrongType($leaves)));

		if (empty($meant)) {
			return array($this->mergeTypeErrors($pointer, $branches));
		}

		// A branch with a block `type` that is different from the one of the data was not meant either.
		$isDiscriminator = fn (array $leaf): bool => $leaf['keyword'] === 'const' && str_ends_with($leaf['pointer'], '/type');
		$candidates = array_values(array_filter($meant, fn (array $leaves): bool => empty(array_filter($leaves, $isDiscriminator))));

		if (empty($candidates)) {
			return array($this->mergeDiscriminatorErrors($meant, $isDiscriminator));
		}

		usort($candidates, fn (array $a, array $b): int => count($a) <=> count($b));

		return $candidates[0];
	}

	/**
	 * Create a leaf for an error without sub errors.
	 *
	 * @param ValidationError $error
	 * @return Leaf
	 */
	private function createLeaf(ValidationError $error): array {
		return array(
			'pointer' => $this->createPointer($error),
			'keyword' => $error->keyword(),
			'message' => $this->createMessage($error),
			'args' => $error->args(),
			'value' => $error->data()->value()
		);
	}

	/**
	 * Create the readable message for an error.
	 *
	 * @param ValidationError $error
	 * @return string
	 */
	private function createMessage(ValidationError $error): string {
		$args = $error->args();

		switch ($error->keyword()) {
			case 'required':
				return 'Missing required properties: ' . $this->formatList((array) ($args['missing'] ?? array())) . '.';

			case 'type':
				return 'Invalid type. Expected ' . $this->formatTypes((array) ($args['expected'] ?? array())) . ', but received `' . ($error->data()->type() ?? 'unknown') . '`.';

			case 'const':
				return 'Value must be equal to ' . $this->encode($args['const'] ?? null) . '.';

			case 'enum':
				$data = $error->schema()->info()->data();
				$allowed = is_object($data) && isset($data->enum) && is_array($data->enum) ? $data->enum : array();

				if (!empty($allowed)) {
					return 'Value must be one of the allowed values: ' . join(', ', array_map(fn ($value): string => $this->encode($value), $allowed)) . '.';
				}

				break;

			case 'additionalProperties':
				return 'Unexpected additional properties: ' . $this->formatList((array) ($args['properties'] ?? array())) . '.';

			case 'format':
				return 'Value does not match the required format: `' . strval($args['format'] ?? 'unknown') . '`.';
		}

		return (new ErrorFormatter())->formatErrorMessage($error);
	}

	/**
	 * Create the JSON pointer of the data of an error.
	 *
	 * @param ValidationError $error
	 * @return string
	 */
	private function createPointer(ValidationError $error): string {
		$path = array_map(
			fn ($segment): string => str_replace(array('~', '/'), array('~0', '~1'), strval($segment)),
			$error->data()->fullPath()
		);

		return empty($path) ? '' : '/' . join('/', $path);
	}

	/**
	 * Encode a value as JSON for messages.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function encode(mixed $value): string {
		return strval(json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * Format a list of names.
	 *
	 * @param array $names
	 * @return string
	 */
	private function formatList(array $names): string {
		return join(', ', array_map(fn ($name): string => '`' . strval($name) . '`', $names));
	}

	/**
	 * Format a list of JSON types.
	 *
	 * @param array $types
	 * @return string
	 */
	private function formatTypes(array $types): string {
		return '`' . join('|', array_map('strval', $types)) . '`';
	}

	/**
	 * Merge the errors of all branches that were rejected because of the block type.
	 * The result is one error that lists all allowed types.
	 *
	 * @param list<list<Leaf>> $branches
	 * @param callable $isDiscriminator
	 * @return Leaf
	 */
	private function mergeDiscriminatorErrors(array $branches, callable $isDiscriminator): array {
		$allowed = array();
		$pointer = '';
		$value = null;

		foreach ($branches as $leaves) {
			foreach (array_filter($leaves, $isDiscriminator) as $leaf) {
				$allowed[] = $this->encode($leaf['args']['const'] ?? null);
				$pointer = $leaf['pointer'];
				$value = $leaf['value'];
			}
		}

		$received = $this->encode($value);

		return array(
			'pointer' => $pointer,
			'keyword' => 'enum',
			'message' => "Invalid block type $received. Allowed types: " . join(', ', array_unique($allowed)) . '.',
			'args' => array(),
			'value' => $value
		);
	}

	/**
	 * Merge the errors of branches that all failed because of the type of the data.
	 *
	 * @param string $pointer
	 * @param list<list<Leaf>> $branches
	 * @return Leaf
	 */
	private function mergeTypeErrors(string $pointer, array $branches): array {
		$expected = array();
		$received = 'unknown';
		$value = null;

		foreach ($branches as $leaves) {
			foreach ($leaves as $leaf) {
				$expected = array_merge($expected, (array) ($leaf['args']['expected'] ?? array()));
				$received = strval($leaf['args']['type'] ?? $received);
				$value = $leaf['value'];
			}
		}

		return array(
			'pointer' => $pointer,
			'keyword' => 'type',
			'message' => 'Invalid type. Expected ' . $this->formatTypes(array_values(array_unique($expected))) . ", but received `$received`.",
			'args' => array('expected' => $expected, 'type' => $received),
			'value' => $value
		);
	}

	/**
	 * Convert arrays to objects when they are associative arrays, since the validator needs objects.
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	private function toObject(mixed $data): mixed {
		if (is_array($data)) {
			if (!empty($data) && array_keys($data) !== range(0, count($data) - 1)) {
				$object = new \stdClass();

				foreach ($data as $key => $value) {
					$object->{$key} = $this->toObject($value);
				}

				return $object;
			}

			return array_map(fn (mixed $item): mixed => $this->toObject($item), $data);
		}

		if ($data instanceof \stdClass) {
			$object = new \stdClass();

			foreach (get_object_vars($data) as $key => $value) {
				$object->{$key} = $this->toObject($value);
			}

			return $object;
		}

		return $data;
	}
}
