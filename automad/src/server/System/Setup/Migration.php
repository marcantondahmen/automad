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

namespace Automad\System\Setup;

use Automad\Core\Debug;
use Automad\System\Setup\Migrations\AbstractMigration;
use Automad\System\Setup\Migrations\ConfigPhpStrings;
use Automad\System\Setup\Migrations\EncryptConfigFiles;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The migration class runs an ordered list of migrations of data that was saved by older versions.
 * Migrations run as part of the setup, which runs once after every update.
 * New migrations are appended to the list and must be idempotent, since all migrations run after every update.
 * A failing migration is logged and doesn't stop the remaining migrations.
 *
 * @see Setup::start()
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Migration {
	/**
	 * The ordered list of migrations.
	 *
	 * @var AbstractMigration[]
	 */
	private array $migrations;

	/**
	 * The constructor.
	 *
	 * @param AbstractMigration[]|null $migrations an optional list of migrations that replaces the default list
	 */
	public function __construct(?array $migrations = null) {
		$this->migrations = $migrations ?? array(
			new ConfigPhpStrings(AM_BASE_DIR . '/config'),
			new EncryptConfigFiles(AM_BASE_DIR . '/config')
		);
	}

	/**
	 * Run all migrations and return the results.
	 *
	 * @return array<string, string|null> the error messages of all migrations by name, null on success
	 */
	public function run(): array {
		$results = array();

		foreach ($this->migrations as $Migration) {
			try {
				$Migration->run();
				$results[$Migration->getName()] = null;
				Debug::log($Migration->getName(), 'Migration completed');
			} catch (\Throwable $th) {
				$results[$Migration->getName()] = $th->getMessage();
				Debug::log($th->getMessage(), 'Migration failed: ' . $Migration->getName());
			}
		}

		return $results;
	}
}
