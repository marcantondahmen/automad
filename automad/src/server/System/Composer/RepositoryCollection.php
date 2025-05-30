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

namespace Automad\System\Composer;

use Automad\Core\Automad;
use Automad\Core\Messenger;
use Automad\Core\Text;
use Automad\System\Composer\RepositoryAdapters\AbstractAdapter;
use Automad\System\Composer\RepositoryAdapters\GitHubAdapter;
use Automad\System\Composer\RepositoryAdapters\GitLabAdapter;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Composer repository collection model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class RepositoryCollection {
	/**
	 * Add a new repository package.
	 *
	 * @param string $name
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @param string $platform
	 * @param Messenger $Messenger
	 * @return bool
	 */
	public static function add(string $name, string $repositoryUrl, string $branch, string $platform, Messenger $Messenger): bool {
		if (empty($name) || empty($repositoryUrl) || empty($branch)) {
			return false;
		}

		$config = Composer::readConfig();

		if (!isset($config['repositories'])) {
			$config['repositories'] = array();
		}

		foreach ($config['repositories'] as $repo) {
			if ($name == $repo['package']['name']) {
				$Messenger->setError(Text::get('repositoryAlreadyExistsError'));

				return false;
			}
		}

		$MetaData = self::getRepositoryAdapter($platform, $name, $repositoryUrl, $branch, $Messenger);

		$config['repositories'][] = $MetaData->getConfig();
		$config['repositories'] = array_values($config['repositories']);

		return Composer::writeConfig($config);
	}

	/**
	 * Get the registered repositories.
	 *
	 * @return array
	 */
	public static function get(): array {
		$config = Composer::readConfig();

		if (!isset($config['repositories'])) {
			return array();
		}

		$repos = array_map(function (array $repo): array | null {
			if ($repo['type'] !== 'package') {
				return null;
			}

			if (empty($repo['package']) || empty($repo['package']['dist'])) {
				return null;
			}

			$package = $repo['package'];
			$dist = $package['dist'];

			return array(
				'platform' => $package['platform'] ?? '',
				'name' => $package['name'] ?? '',
				'description' => $repo['package']['description'] ?? '',
				'repositoryUrl' => $package['repositoryUrl'] ?? '',
				'branch' => $package['branch'] ?? '',
				'installed' => $package['installed'] ?? ''
			);
		}, $config['repositories']);

		return array_filter($repos, function ($repo) { return !empty($repo); });
	}

	/**
	 * Get the version of a package.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function getPackageVersion(string $name): string {
		$config = Composer::readConfig();

		if (!isset($config['repositories'])) {
			return '';
		}

		foreach ($config['repositories'] as $repo) {
			if (isset($repo['package'])) {
				if ($name == $repo['package']['name']) {
					return $repo['package']['version'];
				}
			}
		}

		return '';
	}

	/**
	 * Remove repository form composer.json.
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function remove(string $name): bool {
		$config = Composer::readConfig();

		if (!isset($config['repositories'])) {
			$config['repositories'] = array();
		}

		foreach ($config['repositories'] as $index => $repo) {
			if (isset($repo['package']) && $name === $repo['package']['name']) {
				unset($config['repositories'][$index]);
			}
		}

		$config['repositories'] = array_values($config['repositories']);

		if (empty($config['repositories'])) {
			unset($config['repositories']);
		}

		return Composer::writeConfig($config);
	}

	/**
	 * Get the matching adapter for a repository.
	 *
	 * @param string $platform
	 * @param string $name
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @param Messenger $Messenger
	 * @return AbstractAdapter
	 */
	private static function getRepositoryAdapter(string $platform, string $name, string $repositoryUrl, string $branch, Messenger $Messenger): AbstractAdapter {
		if ($platform == 'github') {
			return new GitHubAdapter($name, $repositoryUrl, $branch, $Messenger);
		}

		return new GitLabAdapter($name, $repositoryUrl, $branch, $Messenger);
	}
}
