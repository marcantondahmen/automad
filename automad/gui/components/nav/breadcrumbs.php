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


namespace Automad\GUI\Components\Nav;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The breadcrumb component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Breadcrumbs {


	/**
	 *	Create a breadcrumb navigation based on $_GET.
	 *
	 *	@param object $Automad
	 *	@return string The breadcrumb naviagtion markup
	 */

	public static function render($Automad) {

		$Selection = new Core\Selection($Automad->getCollection());
		$Selection->filterBreadcrumbs(Core\Request::query('url'));
		$pages = $Selection->getSelection(false);
		
		$html = '<ul class="am-breadcrumbs uk-subnav uk-subnav-pill uk-margin-top">';
		$html .= '<li class="uk-hidden-small"><i class="uk-icon-folder-open-o"></i></li>';
		
		$i = count($pages);
		
		$small = 2;
		$large = 4;
		
		if ($i > $small) {
			$html .= '<li class="uk-visible-small"><i class="uk-icon-angle-double-right"></i></li>';
		}
		
		if ($i > $large) {
			$html .= '<li class="uk-hidden-small"><i class="uk-icon-angle-double-right"></i></li>';
		}
		
		foreach ($pages as $url => $Page) {
			
			if ($i <= $large) {
				
				$class= '';
			
				if ($i > $small) {
					$class .= ' class="uk-hidden-small"';
				}
				
				$html .= '<li' . $class . '><a href="?context=edit_page&url=' . urlencode($url) . '">' . htmlspecialchars($Page->get(AM_KEY_TITLE)) . '</a></li>';
				
				if ($i > 1) {
					$html .= '<li' . $class . '><i class="uk-icon-angle-right"></i></li>';
				}
				
			}
			
			$i--;
			
		}
		
		$html .= '</ul>';
		
		return $html;
	
	}


}