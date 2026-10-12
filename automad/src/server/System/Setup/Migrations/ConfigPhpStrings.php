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
use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Migrate all legacy PHP files in a directory that only contain a raw string without any PHP code
 * to the format of FileSystem::writePhpString() in order to not expose their content when being accessed directly.
 * Files that already contain PHP code are not touched.
 *
 * @see FileSystem::writePhpString()
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ConfigPhpStrings extends AbstractMigration {
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
		return 'Config PHP strings';
	}

	/**
	 * Run the migration.
	 */
	public function run(): void {
		$this->migrated = array();

		foreach (FileSystem::glob($this->dir . '/*.php') as $file) {
			$content = trim(strval(file_get_contents($file)));

			if (str_starts_with($content, '<?php')) {
				continue;
			}

			if (FileSystem::writePhpString($file, $content)) {
				$this->migrated[] = $file;
				Debug::log($file, 'Migrated');
			}
		}
	}
}
