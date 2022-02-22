<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2017-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\Engine\PatternAssembly;
use Automad\UI\Utils\Messenger;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The FileSystem class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2018 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileSystem {
	/**
	 * The cached array of items in the packages directory.
	 */
	private static $packageDirectoryItems = array();

	/**
	 * Append a suffix to a path just before the trailing slash.
	 *
	 * @param string $path
	 * @param string $suffix
	 * @return string The path with appended suffix
	 */
	public static function appendSuffixToPath(string $path, string $suffix) {
		return rtrim($path, '/') . $suffix . '/';
	}

	/**
	 * Open a data text file under the given path, read the data,
	 * append a suffix to the title variable and write back the data.
	 *
	 * @param string $path
	 * @param string $suffix
	 */
	public static function appendSuffixToTitle(string $path, string $suffix) {
		if ($suffix) {
			$path = self::fullPagePath($path);
			$files = self::glob($path . '*.' . AM_FILE_EXT_DATA);

			if (!empty($files)) {
				$file = reset($files);
				$data = Parse::dataFile($file);
				$data[AM_KEY_TITLE] .= ucwords(str_replace('-', ' ', $suffix));
				self::writeData($data, $file);
			}
		}
	}

	/**
	 * Unlike self::movePageDir(), this method only copies all files
	 * within a page directory without (!) any subdirectories.
	 *
	 * @param string $source
	 * @param string $dest
	 */
	public static function copyPageFiles(string $source, string $dest) {
		// Sanatize dirs.
		$source = self::fullPagePath($source);
		$dest = self::fullPagePath($dest);

		// Get files in directory to be copied.
		$files = self::glob($source . '*');
		$files = array_filter($files, 'is_file');

		// Create directoy and copy files.
		self::makeDir($dest);

		foreach ($files as $file) {
			$copy = $dest . basename($file);
			copy($file, $copy);
			chmod($copy, AM_PERM_FILE);
		}
	}
	/**
	 * Delete a file.
	 *
	 * @param string $file
	 * @return bool Return true if the file was deleted succsessfully
	 */
	public static function deleteFile(string $file) {
		if (is_file($file)) {
			if (is_writable($file) && is_writable(dirname($file))) {
				return unlink($file);
			}
		}
	}

	/**
	 * Deletes a file and its caption (if existing).
	 *
	 * @param string $file
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function deleteMedia(string $file, Messenger $Messenger) {
		$fileError = Text::get('permissionsDeniedError') . ' "' . basename($file) . '"';

		if (!is_writable($file)) {
			$Messenger->setError($fileError);

			return false;
		}

		if (!unlink($file)) {
			$Messenger->setError($fileError);

			return false;
		}

		$captionFile = $file . '.' . AM_FILE_EXT_CAPTION;
		$captionError = Text::get('permissionsDeniedError') . ' "' . basename($captionFile) . '"';

		if (file_exists($captionFile)) {
			if (!is_writable($captionFile)) {
				$Messenger->setError($captionError);

				return false;
			}

			if (!unlink($captionFile)) {
				$Messenger->setError($captionError);

				return false;
			}
		}

		return true;
	}

	/**
	 * Get the full file system path for the given path.
	 *
	 * @param string $path
	 * @return string The full path
	 */
	public static function fullPagePath(string $path) {
		if (strpos($path, AM_BASE_DIR . AM_DIR_PAGES) !== 0) {
			$path = AM_BASE_DIR . AM_DIR_PAGES . $path;
		}

		return rtrim($path, '/') . '/';
	}

	/**
	 * Return the extension for a given file.
	 *
	 * @param string $file
	 * @return string The extension
	 */
	public static function getExtension(string $file) {
		$pathInfo = pathinfo($file);

		if (!empty($pathInfo['extension'])) {
			return $pathInfo['extension'];
		}
	}

	/**
	 * Get the human readable size of a given file.
	 *
	 * @param string $file
	 * @return string the file size string
	 */
	public static function getFileSize(string $file) {
		$bytes = filesize($file);
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

		for ($i = 0; $bytes > 1024; $i++) {
			$bytes = $bytes / 1204;
		}

		return round($bytes, 2) . ' ' . $units[$i];
	}

	/**
	 * 	Get file extension for images based on mime types.
	 *
	 * @param string $file
	 * @return mixed The extension or false
	 */
	public static function getImageExtensionFromMimeType(string $file) {
		try {
			$getimagesize = getimagesize($file);
			$type = $getimagesize['mime'];

			switch ($type) {
				case 'image/jpeg':
					$extension = '.jpg';

					break;
				case 'image/gif':
					$extension = '.gif';

					break;
				case 'image/png':
					$extension = '.png';

					break;
				default:
					$extension = '';

					break;
			}

			return $extension;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Get all items in the packages directory, optionally filtered by a regex string.
	 *
	 * @param string $filter
	 * @return array A filtered list with all items in the packages directory
	 */
	public static function getPackagesDirectoryItems(string $filter = '') {
		if (empty(self::$packageDirectoryItems)) {
			self::$packageDirectoryItems = self::listDirectoryRecursively(AM_BASE_DIR . AM_DIR_PACKAGES, AM_BASE_DIR . AM_DIR_PACKAGES);
		}

		if ($filter) {
			return array_values(preg_grep($filter, self::$packageDirectoryItems));
		}

		return self::$packageDirectoryItems;
	}

	/**
	 * Return the file system path for the directory of a page based on $_POST['url'].
	 * In case URL is empty, return the '/shared' directory.
	 *
	 * @param Automad $Automad
	 * @return string The full path to the related directory
	 */
	public static function getPathByPostUrl(Automad $Automad) {
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {
			return self::fullPagePath($Page->path);
		} else {
			return AM_BASE_DIR . AM_DIR_SHARED . '/';
		}
	}

	/**
	 * Return the path of the temp dir if it is writable by the webserver.
	 * In any case, '/tmp' is the preferred directory, because of automatic cleanup at reboot,
	 * while other locations like '/var/tmp' do not get purged by the system.
	 * But since '/tmp' is only available on macos and linux,
	 * sys_get_temp_dir() is used as fallback.
	 *
	 * @return string The path to the temp dir
	 */
	public static function getTmpDir() {
		$tmp = '/tmp';

		if (is_writable($tmp)) {
			return $tmp;
		}

		if (is_writable(sys_get_temp_dir())) {
			return rtrim(sys_get_temp_dir(), '/');
		}
	}

	/**
	 * A wrapper for PHP's built-in glob function.
	 * This method always returns an array, even though glob() returns false
	 * on some systems instead of empty arrays.
	 *
	 * @param string $pattern
	 * @param int $flags
	 * @return array The list of matching files
	 */
	public static function glob(string $pattern, int $flags = 0) {
		$files = glob($pattern, $flags);

		if (!$files) {
			return array();
		}

		return	array_map(function ($path) {
			return self::normalizeSlashes($path);
		}, $files);
	}

	/**
	 * Find files by using the glob() method and filter the resulting array by a regex pattern.
	 * Note that this method should basically replace the usage of GLOB_BRACE to be fully
	 * compatible to systems where this constant is not defined. Instead of a glob pattern
	 * like "/path/*.{jpg,png}" it is more safe to use a generic pattern like "/path/*.*"
	 * filtered by a regex like "/\.(jpg|png)$/i" without using the GLOB_BRACE flag.
	 *
	 * @param string $pattern
	 * @param string $regex
	 * @param int $flags
	 * @return array The filtered list of matching files
	 */
	public static function globGrep(string $pattern, string $regex, int $flags = 0) {
		return array_values(preg_grep($regex, self::glob($pattern, $flags)));
	}

	/**
	 * Tests if a string is a file name with an allowed file extension.
	 *
	 * Basically a possibly existing file extension is checked against the array of allowed file extensions.
	 *
	 * "/url/file.jpg" will return true, "/url/file" or "/url/file.something" will return false.
	 *
	 * @param string $str
	 * @return bool
	 */
	public static function isAllowedFileType(string $str) {
		// Remove possible query string
		$str = preg_replace('/\?.*/', '', $str);

		// Get just the basename
		$str = basename($str);

		// Possible extension
		$extension = strtolower(pathinfo($str, PATHINFO_EXTENSION));

		if (in_array($extension, FileUtils::allowedFileTypes())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Recursively list all items in a directory.
	 *
	 * @param string $directory
	 * @param string $base
	 * @return array The list of items
	 */
	public static function listDirectoryRecursively(string $directory, string $base = AM_BASE_DIR) {
		$items = array();
		$exclude = array('node_modules', 'vendor');

		foreach (self::glob($directory . '/*') as $item) {
			if (!in_array(basename($item), $exclude)) {
				$items[] = Str::stripStart($item, $base);

				if (is_dir($item)) {
					$items = array_merge($items, self::listDirectoryRecursively($item, $base));
				}
			}
		}

		return $items;
	}

	/**
	 * Create directory if not existing.
	 *
	 * @param string $path
	 * @return bool True on success, else false
	 */
	public static function makeDir(string $path) {
		if (!file_exists($path)) {
			$umask = umask(0);
			$return = mkdir($path, AM_PERM_DIR, true);
			umask($umask);
			Debug::log($path, 'Created');

			return $return;
		}
	}

	/**
	 * Move a directory to a new location.
	 * The final path is composed of the parent directoy, the prefix and the title.
	 * In case the resulting path is already occupied, an index get appended to the prefix, to be reproducible when resaving the page.
	 *
	 * @param string $oldPath
	 * @param string $newParentPath (destination)
	 * @param string $prefix
	 * @param string $slug
	 * @return string $newPath
	 */
	public static function movePageDir(string $oldPath, string $newParentPath, string $prefix, string $slug) {
		// Normalize parent path. In case of a 1st level page, dirname(page) will return '\' on windows.
		// Therefore it is needed to convert all backslashes.
		$newParentPath = self::normalizeSlashes($newParentPath);
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');

		// Not only sanitize strings, but also remove all dots, to make sure a single dot will work fine as a prefix.title separator.
		$prefix = ltrim(Str::sanitize($prefix, true, AM_DIRNAME_MAX_LEN) . '.', '.');
		$slug = Str::slug($slug, true, AM_DIRNAME_MAX_LEN);

		// Add trailing slash.
		$slug .= '/';

		// Build new path.
		$newPath = $newParentPath . $prefix . $slug;

		// Contiune only if old and new paths are different.
		if ($oldPath != $newPath) {
			// Get suffix in case the path is already taken.
			$suffix = self::uniquePathSuffix($newPath);
			$newPath = self::appendSuffixToPath($newPath, $suffix);

			// Move dir.
			self::makeDir(self::fullPagePath($newParentPath));
			rename(self::fullPagePath($oldPath), self::fullPagePath($newPath));

			// Update the page title in the .txt file to reflect the actual path suffix.
			self::appendSuffixToTitle($newPath, $suffix);
		}

		return $newPath;
	}

	/**
	 * Replace all backslashes in a given path with forward slashes.
	 *
	 * @param string $path
	 * @return string The processed path with only forward slashes
	 */
	public static function normalizeSlashes(string $path) {
		return str_replace('\\', '/', $path);
	}

	/**
	 * Move all items in /cache to the PHP temp directory.
	 *
	 * @return string $tmp
	 */
	public static function purgeCache() {
		// Check if the temp dir is actually writable.
		if ($tmp = self::getTmpDir()) {
			$tmpSubDir = '/automad-trash';
			$trash = $tmp . $tmpSubDir;
			$n = 0;

			// Create unique subdirectory in temp.
			while (is_dir($trash)) {
				$n++;
				$trash = $tmp . $tmpSubDir . '-' . $n;
			}

			if (self::makeDir($trash)) {
				// Collect items to be removed.
				$cacheItems = array_merge(
					self::glob(AM_BASE_DIR . AM_DIR_CACHE . '/*', GLOB_ONLYDIR),
					self::glob(AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '*')
				);

				foreach ($cacheItems as $item) {
					$dest = $trash . '/' . basename($item);

					if (!@rename($item, $dest)) {
						if (function_exists('exec')) {
							if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
								$cmd = "move $item $dest";
								$cmd = str_replace('/', DIRECTORY_SEPARATOR, $cmd);
							} else {
								$cmd = "mv $item $dest";
							}

							$output = array();
							$code = null;
							@exec($cmd, $output, $code);

							if ($code !== 0) {
								return false;
							}
						} else {
							return false;
						}
					}
				}

				// Return $trash on success.
				return $trash;
			}
		}
	}

	/**
	 * Renames a file and its caption (if existing).
	 *
	 * @param string $oldFile
	 * @param string $newFile
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function renameMedia(string $oldFile, string $newFile, Messenger $Messenger) {
		if (!is_writable(dirname($oldFile))) {
			$Messenger->setError(Text::get('permissionsDeniedError') . ' "' . basename(dirname($oldFile)) . '"');

			return false;
		}

		if (!is_writable($oldFile)) {
			$Messenger->setError(Text::get('permissionsDeniedError') . ' "' . basename($oldFile) . '"');

			return false;
		}

		if (file_exists($newFile)) {
			$Messenger->setError('"' . $newFile . '" ' . Text::get('alreadyExists'));

			return false;
		}

		if (!rename($oldFile, $newFile)) {
			$Messenger->setError(Text::get('permissionsDeniedError') . ' "' . basename($oldFile) . '"');
		}

		// Set new mtime to force refresh of page cache in case the new name was belonging to a delete file before.
		touch($newFile);

		$oldCaptionFile = $oldFile . '.' . AM_FILE_EXT_CAPTION;
		$newCaptionFile = $newFile . '.' . AM_FILE_EXT_CAPTION;

		if (file_exists($oldCaptionFile)) {
			if (is_writable($oldCaptionFile) && (is_writable($newCaptionFile) || !file_exists($newCaptionFile))) {
				rename($oldCaptionFile, $newCaptionFile);
			} else {
				$Messenger->setError(Text::get('permissionsDeniedError') . ' "' . basename($newCaptionFile) . '"');

				return false;
			}
		}

		return true;
	}

	/**
	 * Creates an unique suffix for a path to avoid conflicts with existing directories.
	 *
	 * @param string $path
	 * @param string $prefix (prepended to the numerical suffix)
	 * @return string The suffix
	 */
	public static function uniquePathSuffix(string $path, string $prefix = '') {
		$i = 1;
		$suffix = $prefix;

		while (file_exists(self::appendSuffixToPath(self::fullPagePath($path), $suffix))) {
			$suffix = $prefix . '-' . $i++;
		}

		return $suffix;
	}

	/**
	 * Write content to a file and create the parent directory if needed.
	 *
	 * @param string $file
	 * @param string $content
	 * @return bool True on success, else false
	 */
	public static function write(string $file, string $content) {
		self::makeDir(dirname($file));

		if (!file_exists($file)) {
			@touch($file);
			@chmod($file, AM_PERM_FILE);
			Debug::log($file, 'Created');
		}

		if (is_writable($file)) {
			return @file_put_contents($file, $content, LOCK_EX);
		}
	}

	/**
	 * Format, filter and write the data array a text file.
	 *
	 * @param array $data
	 * @param string $file
	 */
	public static function writeData(array $data, string $file) {
		$pairs = array();
		$data = array_filter($data, 'strlen');

		foreach ($data as $key => $value) {
			// Only keep variables keys starting with a letter.
			// (ignore any kind of system variable)
			if (preg_match('/^' . PatternAssembly::$charClassTextFileVariables . '+$/', $key)) {
				$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
			}
		}

		$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
		self::write($file, $content);
	}
}
