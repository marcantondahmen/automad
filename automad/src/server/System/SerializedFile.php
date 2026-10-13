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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A serialized file stores a serialized value, either an object or an array of objects, of exactly one class
 * as PHP file using FileSystem::writePhpString(), optionally encrypted.
 *
 * The class name is replaced with a placeholder in the serialized string in order to be able to
 * rename the class in the future without breaking already saved files.
 *
 * When reading a file, only objects of the given class can be unserialized. Objects of any other class
 * are blocked and returned as __PHP_Incomplete_Class.
 *
 * Note that encryption only takes effect in case it is enabled.
 *
 * @see FileSystem::writePhpString()
 * @see FileSystem::readPhpString()
 * @see Encryption
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SerializedFile {
	/**
	 * The placeholder for the class name in a serialized string.
	 */
	const PLACEHOLDER = 'O:*:"~"';

	/**
	 * The class of the stored objects.
	 */
	private string $class;

	/**
	 * Whether the file is encrypted.
	 */
	private bool $encrypt;

	/**
	 * The file path.
	 */
	private string $path;

	/**
	 * The constructor.
	 *
	 * @param string $path
	 * @param string $class
	 * @param bool $encrypt
	 */
	public function __construct(string $path, string $class, bool $encrypt = false) {
		$this->path = $path;
		$this->class = $class;
		$this->encrypt = $encrypt;
	}

	/**
	 * Delete the file.
	 *
	 * @return bool true in case the file doesn't exist anymore
	 */
	public function delete(): bool {
		if (!file_exists($this->path)) {
			return true;
		}

		return unlink($this->path);
	}

	/**
	 * Generate the PHP code that is saved to the file without actually writing it.
	 *
	 * @param mixed $value
	 * @return string the PHP code
	 */
	public function encode(mixed $value): string {
		return FileSystem::encodePhpString($this->serialize($value), $this->encrypt);
	}

	/**
	 * Read and unserialize the stored value.
	 *
	 * @return mixed the unserialized value or null in case the file doesn't exist or can't be read, decrypted or unserialized
	 */
	public function read(): mixed {
		$contents = FileSystem::readPhpString($this->path);

		if ($contents === null) {
			return null;
		}

		$serialized = str_replace(self::PLACEHOLDER, self::serializedClass($this->class), $contents);

		try {
			$value = @unserialize($serialized, array('allowed_classes' => array($this->class)));
		} catch (\Throwable $th) {
			return null;
		}

		return $value === false ? null : $value;
	}

	/**
	 * Serialize and save a value.
	 *
	 * @param mixed $value
	 * @return bool true on success
	 */
	public function write(mixed $value): bool {
		return FileSystem::writePhpString($this->path, $this->serialize($value), $this->encrypt);
	}

	/**
	 * Serialize a value and replace the class name with the placeholder.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function serialize(mixed $value): string {
		return str_replace(self::serializedClass($this->class), self::PLACEHOLDER, serialize($value));
	}

	/**
	 * Get the serialized form of a class name.
	 *
	 * @param string $class
	 * @return string
	 */
	private static function serializedClass(string $class): string {
		return 'O:' . strlen($class) . ':"' . $class . '"';
	}
}
