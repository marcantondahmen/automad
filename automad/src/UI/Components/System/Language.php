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

namespace Automad\UI\Components\System;

use Automad\Core\Str;
use Automad\UI\Components\Form\Select;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The language system setting component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Language {
	/**
	 * Renders the language component.
	 *
	 * @return string The rendered HTML
	 */
	public static function render() {
		$Text = Text::getObject();
		$languages = array();

		foreach (glob(dirname(AM_FILE_GUI_TEXT_MODULES) . '/*.txt') as $file) {
			if (strpos($file, 'english.txt') !== false) {
				$value = '';
			} else {
				$value = Str::stripStart($file, AM_BASE_DIR);
			}

			$key = ucfirst(str_replace(array('_', '.txt'), array(' ', ''), basename($file)));
			$languages[$key] = $value;
		}

		$button = Select::render(
			'language',
			$languages,
			AM_FILE_GUI_TRANSLATION,
			'',
			'uk-button-large uk-button-success'
		);

		return <<< HTML
			<p>$Text->sys_language_info</p>
			<form 
			class="uk-form uk-form-stacked"
			data-am-controller="Config::update" 
			data-am-auto-submit
			>
				<input type="hidden" name="type" value="language" />
				$button
			</form>
		HTML;
	}
}
