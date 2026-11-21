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
use Exception;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Remove the static sitemap.xml and robots.txt files that were written by older versions.
 * Both are now generated on request and the static files would otherwise shadow the routes.
 *
 * @see \Automad\Controllers\SitemapController
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class RemoveStaticSitemap extends AbstractMigration {
	/**
	 * The directory that contains the static files.
	 */
	private string $dir;

	/**
	 * The constructor.
	 *
	 * @param string $dir
	 */
	public function __construct(string $dir) {
		$this->dir = rtrim($dir, '/');
	}

	/**
	 * Get the migration name that is used for logging.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'Remove static sitemap';
	}

	/**
	 * Run the migration.
	 */
	public function run(): void {
		foreach (array('sitemap.xml', 'robots.txt') as $name) {
			$file = "{$this->dir}/$name";

			if (!file_exists($file)) {
				continue;
			}

			if (!@unlink($file)) {
				throw new Exception("Can't remove $file");
			}

			Debug::log($file, 'Removed');
		}
	}
}
