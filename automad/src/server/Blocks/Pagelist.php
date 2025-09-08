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
 * Copyright (c) 2020-2025 by Marc Anton Dahmen
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
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class Pagelist extends AbstractBlock {
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
				'data' => array(
					'file' => $file,
					'snippet' => ''
				)
			),
			$Automad
		);

		return "<am-pagelist $attr>$html</am-pagelist>";
	}
}
