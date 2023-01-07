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
 * Copyright (c) 2013-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\API\RequestHandler;
use Automad\Models\Context;
use Automad\Models\Filelist;
use Automad\Models\Page;
use Automad\Models\PageCollection;
use Automad\Models\Pagelist;
use Automad\Models\Shared;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Automad class includes all methods and properties regarding the site, structure and pages.
 * A Automad object is the "main" object. It consists of many single Page objects, the Shared object and holds also additional data like the Filelist and Pagelist objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Automad {
	/**
	 * Automad's Context object.
	 *
	 * The object is part of the Automad class to allow to access always the same instance of the Context class for all objects using the Automad object as parameter.
	 */
	public $Context;

	/**
	 * Automad's Shared object.
	 *
	 * The Shared object is passed also to all Page objects to allow for access of global data from within a page without needing access to the full Automad object.
	 */
	public $Shared;

	/**
	 * Array holding all the site's pages and the related data.
	 *
	 * To access the data for a specific page, use the url as key: $this->collection['url'].
	 */
	private $collection = array();

	/**
	 * Automad's Filelist object
	 *
	 * The object is part of the Automad class to allow to access always the same instance of the Filelist class for all objects using the Automad object as parameter.
	 */
	private $Filelist = false;

	/**
	 * Automad's Pagelist object.
	 *
	 * The object is part of the Automad class to allow to access always the same instance of the Pagelist class for all objects using the Automad object as parameter.
	 */
	private $Pagelist = false;

	/**
	 * Parse sitewide settings, create $collection and set the context to the currently requested page.
	 */
	public function __construct() {
		$this->Shared = new Shared();

		$PageCollection = new PageCollection('/', $this->Shared);
		$this->collection = $PageCollection->get();

		Debug::log(array('Shared' => $this->Shared, 'Collection' => $this->collection), 'New instance created');

		// Set the context initially to the requested page.
		$this->Context = new Context($this->getRequestedPage());
	}

	/**
	 * Define properties to be cached.
	 *
	 * @return array $itemsToCache
	 */
	public function __sleep() {
		$itemsToCache = array('collection', 'Shared');
		Debug::log($itemsToCache, 'Preparing Automad object for serialization! Caching the following items');

		return $itemsToCache;
	}

	/**
	 * Set new Context after being restored from cache.
	 */
	public function __wakeup() {
		Debug::log(get_object_vars($this), 'Automad object got unserialized');
		$this->Context = new Context($this->getRequestedPage());
	}

	/**
	 * Tests wheter the currently requested page actually exists and is not an error page.
	 *
	 * @return bool True if existing
	 */
	public function currentPageExists() {
		$Page = $this->Context->get();

		return ($Page->template != AM_PAGE_NOT_FOUND_TEMPLATE);
	}

	/**
	 * Load Automad instance from cache if possible.
	 *
	 * @return Automad
	 */
	public static function fromCache() {
		$Cache = new Cache();

		return $Cache->getAutomad();
	}

	/**
	 * Return $collection array.
	 *
	 * @return array $this->collection
	 */
	public function getCollection() {
		return $this->collection;
	}

	/**
	 * Return Automad's instance of the Filelist class and create instance when accessed for the first time.
	 *
	 * @return Filelist Filelist object
	 */
	public function getFilelist() {
		if (!$this->Filelist) {
			$this->Filelist = new Filelist($this->Context);
		}

		return $this->Filelist;
	}

	/**
	 * Build the pages array that is used to build a nav tree.
	 *
	 * @return array the rendered data array
	 */
	public function getNavigationMetaData() {
		$pages = array();

		foreach ($this->collection as $Page) {
			$pages[$Page->origUrl] = array(
				'title' => $Page->get(AM_KEY_TITLE),
				'index' => $Page->index,
				'url' => $Page->origUrl,
				'path' => $Page->path,
				'parentPath' => rtrim(dirname($Page->path), '/') . '/',
				'private' => $Page->private,
				'mTime' => $Page->get(AM_KEY_MTIME)
			);
		}

		return $pages;
	}

	/**
	 * If existing, return the page object for the passed relative URL.
	 *
	 * @param string $url
	 * @return Page|null Page or null
	 */
	public function getPage(string $url) {
		if (array_key_exists($url, $this->collection)) {
			return $this->collection[$url];
		}
	}

	/**
	 * Return Automad's instance of the Pagelist class and create instance when accessed for the first time.
	 *
	 * @return Pagelist Pagelist object
	 */
	public function getPagelist() {
		if (!$this->Pagelist) {
			$this->Pagelist = new Pagelist($this->collection, $this->Context);
		}

		return $this->Pagelist;
	}

	/**
	 * Load and buffer a template file and return its content as string. The Automad object gets passed as parameter to be available for all plain PHP within the included file.
	 * This is basically the base method to load a template without parsing the Automad markup. It just gets the parsed PHP content.
	 *
	 * Before returning the markup, all comments <# ... #> get stripped.
	 *
	 * Note that even when the it is possible to use plain PHP in a template file, all that code will be parsed first when buffering, before any of the Automad markup is getting parsed.
	 * That also means, that is not possible to make plain PHP code really interact with any of the Automad placeholder markup.
	 *
	 * @param string $file
	 * @return string The buffered output
	 */
	public function loadTemplate(string $file) {
		$Automad = $this;

		if (is_readable($file)) {
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();
		} else {
			$template = Str::stripStart($file, AM_BASE_DIR . AM_DIR_PACKAGES);
			$title = $this->Context->get()->get(AM_KEY_TITLE);
			$url = $this->Context->get()->get(AM_KEY_URL);
			$output = "<h1>Template $template for page $title ($url) is missing!</h1><h2>Make sure you have selected an existing template for this page!</h2>";
		}

		// Strip comments before return.
		return preg_replace('/(' . preg_quote(AM_DEL_COMMENT_OPEN) . '.*?' . preg_quote(AM_DEL_COMMENT_CLOSE) . ')/s', '', $output);
	}

	/**
	 * Return the page object for the requested page.
	 *
	 * @return Page A page object
	 */
	private function getRequestedPage() {
		if (strpos(AM_REQUEST, RequestHandler::$apiBase) === 0) {
			return $this->getPage(Request::post('url'));
		}

		if (AM_FEED_ENABLED && AM_REQUEST == AM_FEED_URL) {
			return $this->getPage('/');
		}

		if ($Page = $this->getPage(AM_REQUEST)) {
			return $Page;
		} else {
			return $this->pageNotFound();
		}
	}

	/**
	 * Create a temporary page for a missing page and send a 404 header.
	 *
	 * @return Page The error page
	 */
	private function pageNotFound() {
		header('HTTP/1.0 404 Not Found');

		if (file_exists(AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $this->Shared->get(AM_KEY_THEME) . '/' . AM_PAGE_NOT_FOUND_TEMPLATE . '.php')) {
			$data[AM_KEY_TEMPLATE] = AM_PAGE_NOT_FOUND_TEMPLATE;
			$data[AM_KEY_LEVEL] = 0;
			$data[AM_KEY_PARENT] = '';

			return new Page($data, $this->Shared);
		} else {
			exit('<h1>Page not found!</h1>');
		}
	}
}
