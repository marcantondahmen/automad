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

namespace Automad\UI\Views;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The base for all dashboard views with a centered layout.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractCentered extends AbstractView {
	/**
	 * Render the page body.
	 *
	 * @return string the rendered body
	 */
	public function body() {
		$fn = $this->fn;

		return <<< HTML
			<body>
				<div class="am-l-page am-l-page--centered">
					<nav class="am-l-navbar am-l-navbar--centered">
						<div>
							{$fn($this->navbarTitle())}
						</div>
						<div>
							<a href="{$fn(AM_BASE_INDEX)}">close</a>
						</div>
					</nav>
					<main class="am-l-main am-l-main--centered">
						{$fn($this->main())}
					</main>
				</div>
			</body>
			HTML;
	}

	/**
	 * Render the main content of the page body.
	 *
	 * @return string the rendered main content
	 */
	abstract protected function main();

	/**
	 * The navbar title.
	 *
	 * @return string the navbar title
	 */
	abstract protected function navbarTitle();
}
