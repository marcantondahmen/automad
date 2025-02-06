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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console\Commands;

use Automad\Console\Argument;
use Automad\Console\ArgumentCollection;
use Automad\Console\Console;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\PublicationState;
use Automad\Core\Str;
use Automad\Stores\DataStore;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The migrate command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Migrate extends AbstractCommand {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ArgumentCollection = new ArgumentCollection(array(
			new Argument('path', 'The path to the Automad v1 installation', true),
		));
	}

	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Migrate content from a website made with Automad version 1.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return '';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'migrate';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		echo Console::clr('text', 'Creating backup ...') . PHP_EOL;
		@rename(AM_BASE_DIR . AM_DIR_PAGES, AM_BASE_DIR . AM_DIR_PAGES . '.backup-' . time());
		@rename(AM_BASE_DIR . AM_DIR_SHARED, AM_BASE_DIR . AM_DIR_SHARED . '.backup-' . time());

		$source = $this->ArgumentCollection->value('path');

		if (empty($source)) {
			echo Console::clr('error', 'The --path value must be a valid string') . PHP_EOL;
		}

		echo Console::clr('text', "Importing from $source ...") . PHP_EOL;
		echo Console::clr('text', 'Converting shared data ...') . PHP_EOL;

		$shared = $this->dataFile("$source/shared/data.txt");
		$shared['theme'] = str_replace('standard/', 'automad/standard-v1/', $shared['theme']);

		$DataStore = new DataStore();
		$DataStore->setState(PublicationState::DRAFT, $shared)->publish();

		$this->copyFiles("$source/shared", AM_BASE_DIR . AM_DIR_SHARED);

		$files = $this->getFiles("$source/pages", '*.txt');

		foreach ($files as $file) {
			$data = $this->dataFile($file);
			$path = dirname(Str::stripStart($file, "$source/pages"));

			if (!empty($data['theme'])) {
				$data['theme'] = str_replace('standard/', 'automad/standard-v1/', $data['theme']);
			}

			echo "  $path" . PHP_EOL;

			$DataStore = new DataStore($path);
			$DataStore->setState(PublicationState::DRAFT, $data)->publish();

			$this->copyPageFiles("$source/pages", $path);
		}

		echo Console::clr('text', 'Removing prefixes ...');
		$this->removePrefixRecursively(AM_BASE_DIR . AM_DIR_PAGES);

		$count = count($files);

		echo PHP_EOL . Console::clr('text', "Migrated $count pages");

		return 0;
	}

	/**
	 * Copy files.
	 *
	 * @param string $src
	 * @param string $dest
	 */
	private function copyFiles(string $src, string $dest): void {
		$directoryItems = FileSystem::glob("$src/*");
		$files = array_filter($directoryItems, 'is_file');
		$files = array_filter($files, function ($path) {
			return !str_ends_with($path, '.txt');
		});

		foreach ($files as $file) {
			echo Console::clr('text', '    copy ') . Console::clr('code', basename($file)) . PHP_EOL;
			copy($file, $dest . '/' . basename($file));
		}
	}

	/**
	 * Copy media files of pages.
	 *
	 * @param string $sourcePages
	 * @param string $pagePath
	 */
	private function copyPageFiles(string $sourcePages, string $pagePath): void {
		$src = "$sourcePages/$pagePath";
		$dest = AM_BASE_DIR . AM_DIR_PAGES . $pagePath;

		$this->copyFiles($src, $dest);
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
	private function dataFile(string $file) {
		$vars = array();

		if (!file_exists($file)) {
			return $vars;
		}

		$content = preg_replace('/\r\n?/', "\n", strval(file_get_contents($file))) ?? '';

		$pairs = preg_split(
			'/\n\-+\s*\n(?=[\+\w\.\-]+:)/s',
			$content,
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		if (!$pairs) {
			$pairs = array();
		}

		foreach ($pairs as $pair) {
			list($key, $value) = explode(':', $pair, 2);
			$vars[trim($key)] = trim($value);
		}

		$vars = array_filter($vars, 'strlen');

		foreach ($vars as $key => $value) {
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
	private function getFiles(string $dir, string $pattern): array {
		$files = FileSystem::glob("$dir/$pattern");

		foreach (FileSystem::glob("$dir/*", GLOB_ONLYDIR) as $d) {
			$files = array_merge($files, $this->getFiles($d, $pattern));
		}

		return $files;
	}

	/**
	 * Remove prefixes from page directories.
	 *
	 * @param string $dir
	 */
	private function removePrefixRecursively(string $dir): void {
		$dataFiles = FileSystem::glob(rtrim($dir, '/') . '/*/' . DataStore::FILENAME);

		sort($dataFiles);

		foreach ($dataFiles as $file) {
			$path = Str::stripStart(dirname($file), AM_BASE_DIR . AM_DIR_PAGES);
			$slug = basename(preg_replace('/^[^\.]+\./', '', $path) ?? '');
			$newPath = $path;

			if (basename($path) !== $slug) {
				$newPath = FileSystem::movePageDir($path, dirname($path), $slug);
			}

			PageIndex::append(dirname($path), $newPath);

			$this->removePrefixRecursively(AM_BASE_DIR . AM_DIR_PAGES . $newPath);
		}
	}
}
