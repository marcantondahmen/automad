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
			An expandable and collapsible container based on the HTML <details> and <summary> elements
			with a clickable title that reveals nested child blocks. Use it for FAQs, spoilers or
			optional details. Multiple collapsible sections that share the same "group" name form an
			accordion where only one section is open at a time.
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
				<<< TXT
					The initial state of the section. If true, the content is hidden until a visitor clicks
					the title. If false, the section is expanded by default.
					TXT
			),
			'content' => new AgentFieldSchema(
				'array',
				<<< TXT
					The child blocks that are revealed when the section is expanded. Any block type can be
					used here.
					TXT,
				items: array('$ref' => SchemaReference::BLOCK),
				hasBlocks: true
			),
			'group' => new AgentFieldSchema(
				'string',
				<<< TXT
					The name of an accordion group. Sections that share the same group name behave like HTML
					<details> elements that share the same value for their "name" attribute, so opening one
					section closes all others. Use an empty string for an independent section.
					TXT
			),
			'title' => new AgentFieldSchema(
				'string',
				<<< TXT
					The always visible and clickable title of the section that is rendered as the HTML
					<summary> element. For FAQs, use the question as title.
					TXT
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
