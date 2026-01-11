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
 * Copyright (c) 2013-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Debug class holds all methods to help debugging while development.
 * The output of all the contained methods can be activated/deactivated with defining the AM_DEBUG_ENABLED constant.
 * All logged information will be stored in $buffer as JS's console.log() items.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Debug {
	const DIR_LOGS = '/logs';

	/**
	 * This will be true if logging and browser logging are enabled.
	 */
	public static bool $browserIsEnabled = false;

	/**
	 * Log buffer.
	 */
	private static array $buffer = array();

	/**
	 * The log entry index.
	 */
	private static int $index = 0;

	/**
	 * This will be set to true if logging is enabled and there is an actual request.
	 */
	private static bool $isEnabled = false;

	/**
	 * Timestamp when script started.
	 */
	private static ?float $time = null;

	/**
	 * Enable full error reporting, when debugging is enabled.
	 */
	public static function setup(): void {
		self::$isEnabled = AM_DEBUG_ENABLED && !defined('STDIN');
		self::$browserIsEnabled = self::$isEnabled && AM_DEBUG_BROWSER;

		if (self::$isEnabled) {
			error_reporting(E_ALL);
			ini_set('display_errors', '0');
			ini_set('log_errors', 1);
			ini_set('error_log', AM_DEBUG_LOG_PATH);

			if (!file_exists(dirname(AM_DEBUG_LOG_PATH))) {
				mkdir(dirname(AM_DEBUG_LOG_PATH), 0755, true);
			}

			self::sliceLogFile();
		} else {
			error_reporting(E_ERROR);
		}
	}

	/**
	 * Stop timer, calculate execution time, get user & server constants
	 * and return a console log item for every item in the buffer array.
	 *
	 * @return string The Javascript console log
	 */
	public static function consoleLog(): string {
		if (!self::$browserIsEnabled) {
			return '';
		}

		$html = '<script type="text/javascript">' . "\n";

		foreach (self::$buffer as $key => $value) {
			$html .= 'console.log(' . strval(json_encode(array($key => $value))) . ');' . "\n";
		}

		$html .= '</script>' . "\n";

		return $html;
	}

	/**
	 * Log disk usage.
	 */
	public static function diskUsage(): void {
		if (!self::$isEnabled) {
			return;
		}

		self::log(round(FileSystem::diskUsage(), 2), 'Disk usage (M)');
	}

	/**
	 * Return the buffer array, used in API calls.
	 *
	 * @return array The log buffer array
	 */
	public static function getLog(): array {
		return self::$buffer;
	}

	/**
	 * Log any kind of variable and append it to the $buffer array.
	 *
	 * @param mixed $element (The actual content to log)
	 * @param string $description (Basic info, class, method etc.)
	 */
	public static function log($element, string $description = ''): void {
		if (!self::$isEnabled) {
			return;
		}

		// Get backtrace.
		$backtraceAll = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);

		// Remove all backtrace items without any class defined (standard PHP functions) and the items with the functions Debug::log() and {closure}
		// To get a clean array with only the relevant Automad methods in the backtrace.
		$ignoreFunctions = array('log', __NAMESPACE__ . '\{closure}');
		$backtrace = array_filter($backtraceAll, function ($item) use ($ignoreFunctions) {
			return (isset($item['class'], $item['type'], $item['function']) && !in_array($item['function'], $ignoreFunctions));
		});

		// If class, type & method exist, use them to build the description prefix. Else use just the file name from the full backtrace.
		if (count($backtrace) > 0) {
			// When the backtrace array got reduced to the actually relevant items in the backtrace, take the first element (the one calling Debug::log()).
			$backtrace = array_shift($backtrace);
			$prefix = basename(str_replace('\\', '/', $backtrace['class'] ?? '')) . ($backtrace['type'] ?? '') . $backtrace['function'] . '()';
		} else {
			$prefix = basename($backtraceAll[0]['file'] ?? '');
		}

		$request = defined('AM_REQUEST') ? AM_REQUEST . ' => ' : '';
		error_log(trim($description . ': ' . (is_string($element) ? $element : json_encode($element, JSON_UNESCAPED_SLASHES)), ': '));

		$key = self::$index . ': ' . trim($prefix . ': ' . $description, ': ');
		self::$buffer[$key] = $element;
		self::$index++;
	}

	/**
	 * Provide info about memory usage.
	 */
	public static function memoryUsage(): void {
		if (!self::$isEnabled) {
			return;
		}

		self::log(round((memory_get_peak_usage(true) / 1048576), 2), 'Peak memory useage (M)');
	}

	/**
	 * Start the timer on the first call to calculate the execution time when consoleLog() gets called.
	 */
	public static function timerStart(): void {
		if (!self::$isEnabled) {
			return;
		}

		self::$time = microtime(true);
	}

	/**
	 * Stop the timer and log the execution time.
	 */
	public static function timerStop(): void {
		if (!self::$isEnabled) {
			return;
		}

		$executionTime = microtime(true) - self::$time;
		self::log(round($executionTime, 6), 'Time for execution (seconds)');
	}

	/**
	 * Keep the log file size below a given limit.
	 */
	private static function sliceLogFile(): void {
		$lines = file(AM_DEBUG_LOG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		if ($lines === false) {
			return;
		}

		if (count($lines) > AM_DEBUG_LOG_MAX_SIZE) {
			$lines = array_slice($lines, -AM_DEBUG_LOG_MAX_SIZE);
			file_put_contents(AM_DEBUG_LOG_PATH, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
		}
	}
}
