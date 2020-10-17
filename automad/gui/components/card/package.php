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
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The package card component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Package {


	/**
	 *	Render a package card.
	 *	
	 *	@param object $package
	 *	@return string The HTML of the card
	 */

	public static function render($package) {

		$Text = Text::getObject();
		$badge = '';
		$button = '';
		$active = '';

		if ($package->installed) {

			$active = 'uk-active';

			$badge = <<< HTML
					<span class="uk-panel-badge uk-badge uk-badge-success">
						$Text->packages_installed
					</span>
HTML;

			$button = <<< HTML
					<div class="am-panel-bottom-right">
						<form data-am-handler="remove_package">
							<input type="hidden" name="package" value="$package->name">
							<button 
							class="uk-button uk-button-small"
							data-uk-modal="{target:'#am-modal-remove-package-progress',keyboard:false,bgclose:false}"
							>
								$Text->btn_remove
								&nbsp;<i class="uk-icon-times"></i>	
							</button>
						</form>
						<form class="uk-hidden" data-am-handler="update_package">
							<input type="hidden" name="package" value="$package->name">
							<button 
							class="uk-button uk-button-success uk-button-small"
							data-uk-modal="{target:'#am-modal-update-package-progress',keyboard:false,bgclose:false}"
							>
								$Text->btn_update
								&nbsp;<i class="uk-icon-refresh"></i>	
							</button>
						</form>
					</div>		
HTML;

		} else {

			$button = <<< HTML
					<form class="am-panel-bottom-right" data-am-handler="install_package">
						<input type="hidden" name="package" value="$package->name">
						<button 
						class="uk-button uk-button-small"
						data-uk-modal="{target:'#am-modal-install-package-progress',keyboard:false,bgclose:false}"
						>
							$Text->btn_install	
							&nbsp;<i class="uk-icon-download"></i>
						</button>
					</form>
HTML;

		}

		return <<< HTML
				<div class="uk-panel uk-panel-box $active" data-package="$package->name">
					<a 
					href="$package->info" 
					class="uk-display-block"
					target="_blank"
					>
						<i class="uk-icon-file-zip-o uk-icon-small"></i>
						<div class="uk-panel-title uk-margin-small-top uk-padding-top-remove">
								$package->name
						</div>
						<div class="uk-text-small">
							$package->description
						</div>
						$badge
					</a>
					<div class="am-panel-bottom am-panel-bottom-large">
						<div class="am-panel-bottom-left">
							<a 
							href="$package->info" 
							class="am-panel-bottom-link"
							target="_blank"
							title="$Text->btn_readme"
							data-uk-tooltip
							>
								<i class="uk-icon-file-text-o"></i>
							</a>
						</div>
						$button
					</div>
				</div>
HTML;

	}


}
