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


namespace Automad\GUI\Components;
use Automad\Core as Core;
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The page grid component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class PageGrid {


	/**
	 *	Generate thumbnail for page grid.
	 *      
	 *  @param string $file  
	 *  @param float $w     
	 *  @param float $h     
	 *  @param string $gridW (uk-width-* suffix) 
	 *  @return string The generated markup
	 */
	
	private static function gridThumbnail($file, $w, $h, $gridW) {
		
		$img = new Core\Image($file, $w, $h, true);
		return 	'<li class="uk-width-' . $gridW . '">' .
				'<img src="' . AM_BASE_URL . $img->file . '" alt="' . basename($img->file) . '" width="' . $img->width . '" height="' . $img->height . '">' .
				'</li>';
	
	}


	/**
	 *	Create a grid based page list for the given array of pages.
	 *
	 *	@param array $pages
	 *	@return string The HTML for the grid
	 */
	
	public static function render($pages) {
	
		$html = '<ul class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-top" data-uk-grid-match="{target:\'.uk-panel\'}" data-uk-grid-margin>';
		
		foreach ($pages as $key => $Page) {
			
			$link = '?context=edit_page&url=' . urlencode($key);
			
			$html .= '<li>' . 
					 '<div class="uk-panel uk-panel-box">' . 
					 '<a href="' . $link . '" class="uk-panel-teaser uk-display-block">'; 
			
			// Build file grid with up to 6 images.
			$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			$files = Core\FileSystem::globGrep($path . '*.*', '/(jpg|jpeg|png|gif)$/i');
			
			if (!empty($files)) {
				
				$count = count($files);
				$wFull = 320;
				$hFull = 240;

				// File grid.
				$html .= '<ul class="uk-grid uk-grid-collapse">';
				
				if ($count == 1) {
					$html .= self::gridThumbnail($files[0], $wFull, $hFull, '1-1');
				}
				
				if ($count == 2) {
					$html .= self::gridThumbnail($files[0], $wFull/2, $hFull, '1-2');
					$html .= self::gridThumbnail($files[1], $wFull/2, $hFull, '1-2');
				}
				
				if ($count == 3) {
					$html .= self::gridThumbnail($files[0], $wFull, $hFull/2, '1-1');
					$html .= self::gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= self::gridThumbnail($files[2], $wFull/2, $hFull/2, '1-2');
				}
				
				if ($count == 4) {
					$html .= self::gridThumbnail($files[0], $wFull/2, $hFull/2, '1-2');
					$html .= self::gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= self::gridThumbnail($files[2], $wFull/2, $hFull/2, '1-2');
					$html .= self::gridThumbnail($files[3], $wFull/2, $hFull/2, '1-2');
				}
				
				if ($count == 5) {
					$html .= self::gridThumbnail($files[0], $wFull/2, $hFull/2, '1-2');
					$html .= self::gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= self::gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[3], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[4], $wFull/3, $hFull/2, '1-3');
				}
				
				if ($count >= 6) {
					$html .= self::gridThumbnail($files[0], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[1], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[3], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[4], $wFull/3, $hFull/2, '1-3');
					$html .= self::gridThumbnail($files[5], $wFull/3, $hFull/2, '1-3');
				}
				
				$html .= '</ul>';
				
			} else {
				
				$html .= '<div class="am-panel-icon"><i class="uk-icon-folder-open"></i></div>';
				
			}
			
			$html .= 	'</a>' .
						// Title & date. 
						'<div class="uk-panel-title">' .
						$Page->get(AM_KEY_TITLE) . 
						'</div>' .
						'<div class="uk-text-small">' . Core\Str::dateFormat($Page->getMtime(), 'j. M Y') . '</div>' .
						'<div class="am-panel-bottom">' .
						'<span>' . 
						'<a href="' . $link . '" title="' . Text::get('btn_edit_page') . '" class="uk-icon-button uk-icon-pencil" data-uk-tooltip></a>&nbsp;' .
						'<a href="' . AM_BASE_INDEX . $Page->url . '" title="' . Text::get('btn_inpage_edit') . '" class="uk-icon-button uk-icon-share" data-uk-tooltip></a>' .
						'</span>' .
						'</div>' .
						'</div>' .
						'</li>';
				
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	

}