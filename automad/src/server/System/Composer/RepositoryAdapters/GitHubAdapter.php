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
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The GitHub Composer repository meta data provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type Commit from AbstractAdapter
 */
class GitHubAdapter extends AbstractAdapter {
	/**
	 * Get latest commit details.
	 *
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @return Commit
	 */
	public function getLatestCommit(string $repositoryUrl, string $branch): array {
		$repo = basename(dirname($repositoryUrl)) . '/' . basename($repositoryUrl);

		$json = Fetch::request("https://api.github.com/repos/{$repo}/branches/{$branch}", $this->getHeaders());
		$data = json_decode($json);

		return array(
			'hash' => $data->commit->sha ?? '',
			'message' => $data->commit->commit->message ?? '',
			'timestamp' => $data->commit->commit->committer->date ?? '',
			'url' => $data->commit->html_url ?? ''
		);
	}

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
