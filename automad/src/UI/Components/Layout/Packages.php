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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Layout;

use Automad\UI\Components\Grid\Packages as GridPackages;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The packages layout.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Packages {
	/**
	 * Render the packages layout.
	 *
	 * @param array $packages
	 * @return string the rendered packages layout.
	 */
	public static function render(array $packages) {
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<form class="uk-display-inline-block" data-am-controller="PackageManager::updateAll">
				<button 
				class="uk-button uk-button-success"
				data-uk-modal="{target:'#am-modal-update-all-packages-progress',keyboard:false,bgclose:false}"
				>
					<i class="uk-icon-refresh"></i>&nbsp;
					{$fn(Text::get('packages_update_all'))}
				</button>&nbsp;
			</form>
			<a 
			href="https://packages.automad.org" 
			class="uk-button uk-button-link uk-hidden-small" 
			target="_blank"
			>
				<i class="uk-icon-folder-open-o"></i>&nbsp;
				{$fn(Text::get('packages_browse'))}
			</a>
			{$fn(GridPackages::render($packages))}
		HTML;
	}
}
