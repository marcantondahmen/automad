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
 * Copyright (c) 2020-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The pagelist block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Pagelist extends AbstractBlock {
	/**
	 * Render a pagelist block.
	 *
	 * @param object{id: string, data: object, tunes: object} $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $block, Automad $Automad): string {
		$Pagelist = $Automad->getPagelist();

		$match = false;

		if (!empty($block->data->matchUrl)) {
			$match = json_encode(array('url' => '/(' . $block->data->matchUrl . ')/'));
		}

		$Pagelist->config(
			array_merge(
				$Pagelist->getDefaults(),
				array(
					'context' => $block->data->context ?? false,
					'excludeCurrent' => $block->data->excludeCurrent ?? false,
					'excludeHidden' => $block->data->excludeHidden ?? true,
					'filter' => $block->data->filter ?? false,
					'limit' => intval($block->data->limit ?? 10),
					'match' => $match,
					'offset' => intval($block->data->offset ?? 0),
					'sort' => ($block->data->sortField ?? ':index') . ' ' . ($block->data->sortOrder ?? 'asc'),
					'template' => $block->data->template ?? '',
					'type' => $block->data->type ?? ''
				)
			)
		);

		$file = AM_DIR_PACKAGES . ($block->data->file ?? '');

		if (!is_file(AM_BASE_DIR . $file)) {
			$file = '/automad/src/server/Blocks/Templates/Pagelist.php';
		}

		$attr = Attr::render($block->tunes);
		$html = Snippet::render(
			(object) array(
				'id' => '',
				'data' => (object) array(
					'file' => $file,
					'snippet' => ''
				)
			),
			$Automad
		);

		return "<am-pagelist $attr>$html</am-pagelist>";
	}
}
