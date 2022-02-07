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
	 * @return Response the response object
	 */
	public static function add() {
		$Automad = UICache::get();
		$Response = new Response();
		$url = Request::post('url');

		// Validation of $_POST. URL, title and template must exist and != false.
		if ($url && ($Page = $Automad->getPage($url))) {
			$subpage = Request::post('subpage');

			if (is_array($subpage) && !empty($subpage['title'])) {
				// Check if the current page's directory is writable.
				if (is_writable(dirname(PageModel::getPageFilePath($Page)))) {
					Debug::log($Page->url, 'page');
					Debug::log($subpage, 'new subpage');

					// The new page's properties.
					$title = $subpage['title'];
					$themeTemplate = self::getTemplateNameFromArray($subpage, 'theme_template');
					$isPrivate = (!empty($subpage['private']));

					$Response->setRedirect(PageModel::add($Page, $title, $themeTemplate, $isPrivate));
				} else {
					$Response->setError(
						Text::get('error_permission') .
						'<p>' . dirname(PageModel::getPageFilePath($Page)) . '</p>'
					);
				}
			} else {
				$Response->setError(Text::get('error_page_title'));
			}
		} else {
			$Response->setError(Text::get('error_no_destination'));
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
					Request::post('prefix'),
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
						'prefix' => PageModel::extractPrefixFromPath($Page->path),
						'slug' => PageModel::extractSlugFromPath($Page->path),
						'template' => $Page->getTemplate(),
						'fields' => $fields,
						'shared' => $Automad->Shared->data
					)
				);
			}
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

				$Response->setRedirect(AM_BASE_INDEX . AM_PAGE_DASHBOARD . '/page?url=' . urlencode($Page->parentUrl));
				Debug::log($Page->url, 'deleted');

				Cache::clear();
			} else {
				$Response->setError(
					Text::get('error_permission') .
					'<p>' . dirname(dirname(PageModel::getPageFilePath($Page))) . '</p>'
				);
			}
		} else {
			$Response->setError(Text::get('error_page_not_found'));
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
					$Response->setError(Text::get('error_permission'));
				}
			} else {
				$Response->setError(Text::get('error_page_not_found'));
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
							PageModel::extractPrefixFromPath($Page->path),
							PageModel::extractSlugFromPath($Page->path)
						);

						$Response->setRedirect(PageModel::contextUrlByPath($newPagePath));
						Debug::log($Page->path, 'page');
						Debug::log($dest->path, 'destination');

						Cache::clear();
					} else {
						$Response->setError(
							Text::get('error_permission') .
							'<p>' . dirname(dirname(PageModel::getPageFilePath($Page))) . '</p>'
						);
					}
				} else {
					$Response->setError(
						Text::get('error_permission') .
						'<p>' . FileSystem::fullPagePath($dest->path) . '</p>'
					);
				}
			}
		} else {
			$Response->setError(Text::get('error_no_destination'));
		}

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
	 * @param string $prefix
	 * @param string $slug
	 * @return Response the response object
	 */
	private static function save(Page $Page, string $url, array $data, string $prefix, string $slug) {
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

					if ($redirectUrl = PageModel::save($Page, $url, $data, $themeTemplate, $prefix, $slug)) {
						$Response->setRedirect($redirectUrl);
					} else {
						$Response->setSuccess(Text::get('success_saved'));
					}
				} else {
					$Response->setError(Text::get('error_permission'));
				}
			} else {
				$Response->setError(Text::get('error_permission'));
			}
		} else {
			// If the title is missing, just return an error.
			$Response->setError(Text::get('error_page_title'));
		}

		return $Response;
	}
}