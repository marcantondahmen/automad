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

		$fn = $this->fn;

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled"><i class="uk-icon-search"></i></li>
				<li><a href="">{$fn(Text::get('search_title'))}</a></li>
			</ul>
			<div class="uk-form" data-am-search>
				<div class="am-sticky uk-form-row">
					<input 
					class="uk-width-1-1" 
					type="search" 
					name="searchValue" 
					placeholder="{$fn(Text::get('search_placeholder'))}"
					value="{$fn(Request::query('search'))}"
					>
				</div>
				<div class="uk-form-row">
					<input 
					class="uk-width-1-1" 
					type="text" 
					name="replaceValue" 
					placeholder="{$fn(Text::get('search_replace_placeholder'))}"
					value=""
					>
				</div>
				<button type="button" name="replaceSelected">Replace Selected</button>
				<button type="button" name="checkAll">Check All</button>
				<button type="button" name="unCheckAll">Uncheck All</button>
				<label for="">Regex</label>
				<input type="checkbox" name="isRegex">
				<form class="uk-form"></form>
			</div>
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


}