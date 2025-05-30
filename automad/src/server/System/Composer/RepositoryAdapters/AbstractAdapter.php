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

namespace Automad\System\Composer\RepositoryAdapters;

use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Core\Text;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract Composer repository adapter.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractAdapter {
	const KEYS = array('description', 'autoload', 'require', 'type');

	/**
	 * The branch name.
	 */
	protected string $branch;

	/**
	 * The repository config.
	 */
	protected array $config = array();

	/**
	 * The package name.
	 */
	protected string $name;

	/**
	 * The repository URL.
	 */
	protected string $repositoryUrl;

	/**
	 * The constructor.
	 *
	 * @param string $name
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @param Messenger $Messenger
	 */
	public function __construct(string $name, string $repositoryUrl, string $branch, Messenger $Messenger) {
		$composerJson = Fetch::get($this->getComposerJsonUrl($repositoryUrl, $branch), $this->getHeaders());

		Debug::log($composerJson, 'composer.json');

		if (empty($composerJson)) {
			$Messenger->setError(Text::get('repositoryComposerJsonError'));
		}

		$this->config = $composerJson ? json_decode($composerJson, true) : array();
		$this->name = $name;
		$this->repositoryUrl = $repositoryUrl;
		$this->branch = $branch;
	}

	/**
	 * Return the generated config.
	 */
	public function getConfig(): array {
		$config = array(
			'type'=>'package',
			'package' => array(
				'name' => $this->name,
				'version' => "dev-{$this->branch}",
				'dist' => array(
					'url' => $this->getArchiveUrl($this->repositoryUrl, $this->branch),
					'type' => 'zip'
				),
				'branch' => $this->branch,
				'repositoryUrl' => $this->repositoryUrl,
				'platform' => $this->getPlatformType()
			)
		);

		foreach (self::KEYS as $key) {
			if (!empty($this->config[$key])) {
				$config['package'][$key] = $this->config[$key];
			}
		}

		return $config;
	}

	/**
	 * Generate the archive URL.
	 *
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @return string
	 */
	abstract protected function getArchiveUrl(string $repositoryUrl, string $branch): string;

	/**
	 * Generate the raw composer.json URL.
	 *
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @return string
	 */
	abstract protected function getComposerJsonUrl(string $repositoryUrl, string $branch): string;

	/**
	 * Generate the headers array.
	 *
	 * @return array
	 */
	abstract protected function getHeaders(): array;

	/**
	 * The platform type.
	 *
	 * @return string
	 */
	abstract protected function getPlatformType(): string;
}
