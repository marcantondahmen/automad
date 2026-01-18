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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\PublicationState;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\Models\Page;
use Automad\Models\Selection;
use Automad\Stores\DataStore;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page controller handles all requests related to page data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageController {
	/**
	 * Add page based on data in $_POST.
	 *
	 * @return Response the response object
	 */
	public static function add(): Response {
		$Response = new Response();

		if (FileSystem::diskQuotaExceeded()) {
			return $Response->setError(Text::get('diskQuotaExceeded'))->setCode(403);
		}

		$targetPage = Request::post('targetPage');
		$Parent = Page::fromCache($targetPage);
		$title = Request::post('title');

		if (!$Parent) {
			return $Response->setError(Text::get('missingTargetPageError'));
		}

		if (!$title) {
			return $Response->setError(Text::get('missingPageTitleError'));
		}

		if (!is_writable(dirname($Parent->getFile()))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		Debug::log($Parent->url, 'parent page');

		$themeTemplate = self::getTemplateNameFromPost();
		$isPrivate = (bool) Request::post('private');

		return $Response->setRedirect(Page::add($Parent, $title, $themeTemplate, $isPrivate));
	}

	/**
	 * Get a breadcrumb trail for a requested page.
	 *
	 * @return Response the response data
	 */
	public static function breadcrumbs(): Response {
		$Automad = Automad::fromCache();
		$Response = new Response();
		$url = Request::post('url');
		$Page = $Automad->getPage($url);

		if ($Page) {
			$Selection = new Selection($Automad->getPages());
			$Selection->filterBreadcrumbs($url);

			$breadcrumbs = array();

			foreach ($Selection->getSelection(false) as $Page) {
				$breadcrumbs[] = array(
					'url' => $Page->origUrl,
					'title' => $Page->get(Fields::TITLE)
				);
			}

			$Response->setData($breadcrumbs);
		}

		return $Response;
	}

	/**
	 * Send form when there is no posted data in the request or save data if there is.
	 *
	 * @return Response the response object
	 */
	public static function data(): Response {
		$Automad = Automad::fromCache();
		$Response = new Response();
		$url = Request::post('url');
		$Page = $Automad->getPage($url);

		if (!$Page) {
			return $Response->setCode(404);
		}

		// If the posted form contains any "data", save the form's data to the page file.
		if ($data = Request::post('data')) {
			if (filemtime($Page->getFile()) > Request::post('dataFetchTime')) {
				return $Response->setError(Text::get('preventDataOverwritingError'))->setCode(403);
			}

			if (is_array($data)) {
				// Save page and replace $Response with the returned $Response object (error or redirect).
				return self::save(
					$Page,
					$data,
				);
			}
		}

		// If only the URL got submitted, just get the form ready.
		$ThemeCollection = new ThemeCollection();
		$Theme = $ThemeCollection->getThemeByKey(strval($Page->get(Fields::THEME)));
		$keys = Fields::inCurrentTemplate($Page, $Theme);

		$DataStore = new DataStore($Page->path);
		$data = $DataStore->getState(PublicationState::DRAFT) ?? array();

		$supportedFields = array_merge(
			array_fill_keys(Fields::$reserved, ''),
			array_fill_keys($keys, ''),
		);

		$fields = array_intersect_key(
			array_merge(
				$supportedFields,
				$data
			),
			$supportedFields
		);

		$unusedKeys = array_diff(array_keys($data), array_keys($fields));
		$unusedFields = array_intersect_key($data, array_fill_keys($unusedKeys, ''));

		Debug::log($unusedFields, 'Unused data');

		return $Response->setData(
			array(
				'url' => $Page->origUrl,
				'template' => $Page->getTemplate(),
				'fields' => $fields,
				'unused' => $unusedFields,
				'shared' => $Automad->Shared->data,
				'readme' => isset($Theme) ? $Theme->readme : ''
			)
		);
	}

	/**
	 * Delete page.
	 *
	 * @return Response the response object
	 */
	public static function delete() {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if ($url == '/') {
			return $Response;
		}

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setReload(true);
		}

		$pageFile = $Page->getFile();

		if (!is_writable(dirname($pageFile)) || !is_writable(dirname(dirname($pageFile)))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		$Page->delete();

		$Response->setSuccess(Text::get('deteledSuccess') . ' ' . $Page->origUrl);
		$Response->setRedirect('page?url=' . urlencode($Page->parentUrl));
		Debug::log($Page->url, 'deleted');

		Cache::clear();

		return $Response;
	}

	/**
	 * Discard a draft and revert content to the last published version.
	 *
	 * @return Response the response object
	 */
	public static function discardDraft(): Response {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if (!$Page) {
			return $Response;
		}

		$DataStore = new DataStore($Page->path);
		$DataStore->setState(PublicationState::DRAFT, array())->save();

		Cache::clear();

		return $Response->setReload(true);
	}

	/**
	 * Duplicate a page.
	 *
	 * @return Response the response object
	 */
	public static function duplicate(): Response {
		$Response = new Response();

		if (FileSystem::diskQuotaExceeded()) {
			return $Response->setError(Text::get('diskQuotaExceeded'))->setCode(403);
		}

		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if ($url == '/') {
			return $Response;
		}

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setReload(true);
		}

		if (!is_writable(dirname(FileSystem::fullPagePath($Page->path)))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		return $Response->setRedirect($Page->duplicate());
	}

	/**
	 * Get the publication state for a given page URL.
	 *
	 * @return Response
	 */
	public static function getPublicationState(): Response {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if (empty($Page)) {
			return $Response;
		}

		$DataStore = new DataStore($Page->path);

		return $Response->setData(
			array(
				'isPublished' => $DataStore->isPublished(),
				'lastPublished' => $DataStore->lastPublished()
			)
		);
	}

	/**
	 * Move a page.
	 *
	 * @return Response the response object
	 */
	public static function move(): Response {
		$Automad = Automad::fromCache();
		$Response = new Response();
		$url = Request::post('url');
		$dest = Request::post('targetPage');
		$layout = Request::post('layout');
		$Page = $Automad->getPage($url);
		$dest = $Automad->getPage($dest);

		if ($url === '/') {
			return $Response;
		}

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setReload(true);
		}

		if (!$dest) {
			return $Response->setError(Text::get('missingTargetPageError'));
		}

		if (!is_writable(FileSystem::fullPagePath($dest->path))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		$pageFile = $Page->getFile();

		if (!is_writable(dirname($pageFile)) || !is_writable(dirname(dirname($pageFile)))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		if ($layout) {
			$layout = json_decode($layout);
		} else {
			$layout = null;
		}

		$newPagePath = $Page->moveDirAndUpdateLinks(
			$dest->path,
			basename($Page->path),
			$layout
		);

		$Response->setRedirect(Page::dashboardUrlByPath($newPagePath));
		Debug::log($Page->path, 'Page');
		Debug::log($dest->path, 'Destination');

		Cache::clear();

		$Page = Page::findByPath($newPagePath);

		if ($Page) {
			$Response->setData(array('url' => $Page->origUrl));
		}

		return $Response;
	}

	/**
	 * Publish a page.
	 *
	 * @return Response
	 */
	public static function publish(): Response {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setReload(true);
		}

		$newPagePath = $Page->publish();
		$Response->setSuccess(Text::get('publishedSuccessfully'));

		if (!empty($newPagePath)) {
			return $Response->setRedirect(Page::dashboardUrlByPath($newPagePath));
		}

		return $Response;
	}

	/**
	 * Update the index of a page after reordering it in the nav tree.
	 *
	 * @return Response the response object
	 */
	public static function updateIndex(): Response {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setReload(true);
		}

		$parentPath = Request::post('parentPath');
		$layout = json_decode(Request::post('layout'));

		Cache::clear();

		return $Response->setData(
			array(
				'index' => PageIndex::write($parentPath, $layout),
				'path' => $parentPath
			)
		);
	}

	/**
	 * Get the theme/template file from posted data or return a default template name.
	 *
	 * @return string The template filename
	 */
	private static function getTemplateNameFromPost(): string {
		/** @var array<string, string|null> $_POST */
		return $_POST['theme_template'] ?? Page::TEMPLATE_FILE_DEFAULT;
	}

	/**
	 * Save a page.
	 *
	 * @param Page $Page
	 * @param array $data
	 * @return Response the response object
	 */
	private static function save(Page $Page, array $data): Response {
		$Response = new Response();

		if (FileSystem::diskQuotaExceeded()) {
			return $Response->setError(Text::get('diskQuotaExceeded'))->setCode(403);
		}

		$pageFile = $Page->getFile();

		if (!$data[Fields::TITLE]) {
			return $Response->setError(Text::get('missingPageTitleError'));
		}

		if ($Page->origUrl != '/' && !is_writable(dirname(dirname($pageFile)))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		if (!is_writable($pageFile) || !is_writable(dirname($pageFile))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		// The theme and the template get passed as theme/template.php combination separate
		// form $_POST['data']. That information has to be parsed first and "subdivided".
		$themeTemplate = self::getTemplateNameFromPost();

		$result = $Page->save($data, $themeTemplate);

		if (!empty($result['redirect'])) {
			$Response->setRedirect($result['redirect']);
		}

		if (!empty($result)) {
			$Response->setData($result);
		}

		return $Response;
	}
}
