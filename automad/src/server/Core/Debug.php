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
 * Copyright (c) 2013-2024 by Marc Anton Dahmen
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
 * @copyright Copyright (c) 2013-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Debug {
	const DIR_LOGS = '/logs';

	/**
	 * Log buffer.
	 */
	private static array $buffer = array();

	/**
	 * The log entry index.
	 */
	private static int $index = 0;

	/**
	 * Timestamp when script started.
	 */
	private static ?float $time = null;

	/**
	 * Stop timer, calculate execution time, get user & server constants
	 * and return a console log item for every item in the buffer array.
	 *
	 * @return string The Javascript console log
	 */
	public static function consoleLog(): string {
		if (!AM_DEBUG_ENABLED) {
			return '';
		}

		// Stop timer.
		self::timerStop();

		// Memory usage.
		self::memory();

		// Get server constants.
		self::log($_SERVER, 'Server');

		// Get last error.
		self::log(error_get_last(), 'Last error');

		$html = '<script type="text/javascript">' . "\n";

		foreach (self::$buffer as $key => $value) {
			$html .= 'console.log(' . json_encode(array($key => $value)) . ');' . "\n";
		}

		$html .= '</script>' . "\n";

		return $html;
	}

	/**
	 * Return the buffer array.
	 *
	 * @return array The log buffer array
	 */
	public static function getLog(): array {
		return self::$buffer;
	}

	/**
	 * Write log to json file.
	 */
	public static function json(): void {
		if (!AM_DEBUG_ENABLED) {
			return;
		}

		$file = AM_DIR_TMP . self::DIR_LOGS . AM_REQUEST . '/log.json';

		FileSystem::writeJson($file, self::$buffer);
	}

	/**
	 * Log any kind of variable and append it to the $buffer array.
	 *
	 * @param mixed $element (The actual content to log)
	 * @param string $description (Basic info, class, method etc.)
	 */
	public static function log($element, string $description = ''): void {
		if (!AM_DEBUG_ENABLED) {
			return;
		}

		// Start timer. self::timerStart() only saves the time on the first call.
		self::timerStart();

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
			$prefix = basename(str_replace('\\', '/', $backtrace['class'] ?? '')) . ($backtrace['type'] ?? '') . $backtrace['function'] . '(): ';
		} else {
			$prefix = basename($backtraceAll[0]['file'] ?? '') . ': ';
		}

		// Prepend the method to $description.
		$description = self::$index . ': ' . trim($prefix . $description, ': ');

		self::$buffer[$description] = $element;

		self::$index++;
	}

	/**
	 * Provide info about memory usage.
	 */
	private static function memory(): void {
		self::log((memory_get_peak_usage(true) / 1048576) . 'M of ' . ini_get('memory_limit'), 'Memory used');
	}

	/**
	 * Start the timer on the first call to calculate the execution time when consoleLog() gets called.
	 */
	private static function timerStart(): void {
		// Only save time on first call.
		if (!self::$time) {
			self::$time = microtime(true);
			self::log(date('d. M Y, H:i:s'));
		}
	}

	/**
	 * Stop the timer and log the execution time.
	 */
	private static function timerStop(): void {
		if (self::$time) {
			$executionTime = microtime(true) - self::$time;
			self::log($executionTime . ' seconds', 'Time for execution');
		}
	}
}
