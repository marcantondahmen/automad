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
 * Copyright (c) 2015-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Context represents the current page within statements (loops) or just the requested page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2015-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Context {
	/**
	 * The context Page.
	 */
	private Page $Page;

	/**
	 * The constructor.
	 *
	 * @param Page|null $Page
	 */
	public function __construct(?Page $Page) {
		$this->setOrCreate($Page);
	}

	/**
	 * Return $Page.
	 *
	 * @return Page $Page
	 */
	public function get(): Page {
		return $this->Page;
	}

	/**
	 * Set the context.
	 *
	 * @param Page|null $Page
	 */
	public function set(?Page $Page): void {
		$this->setOrCreate($Page);
	}

	/**
	 * Set or create an undefined page.
	 *
	 * @param Page|null $Page
	 */
	private function setOrCreate(?Page $Page): void {
		if ($Page === null) {
			Debug::log('Create new undefined page object');
			$this->Page = Page::undefined();

			return;
		}

		$this->Page = $Page;
		Debug::log($Page->origUrl);
	}
}
