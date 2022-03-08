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

namespace Automad\Controllers;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Page;
use Automad\Core\PageIndex;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Core\Selection;
use Automad\Models\PageModel;
use Automad\System\Fields;
use Automad\System\ThemeCollection;
use Automad\UI\Utils\Text;

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
	 * /api/Page/add
	 *
	 * @return Response the response object
	 */
	public static function add() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$Response = new Response();
		$targetPage = Request::post('targetPage');

		if ($targetPage && ($Parent = $Automad->getPage($targetPage))) {
			if ($title = Request::post('title')) {
				if (is_writable(dirname(PageModel::getPageFilePath($Parent)))) {
					Debug::log($Parent->url, 'parent page');

					$themeTemplate = self::getTemplateNameFromArray($_POST, 'theme_template');
					$isPrivate = Request::post('private');

					$Response->setRedirect(PageModel::add($Parent, $title, $themeTemplate, $isPrivate));
				} else {
					$Response->setError(
						Text::get('permissionsDeniedError') .
						'<p>' . dirname(PageModel::getPageFilePath($Parent)) . '</p>'
					);
				}
			} else {
				$Response->setError(Text::get('missingPageTitleError'));
			}
		} else {
			$Response->setError(Text::get('missingTargetPageError'));
		}

		return $Response;
	}

	/**
	 * Get a breadcrumb trail for a requested page.
	 *
	 * /api/Page/breadcrumbs
	 *
	 * @return Response the response data
	 */
	public static function breadcrumbs() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
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
	 * /api/Page/data
	 *
	 * @return Response the response object
	 */
	public static function data() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$Response = new Response();
		$url = Request::post('url');

		if ($url && ($Page = $Automad->getPage($url))) {
			// If the posted form contains any "data", save the form's data to the page file.
			if ($data = Request::post('data')) {
				// Save page and replace $Response with the returned $Response object (error or redirect).
				$Response = self::save(
					$Page,
					$url,
					$data,
					Request::post('slug')
				);
			} else {
				// If only the URL got submitted, just get the form ready.
				$ThemeCollection = new ThemeCollection();
				$Theme = $ThemeCollection->getThemeByKey($Page->get(AM_KEY_THEME));
				$keys = Fields::inCurrentTemplate($Page, $Theme);
				$data = Parse::dataFile(PageModel::getPageFilePath($Page));

				$fields = array_merge(
					array_fill_keys(Fields::$reserved, ''),
					array_fill_keys($keys, ''),
					$data
				);

				ksort($fields);

				$Response->setData(
					array(
						'url' => $Page->origUrl,
						'slug' => basename($Page->path),
						'template' => $Page->getTemplate(),
						'fields' => $fields,
						'shared' => $Automad->Shared->data
					)
				);
			}
		} else {
			$Response->setCode(404);
		}

		return $Response;
	}

	/**
	 * Delete page.
	 *
	 * /api/Page/delete
	 *
	 * @return Response the response object
	 */
	public static function delete() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$Response = new Response();
		$url = Request::post('url');

		// Validate $_POST.
		if ($url && ($Page = $Automad->getPage($url)) && $url != '/') {
			// Check if the page's directory and parent directory are wirtable.
			if (is_writable(dirname(PageModel::getPageFilePath($Page)))
				&& is_writable(dirname(dirname(PageModel::getPageFilePath($Page))))) {
				PageModel::delete($Page);

				$Response->setSuccess(Text::get('deteledSuccess') . ' ' . $Page->origUrl);
				$Response->setRedirect('page?url=' . urlencode($Page->parentUrl));
				Debug::log($Page->url, 'deleted');

				Cache::clear();
			} else {
				$Response->setError(
					Text::get('permissionsDeniedError') .
					'<p>' . dirname(dirname(PageModel::getPageFilePath($Page))) . '</p>'
				);
			}
		} else {
			$Response->setError(Text::get('pageNotFoundError'));
		}

		return $Response;
	}

	/**
	 * Duplicate a page.
	 *
	 * /api/Page/duplicate
	 *
	 * @return Response the response object
	 */
	public static function duplicate() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$Response = new Response();
		$url = Request::post('url');

		if ($url) {
			if ($url != '/' && ($Page = $Automad->getPage($url))) {
				// Check permissions.
				if (is_writable(dirname(FileSystem::fullPagePath($Page->path)))) {
					$Response->setRedirect(PageModel::duplicate($Page));
				} else {
					$Response->setError(Text::get('permissionsDeniedError'));
				}
			} else {
				$Response->setError(Text::get('pageNotFoundError'));
			}
		}

		return $Response;
	}

	/**
	 * Move a page.
	 *
	 * /api/Page/move
	 *
	 * @return Response the response object
	 */
	public static function move() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$Response = new Response();
		$url = Request::post('url');
		$dest = Request::post('targetPage');

		// Validation of $_POST. To avoid all kinds of unexpected trouble,
		// the URL and the destination must exist in the Automad's collection.
		if ($url
			&& $dest
			&& ($Page = $Automad->getPage($url))
			&& ($dest = $Automad->getPage($dest))) {
			// The home page can't be moved!
			if ($url != '/') {
				// Check if new parent directory is writable.
				if (is_writable(FileSystem::fullPagePath($dest->path))) {
					// Check if the current page's directory and parent directory is writable.
					if (is_writable(dirname(PageModel::getPageFilePath($Page)))
						&& is_writable(dirname(dirname(PageModel::getPageFilePath($Page))))) {
						// Move page
						$newPagePath = PageModel::moveDirAndUpdateLinks(
							$Page,
							$dest->path,
							basename($Page->path)
						);

						$Response->setRedirect(PageModel::contextUrlByPath($newPagePath));
						Debug::log($Page->path, 'page');
						Debug::log($dest->path, 'destination');

						Cache::clear();
					} else {
						$Response->setError(
							Text::get('permissionsDeniedError') .
							'<p>' . dirname(dirname(PageModel::getPageFilePath($Page))) . '</p>'
						);
					}
				} else {
					$Response->setError(
						Text::get('permissionsDeniedError') .
						'<p>' . FileSystem::fullPagePath($dest->path) . '</p>'
					);
				}
			}
		} else {
			$Response->setError(Text::get('missingTargetPageError'));
		}

		return $Response;
	}

	/**
	 * Update the index of a page after reordering it in the nav tree.
	 *
	 * @return Response the response object
	 */
	public static function updateIndex() {
		$Response = new Response();

		$parentPath = Request::post('parentPath');
		$layout = json_decode(Request::post('layout'));

		$Response->setData(
			array(
				'index' => PageIndex::write($parentPath, $layout),
				'path' => $parentPath
			)
		);

		return $Response;
	}

	/**
	 * Get the theme/template file from posted data or return a default template name.
	 *
	 * @param array $array
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

		// A title is required for building the page's path.
		// If there is no title provided, an error will be returned instead of saving and moving the page.
		if ($data[AM_KEY_TITLE]) {
			// Check if the parent directory is writable for all pages but the homepage.
			// Since the directory of the homepage is just "pages" and its parent directory
			// is the base directory, it should not be necessary to set the base directoy permissions
			// to 777, since the homepage directory will never be renamed or moved.
			if ($url =='/' || is_writable(dirname(dirname(PageModel::getPageFilePath($Page))))) {
				// Check if the page's file and the page's directory is writable.
				if (is_writable(PageModel::getPageFilePath($Page))
					&& is_writable(dirname(PageModel::getPageFilePath($Page)))) {
					// The theme and the template get passed as theme/template.php combination separate
					// form $_POST['data']. That information has to be parsed first and "subdivided".
					$themeTemplate = self::getTemplateNameFromArray($_POST, 'theme_template');

					if ($result = PageModel::save($Page, $url, $data, $themeTemplate, $slug)) {
						if (!empty($result['redirect'])) {
							$Response->setRedirect($result['redirect']);
						} else {
							$Response->setData(array('update' => $result));
						}
					}
				} else {
					$Response->setError(Text::get('permissionsDeniedError'));
				}
			} else {
				$Response->setError(Text::get('permissionsDeniedError'));
			}
		} else {
			// If the title is missing, just return an error.
			$Response->setError(Text::get('missingPageTitleError'));
		}

		return $Response;
	}
}
