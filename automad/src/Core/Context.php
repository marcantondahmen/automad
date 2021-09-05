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
 * Copyright (c) 2015-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Context represents the current page within statements (loops) or just the requested page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2015-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Context {
	/**
	 * The context Page.
	 */
	private $Page;

	/**
	 * The constructor.
	 *
	 * @param Page $Page
	 */
	public function __construct(?Page $Page) {
		$this->set($Page);
	}

	/**
	 * Return $Page.
	 *
	 * @return Page $Page
	 */
	public function get() {
		return $this->Page;
	}

	/**
	 * Set the context.
	 *
	 * @param Page $Page
	 */
	public function set(?Page $Page) {
		// Test whether $Page is empty - that can happen, when accessing the UI.
		if (!empty($Page)) {
			$this->Page = $Page;
			Debug::log($Page, 'Set context to ' . $Page->url);
		}
	}
}
