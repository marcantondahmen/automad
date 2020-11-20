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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\Modal;
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The about modal. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class About {


	/**
	 * 	Renders the about modal.
	 * 
	 *	@param string $id
	 *	@return string The rendered HTML
	 */

	public static function render($id) {

		$Text = Text::getObject();
		$logo = file_get_contents(AM_BASE_DIR . '/automad/gui/svg/logo.svg');
		$version = AM_VERSION;
		$year = '2013-' . date('Y');
		
		return <<< HTML
				<div id="$id" class="uk-modal">
					<div class="uk-modal-dialog uk-modal-dialog-about">
						<a href="#" class="uk-modal-close">
							<div class="uk-margin-small-top uk-text-center">
								$logo
							</div>
							<hr>
						</a>
						<strong>Automad</strong> &mdash; a flat-file<br>
						content management system<br>
						and template engine
						<div class="uk-margin-small-top uk-margin-bottom">
							<a 
							href="https://automad.org" 
							class="uk-button uk-button-primary uk-button-mini"
							target="_blank"
							>
								$Text->btn_open_website
							</a><a 
							href="https://automad.org/release-notes" 
							class="uk-button uk-button-mini"
							target="_blank"
							>
								Version $version
							</a>
						</div>
						<p class="uk-text-small">
							<a 
							href="https://automad.org/license" 
							class="uk-text-muted"
							target="_blank"
							>
								Released under the MIT License
							</a>
							<br>
							<a 
							href="https://marcdahmen.de" 
							class="uk-text-muted"
							target="_blank"
							>
								&copy; $year by Marc Anton Dahmen
							</a>
						</p>
					</div>
				</div>
HTML;

	}


}