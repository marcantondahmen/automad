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


namespace Automad\UI\Views;

use Automad\Core\Request;
use Automad\UI\Components\Alert\Alert;
use Automad\UI\Components\Grid\Pages;
use Automad\UI\Controllers\Search as ControllersSearch;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The search page.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Search extends View {


	/**
	 *	Render body.
	 *
	 *	@return string the rendered items
	 */

	protected function body() {

		$results = ControllersSearch::results();

		$fn = $this->fn;

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled"><i class="uk-icon-search"></i></li>
				<li><a href="">{$fn(Text::get('search_title'))}</a></li>
			</ul>
			<h2 class="uk-margin-top-remove">
				<i class="uk-icon-angle-double-left"></i>
				{$fn(Request::query('search'))}
				<i class="uk-icon-angle-double-right"></i>&nbsp;
				<span class="uk-badge">{$fn(count($results))}</span>
			</h2>
			{$fn($this->results($results))}
HTML;

	}


	/**
	 *	Get the title for the dashboard view.
	 *
	 *	@return string the rendered items
	 */

	protected function title() {

		$title = Text::get('search_title');

		return "$title &mdash; Automad";

	}


	/**
	 *	Render page grid or alert.
	 *
	 *	@return string the rendered page grid
	 */

	private function results($results) {

		if ($results) {
			return Pages::render($results);
		}

		return Alert::render(Text::get('search_no_results'), 'uk-alert-danger uk-margin-top');

	}


}