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
 *	The link modal component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Link {


	/**
	 *  Create the modal dialog for adding links.
	 * 
	 *	@return string The HTML for the modal dialog
	 */

	public static function render() {

		$Text = Text::getObject();

		// Add dashboard URL to handler to make dialog work in in-Page edit mode.
		$autocomplete = AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?ajax=autocomplete_link';

		return <<< HTML

			<div id="am-link-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						$Text->link_title
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<div 
					class="am-form-input-button uk-form uk-flex" 
					data-am-link="$autocomplete"
					>
						<div class="uk-autocomplete uk-width-1-1">
							<input class="uk-form-controls uk-width-1-1" type="text" placeholder="$Text->link_placeholder">
							<script type="text/autocomplete">	
								<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">
									{{~items}}
										<li data-value="{{ \$item.value }}">
											<a>
												<i class="uk-icon-link"></i>&nbsp;
												{{ \$item.title }}
											</a>
										</li>
									{{/items}}
								</ul>
							</script>
						</div>	
						<button type="button" class="uk-button uk-button-success uk-text-nowrap">
							<i class="uk-icon-link"></i>&nbsp;
							$Text->btn_link
						</button>
					</div>
				</div>
			</div>

HTML;

	}
	

}