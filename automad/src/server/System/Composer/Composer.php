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

namespace Automad\System\Composer;

use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Messenger;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Composer class is a wrapper for setting up Composer and executing commands.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Composer {
	const COMPOSER_FILE = AM_BASE_DIR . '/composer.json';
	const COMPOSER_VERSION = '2.8.6';
	const INSTALL_DIR = AM_BASE_DIR . AM_DIR_CACHE . '/composer';

	/**
	 * The path to the downloaded phar file.
	 */
	private string $pharPath;

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
	 * Set up Composer by downloading the composer.phar to a temporary directory,
	 * defining some environment variables, registering a shutdown function, updating
	 * the composer.json file and bootstrapping Composer from the PHAR file.
	 */
	public function __construct() {
		$this->pharUrl = 'https://getcomposer.org/download/' . self::COMPOSER_VERSION . '/composer.phar';
		$this->pharPath = self::INSTALL_DIR . '/' . hash('sha256', self::COMPOSER_VERSION . AM_BASE_DIR) . '.phar';

		$updatePackageInstaller = false;

		if (!is_readable($this->pharPath)) {
			if (!$this->downloadPhar()) {
				Debug::log('Download of PHAR failed');

				return;
			}

			$updatePackageInstaller = true;
		} else {
			Debug::log($this->pharPath, 'Using existing PHAR');
		}

		putenv('COMPOSER_HOME=' . AM_DIR_TMP . '/composer_home');

		$decoded = self::readConfig();
		$config = $decoded['config'] ?? array();
		$decoded['config'] = array_merge_recursive($config, array('allow-plugins' => array()));
		$decoded['config']['allow-plugins']['automad/package-installer'] = true;

		self::writeConfig($decoded);

		if ($updatePackageInstaller) {
			$this->run('require automad/package-installer');
			$this->run('update automad/package-installer');
		}

		$this->run('clear-cache');
		Auth::get()->setEnv();
	}

	/**
	 * Read the composer.json.
	 *
	 * @return array
	 */
	public static function readConfig(): array {
		return FileSystem::readJson(Composer::COMPOSER_FILE, true);
	}

	/**
	 * Run a given Composer command.
	 *
	 * @param string $command
	 * @param Messenger $Messenger
	 * @return int
	 */
	public function run(string $command, Messenger $Messenger = new Messenger()): int {
		$this->shutdownOnError();

		chdir(AM_BASE_DIR);

		set_time_limit(0);
		ini_set('memory_limit', '-1');

		Debug::log($command, 'Command');

		$exitCode = 0;
		$buffer = '';

		try {
			@include_once 'phar://' . $this->pharPath . '/src/bootstrap.php';

			if (!class_exists('\Composer\Console\Application')) {
				throw new \Exception('Error including from PHAR!');
			}

			$input = new \Symfony\Component\Console\Input\StringInput($command);
			$output = new \Symfony\Component\Console\Output\BufferedOutput();
			$application = new \Composer\Console\Application();

			$application->setAutoExit(false);
			$application->setCatchExceptions(false);

			$exitCode = $application->run($input, $output);
			$buffer = $output->fetch();
			Debug::log($buffer, 'Buffer');

			if ($exitCode !== 0) {
				$Messenger->setError($buffer);
			}
		} catch (\Exception $e) {
			// Try to fall back to running composer.phar using exec() in case
			// there was any execption raised using the Composer API.
			// That could be for example the case on Windows machines.
			if (!function_exists('exec')) {
				$Messenger->setError('The exec() function is disabled in your php.ini file!');

				return 1;
			}

			$php = self::findPhpBinary();

			if (!$php) {
				$Messenger->setError('PHP executable not found!');

				return 1;
			}

			$execOutput = array();
			exec("$php $this->pharPath $command 2>&1", $execOutput, $exitCode);
			$buffer = implode("\n", $execOutput);

			Debug::log("$php $this->pharPath $command", 'Use exec() function as fallback');
			Debug::log($exitCode, 'exec() exit code');
			Debug::log($buffer, 'exec() buffer');

			if ($exitCode !== 0) {
				$Messenger->setError($e->getMessage());

				return $exitCode;
			}
		}

		$bufferJsonOnly = preg_replace('/^[^\{]*(\{.*\})[^\}]*$/is', '$1', $buffer) ?? '';
		$bufferJsonOnly = preg_replace('/\s+/is', ' ', $bufferJsonOnly) ?? '';

		Debug::log(round(memory_get_peak_usage() / 1024 / 1024) . ' mb', 'Memory used');
		Debug::log($bufferJsonOnly, 'Buffer JSON only');

		$Messenger->setData(json_decode($bufferJsonOnly, true) ?? array());

		return $exitCode;
	}

	/**
	 * Write the composer.json.
	 *
	 * @param array $config
	 * @return bool
	 */
	public static function writeConfig(array $config): bool {
		return FileSystem::write(
			self::COMPOSER_FILE,
			strval(
				json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
			)
		);
	}

	/**
	 * Download the composer.phar file to a given directory.
	 *
	 * @return bool The full path to the downloaded composer.phar file
	 */
	private function downloadPhar(): bool {
		Debug::log($this->pharPath, 'Downloading PHAR');

		if (is_writable($this->pharPath)) {
			unlink($this->pharPath);
		}

		if (is_writable(dirname(self::INSTALL_DIR))) {
			FileSystem::makeDir(self::INSTALL_DIR);

			return Fetch::download($this->pharUrl, $this->pharPath);
		}

		return false;
	}

	/**
	 * Find a working PHP CLI binary.
	 *
	 * @return false|string
	 */
	private static function findPhpBinary(): string|false {
		$binaries = array(
			'php',
			'/usr/bin/php',
			'/usr/local/bin/php',
			'/opt/homebrew/bin/php',
			'/Applications/XAMPP/xamppfiles/bin/php',
			'C:\php\php.exe',
			'C:\xampp\php\php.exe'
		);

		putenv('PATH=' . strval(getenv('PATH')));

		foreach ($binaries as $php) {
			exec("$php -v", $output, $code);

			if ($code === 0) {
				return $php;
			}
		}

		return false;
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
