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

namespace Automad\System\Setup\Migrations;

use Automad\Core\Debug;
use Automad\System\Encryption;
use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Encrypt all PHP files in a directory that contain secrets in case encryption is enabled.
 * Only files that match the list of patterns are encrypted. The main config file and the accounts file
 * are never encrypted. Files that are already encrypted are not touched.
 * Note that this migration has to run after the ConfigPhpStrings migration.
 *
 * @see Encryption
 * @see ConfigPhpStrings
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class EncryptConfigFiles extends AbstractMigration {
	/**
	 * The patterns of files that contain secrets and therefore have to be encrypted.
	 */
	const PATTERNS = array(
		'ai.*.php',
		'composer.auth.php',
		'config.*.php',
		'tokens.auth.php'
	);

	/**
	 * The directory that contains the PHP files.
	 */
	private string $dir;

	/**
	 * The list of files that were migrated during the last run.
	 *
	 * @var string[]
	 */
	private array $migrated;

	/**
	 * The constructor.
	 *
	 * @param string $dir
	 */
	public function __construct(string $dir) {
		$this->dir = rtrim($dir, '/');
		$this->migrated = array();
	}

	/**
	 * Get the list of files that were migrated during the last run.
	 *
	 * @return string[]
	 */
	public function getMigrated(): array {
		return $this->migrated;
	}

	/**
	 * Get the migration name that is used for logging.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'Encrypt config files';
	}

	/**
	 * Run the migration.
	 */
	public function run(): void {
		$this->migrated = array();

		if (!Encryption::isEnabled()) {
			return;
		}

		foreach (self::PATTERNS as $pattern) {
			foreach (FileSystem::glob($this->dir . '/' . $pattern) as $file) {
				$content = @file_get_contents($file);

				// Never overwrite files that can't be read.
				if ($content === false) {
					continue;
				}

				$content = trim($content);

				if (!str_starts_with($content, '<?php')) {
					continue;
				}

				$data = include $file;

				if (!is_string($data) || Encryption::isEncrypted($data)) {
					continue;
				}

				if (FileSystem::writePhpString($file, $data, true)) {
					$this->migrated[] = $file;
					Debug::log($file, 'Encrypted');
				}
			}
		}
	}
}
