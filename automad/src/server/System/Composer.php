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
 * Copyright (c) 2019-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Composer\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\PhpExecutableFinder;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Composer class is a wrapper for setting up Composer and executing commands.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Composer {
	/**
	 * Composer autoloader within the temporary extraction directory.
	 */
	private string $autoloader = '/vendor/autoload.php';

	/**
	 * The path to the composer.json file.
	 */
	private string $composerFile = AM_BASE_DIR . '/composer.json';

	/**
	 * The Composer version to be used.
	 */
	private string $composerVersion = '2.5.1';

	/**
	 * Composer extraction directory within temporary directory.
	 */
	private string $extractionDir = '/extracted';

	/*
	 * The base directory for the installation.
	 */
	private string $installBaseDir;

	/**
	 * A chached file including the temporary Composer install directory.
	 */
	private string $installDirCacheFile;

	/**
	 * The download URL for the composer.phar file.
	 */
	private string $pharUrl;

	/**
	 * A variable to reserve memory for running a shutdown function
	 * when Composer reaches the allowd memory limit.
	 */
	private mixed $reservedShutdownMemory = null;

	/**
	 * The constructor runs the setup.
	 */
	public function __construct() {
		$this->pharUrl = 'https://getcomposer.org/download/' . $this->composerVersion . '/composer.phar';
		$this->installBaseDir = AM_DIR_TMP . '/composer/' . $this->composerVersion;
		$this->installDirCacheFile = $this->installBaseDir . '/path';
		$this->setUp();
	}

	/**
	 * Set up Composer by downloading and extracting the composer.phar to a temporary directory
	 * outside the document root, defining some environment variables, registering a shutdown
	 * function and including the autoloader.
	 */
	private function setUp(): void {
		$installDir = $this->getInstallDir();
		$updatePackageInstaller = false;

		$srcDir = $installDir . $this->extractionDir;

		if (!file_exists($srcDir)) {
			$file = $this->downloadPhar($installDir);

			if (is_null($file) || !is_readable($file)) {
				Debug::log($file, 'Download of composer.phar failed');

				return;
			}

			$phar = new \Phar($file);
			$phar->extractTo($srcDir);

			$updatePackageInstaller = true;
		}

		$autoloader = $installDir . $this->extractionDir . $this->autoloader;

		if (!is_readable($autoloader)) {
			Debug::log($autoloader, 'Composer autoloader not found');

			return;
		}

		putenv('COMPOSER_HOME=' . $installDir . '/home');

		Debug::log($autoloader, 'Require Composer autoloader');
		require_once($autoloader);

		$decoded = json_decode(strval(file_get_contents($this->composerFile)), true);
		$config = $decoded['config'] ?? array();
		$decoded['config'] = array_merge_recursive($config, array('allow-plugins' => array()));
		$decoded['config']['allow-plugins']['automad/package-installer'] = true;
		FileSystem::write($this->composerFile, strval(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));

		if ($updatePackageInstaller) {
			$this->run('require automad/package-installer');
			$this->run('update automad/package-installer');
		}
	}

	/**
	 * Run a given Composer command.
	 *
	 * @param string $command
	 * @param bool $getBuffer
	 * @return string The command output on false or in case $getBuffer is true
	 */
	public function run(string $command, bool $getBuffer = false): string {
		$this->shutdownOnError();

		chdir(AM_BASE_DIR);

		set_time_limit(0);
		ini_set('memory_limit', '-1');

		$input = new StringInput($command);
		$output = new BufferedOutput();
		$application = new Application();

		$application->setAutoExit(false);
		$application->setCatchExceptions(false);

		Debug::log($command, 'Command');

		$buffer = '';

		try {
			$application->run($input, $output);
			$buffer = $output->fetch();
			Debug::log($buffer, 'Buffer');
		} catch (\Exception $e) {
			// Try to fall back to running composer.phar using exec() in case
			// there was any execption raised using the Composer API.
			// That could be for example the case on Windows machines.
			if (!function_exists('exec')) {
				return 'The exec() function is disabled in your php.ini file!';
			}

			$binFinder = new PhpExecutableFinder();
			$php = $binFinder->find();
			$phar = $this->getInstallDir() . '/composer.phar';
			$exitCode = null;

			$execOutput = array();
			@exec("$php $phar $command 2>&1", $execOutput, $exitCode);
			$buffer = implode("\n", $execOutput);

			Debug::log("$php $phar $command", 'Use exec() function as fallback');
			Debug::log($exitCode, 'exec() exit code');
			Debug::log($buffer, 'exec() buffer');

			if ($exitCode !== 0) {
				return $e->getMessage();
			}
		}

		$bufferJsonOnly = preg_replace('/^[^\{]*(\{.*\})[^\}]*$/is', '$1', $buffer) ?? '';
		$bufferJsonOnly = preg_replace('/\s+/is', ' ', $bufferJsonOnly) ?? '';

		Debug::log(round(memory_get_peak_usage() / 1024 / 1024) . ' mb', 'Memory used');
		Debug::log($bufferJsonOnly, 'Buffer JSON only');

		if ($getBuffer) {
			return $bufferJsonOnly;
		}

		return '';
	}

	/**
	 * Download the composer.phar file to a given directory.
	 *
	 * @param string $dir
	 * @return string|null The full path to the downloaded composer.phar file
	 */
	private function downloadPhar(string $dir): ?string {
		$phar = null;

		if (is_writable($dir)) {
			$phar = $dir . '/composer.phar';

			if (is_writable($phar)) {
				unlink($phar);
			}

			Fetch::download($this->pharUrl, $phar);
			Debug::log($phar, 'Downloaded Composer PHAR');
		}

		return $phar;
	}

	/**
	 * Read the Composer install directory from cache to reuse the installation
	 * in case Composer was already used before. In case Composer hasn't been used before,
	 * a new path will be generated and save to the cache.
	 *
	 * @return string The path to the installation directory
	 */
	private function getInstallDir(): string {
		// Get Composer install directory from cache or create new path.
		if (is_readable($this->installDirCacheFile)) {
			$installDir = file_get_contents($this->installDirCacheFile);
			Debug::log($installDir, 'Getting Composer installation directory from cache');

			// To verify that the directory actually contains Composer, simply test for existance of the autoloader.
			if (!$installDir || !is_readable($installDir . $this->extractionDir . $this->autoloader)) {
				Debug::log(strval($installDir) . $this->extractionDir . $this->autoloader, 'Autoloader not found');

				return $this->newInstallDir();
			}

			return $installDir;
		}

		return $this->newInstallDir();
	}

	/**
	 * Generate a fresh installation directory for Composer.
	 *
	 * @return string The path to the directory
	 */
	private function newInstallDir(): string {
		$installDir = $this->installBaseDir . '/' . bin2hex(random_bytes(32));
		Debug::log($installDir, 'Generating new Composer installation path');
		FileSystem::write($this->installDirCacheFile, $installDir);
		FileSystem::makeDir($installDir);

		return $installDir;
	}

	/**
	 * A shutdown function to handle memory limit erros.
	 */
	private function shutdownOnError(): void {
		ini_set('display_errors', 'Off');
		error_reporting(E_ALL);

		// This memory is cleared on error (case of allowed memory exhausted)
		// to use that memory to run the shutdown function.
		$this->reservedShutdownMemory = str_repeat('*', 1024 * 1024);

		register_shutdown_function(function () {
			// Reuse the reserved memory.
			$this->reservedShutdownMemory = null;
			$error = error_get_last();

			if (is_array($error) && !empty($error['type']) && $error['type'] === 1) {
				exit('{"error": "' . $error['message'] . '", "trigger": "composerDone"}');
			}
		});
	}
}
