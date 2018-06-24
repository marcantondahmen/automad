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
 *	Copyright (c) 2017-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\System;
use Automad\Core as Core;
use Automad\GUI as GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Update class handles the process of updating Automad using the dashboard or the CLI. 
 *	Note that initializing the update will first clone a light version of Automad to the cache
 *	directory to run updates from an external location. That extra step is required to make 
 *	updates work on windows.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017-2018 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Update {
	
	
	/**
	 *	The lock file indicates, that there is an update in progress.
	 *	This is only required on Windows servers to deny access to core files 
	 *	during updates to avoid file locking issues. 
	 */
	
	private static $updateLock = AM_BASE_DIR . AM_DIR_CACHE . '/lock';
	
	
	/**
	 *	The update timestamp.
	 */
	
	private static $timestamp = NULL;
	
	
	/**
	 *	Move currently installed items to /cache/update/backup.
	 *	
	 *	@param array $items
	 *	@return boolean True on success, false on error
	 */
	
	private static function backupCurrent($items) {
		
		$backup = AM_BASE_DIR . AM_UPDATE_TEMP . '/backup/' . self::$timestamp;
		
		Core\FileSystem::makeDir($backup);
		
		foreach($items as $item) {
			
			$itemPath = AM_BASE_DIR . $item;
			$backupPath = $backup . $item;
			
			// Only try to backup in case item exists.
			if (file_exists($itemPath)) {
				
				if (is_writable($itemPath) && is_writable(dirname($itemPath))) {
					Core\FileSystem::makeDir(dirname($backupPath));
					$success = rename($itemPath, $backupPath);
					self::log('Backing up ' . Core\Str::stripStart($itemPath, AM_BASE_DIR) . ' to ' . Core\Str::stripStart($backupPath, AM_BASE_DIR));
				} else {
					$success = false;
				}
				
				if (!$success) {
					return false;
				}
				
			}
			
		}
		
		return true;
		
	}
	
	
	/**
	 *	Extract version number form content of version.php.
	 *
	 *	@param string $str
	 *	@return string The version number
	 */
	
	private static function extractVersion($str) {
		
		if (preg_match('/\(\'AM_VERSION\', \'([^\']+)\'\);/', $str, $matches)) {	
			return $matches[1];
		}
		
	}
	
	
	/**
	 * 	Download zip-archive to be installed.
	 *	
	 *	@return string Path to the downloaded archive or false on error
	 */
	
	private static function getArchive() {
		
		$archive = AM_BASE_DIR . AM_UPDATE_TEMP . '/download/' . self::$timestamp . '.zip';
		
		Core\FileSystem::makeDir(dirname($archive));
		
		set_time_limit(0);
		
		$fp = fopen($archive, 'w+');
		
		$options = array(
			CURLOPT_TIMEOUT => 120,
			CURLOPT_FILE => $fp,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => AM_UPDATE_REPO_URL . AM_UPDATE_REPO_GET_PATH . '/' . AM_UPDATE_BRANCH . '.zip'
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		curl_exec($curl); 
		
		self::log('Downloading branch "' . AM_UPDATE_BRANCH . '" form ' . AM_UPDATE_REPO_URL);
		
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200 || curl_errno($curl)) {
			$archive = false;
			self::log('Download failed!');
		}
		
		curl_close($curl);
		fclose($fp);
		
		return $archive;
		
	}
	
	
	/**
	 *	Download version file and extract version number.    
	 *
	 *	@return string Version number or false on error.
	 */
	
	public static function getVersion() {
		
		$version = false;
		
		$options = array(
			CURLOPT_RETURNTRANSFER => 1, 
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => AM_UPDATE_REPO_URL . AM_UPDATE_REPO_RAW_PATH . '/' . AM_UPDATE_BRANCH . AM_UPDATE_REPO_VERSION_FILE
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$output = curl_exec($curl);
		
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200 && !curl_errno($curl)) {	
			$version = self::extractVersion($output);
		}
		
		curl_close($curl);
		
		return $version;
		
	}
	
	
	/**
	 * 	Initialize the update process by first cloning a light version of Automad 
	 * 	to the cache and the redirect to that version to start the actual update.
	 */
	
	public static function init() {
		
		$cloneDir = AM_DIR_CACHE . '/update/clone';
			
		$globs = array(
			'/core/*.php',
			'/system/*.php',
			'/init_update.php',
			'/const.php',
			'/version.php'
		);
		
		// Create file map for all required files for cloning.
		$map = array();
		
		foreach ($globs as $glob) {	
			foreach (glob(AM_BASE_DIR . '/automad' . $glob) as $file) {
				$map[$file] = 	AM_BASE_DIR . $cloneDir . '/' . 
								Core\Str::stripStart($file, AM_BASE_DIR . '/automad');
			}
		}
		
		// Clone required Automad files.
		foreach ($map as $src => $dest) {
			Core\FileSystem::makeDir(dirname($dest));
			copy($src, $dest);
		}
		
		// Lock site during update, to avoid file locks on Windows servers.
		touch(self::$updateLock);
		
		// Redirect to the cloned version.
		header('Location: ' . AM_BASE_URL . $cloneDir . '/init_update.php');
		
	}
	
	
	/**
	 *	Get items to be updated from config.
	 *
	 *	@return array The array of items to be updated or false on error
	 */
	
	private static function items() {
		
		$items = Core\Parse::csv(AM_UPDATE_ITEMS);
		
		if (is_array($items)) {
			
			$items = array_filter($items);
			
			if (!empty($items)) {
				return $items;
			}
			
		}
		
		return false;
		
	}
	
	
	/**
	 *	Log events to the update log file.
	 *
	 *	@param string $data
	 *	@return string The path to the log file
	 */
	
	private static function log($data) {
		
		$file = AM_BASE_DIR . AM_UPDATE_TEMP . '/' . self::$timestamp . '.log';
		Core\FileSystem::makeDir(dirname($file));
		file_put_contents($file, $data . "\r\n", FILE_APPEND);
		
		return $file;
		
	}
	
	
	/**
	 *	Test if permissions for all items to be updated are granted.
	 *
	 *	@param array $items
	 *	@return boolean True on success, false on error
	 */
	
	private static function permissionsGranted($items) {
		
		foreach($items as $item) {
			
			$item = AM_BASE_DIR . $item;
			
			if ((file_exists($item) && !is_writable($item)) || !is_writable(dirname($item))) {
				return false;
			} 
			
		}
		
		return true;
		
	}
	
	
	/**
	 *	Run the actual update.
	 *
	 *	@return array The $output array (AJAX response)
	 */
	
	public static function run() {
		
		$items = self::items();
		
		if ($items) {
		
			if (self::permissionsGranted($items)) {
				
				self::$timestamp = date('Ymd-His');
				self::log('Starting update ' . date('c'));
				self::log('Version to be updated: ' . AM_VERSION);
				self::log('Updating items: ' . implode(', ', $items));
				
				if ($archive = self::getArchive()) {
					
					if (self::backupCurrent($items)) {
						
						if (self::unpack($archive, $items)) {
							
							$success = true;
							
							// Clear cache.
							if (file_exists(AM_FILE_SITE_MTIME)) {
								unlink(AM_FILE_SITE_MTIME);
							}
							
						} else {
							
							$success = false;
							
						}
					
					} else {
						
						$success = false;
							
					}
					
					if ($success) {
						
						$versionFile = AM_BASE_DIR . '/automad/version.php';
						
						if (is_readable($versionFile)) {	
							$version = self::extractVersion(file_get_contents($versionFile));
							$log = self::log('Successfully updated Automad to version ' . $version);
						}
						
						$output['success'] = 'Successfully updated to version ' . $version;
						
					} else {
						
						$output['error'] = 'Update failed!';
						
					}
					
				} else {
					
					$output['error'] = 'Downloading update failed!';
					
				}

			} else {
				
				$output['error'] = 'Permission denied!';
				
			}
			
		} else {
			
			$output['error'] = 'Invalid list of items to be updated!';
		
		}
		
		// Remove lock.
		if (file_exists(self::$updateLock)) {
			unlink(self::$updateLock);
		}
		
		// Init a clean form on success to show the updated status.
		$output['init'] = true;
		
		return $output;
		
	}
	
	
	/**
	 *	Test if the server supports all required functions.
	 *	
	 *	@return boolean True on success, false on error
	 */
	
	public static function supported() {
		
		return (function_exists('curl_version') && function_exists('zip_open'));
		
	}
	
	
	/**
	 *	Unpack all item matching AM_UPDATE_ITEM.
	 *
	 *	@param string $archive
	 *	@param array $items
	 *	@return boolean True on success, false on error
	 */
	
	private static function unpack($archive, $items) {

		$success = true;
		$zip = zip_open($archive);
		$itemsMatchRegex = 	'/^[\w\-]+(' . 
							addcslashes(implode('|', $items), '/') . 
							')/';
		
		if (is_resource($zip)) {
			
			// Iterate over zip entries and unpack item in case the filename matches on of the update items.
			while($zipEntry = zip_read($zip)) { 
				
				$filename = zip_entry_name($zipEntry);
				
				if (preg_match($itemsMatchRegex, $filename)) {
					
					$filename = preg_replace('/^([\w\-]+)/', AM_BASE_DIR, $filename); 
					
					if (zip_entry_open($zip, $zipEntry)) {
						
						if (Core\FileSystem::write($filename, zip_entry_read($zipEntry, zip_entry_filesize($zipEntry)))) {
							self::log('Extracted ' . Core\Str::stripStart($filename, AM_BASE_DIR));
						} else {
							$success = false;
						}
						
						zip_entry_close($zipEntry);
						
					} else {
						
						$success = false;
						
					}
					
				}
				
			} 
			
		} else {
			
			$success = false;
			
		}
		
		zip_close($zip);
		unlink($archive);

		return $success;
		
	}
	

}
