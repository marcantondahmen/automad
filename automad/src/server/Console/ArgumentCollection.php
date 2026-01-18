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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The console argument collection type class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ArgumentCollection {
	/**
	 * The args array.
	 *
	 * @var Argument[]
	 */
	public readonly array $args;

	/**
	 * The constructor.
	 *
	 * @param Argument[] $args
	 */
	public function __construct(array $args) {
		$this->args = $args;
	}

	/**
	 * Find an argument by name.
	 *
	 * @param string $name
	 * @return Argument|null
	 */
	public function get(string $name): Argument|null {
		foreach ($this->args as $Argument) {
			if ($Argument->name === $name) {
				return $Argument;
			}
		}

		return null;
	}

	/**
	 * Test whether an arguments does exist in argv. This also returns false if the arg is not a valid one.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isInArgv(string $name): bool {
		$Argument = $this->get($name);

		if ($Argument) {
			return $Argument->isInArgv;
		}

		return false;
	}

	/**
	 * Parse value string.
	 *
	 * @param array $argv
	 * @return bool
	 */
	public function parseArgv(array $argv): bool {
		if (!$this->validateArgv($argv)) {
			return false;
		}

		if (count($argv) <= 2) {
			return true;
		}

		unset($argv[0]);
		unset($argv[1]);

		$argv = array_values($argv);
		$options = array();

		foreach ($argv as $index => $item) {
			if (str_starts_with($item, '--')) {
				$next = strval($argv[$index + 1] ?? '');
				$name = trim($item, '-');
				$Argument = $this->get($name);

				if ($Argument) {
					$Argument->value = str_starts_with($next, '--') ? '' : $next;
					$Argument->isInArgv = true;
				}
			}
		}

		return true;
	}

	/**
	 * Validate if all required arguments are present.
	 *
	 * @param array $argv
	 * @return bool
	 */
	public function validateArgv(array $argv): bool {
		foreach ($this->args as $Argument) {
			if (array_search('--' . $Argument->name, $argv) === false && $Argument->required) {
				echo Console::clr('error', "Argument --{$Argument->name} is required") . PHP_EOL;

				return false;
			}
		}

		return true;
	}

	/**
	 * Return a value of an argument by its name.
	 *
	 * @param string $name
	 * @return string
	 */
	public function value(string $name): string {
		$Argument = $this->get($name);

		if ($Argument) {
			return $Argument->value;
		}

		return '';
	}
}
