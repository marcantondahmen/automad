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
 * Copyright (c) 2017-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Exception;
use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The FileSystem class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileSystem {
	const array FILE_TYPES_IMAGE = array('jpg', 'jpeg', 'gif', 'png', 'webp');
	const array FILE_TYPES_VIDEO = array('mp4', 'webm');

	/**
	 * Append a suffix to a path just before the trailing slash.
	 *
	 * @param string $path
	 * @param string $suffix
	 * @return string The path with appended suffix
	 */
	public static function appendSuffixToPath(string $path, string $suffix): string {
		return rtrim($path, '/') . $suffix . '/';
	}

	/**
	 * Unlike self::movePageDir(), this method only copies all files
	 * within a page directory without (!) any subdirectories.
	 *
	 * @param string $source
	 * @param string $dest
	 */
	public static function copyPageFiles(string $source, string $dest): void {
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
	 * Deletes a file and its caption (if existing).
	 *
	 * @param string $file
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function deleteMedia(string $file, Messenger $Messenger): bool {
		$fileError = Text::get('permissionsDeniedError');

		if (!is_writable($file)) {
			$Messenger->setError($fileError);

			return false;
		}

		if (!unlink($file)) {
			$Messenger->setError($fileError);

			return false;
		}

		$captionFile = $file . '.' . FileUtils::EXT_CAPTION;
		$captionError = Text::get('permissionsDeniedError');

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
	 * Check whether the disk quota is exeeded.
	 *
	 * @return bool
	 */
	public static function diskQuotaExceeded(): bool {
		if (!AM_DISK_QUOTA) {
			return false;
		}

		return (self::diskUsage() > AM_DISK_QUOTA);
	}

	/**
	 * Get the disk usage of the installation in MB.
	 *
	 * @return float the disk usage in MB
	 */
	public static function diskUsage(): float {
		$bytes = 0.0;
		$dirIterator = new RecursiveDirectoryIterator(AM_BASE_DIR, FilesystemIterator::SKIP_DOTS);

		$filterIterator = new RecursiveCallbackFilterIterator($dirIterator, function ($item) {
			if (is_link($item->getPathname())) {
				return false;
			}

			return true;
		});

		$objects = new RecursiveIteratorIterator($filterIterator);

		foreach ($objects as $object) {
			try {
				$bytes += $object->getSize();
			} catch (Exception $e) {
				Debug::log($e->getMessage());
			}
		}

		return round($bytes / (1024 * 1024), 2);
	}

	/**
	 * Get the full file system path for the given path.
	 *
	 * @param string $path
	 * @return string The full path
	 */
	public static function fullPagePath(string $path): string {
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
	public static function getExtension(string $file): string {
		$pathInfo = pathinfo($file);

		if (!empty($pathInfo['extension'])) {
			return $pathInfo['extension'];
		}

		return '';
	}

	/**
	 * Get the human readable size of a given file.
	 *
	 * @param string $file
	 * @return string the file size string
	 */
	public static function getFileSize(string $file): string {
		$bytes = filesize($file);
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

		for ($i = 0; $bytes > 1024 && $i <= 4; $i++) {
			$bytes = $bytes / 1204;
		}

		return round(intval($bytes), 2) . ' ' . $units[$i];
	}

	/**
	 * 	Get file extension for images based on mime types.
	 *
	 * @param string $file
	 * @return string The extension or an empty string
	 */
	public static function getImageExtensionFromMimeType(string $file): string {
		try {
			$getimagesize = getimagesize($file);

			if (!$getimagesize) {
				return '';
			}

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
				case 'image/webp':
					$extension = '.webp';

					break;
				default:
					$extension = '';

					break;
			}

			return $extension;
		} catch (\Exception $e) {
			return '';
		}
	}

	/**
	 * Return the file system path for the directory of a page based on $_POST['url'].
	 * In case URL is empty, return the '/shared' directory.
	 *
	 * @param Automad $Automad
	 * @return string The full path to the related directory
	 */
	public static function getPathByPostUrl(Automad $Automad): string {
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {
			return self::fullPagePath($Page->path);
		} else {
			return AM_BASE_DIR . AM_DIR_SHARED . '/';
		}
	}

	/**
	 * Generate a dedicated cache directory for a single installation on a server based on its base directory
	 * that can be used to cache files that should not be exposed to the public.
	 *
	 * @return string the generated path
	 */
	public static function getTmpDir(): string {
		$dir = self::getSystemTmpDir() . '/automad/' . basename(AM_BASE_DIR) . '-' . sha1(AM_BASE_DIR);

		try {
			self::makeDir($dir);
		} catch (\Throwable $th) {
			throw new Exception('The system temp directory is not writable &mdash; please define <code>AM_TMP_DIR</code>.');
		}

		return $dir;
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
	public static function glob(string $pattern, int $flags = 0): array {
		$files = glob($pattern, $flags);

		if (!$files) {
			return array();
		}

		return array_map(function ($path) {
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
	public static function globGrep(string $pattern, string $regex, int $flags = 0): array {
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
	public static function isAllowedFileType(string $str): bool {
		// Remove possible query string
		$str = preg_replace('/\?.*/', '', $str) ?? '';

		// Get just the basename
		$str = basename($str);

		// Possible extension
		$extension = strtolower(pathinfo($str, PATHINFO_EXTENSION));

		if (in_array($extension, FileUtils::allowedFileTypes())) {
			return true;
		}

		return false;
	}

	/**
	 * Recursively list all items in a directory.
	 *
	 * @param string $directory
	 * @param string $base
	 * @return array The list of items
	 */
	public static function listDirectoryRecursively(string $directory, string $base = AM_BASE_DIR): array {
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

		return true;
	}

	/**
	 * Move a directory to a new location.
	 *
	 * @param string $oldPath
	 * @param string $newParentPath (destination)
	 * @param string $slug
	 * @return string $newPath
	 */
	public static function movePageDir(string $oldPath, string $newParentPath, string $slug): string {
		// Normalize parent path. In case of a 1st level page, dirname(page) will return '\' on windows.
		// Therefore it is needed to convert all backslashes.
		$newParentPath = self::normalizeSlashes($newParentPath);
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');

		$slug = Str::slug($slug, true, AM_DIRNAME_MAX_LEN) . '/';

		// Build new path.
		$newPath = $newParentPath . $slug;

		// Contiune only if old and new paths are different.
		if ($oldPath != $newPath) {
			// Get suffix in case the path is already taken.
			$suffix = self::uniquePathSuffix($newPath);
			$newPath = self::appendSuffixToPath($newPath, $suffix);

			// Move dir.
			self::makeDir(self::fullPagePath($newParentPath));
			rename(self::fullPagePath($oldPath), self::fullPagePath($newPath));
		}

		return $newPath;
	}

	/**
	 * Replace all backslashes in a given path with forward slashes.
	 *
	 * @param string $path
	 * @return string The processed path with only forward slashes
	 */
	public static function normalizeSlashes(string $path): string {
		return str_replace('\\', '/', $path);
	}

	/**
	 * Move all items in /cache to the PHP temp directory.
	 *
	 * @return mixed $tmp
	 */
	public static function purgeCache(): mixed {
		$cacheItems = self::glob(AM_BASE_DIR . AM_DIR_CACHE . '/*', GLOB_ONLYDIR);
		$target = self::trash($cacheItems);

		Cache::clear();

		return $target;
	}

	/**
	 * Read a .json file.
	 *
	 * @param string $file
	 * @param bool $returnAssocArray
	 * @return array|object
	 * @psalm-return ($returnAssocArray is true ? array : object)
	 */
	public static function readJson(string $file, bool $returnAssocArray = true): array|object {
		return json_decode(strval(file_get_contents($file)), $returnAssocArray) ?? array();
	}

	/**
	 * Renames a file and its caption (if existing).
	 *
	 * @param string $oldFile
	 * @param string $newFile
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function renameMedia(string $oldFile, string $newFile, Messenger $Messenger): bool {
		if (!is_writable(dirname($oldFile))) {
			$Messenger->setError(Text::get('permissionsDeniedError'));

			return false;
		}

		if (!is_writable($oldFile)) {
			$Messenger->setError(Text::get('permissionsDeniedError'));

			return false;
		}

		if (file_exists($newFile)) {
			$Messenger->setError('"' . $newFile . '" ' . Text::get('alreadyExists'));

			return false;
		}

		if (!rename($oldFile, $newFile)) {
			$Messenger->setError(Text::get('permissionsDeniedError'));
		}

		// Set new mtime to force refresh of page cache in case the new name was belonging to a delete file before.
		touch($newFile);

		$oldCaptionFile = $oldFile . '.' . FileUtils::EXT_CAPTION;
		$newCaptionFile = $newFile . '.' . FileUtils::EXT_CAPTION;

		if (file_exists($oldCaptionFile)) {
			if (is_writable($oldCaptionFile) && (is_writable($newCaptionFile) || !file_exists($newCaptionFile))) {
				rename($oldCaptionFile, $newCaptionFile);
			} else {
				$Messenger->setError(Text::get('permissionsDeniedError'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Move an array of items to the trash.
	 *
	 * @param array $items
	 */
	public static function trash(array $items): string | null {
		$trash = AM_DIR_TMP . '/trash/';
		$n = 0;
		$target = $trash . $n;

		// Create unique subdirectory in temp.
		while (is_dir($target)) {
			$n++;
			$target = $trash . $n;
		}

		if (!self::makeDir($target)) {
			return null;
		}

		foreach ($items as $item) {
			$dest = $target . '/' . basename($item);

			if (!@rename($item, $dest)) {
				if (function_exists('exec')) {
					$item = '"' . $item . '"';
					$dest = '"' . $dest . '"';

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
						return null;
					}
				}
			}
		}

		return $target;
	}

	/**
	 * Creates an unique suffix for a path to avoid conflicts with existing directories.
	 *
	 * @param string $path
	 * @param string $suffixPrefix (prepended to the numerical suffix)
	 * @return string The suffix
	 */
	public static function uniquePathSuffix(string $path, string $suffixPrefix = ''): string {
		$i = 1;
		$suffix = $suffixPrefix;

		while (file_exists(self::appendSuffixToPath(self::fullPagePath($path), $suffix))) {
			$suffix = $suffixPrefix . '-' . $i++;
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
	public static function write(string $file, string $content): bool {
		self::makeDir(dirname($file));

		if (!file_exists($file)) {
			@touch($file);
			@chmod($file, AM_PERM_FILE);
			Debug::log($file, 'Created');
		}

		if (is_writable($file)) {
			return (bool) @file_put_contents($file, $content, LOCK_EX);
		}

		return false;
	}

	/**
	 * Write a .json file.
	 *
	 * @param string $file
	 * @param array $data
	 * @return bool
	 */
	public static function writeJson(string $file, array $data): bool {
		return self::write($file, strval(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
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
	private static function getSystemTmpDir(): string {
		$tmp = '/tmp';

		if (is_writable($tmp)) {
			return $tmp;
		}

		return rtrim(sys_get_temp_dir(), '/');
	}
}
