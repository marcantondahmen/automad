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


namespace Automad\UI\Controllers;

use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Components\Alert\Alert;
use Automad\UI\Components\Layout\SearchResults;
use Automad\UI\Models\Replacement;
use Automad\UI\Models\Search as ModelsSearch;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Search controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Search { 


	/**
	 *	Perform a search and replace.
	 * 
	 *	@return array the output array
	 */

	public static function searchAndReplace() {

		$output = array();
		
		if (Request::post('replaceSelected')) {

			$Replacement = new Replacement(
				Request::post('searchValue'),
				Request::post('replaceValue'), 
				Request::post('isRegex')
			);

			$Replacement->replaceInFiles(Request::post('files'));

		}

		$Search = new ModelsSearch(
			Request::post('searchValue'),
			Request::post('isRegex')
		);

		$resultsPerFile = $Search->searchPerFile();

		Debug::log($resultsPerFile, 'Results per file');

		if (!$html = SearchResults::render($resultsPerFile)) {
			$html = Alert::render(Text::get('search_no_results'), 'uk-margin-top');
		}

		$output['html'] = $html;

		return $output;

	}
	

}