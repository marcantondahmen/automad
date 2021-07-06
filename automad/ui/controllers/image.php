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


namespace Automad\UI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Image as CoreImage;
use Automad\Core\Request;
use Automad\UI\Components\Layout\SelectImage;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Image controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Image {


	/**
	 *	Copy an image resized based on $_POST.
	 *
	 *	@return array the $output array
	 */

	public static function copyResized() {

		$output = array();
		$Automad = UICache::get();

		$options = array_merge(
			array(
				'url' => '',
				'filename' => '',
				'width' => false,
				'height' => false,
				'crop' => false
			),
			$_POST
		);

		$width = $options['width'];
		$height = $options['height'];

		if (!((is_numeric($width) || is_bool($width)) && (is_numeric($height) || is_bool($height)))) {
			$output['error'] = Text::get('error_file_size');
			return $output;
		}

		if ($options['filename']) {

			// Get parent directory.
			if ($options['url']) {
				$Page = $Automad->getPage($options['url']);
				$directory = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			} else {
				$directory = AM_BASE_DIR . AM_DIR_SHARED . '/';
			}

			$file = $directory . $options['filename'];

			Debug::log($file, 'file');
			Debug::log($options, 'options');

			if (file_exists($file)) {

				if (is_writable($directory)) {

					$img = new CoreImage(
						$file,
						$width,
						$height,
						boolval($options['crop'])
					);

					$cachedFile = AM_BASE_DIR . $img->file;
					$resizedFile = preg_replace(
						'/(\.\w{3,4})$/', 
						'-' . floor($img->width) . 'x' . floor($img->height) . '$1', 
						$file
					);

					if (!$output['error'] = FileSystem::renameMedia($cachedFile, $resizedFile)) {
						$output['success'] = Text::get('success_created') . ' "' . basename($resizedFile) . '"';
						Cache::clear();
					}
				} else {
					$output['error'] = Text::get('error_permission') . ' "' . $directory . '"';
				}

			} else {

				$output['error'] = Text::get('error_file_not_found');

			}

		}

		return $output;
	}


	/**
	 *	Select an image.
	 *
	 *	@return array the $output array
	 */

	public static function select() {

		$Automad = UICache::get();
		$output = array();
		$pageImages = array();

		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		$url = Request::post('url');

		if (!array_key_exists($url, $Automad->getCollection())) {
			$url = '';
		}

		if ($url) {

			$pageImages = FileSystem::globGrep(
				FileSystem::getPathByPostUrl($Automad) . '*.*', 
				'/\.(jpg|jpeg|gif|png)$/i'
			);

			sort($pageImages);

		}

		$sharedImages = FileSystem::globGrep(
			AM_BASE_DIR . AM_DIR_SHARED . '/*.*', 
			'/\.(jpg|jpeg|gif|png)$/i'
		);

		sort($sharedImages);

		$output['html'] = SelectImage::render($pageImages, $sharedImages);

		return $output;

	}


}