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
 * The filelist block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Filelist extends AbstractBlock {
	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return 'A dynamically generated filelist that renders a list of files based on a glob pattern.';
	}

	/**
	 * Render a filelist block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$Filelist = $Automad->Filelist;

		$Filelist->config(
			array(
				'glob' => $block['data']['glob'] ?? '*.*',
				'sort' => $block['data']['sortOrder'] ?? 'asc'
			)
		);

		$file = AM_DIR_PACKAGES . ($block['data']['file'] ?? '');

		if (!is_file(AM_BASE_DIR . $file)) {
			$file = '/automad/src/server/Blocks/Templates/Filelist.php';
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

		return "<am-filelist $attr>$html</am-filelist>";
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
			'file' => new AgentFieldSchema(
				'string',
				<<< TXT
					The Automad template used to render each file. 
					Before selecting a template, read the available file-list templates 
					from the `automad://templates/filelist` resource. 
					Use one of the returned template files.'
					TXT
			),
			'glob' => new AgentFieldSchema(
				'string',
				'A glob pattern that used to select files that are included in the file list.'
			),
			'sortOrder' => new AgentFieldSchema(
				'string',
				'The sorting order. Either "asc" or "desc".',
				enum: array('asc', 'desc')
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
