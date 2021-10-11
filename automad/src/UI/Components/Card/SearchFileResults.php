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
use Automad\UI\Models\Search\FileResultsModel;
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
	 * @param FileResultsModel $FileResultsModel
	 * @return string the rendered card
	 */
	public static function render(FileResultsModel $FileResultsModel) {
		$dir = dirname($FileResultsModel->path);
		$id = 'am-search-file-' . Str::slug($dir, true, 500);
		$results = '';
		$keys = array();
		$editText = $FileResultsModel->url;
		$editUrl = '?view=Page&url=' . urlencode($FileResultsModel->url);

		if (!$FileResultsModel->url) {
			$editText = Text::get('shared_title');
			$editUrl = '?view=Shared';
		}

		foreach ($FileResultsModel->fieldResultsArray as $FieldResultsModel) {
			$keys[] = $FieldResultsModel->key;
			$results .= "<hr><small class='uk-text-muted'>{$FieldResultsModel->key}</small><br>{$FieldResultsModel->context}";
		}

		$keysJson = json_encode($keys);

		return <<< HTML
			<div id="$id" class="uk-panel uk-panel-box uk-margin-small-top">
				<div class="uk-flex uk-flex-space-between">
					<a href="$editUrl" class="am-panel-link uk-text-truncate">
						<i class="uk-icon-file-text-o"></i>&nbsp; 
						$editText
					</a>
					<label 
					class="am-toggle-checkbox" 
					data-am-toggle="#$id">
						<input type="checkbox" name="files[{$FileResultsModel->path}]" value='$keysJson' />
					</label>
				</div>
				$results
			</div>
		HTML;
	}
}
