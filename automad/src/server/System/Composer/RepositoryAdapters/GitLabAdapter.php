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

use Automad\Core\Str;
use Automad\System\Composer\Auth;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The GitLab Composer repository meta data provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type Commit from AbstractAdapter
 */
class GitLabAdapter extends AbstractAdapter {
	/**
	 * Get latest commit details.
	 *
	 * @param string $repositoryUrl
	 * @param string $branch
	 * @return Commit
	 */
	public function getLatestCommit(string $repositoryUrl, string $branch): array {
		$repo = basename(dirname($repositoryUrl)) . '/' . basename($repositoryUrl);
		$gitlabBase = Str::stripEnd($repositoryUrl, '/' . $repo);
		$project = json_decode(Fetch::request("$gitlabBase/api/v4/projects/" . urlencode($repo), $this->getHeaders()), true);
		$id = $project['id'];

		$json = Fetch::request("{$gitlabBase}/api/v4/projects/$id/repository/branches/$branch", $this->getHeaders());
		$data = json_decode($json);

		return array(
			'hash' => $data->commit->id ?? '',
			'message' => $data->commit->message ?? '',
			'timestamp' => $data->commit->created_at ?? '',
			'url' => $data->commit->web_url ?? ''
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
		$basename = basename($repositoryUrl);

		return "{$repositoryUrl}/-/archive/{$branch}/{$basename}-{$branch}.zip";
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
		$gitlabBase = Str::stripEnd($repositoryUrl, '/' . $repo);
		$project = json_decode(Fetch::request("$gitlabBase/api/v4/projects/" . urlencode($repo), $this->getHeaders()), true);
		$id = $project['id'];

		return "{$gitlabBase}/api/v4/projects/$id/repository/files/composer.json/raw?ref=$branch";
	}

	/**
	 * Generate the headers array.
	 *
	 * @return array
	 */
	protected function getHeaders(): array {
		$Auth = Auth::get();

		return array("PRIVATE-TOKEN: $Auth->gitlabToken");
	}

	/**
	 * The platform type.
	 *
	 * @return string
	 */
	protected function getPlatformType(): string {
		return 'gitlab';
	}
}
