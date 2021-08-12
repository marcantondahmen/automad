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

namespace Automad\UI\Components\Card;

use Automad\Core\Str;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The search match card component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SearchFileResults {
	/**
	 * Render a search match card.
	 *
	 * @param \Automad\UI\Models\Search\FileResults $FileResults
	 * @return string the rendered card
	 */
	public static function render($FileResults) {
		$dir = dirname($FileResults->path);
		$id = 'am-search-file-' . Str::slug($dir, true, 500);
		$results = '';
		$keys = array();
		$editText = $FileResults->url;
		$editUrl = '?view=Page&url=' . urlencode($FileResults->url);

		if (!$FileResults->url) {
			$editText = Text::get('shared_title');
			$editUrl = '?view=Shared';
		}

		foreach ($FileResults->fieldResultsArray as $FieldResults) {
			$keys[] = $FieldResults->key;
			$results .= "<hr><small class='uk-text-muted'>{$FieldResults->key}</small><br>{$FieldResults->context}";
		}

		$keysJson = json_encode($keys);

		return <<< HTML
			<div id="$id" class="uk-panel uk-panel-box uk-active uk-margin-small-top">
				<div class="uk-flex uk-flex-space-between">
					<a href="$editUrl" class="am-panel-link uk-text-truncate">
						<i class="uk-icon-file-text-o"></i>&nbsp; 
						$editText
					</a>
					<label 
					class="am-toggle-checkbox uk-active" 
					data-am-toggle="#$id">
						<input type="checkbox" name="files[{$FileResults->path}]" value='$keysJson' checked="on" />
					</label>
				</div>
				$results
			</div>
HTML;
	}
}
