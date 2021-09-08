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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The pagelist block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Pagelist extends AbstractBlock {
	/**
	 * Render a pagelist block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		$Pagelist = $Automad->getPagelist();

		// Reset pagelist.
		$Pagelist->config($Pagelist->getDefaults());

		$defaults = array(
			'type' => '',
			'matchUrl' => '',
			'excludeHidden' => true,
			'filter' => '',
			'template' => '',
			'excludeCurrent' => true,
			'limit' => null,
			'offset' => 0,
			'sortKey' => ':path',
			'sortOrder' => 'asc',
			'file' => ''
		);

		$options = array_merge($defaults, (array) $data);
		$options['sort'] = $options['sortKey'] . ' ' . $options['sortOrder'];

		if (!empty($options['matchUrl'])) {
			$options['match'] = json_encode(array('url' => '/(' . $options['matchUrl'] . ')/'));
		}

		$Pagelist->config($options);

		$options['file'] = AM_DIR_PACKAGES . $options['file'];

		if (!is_readable(AM_BASE_DIR . $options['file'])) {
			$options['file'] = '/automad/blocks/templates/pagelist.php';
		}

		$html = Snippet::render((object) $options, $Automad);
		$class = self::classAttr();

		return "<am-pagelist $class>$html</am-pagelist>";
	}
}
