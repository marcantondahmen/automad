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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

use Automad\Core\Debug;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Pipe class handles the chain of processes to manipulate variable values.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Pipe {
	/**
	 * Whitelist of standard PHP functions.
	 */
	private static $phpFunctions = 	array(
		'ceil',
		'floor',
		'round',
		'strlen',
		'strtolower',
		'strtoupper',
		'ucwords'
	);

	/**
	 * Processes an array of functions applied to a given value.
	 *
	 * $functions is an array of associative arrays with function name and parameters.
	 * In case the function name is a mathematical operator,
	 * the value is just the numeric value instead of an array.
	 *
	 * @param string $value
	 * @param array $functions
	 * @return string The modified $value
	 */
	public static function process(string $value, array $functions) {
		Debug::log($functions);

		foreach ($functions as $function) {
			if (preg_match('/^[\+\/\*\-]$/', $function['name'])) {
				$value = Pipe::math($function['name'], $function['parameters'], $value);
			} else {
				$value = Pipe::stringFunction($function['name'], $function['parameters'], $value);
			}
		}

		return $value;
	}

	/**
	 * Call custom string function.
	 *
	 * @param string $function
	 * @param array $parameters
	 * @param string $value
	 * @return string $value
	 */
	private static function extension(string $function, array $parameters, string $value) {
		$class = '\\' . str_replace('/', '\\', $function);

		if (!class_exists($class, false)) {
			$file = strtolower(AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $function . '/' . basename($function) . '.php');

			if (is_readable($file)) {
				require_once($file);
			}
		}

		if (class_exists($class)) {
			$object = new $class();
			$method = basename($function);

			if (method_exists($class, $method)) {
				$value = strval(call_user_func_array(array($object, $method), $parameters));
				Debug::log(array('Result' => $value, 'Parameters' => $parameters), 'Call ' . $function);
			}
		}

		return $value;
	}

	/**
	 * Simple math operations.
	 *
	 * @param string $operator
	 * @param string $number
	 * @param string $value
	 * @return string $value
	 */
	private static function math(string $operator, string $number, string $value) {
		$number = floatval($number);
		$value = floatval($value);

		switch ($operator) {
			case '+':
				$result = $value + $number;

				break;
			case '-':
				$result = $value - $number;

				break;
			case '*':
				$result = $value * $number;

				break;
			case '/':
				$result = $value / $number;

				break;
		}

		Debug::log($result, $value . $operator . $number);

		return strval($result);
	}

	/**
	 * Apply string function to $value.
	 *
	 * @param string $function
	 * @param array $parameters
	 * @param string $value
	 * @return string $value
	 */
	private static function stringFunction(string $function, array $parameters, string $value = '') {
		if (!$parameters) {
			$parameters = array();
		}

		// Add the actual $value to the parameters array as its first element.
		$parameters = array_merge(array(0 => $value), $parameters);

		// Call a String class method.
		if (method_exists('\Automad\Core\Str', $function)) {
			$value = call_user_func_array('\Automad\Core\Str::' . $function, $parameters);
			Debug::log(array('Result' => $value, 'Parameters' => $parameters), 'Call Str::' . $function);

			return $value;
		}

		// Call standard PHP string function.
		if (in_array(strtolower($function), Pipe::$phpFunctions)) {
			$value = strval(call_user_func_array($function, $parameters));
			Debug::log(array('Result' => $value, 'Parameters' => $parameters), 'Call ' . $function);

			return $value;
		}

		// In case $function is a number, call Str::shorten() method and pass $function as paramter
		// for the max number of characters.
		if (is_numeric($function)) {
			Debug::log($value, 'Shorten content to max ' . $function . ' characters');

			return Str::shorten($value, $function);
		}

		// Loading custom string functions as extensions.
		return self::extension($function, $parameters, $value);
	}
}
