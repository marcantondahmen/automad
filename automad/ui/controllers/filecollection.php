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
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Core\Str;
use Automad\UI\Components\Layout\FileCollection as LayoutFileCollection;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The file collection controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class FileCollection {


	/**
	 *	Remove selected files from the selection or 
	 *	simply return the collection of uploaded files for a context.
	 *
	 *	@return array $output
	 */

	public static function edit() {

		$Automad = UICache::get();
		$output = array();
		$url = Request::post('url');

		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		if (!array_key_exists($url, $Automad->getCollection())) {
			$url = '';
			$modalTitle = Text::get('shared_title');
		} else {
			$modalTitle = $Automad->getPage($url)->get(AM_KEY_TITLE);
		}

		$path = FileSystem::getPathByPostUrl($Automad);

		// Delete files in $_POST['delete'].
		if ($delete = Request::post('delete')) {
			$output = self::deleteFiles($delete, $path);
		}

		// Get files for each allowed file type.
		$files = FileSystem::globGrep(
			$path . '*.*', 
			'/\.(' . implode('|', Parse::allowedFileTypes()) . ')$/i'
		);

		$output['html'] = LayoutFileCollection::render($files, $url, $modalTitle);

		return $output;

	}


	/**
	 *	Upload controller based on $_POST and $_FILES.
	 *
	 *	@return array $output
	 */

	public static function upload() {

		$Automad = UICache::get();
		$output = array();
		Debug::log($_POST + $_FILES, 'files');

		// Set path.
		// If an URL is also posted, use that URL's page path. Without any URL, the /shared path is used.
		$path = FileSystem::getPathByPostUrl($Automad);

		// Move uploaded files
		if (isset($_FILES['files']['name'])) {

			// Check if upload destination is writable.
			if (is_writable($path)) {

				$errors = array();

				// In case the $_FILES array consists of multiple files (IE uploads!).
				for ($i = 0; $i < count($_FILES['files']['name']); $i++) {

					// Check if file has a valid filename (allowed file type).
					if (FileSystem::isAllowedFileType($_FILES['files']['name'][$i])) {
						$newFile = $path . Str::sanitize($_FILES['files']['name'][$i]);
						move_uploaded_file($_FILES['files']['tmp_name'][$i], $newFile);
					} else {
						$errors[] = Text::get('error_file_format') . ' "' . 
									FileSystem::getExtension($_FILES['files']['name'][$i]) . '"';
					}
				}

				Cache::clear();

				if ($errors) {
					$output['error'] = implode('<br />', $errors);
				}

			} else {

				$output['error'] = Text::get('error_permission') . ' "' . basename($path) . '"';

			}

		}

		return $output;

	}


	/**
	 *	Delete files.
	 *
	 *	@param array $files
	 *	@param string $path
	 *	@return array $output
	 */

	private static function deleteFiles($files, $path) {

		$output = array();

		// Check if directory is writable.
		if (is_writable($path)) {

			$success = array();
			$errors = array();

			foreach ($files as $f) {

				// Make sure submitted filename has no '../' (basename).
				$file = $path . basename($f);

				if ($error = FileSystem::deleteMedia($file)) {
					$errors[] = $error;
				} else {
					$success[] = '"' . basename($file) . '"';
				}
			}

			Cache::clear();

			$output['success'] = Text::get('success_remove') . '<br />' . implode('<br />', $success);
			$output['error'] = implode('<br />', $errors);

		} else {

			$output['error'] = Text::get('error_permission') . ' "' . basename($path) . '"';

		}

		return $output;

	}


}