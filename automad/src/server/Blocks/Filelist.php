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
 * The filelist block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Filelist extends AbstractBlock {
	/**
	 * Render a filelist block.
	 *
	 * @param object{id: string, data: object, tunes: object} $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $block, Automad $Automad): string {
		$Filelist = $Automad->getFilelist();

		$Filelist->config(
			array(
				'glob' => $block->data->glob ?? '*.*',
				'sort' => $block->data->sortOrder ?? 'asc'
			)
		);

		$file = AM_DIR_PACKAGES . ($block->data->file ?? '');

		if (!is_file(AM_BASE_DIR . $file)) {
			$file = '/automad/src/server/Blocks/Templates/Filelist.php';
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

		return "<am-filelist $attr>$html</am-filelist>";
	}
}
