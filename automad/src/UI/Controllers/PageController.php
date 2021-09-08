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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Page;
use Automad\Core\Request;
use Automad\UI\Components\Layout\PageData;
use Automad\UI\Models\PageModel;
use Automad\UI\Response;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Page controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
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
	 * Send form when there is no posted data in the request or save data if there is.
	 *
	 * @return Response the response object
	 */
	public static function data() {
		$Automad = UICache::get();
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
				$PageData = new PageData($Automad, $Page);
				$Response->setHtml($PageData->render());
			}
		}

		return $Response;
	}

	/**
	 * Delete page.
	 *
	 * @return Response the response object
	 */
	public static function delete() {
		$Automad = UICache::get();
		$Response = new Response();
		$url = Request::post('url');
		$title = Request::post('title');

		// Validate $_POST.
		if ($url && ($Page = $Automad->getPage($url)) && $url != '/' && $title) {
			// Check if the page's directory and parent directory are wirtable.
			if (is_writable(dirname(PageModel::getPageFilePath($Page)))
				&& is_writable(dirname(dirname(PageModel::getPageFilePath($Page))))) {
				PageModel::delete($Page, $title);

				$Response->setRedirect('?view=Page&url=' . urlencode($Page->parentUrl));
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
	 * @return Response the response object
	 */
	public static function duplicate() {
		$Automad = UICache::get();
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
	 * @return Response the response object
	 */
	public static function move() {
		$Automad = UICache::get();
		$Response = new Response();
		$url = Request::post('url');
		$dest = Request::post('destination');
		$title = Request::post('title');

		// Validation of $_POST. To avoid all kinds of unexpected trouble,
		// the URL and the destination must exist in the Automad's collection and a title must be present.
		if ($url
			&& $dest
			&& $title
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
	 * 	Get the theme/template file from posted data or return a default template name.
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
