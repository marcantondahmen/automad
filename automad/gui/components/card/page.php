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
 *	The page card component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Page {


	/**
	 *	Generate thumbnail for page grid.
	 *      
	 *	@param string $file  
	 *	@param float $w     
	 *	@param float $h     
	 *	@param string $gridW (uk-width-* suffix) 
	 *	@return string The generated markup
	 */
	
	private static function thumbnail($file, $w, $h, $gridW) {
		
		$img = new Core\Image($file, $w, $h, true);
		return 	'<li class="uk-width-' . $gridW . '"><img src="' . AM_BASE_URL . $img->file . '" /></li>';
	
	}


	/**
	 *	Layout preview images.
	 *
	 *	@param array $images
	 *	@return string The generated HTML 
	 */

	private static function layout($images) {

		$count = count($images);
		$wFull = 320;
		$hFull = 240;

		$html = '<ul class="uk-grid uk-grid-collapse">';
		
		if ($count == 1) {
			$html .= self::thumbnail($images[0], $wFull, $hFull, '1-1');
		}
		
		if ($count == 2) {
			$html .= self::thumbnail($images[0], $wFull/2, $hFull, '1-2');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull, '1-2');
		}
		
		if ($count == 3) {
			$html .= self::thumbnail($images[0], $wFull, $hFull/2, '1-1');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[2], $wFull/2, $hFull/2, '1-2');
		}
		
		if ($count == 4) {
			$html .= self::thumbnail($images[0], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[2], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[3], $wFull/2, $hFull/2, '1-2');
		}
		
		if ($count == 5) {
			$html .= self::thumbnail($images[0], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[2], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[3], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[4], $wFull/3, $hFull/2, '1-3');
		}
		
		if ($count >= 6) {
			$html .= self::thumbnail($images[0], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[1], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[2], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[3], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[4], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[5], $wFull/3, $hFull/2, '1-3');
		}
		
		$html .= '</ul>';

		return $html;

	}


	/**
	 *	Render a page card.
	 *	
	 *	@param object $Page
	 *	@return string The HTML of the card
	 */

	public static function render($Page) {

		$link = '?context=edit_page&url=' . urlencode($Page->get(AM_KEY_ORIG_URL));

		$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
		$images = Core\FileSystem::globGrep($path . '*.*', '/(jpg|jpeg|png|gif)$/i');
		
		if (!empty($images)) {
			$preview = self::layout($images);
		} else {
			$preview = '<i class="uk-icon-file-text-o"></i>';
		}

		$pageTitle = htmlspecialchars($Page->get(AM_KEY_TITLE));
		$pageMTime = Core\Str::dateFormat($Page->getMtime(), 'j. M Y');
		$pageUrl = AM_BASE_INDEX . $Page->url;
		$Text = Text::getObject();

		if ($Page->private) {
			$badge = '<span class="uk-panel-badge uk-badge">' . 
					 '<i class="uk-icon-lock"></i>&nbsp;&nbsp;' . 
					 $Text->page_private .
					 '</span>';
		} else {
			$badge = '';
		}

		return <<< HTML

				<div class="uk-panel uk-panel-box">
					<a 
					href="$link" 
					class="uk-panel-teaser uk-display-block"
					>
						<div class="am-cover-4by3">
							$preview
						</div>
					</a>
					<div class="uk-panel-title">$pageTitle</div>
					<div class="uk-text-small">$pageMTime</div>
					<div class="am-panel-bottom">
						<div class="am-panel-bottom-left">
							<a 
							href="$link" 
							title="$Text->btn_edit_page"  
							class="am-panel-bottom-link"
							data-uk-tooltip
							>
								<i class="uk-icon-file-text-o"></i>
							</a>
							<a 
							href="$pageUrl" 
							title="$Text->btn_inpage_edit" 
							class="am-panel-bottom-link"
							data-uk-tooltip
							>
								<i class="uk-icon-bookmark-o"></i>
							</a>
						</div>
					</div>
					$badge
				</div>

HTML;
			
	}


}