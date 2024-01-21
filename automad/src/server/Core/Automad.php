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
 * Copyright (c) 2013-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\API\RequestHandler;
use Automad\Engine\Delimiters;
use Automad\Models\Context;
use Automad\Models\Filelist;
use Automad\Models\Page;
use Automad\Models\PageCollection;
use Automad\Models\Pagelist;
use Automad\Models\Shared;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Automad class includes all methods and properties regarding the site, structure and pages.
 * A Automad object is the "main" object. It consists of many single Page objects, the Shared object and holds also additional data like the Filelist and Pagelist objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Automad {
	/**
	 * Automad's Context object.
	 *
	 * The object is part of the Automad class to allow to access always the same instance of the Context class for all objects using the Automad object as parameter.
	 */
	public Context $Context;

	/**
	 * Automad's Shared object.
	 *
	 * The Shared object is passed also to all Page objects to allow for access of global data from within a page without needing access to the full Automad object.
	 */
	public Shared $Shared;

	/**
	 * Array holding all the site's pages and the related data.
	 *
	 * To access the data for a specific page, use the url as key: $this->collection['url'].
	 *
	 * @var array<Page>
	 */
	private array $collection = array();

	/**
	 * Automad's Filelist object
	 *
	 * The object is part of the Automad class to allow to access always the same instance of the Filelist class for all objects using the Automad object as parameter.
	 */
	private ?Filelist $Filelist = null;

	/**
	 * Automad's Pagelist object.
	 *
	 * The object is part of the Automad class to allow to access always the same instance of the Pagelist class for all objects using the Automad object as parameter.
	 */
	private ?Pagelist $Pagelist = null;

	/**
	 * Set collection and Shared properties and create the context object with the currently requested page.
	 *
	 * @param array<Page> $collection
	 * @param Shared $Shared
	 */
	public function __construct(array $collection, Shared $Shared) {
		$this->collection = $collection;
		$this->Shared = $Shared;

		Debug::log(array('Shared' => $this->Shared, 'Collection' => $this->collection), 'New instance created');

		$this->Context = new Context($this->getRequestedPage());
	}

	/**
	 * Define properties to be cached.
	 *
	 * @return array $itemsToCache
	 */
	public function __sleep(): array {
		$itemsToCache = array('collection', 'Shared');
		Debug::log($itemsToCache, 'Preparing Automad object for serialization! Caching the following items');

		return $itemsToCache;
	}

	/**
	 * Set new Context after being restored from cache.
	 */
	public function __wakeup(): void {
		Debug::log(get_object_vars($this), 'Automad object got unserialized');
		$this->Context = new Context($this->getRequestedPage());
	}

	/**
	 * Create a new Automad instance including its required dependencies.
	 *
	 * @return Automad
	 */
	public static function create(): Automad {
		$Shared = new Shared();
		$PageCollection = new PageCollection($Shared);

		return new Automad($PageCollection->get(), $Shared);
	}

	/**
	 * Tests wheter the currently requested page actually exists and is not an error page.
	 *
	 * @return bool True if existing
	 */
	public function currentPageExists(): bool {
		$Page = $this->Context->get();

		return ($Page->template != Page::TEMPLATE_NAME_404);
	}

	/**
	 * Load Automad instance from cache if possible.
	 *
	 * @return Automad
	 */
	public static function fromCache(): Automad {
		$Cache = new Cache();

		return $Cache->getAutomad();
	}

	/**
	 * Return $collection array.
	 *
	 * @return array<string, Page> $this->collection
	 */
	public function getCollection(): array {
		return $this->collection;
	}

	/**
	 * Return Automad's instance of the Filelist class and create instance when accessed for the first time.
	 *
	 * @return Filelist Filelist object
	 */
	public function getFilelist(): Filelist {
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
	public function getNavigationMetaData(): array {
		$pages = array();

		foreach ($this->collection as $Page) {
			$pages[$Page->origUrl] = array(
				'title' => $Page->get(Fields::TITLE),
				'index' => $Page->index,
				'url' => $Page->origUrl,
				'path' => $Page->path,
				'parentUrl' => $Page->parentUrl,
				'private' => $Page->private,
				'lastModified' => $Page->get(Fields::TIME_LAST_MODIFIED),
				'publicationState' => $Page->get(Fields::PUBLICATION_STATE)
			);
		}

		return $pages;
	}

	/**
	 * If existing, return the page object for the passed relative URL.
	 *
	 * @param string $url
	 * @return Page|null Page
	 */
	public function getPage(string $url): ?Page {
		if (array_key_exists($url, $this->collection)) {
			return $this->collection[$url];
		}

		return null;
	}

	/**
	 * Return Automad's instance of the Pagelist class and create instance when accessed for the first time.
	 *
	 * @return Pagelist Pagelist object
	 */
	public function getPagelist(): Pagelist {
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
	public function loadTemplate(string $file): string {
		// Expose $Automad to templates.
		$Automad = $this;

		if (is_readable($file)) {
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();
		} else {
			$template = Str::stripStart($file, AM_BASE_DIR . AM_DIR_PACKAGES);
			$title = $Automad->Context->get()->get(Fields::TITLE);
			$url = $Automad->Context->get()->get(Fields::URL);
			$output = "<h1>Template $template for page $title ($url) is missing!</h1><h2>Make sure you have selected an existing template for this page!</h2>";
		}

		// Strip comments before return.
		return preg_replace('/(' . preg_quote(Delimiters::COMMENT_OPEN) . '.*?' . preg_quote(Delimiters::COMMENT_CLOSE) . ')/s', '', $output);
	}

	/**
	 * Return the page object for the requested page.
	 *
	 * @return Page|null A page object
	 */
	private function getRequestedPage(): ?Page {
		if (strpos(AM_REQUEST, RequestHandler::$apiBase) === 0) {
			return $this->getPage(Request::post('url'));
		}

		if (AM_FEED_ENABLED && AM_REQUEST == AM_FEED_URL) {
			return $this->getPage('/');
		}

		if ($Page = $this->getPage(AM_REQUEST)) {
			return $Page;
		}

		return $this->pageNotFound();
	}

	/**
	 * Create a temporary page for a missing page and send a 404 header.
	 *
	 * @return Page The error page
	 */
	private function pageNotFound(): Page {
		header('HTTP/1.0 404 Not Found');

		if (file_exists(AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $this->Shared->get(Fields::THEME) . '/' . Page::TEMPLATE_NAME_404 . '.php')) {
			$data = array();
			$data[Fields::TEMPLATE] = Page::TEMPLATE_NAME_404;
			$data[Fields::LEVEL] = 0;
			$data[Fields::PARENT] = '';

			return new Page($data, $this->Shared);
		}

		exit('<h1>Page not found!</h1>');
	}
}
