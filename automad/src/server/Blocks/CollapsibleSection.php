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
 * Copyright (c) 2025-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Ai\Mcp\SchemaReference;
use Automad\Blocks\Schema\AgentFieldSchema;
use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The collapsible editor block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class CollapsibleSection extends AbstractBlock {
	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return <<< TXT
			A collapsible section block is a wrapper for the HTML details element. 
			Multiple collapsible sections sharing the same group name can be used for a classic FAQ accordion.
			TXT;
	}

	/**
	 * Render a collapsible section block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];
		$attr = Attr::render($block['tunes'], array('am-collapsible'));

		$title = $data['title'] ?? '';
		$content = '';

		if (empty($data['collapsed'])) {
			$attr .= ' open';
		}

		if (!empty($data['group'])) {
			$attr .= ' name="' . $data['group'] . '"';
		}

		if ($data['content']) {
			$content = Blocks::render($data['content'], $Automad);
		}

		return <<< HTML
			<details $attr>
				<summary>$title</summary>
				<section>$content</section>
			</details>
		HTML;
	}

	/**
	 * Search and replace inside block data.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @param string $searchRegex
	 * @param string $replace
	 * @param bool $replaceInPublishedComponent
	 * @return BlockData
	 */
	public static function replace(
		array $block,
		ComponentCollection $ComponentCollection,
		string $searchRegex,
		string $replace,
		bool $replaceInPublishedComponent
	): array {
		if (isset($block['data']['content']['blocks'])) {
			$block['data']['content']['blocks'] = Blocks::replace(
				$block['data']['content']['blocks'],
				$ComponentCollection,
				$searchRegex,
				$replace,
				$replaceInPublishedComponent
			);
		}

		$block['data'] = Replacement::replaceInBlockFields(
			$block['data'],
			array('title'),
			$searchRegex,
			$replace
		);

		return $block;
	}

	/**
	 * Return a searchable string representation of a block.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @return string
	 */
	public static function toString(array $block, ComponentCollection $ComponentCollection): string {
		$title = '';
		$blocks = array();

		if (isset($block['data']['content']['blocks']) && is_array($block['data']['content']['blocks'])) {
			$blocks = $block['data']['content']['blocks'];
		}

		if (isset($block['data']['title']) && is_string($block['data']['title'])) {
			$title = $block['data']['title'];
		}

		return trim($title . ' ' . Blocks::toString($blocks, $ComponentCollection));
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'collapsed' => new AgentFieldSchema(
				'boolean',
				'The state of the collapsible section. If true, the section content is hidden.'
			),
			'content' => new AgentFieldSchema(
				'array',
				'The collapsible section main content.',
				items: array('$ref' => SchemaReference::BLOCK),
				hasBlocks: true
			),
			'group' => new AgentFieldSchema(
				'string',
				<<< TXT
					The group name that is used to convert multiple collapsible sections into a connected accordion.
					Sections that share the same name behave like HTML <details> elements that share
					the same value for thier `name` attribute.
					TXT
			),
			'title' => new AgentFieldSchema(
				'string',
				'The clickable section title. The title behaves like the HTML <summary> element.'
			)
		);
	}

	/**
	 * Defines whether a block can be stretched.
	 *
	 * @return bool
	 */
	protected static function isStretchable(): bool {
		return true;
	}
}
