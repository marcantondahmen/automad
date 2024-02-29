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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console\Commands;

use Automad\Core\DataStore;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\PublicationState;
use Automad\Core\Str;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The migrate command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Migrate extends AbstractCommand {
	/**
	 * Get the command help.
	 *
	 * @return string the command help
	 */
	public static function help(): string {
		return 'Migrate content from a website made with Automad version 1.';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public static function name(): string {
		return 'migrate';
	}

	/**
	 * The actual command action.
	 */
	public static function run(): void {
		$argv = $_SERVER['argv'] ?? array();

		if (empty($argv[2])) {
			exit('Please specify a source installation!' . PHP_EOL);
		}

		echo 'Creating backup ...' . PHP_EOL;
		@rename(AM_BASE_DIR . AM_DIR_PAGES, AM_BASE_DIR . AM_DIR_PAGES . '.backup-' . time());
		@rename(AM_BASE_DIR . AM_DIR_SHARED, AM_BASE_DIR . AM_DIR_SHARED . '.backup-' . time());

		$source = $argv[2];

		echo "Importing from $source ..." . PHP_EOL;
		echo 'Converting shared data ...' . PHP_EOL;

		$DataStore = new DataStore();
		$DataStore->setState(PublicationState::DRAFT, self::dataFile("$source/shared/data.txt"))->publish();

		self::copyFiles("$source/shared", AM_BASE_DIR . AM_DIR_SHARED);

		$files = self::getFiles("$source/pages", '*.txt');

		foreach($files as $file) {
			$data = self::dataFile($file);
			$path = dirname(Str::stripStart($file, "$source/pages"));

			echo "  $path" . PHP_EOL;

			$DataStore = new DataStore($path);
			$DataStore->setState(PublicationState::DRAFT, $data)->publish();

			self::copyPageFiles("$source/pages", $path);
		}

		echo 'Removing prefixes ...';
		self::removePrefixRecursively(AM_BASE_DIR . AM_DIR_PAGES);

		$count = count($files);

		echo PHP_EOL . "Migrated $count pages";
	}

	/**
	 * Copy files.
	 *
	 * @param string $src
	 * @param string $dest
	 */
	private static function copyFiles(string $src, string $dest): void {
		$directoryItems = FileSystem::glob("$src/*");
		$files = array_filter($directoryItems, 'is_file');
		$files = array_filter($files, function ($path) {
			return !str_ends_with($path, '.txt');
		});

		foreach ($files as $file) {
			echo '    copy ' . basename($file) . PHP_EOL;
			copy($file, $dest . '/' . basename($file));
		}
	}

	/**
	 * Copy media files of pages.
	 *
	 * @param string $sourcePages
	 * @param string $pagePath
	 */
	private static function copyPageFiles(string $sourcePages, string $pagePath): void {
		$src = "$sourcePages/$pagePath";
		$dest = AM_BASE_DIR . AM_DIR_PAGES . $pagePath;

		self::copyFiles($src, $dest);
	}

	/**
	 * Loads and parses a v1 text file.
	 *
	 * First it separates the different blocks into simple key/value pairs.
	 * Then it creates an array of vars by splitting the pairs.
	 *
	 * @psalm-suppress PossiblyUndefinedArrayOffset
	 * @param string $file
	 * @return array
	 */
	private static function dataFile(string $file) {
		$vars = array();

		if (!file_exists($file)) {
			return $vars;
		}

		$content = preg_replace('/\r\n?/', "\n", file_get_contents($file));

		$pairs = preg_split(
			'/\n\-+\s*\n(?=[\+\w\.\-]+:)/s',
			$content,
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		foreach ($pairs as $pair) {
			list($key, $value) = explode(':', $pair, 2);
			$vars[trim($key)] = trim($value);
		}

		$vars = array_filter($vars, 'strlen');

		foreach($vars as $key => $value) {
			if (str_starts_with($key, '+')) {
				$vars[$key] = json_decode($value);
			}
		}

		return $vars;
	}

	/**
	 * Get list of files in v1 installation.
	 *
	 * @param string $dir
	 * @param string $pattern
	 * @return array
	 */
	private static function getFiles(string $dir, string $pattern): array {
		$files = FileSystem::glob("$dir/$pattern");

		foreach(FileSystem::glob("$dir/*", GLOB_ONLYDIR) as $d) {
			$files = array_merge($files, self::getFiles($d, $pattern));
		}

		return $files;
	}

	/**
	 * Remove prefixes from page directories.
	 *
	 * @param string $dir
	 */
	private static function removePrefixRecursively(string $dir): void {
		$dataFiles = FileSystem::glob(rtrim($dir, '/') . '/*/' . DataStore::FILENAME);

		sort($dataFiles);

		foreach($dataFiles as $file) {
			$path = Str::stripStart(dirname($file), AM_BASE_DIR . AM_DIR_PAGES);
			$slug = basename(preg_replace('/^[^\.]+\./', '', $path));
			$newPath = $path;

			if (basename($path) !== $slug) {
				$newPath = FileSystem::movePageDir($path, dirname($path), $slug);
			}

			PageIndex::append(dirname($path), $newPath);

			self::removePrefixRecursively(AM_BASE_DIR . AM_DIR_PAGES . $newPath);
		}
	}
}
