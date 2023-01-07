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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\Admin\Models\PageModel;
use Automad\Admin\Text;
use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\PageIndex;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Models\Page;
use Automad\Models\Selection;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App controller handles all requests related to page data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageController {
	/**
	 * Add page based on data in $_POST.
	 *
	 * @return Response the response object
	 */
	public static function add() {
		$Response = new Response();
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

		$themeTemplate = self::getTemplateNameFromArray($_POST, 'theme_template');
		$isPrivate = (bool) Request::post('private');

		return $Response->setRedirect(Page::add($Parent, $title, $themeTemplate, $isPrivate));
	}

	/**
	 * Get a breadcrumb trail for a requested page.
	 *
	 * @return Response the response data
	 */
	public static function breadcrumbs() {
		$Automad = Automad::fromCache();
		$Response = new Response();
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {
			$Selection = new Selection($Automad->getCollection());
			$Selection->filterBreadcrumbs($url);

			$breadcrumbs = array();

			foreach ($Selection->getSelection(false) as $Page) {
				$breadcrumbs[] = array(
					'url' => $Page->origUrl,
					'title' => $Page->get(AM_KEY_TITLE)
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
	public static function data() {
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
					$url,
					$data,
					Request::post('slug')
				);
			}
		}

		// If only the URL got submitted, just get the form ready.
		$ThemeCollection = new ThemeCollection();
		$Theme = $ThemeCollection->getThemeByKey($Page->get(AM_KEY_THEME));
		$keys = Fields::inCurrentTemplate($Page, $Theme);
		$data = Parse::dataFile($Page->getFile());

		$fields = array_merge(
			array_fill_keys(Fields::$reserved, ''),
			array_fill_keys($keys, ''),
			$data
		);

		ksort($fields);

		return $Response->setData(
			array(
				'url' => $Page->origUrl,
				'slug' => basename($Page->path),
				'template' => $Page->getTemplate(),
				'fields' => $fields,
				'shared' => $Automad->Shared->data
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
	 * Duplicate a page.
	 *
	 * @return Response the response object
	 */
	public static function duplicate() {
		$Response = new Response();
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
	 * Move a page.
	 *
	 * @return Response the response object
	 */
	public static function move() {
		$Automad = Automad::fromCache();
		$Response = new Response();
		$url = Request::post('url');
		$dest = Request::post('targetPage');
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

		$newPagePath = $Page->moveDirAndUpdateLinks(
			$dest->path,
			basename($Page->path)
		);

		$Response->setRedirect(Page::dashboardUrlByPath($newPagePath));
		Debug::log($Page->path, 'page');
		Debug::log($dest->path, 'destination');

		Cache::clear();

		$Page = Page::findByPath($newPagePath);
		$Response->setData(array('url' => $Page->origUrl));

		return $Response;
	}

	/**
	 * Update the index of a page after reordering it in the nav tree.
	 *
	 * @return Response the response object
	 */
	public static function updateIndex() {
		$Response = new Response();
		$url = Request::post('url');
		$Page = Page::fromCache($url);

		if (!$Page) {
			return $Response->setError(Text::get('pageNotFoundError'))->setReload(true);
		}

		$parentPath = Request::post('parentPath');
		$layout = json_decode(Request::post('layout'));

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
	 * @param array|null $array
	 * @param string|null $key
	 * @return string The template filename
	 */
	private static function getTemplateNameFromArray(?array $array = null, ?string $key = null) {
		$template = 'data.php';

		if (is_array($array) && $key) {
			if (!empty($array[$key])) {
				$template = $array[$key];
			}
		}

		Debug::log($template, 'Template');

		return $template;
	}

	/**
	 * Save a page.
	 *
	 * @param Page $Page
	 * @param string $url
	 * @param array $data
	 * @param string $slug
	 * @return Response the response object
	 */
	private static function save(Page $Page, string $url, array $data, string $slug) {
		$Response = new Response();
		$pageFile = $Page->getFile();

		if (!$data[AM_KEY_TITLE]) {
			return $Response->setError(Text::get('missingPageTitleError'));
		}

		if ($url != '/' && !is_writable(dirname(dirname($pageFile)))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		if (!is_writable($pageFile) || !is_writable(dirname($pageFile))) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		// The theme and the template get passed as theme/template.php combination separate
		// form $_POST['data']. That information has to be parsed first and "subdivided".
		$themeTemplate = self::getTemplateNameFromArray($_POST, 'theme_template');

		if ($result = $Page->save($url, $data, $themeTemplate, $slug)) {
			if (!empty($result['redirect'])) {
				return $Response->setRedirect($result['redirect']);
			}

			$Response->setData(array('update' => $result));
		}

		return $Response;
	}
}
