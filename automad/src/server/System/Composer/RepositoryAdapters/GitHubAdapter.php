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
 * Copyright (c) 2025-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System\Composer\RepositoryAdapters;

use Automad\System\Composer\Auth;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The GitHub Composer repository meta data provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class GitHubAdapter extends AbstractAdapter {
	/**
	 * Generate the archive URL.
	 *
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @return string
	 */
	protected function getArchiveUrl(string $repositoryUrl, string $branch): string {
		$repo = basename(dirname($repositoryUrl)) . '/' . basename($repositoryUrl);

		return "https://api.github.com/repos/{$repo}/zipball/{$branch}";
	}

	/**
	 * Generate the raw composer.json URL.
	 *
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @return string
	 */
	protected function getComposerJsonUrl(string $repositoryUrl, string $branch): string {
		$repo = basename(dirname($repositoryUrl)) . '/' . basename($repositoryUrl);

		return "https://api.github.com/repos/{$repo}/contents/composer.json?ref={$branch}";
	}

	/**
	 * Generate the headers array.
	 *
	 * @return array
	 */
	protected function getHeaders(): array {
		$Auth = Auth::get();

		return array(
			"Authorization: token $Auth->githubToken",
			'Accept: application/vnd.github.v3.raw',
			'User-Agent: Automad'
		);
	}

	/**
	 * The platform type.
	 *
	 * @return string
	 */
	protected function getPlatformType(): string {
		return 'github';
	}
}
