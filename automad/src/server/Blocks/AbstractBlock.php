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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Blocks\Schema\AgentFieldSchema;
use Automad\Blocks\Utils\Id;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Models\ComponentCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract base block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-type Tunes = array{
 *		id: string,
 *		className: string,
 *		layout: array|null,
 *		spacing: array{
 *			top?: string,
 *			right?: string,
 *			bottom?: string,
 *			left?: string
 *		}
 *	}
 *
 * @psalm-type BlockData = array{
 *		id: string,
 *		type: string,
 *		data: array,
 *		tunes: Tunes
 *	}
 *
 * @psalm-type AgentSchema = array<string, AgentFieldSchema>
 */
abstract class AbstractBlock {
	/**
	 * Convert agent-provided data into the Editor.js block data structure.
	 *
	 * @param array $data
	 * @return array|null
	 */
	public static function fromAgent(array $data): array|null {
		if (empty($data['type'])) {
			return null;
		}

		$block = array(
			'id' => $data['id'] ?? Id::generate(),
			'type' => $data['type'],
			'data' => array(),
			'tunes' => array()
		);

		if (!empty($data['stretched']) || !empty($data['width'])) {
			$block['tunes']['layout'] = array();

			if (isset($data['stretched'])) {
				$block['tunes']['layout']['stretched'] =  $data['stretched'] ?? false;
			}

			if (!empty($data['width'])) {
				$block['tunes']['layout']['width'] =  $data['width'];
			}
		}

		$schema = static::agentDataSchema();

		foreach ($data as $key => $value) {
			if (str_starts_with($key, 'data.')) {
				$key = str_replace('data.', '', $key);

				if (isset($schema[$key])) {
					$fieldSchema = $schema[$key];

					if ($fieldSchema->hasBlocks === true) {
						$value = array('blocks' => Blocks::fromAgent($value));
					}
				}

				$block['data'][$key] = $value;
			}
		}

		return $block;
	}

	/**
	 * Return the JSON schema describing the block's data shape for AI agents.
	 *
	 * @return AgentSchema
	 */
	public static function getAgentSchema(): array {
		$properties = array(
			'width' => new AgentFieldSchema(
				'string',
				<<< TXT
					The block width as a fraction in the form of 1/2. 
					This property is only used when the block is located inside a layoutSection block.
					Skip this in order to use the default theme defined width.
					TXT,
				true,
				array('1/4', '1/3', '1/2', '2/3', '3/4', '1/1')
			)
		);

		if (static::isStretchable()) {
			$properties['stretched'] = new AgentFieldSchema(
				'boolean',
				'If true the section will be stretched to the full width.',
				true
			);
		}

		foreach (static::agentDataSchema() as $key => $fieldSchema) {
			/** @var AgentFieldSchema */
			$properties["data.$key"] = $fieldSchema;
		}

		return $properties;
	}

	/**
	 * The block description.
	 */
	abstract public static function getDescription(): string;

	/**
	 * Get the properties that are required for a block.
	 *
	 * @return string[]
	 */
	public static function getRequiredFromAgentSchema(): array {
		$required = array();

		foreach (static::getAgentSchema() as $key => $prop) {
			if (!$prop->optional) {
				$required[] = $key;
			}
		}

		return $required;
	}

	/**
	 * Render a paragraph block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	abstract public static function render(array $block, Automad $Automad): string;

	/**
	 * Search and replace inside a block.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @param string $searchRegex
	 * @param string $replace
	 * @param bool $replaceInPublishedComponent
	 * @return BlockData
	 */
	abstract public static function replace(
		array $block,
		ComponentCollection $ComponentCollection,
		string $searchRegex,
		string $replace,
		bool $replaceInPublishedComponent
	): array;

	/**
	 * Convert block data into an agent-optimized representation.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @return array
	 */
	public static function toAgent(array $block, ComponentCollection $ComponentCollection): array {
		$data = array('id' => $block['id'], 'type' => $block['type']);

		foreach (static::getAgentSchema() as $key => $fieldSchema) {
			$value = self::getAgentValue($fieldSchema, $block, $key, $ComponentCollection);

			if (!is_null($value)) {
				$data[$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Return a searchable string representation of a block.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @return string
	 */
	abstract public static function toString(array $block, ComponentCollection $ComponentCollection): string;

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array();
	}

	/**
	 * Defines whether a block can be stretched.
	 *
	 * @return bool
	 */
	protected static function isStretchable(): bool {
		return false;
	}

	/**
	 * Get a block value by name according to schema.
	 *
	 * @param AgentFieldSchema $schema
	 * @param BlockData $block
	 * @param string $name
	 * @param ComponentCollection $ComponentCollection
	 * @return mixed
	 */
	private static function getAgentValue(AgentFieldSchema $schema, array $block, string $name, ComponentCollection $ComponentCollection): mixed {
		$value = $schema->getDefault();

		switch ($name) {
			case ('id'):
				$value = $block['id'];

				break;
			case ('type'):
				$value = $block['type'];

				break;
			case ('width'):
				$value = $block['tunes']['layout']['width'] ?? '';

				break;
			case ('stretched'):
				$value = $block['tunes']['layout']['stretched'] ?? false;

				break;

			default:
				$key = str_replace('data.', '', $name);
				$value = $schema->hasBlocks
					? array(
						'blocks' => Blocks::toAgent(
							$block['data']['content']['blocks'] ?? array(),
							$ComponentCollection
						)
					)
					: ($block['data'][$key] ?? $schema->getDefault());
		}

		if (!$value) {
			$value = null;
		}

		return $value;
	}
}
