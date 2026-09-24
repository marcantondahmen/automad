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

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The pagelist block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Pagelist extends AbstractBlock {
	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return <<< TXT
			A dynamically generated list of pages that are renderd based on a selected pagelist template.
			TXT;
	}

	/**
	 * Render a pagelist block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$Pagelist = $Automad->Pagelist;
		$data = $block['data'];

		$match = false;

		if (!empty($block['data']['matchUrl'])) {
			$match = json_encode(array('url' => '/(' . $block['data']['matchUrl'] . ')/'));
		}

		$Pagelist->config(
			array_merge(
				$Pagelist->getDefaults(),
				array(
					'context' => $data['context'] ?? false,
					'excludeCurrent' => $data['excludeCurrent'] ?? false,
					'excludeHidden' => $data['excludeHidden'] ?? true,
					'filter' => $data['filter'] ?? false,
					'limit' => intval($data['limit'] ?? 10),
					'match' => $match,
					'offset' => intval($data['offset'] ?? 0),
					'sort' => ($data['sortField'] ?? ':index') . ' ' . ($data['sortOrder'] ?? 'asc'),
					'template' => $data['template'] ?? '',
					'type' => $data['type'] ?? ''
				)
			)
		);

		$file = AM_DIR_PACKAGES . ($data['file'] ?? '');

		if (!is_file(AM_BASE_DIR . $file)) {
			$file = '/automad/src/server/Blocks/Templates/Pagelist.php';
		}

		$attr = Attr::render($block['tunes']);
		$html = Snippet::render(
			array(
				'id' => '',
				'type' => '',
				'data' => array(
					'file' => $file,
					'snippet' => ''
				),
				'tunes' => array(
					'id' => '',
					'className' => '',
					'layout' => null,
					'spacing'=> array()
				)
			),
			$Automad
		);

		return "<am-pagelist $attr>$html</am-pagelist>";
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
		return '';
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'context' => new AgentFieldSchema(
				'string',
				'The local page URL as an absolute path.',
				true
			),
			'file' => new AgentFieldSchema(
				'string',
				<<< TXT
					The Automad template used to render each page preview. 
					Before selecting a template, read the available page-list templates 
					from the `automad://templates/pagelist` resource. 
					Use one of the returned template files.'
					TXT
			),
			'limit' => new AgentFieldSchema(
				'number',
				'The maximum amount of pages that are rendered. Skip this in order to render all pages.',
				true
			),
			'sortField' => new AgentFieldSchema(
				'string',
				'The field that is used for sorting.',
				true,
				array(':index', 'date', 'title')
			),
			'sortOrder' => new AgentFieldSchema(
				'string',
				'The sorting order',
				true,
				array('asc', 'desc')
			),
			'type' => new AgentFieldSchema(
				'string',
				<<< TXT
					The type of pagelist in term of context, like children, siblings, 
					or related pages of the context page. Skip this for showing all pages.
					TXT,
				true,
				array('children', 'siblings', 'related')
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
