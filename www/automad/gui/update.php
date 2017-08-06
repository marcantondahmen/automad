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
 *	Copyright (c) 2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Update class handles the process of updating Automad using the dashboard. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2017 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Update {
	
	
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
		
		self::preloadClasses();
		
		$backup = AM_BASE_DIR . AM_UPDATE_TEMP . '/backup/' . self::$timestamp;
		
		FileSystem::makeDir($backup);
		
		foreach($items as $item) {
			
			$itemPath = AM_BASE_DIR . $item;
			$backupPath = $backup . $item;
			
			if (is_writable($itemPath) && is_writable(dirname($itemPath))) {
				FileSystem::makeDir(dirname($backupPath));
				$success = rename($itemPath, $backupPath);
				self::log('Backing up ' . Core\Str::stripStart($itemPath, AM_BASE_DIR) . ' to ' . Core\Str::stripStart($backupPath, AM_BASE_DIR));
			} else {
				$success = false;
			}
			
			if (!$success) {
				return false;
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
	 *	Get items to be updated from config.
	 *
	 *	@return array The array of items to be updated or false on error
	 */
	
	private static function items() {
		
		$items = explode(AM_PARSE_STR_SEPARATOR, AM_UPDATE_ITEMS);
		
		if (is_array($items)) {
			
			$items = array_map(
					function($item) {
						return trim($item); 
					}, $items
				);
		
			$items = array_filter($items);
			
			if (!empty($items)) {
				return $items;
			}
			
		}
		
		return false;
		
	}
	
	
	/**
	 * 	Download zip-archive to be installed.
	 *	
	 *	@return string Path to the downloaded archive or false on error
	 */
	
	private static function getArchive() {
		
		$archive = AM_BASE_DIR . AM_UPDATE_TEMP . '/download/' . self::$timestamp . '.zip';
		
		FileSystem::makeDir(dirname($archive));
		
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
	 *	The version number must match the pattern "\d+\.\d+\.\d+".
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
	 *	Log events to the update log file.
	 *
	 *	@param string $data
	 *	@return string The path to the log file
	 */
	
	private static function log($data) {
		
		$file = AM_BASE_DIR . AM_UPDATE_TEMP . '/' . self::$timestamp . '.log';
		FileSystem::makeDir(dirname($file));
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
			
			if (!is_writable($item) || !is_writable(dirname($item))) {
				return false;
			} 
			
		}
		
		return true;
		
	}
	
	
	/**
	 *	Preload required classes before removing old installation.
	 */
	
	private static function preloadClasses() {
		
		require_once(AM_BASE_DIR . '/automad/gui/prefix.php');
		
	}
	
	
	/**
	 *	Run the actual update.
	 *
	 *	@return array The $output array (AJAX response)
	 */
	
	public static function run() {
		
		$items = self::items();
		
		if (!$items) {
			$output['html'] = '<div class="uk-alert uk-alert-danger">' . Text::get('error_update_items') . '</div>';
			return $output;
		}
		
		if (!self::permissionsGranted($items)) {
			$output['html'] = '<div class="uk-alert uk-alert-danger">' . Text::get('error_update_permission') . '</div>';
			return $output;
		}
		
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
				
				$output['html'] = '<div class="uk-alert uk-alert-success">' . Text::get('sys_update_not_required') . '</div>';
				
				$versionFile = AM_BASE_DIR . '/automad/version.php';
				
				if (is_readable($versionFile)) {
					
					$version = self::extractVersion(file_get_contents($versionFile));
					$log = self::log('Successfully updated Automad to version ' . $version);
					$logUrl = str_replace(AM_BASE_DIR, AM_BASE_URL, $log);
					$output['html'] .= '<a href="' . $logUrl . '" target="_blank" class="uk-button">' .
							   '<i class="uk-icon-file-text-o"></i>&nbsp;&nbsp;' . 
							   Text::get('btn_open_log') .
							   '</a>';
					
				}
				
				$output['success'] = Text::get('success_update');
				
			} else {
				
				$output['html'] = '<div class="uk-alert uk-alert-danger">' . Text::get('error_update_failed') . '</div>';
				
			}
			
		} else {
			
			$output['html'] = '<div class="uk-alert uk-alert-danger">' . Text::get('error_update_download') . '</div>';
			
		}
		
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
		$itemsMatchRegex = 	'/^[\w\-]+\/www(' . 
					addcslashes(implode('|', $items), '/') . 
					')/';
		
		if (is_resource($zip)) {
			
			// Iterate over zip entries and unpack item in case the filename matches on of the update items.
			while($zipEntry = zip_read($zip)) { 
				
				$filename = zip_entry_name($zipEntry);
				
				if (preg_match($itemsMatchRegex, $filename)) {
					
					$filename = preg_replace('/^([\w\-]+\/www)/', AM_BASE_DIR, $filename); 
					
					if (zip_entry_open($zip, $zipEntry)) {
						
						if (FileSystem::write($filename, zip_entry_read($zipEntry, zip_entry_filesize($zipEntry)))) {
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
