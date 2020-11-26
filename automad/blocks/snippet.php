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


namespace Automad\Blocks;
use Automad\Core\View as View;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The snippet block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Snippet {


	/**
	 *	This variable tracks whether a snippet is called by another snippet to prevent inifinte recursive loops.
	 */

	private static $snippetIsRendering = false;


	/**	
	 *	Render a snippet block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		// Prevent infinite recursion.
		if (self::$snippetIsRendering) {
			return false;
		}

		self::$snippetIsRendering = true;
		$View = new View($Automad);
		$output = '';

		if (!empty($data->snippet)) {
			$output .= $View->interpret($data->snippet, AM_BASE_DIR . AM_DIR_PACKAGES);
		} 

		if (!empty($data->file)) {

			// Test for files with or without leading slash.
			$file = AM_BASE_DIR . '/' . trim($data->file, '/');

			if (!is_readable($file)) {
				// Test also path without packages directory.
				$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . trim($data->file, '/');
			} 

			if (is_readable($file)) {
				$template = $Automad->loadTemplate($file);
				$output .= $View->interpret($template, dirname($file));
			}

		}

		$View->mergeExtensionAssets(Core\Blocks::$extensionAssets);
		Core\Blocks::$extensionAssets = $View->extensionAssets;
		
		self::$snippetIsRendering = false;

		return $output;

	}


}