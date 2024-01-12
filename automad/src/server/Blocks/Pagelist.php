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

		// Reset pagelist.
		$Pagelist->config($Pagelist->getDefaults());

		$defaults = array(
			'file' => '',
			'sortField' => ':index',
			'sortOrder' => 'asc',
			'type' => '',
			'excludeHidden' => true,
			'excludeCurrent' => false,
			'matchUrl' => '',
			'filter' => '',
			'template' => '',
			'limit' => null,
			'offset' => 0,
		);

		$options = array_merge($defaults, (array) $block->data);
		$options['sort'] = $options['sortField'] . ' ' . $options['sortOrder'];

		if (!empty($options['matchUrl'])) {
			$options['match'] = json_encode(array('url' => '/(' . $options['matchUrl'] . ')/'));
		}

		$Pagelist->config($options);

		$options['file'] = AM_DIR_PACKAGES . $options['file'];

		if (!is_readable(AM_BASE_DIR . $options['file'])) {
			$options['file'] = '/automad/src/server/Blocks/Templates/Pagelist.php';
		}

		$html = Snippet::render((object) $options, $Automad);
		$attr = Attr::render($block->tunes);

		return "<am-pagelist $attr>$html</am-pagelist>";
	}
}
