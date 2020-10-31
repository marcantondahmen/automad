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


namespace Automad\GUI\Components\Card;
use Automad\Core as Core;
use Automad\GUI as GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The theme card component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Theme {


	/**
	 *	Render a theme card.
	 *	
	 *	@param object $Theme
	 *	@param object $activeTheme
	 *	@param string $id
	 *	@return string The HTML of the card
	 */

	public static function render($Theme, $activeTheme, $id) {

		$Text = GUI\Text::getObject();
		$path = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Theme->path;
		$files = GUI\FileSystem::glob($path . '/*');
		$key = AM_KEY_THEME;
	
		// Set icon.
		if ($images = preg_grep('/\.(jpg|jpeg|png|gif$)/i', $files)) {
			$img = new Core\Image(reset($images), 600, 450, true);
			$icon = '<img src="' . AM_BASE_URL . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
		} else {
			$icon = '<i class="uk-icon-code"></i>';
		}

		// Check currently active theme.
		$attrChecked = '';

		if ($activeTheme) {
			if ($Theme->path === $activeTheme->path) {
				$attrChecked = ' checked';
			} 
		}

		if ($Theme->readme) {

			$icon = <<< HTML
					<a href="#{$id}-modal" data-uk-modal>$icon</a>
HTML;

		}

		$badge = '';
		$author = '';
		$license = '';
		$readmeButton = '';

		if ($Theme->version) {
			$badge = <<< HTML
					<div class="uk-panel-badge uk-badge">$Theme->version</div>
HTML;
		} 

		if ($Theme->author) {
			$author = <<< HTML
					<div class="uk-text-small uk-hidden-small">
						<i class="uk-icon-copyright uk-icon-justify"></i>&nbsp;
						$Theme->author
					</div>
HTML;
		}

		if ($Theme->license) {
			$license = <<< HTML
					<div class="uk-text-small uk-hidden-small">
						<i class="uk-icon-balance-scale uk-icon-justify"></i>&nbsp;
						$Theme->license
					</div>
HTML;
		}

		if ($Theme->readme) {
			$readmeButton = <<< HTML
							<div class="am-panel-bottom-left">
								<a 
								href="#{$id}-modal"
								class="am-panel-bottom-link"
								title="$Text->btn_readme"
								data-uk-tooltip
								data-uk-modal
								>
									<i class="uk-icon-file-text-o"></i>
								</a>
							</div>
HTML;
		}

		$readme = GUI\Components\Modal\Readme::render($id . '-modal', $Theme->readme);

		return <<< HTML
					$readme
					<div id="$id" class="uk-panel uk-panel-box">
						<div class="uk-panel-teaser">
							<div class="am-cover-4by3">
								$icon
							</div>
						</div>
						$badge
						<div class="uk-panel-title">
							$Theme->name
						</div>
						<div class="uk-text-small uk-hidden-small">
							$Theme->description
						</div>
						$author
						$license
						<div class="am-panel-bottom">
							$readmeButton	
							<div class="am-panel-bottom-right">
								<label 
								class="am-toggle-checkbox am-panel-bottom-link" 
								data-am-toggle="#$id"
								>
									<input 
									type="radio" 
									name="data[$key]" 
									value="$Theme->path" 
									data-am-modal-on-change="#am-apply-theme-modal"
									$attrChecked 
									/>
								</label>
							</div>
						</div>
					</div>
HTML;
			
	}


}