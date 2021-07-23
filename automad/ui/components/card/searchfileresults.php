<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI\Components\Card;

use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The search match card component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class SearchFileResults {


	/**
	 *	Render a search match card.
	 *
	 *	@param string $file
	 *	@param object $fileResults
	 *	@return string the rendered card
	 */

	public static function render($file, $fileResults) {

		$dir = dirname($file);
		$id = 'am-search-file-' . Str::sanitize($dir, true, 500);
		$results = '';

		foreach ($fileResults as $match) {
			$results .= "<hr><small class='uk-text-muted'>{$match->key}</small><br>{$match->context}";
		}
		
		return <<< HTML
			<div id="$id" class="uk-panel uk-panel-box uk-active uk-margin-small-top">
				<div class="uk-flex uk-flex-space-between">
					<div class="uk-text-truncate">
						<i class="uk-icon-file-text-o"></i>&nbsp; 
						$dir
					</div>
					<label 
					class="am-toggle-checkbox uk-active" 
					data-am-toggle="#$id">
						<input type="checkbox" name="files[]" value="$file" checked="on" />
					</label>
				</div>
				$results
			</div>
HTML;

	}


}