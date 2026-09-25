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
 * Copyright (c) 2020-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Blocks\Schema\AgentFieldSchema;
use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The table block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Table extends AbstractBlock {
	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return <<< TXT
			A data table that is rendered as an HTML <table>, optionally using the first row as
			heading row. Use it for tabular data like comparisons, specifications, schedules or
			pricing. Tables are not meant for layout purposes, use the "layoutSection" block to
			arrange content in columns instead.
			TXT;
	}

	/**
	 * Render a table block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$attr = Attr::render($block['tunes']);
		$data = $block['data'];
		$html = "<am-table $attr><table>";
		$rows = $data['content'] ?? array();

		if (!empty($data['withHeadings'])) {
			$firstRow = array_shift($rows) ?? array();

			$html .= '<thead>';
			$html .= '<tr>';

			foreach ($firstRow as $item) {
				$html .= "<th>$item</th>";
			}

			$html .= '</tr>';
			$html .= '</thead>';
		}

		$html .= '<tbody>';

		foreach ($rows as $row) {
			$html .= '<tr>';

			foreach ($row as $item) {
				$html .= "<td>$item</td>";
			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table></am-table>';

		return $html;
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
		if (empty($block['data']['content'])) {
			return $block;
		}

		$block['data']['content'] = array_map(function (array $row) use ($searchRegex, $replace): array {
			return array_map(fn (string $item) => Replacement::replace($item, $searchRegex, $replace), $row);
		}, $block['data']['content']);

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
		$rows = $block['data']['content'] ?? array();
		$cells = array();

		foreach ($rows as $row) {
			$cells = array_merge($cells, $row);
		}

		return join(' ', $cells);
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'withHeadings' => new AgentFieldSchema(
				'boolean',
				'If true, the first row is rendered as the table heading.',
				true
			),
			'content' => new AgentFieldSchema(
				'array',
				<<< TXT
					The table rows as an array of rows, where each row is an array of cell strings. All rows
					must contain the same number of cells. Cells support inline HTML formatting.
					TXT,
				items: array('type' => 'array', 'items' => array( 'type' => 'string'))
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
