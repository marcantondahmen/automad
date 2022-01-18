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

use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The base for all dashboard views with a sidebar layout.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractSidebar extends AbstractView {
	/**
	 * Render the page body.
	 *
	 * @return string the rendered body
	 */
	public function body() {
		$fn = $this->fn;
		$Text = Text::getObject();

		return <<< HTML
			<body>
				<div class="am-l-page am-l-page--sidebar">
					<am-toggle class="am-l-sidebar__overlay" target="body" cls="am-l-page--sidebar-open"></am-toggle>
					<nav class="am-l-sidebar">
						<am-sidebar class="am-l-sidebar__content">
							<div class="am-l-sidebar__logo">
								Logo
							</div>
							<div class="am-l-sidebar__nav">
								<nav class="am-c-nav">
									<span class="am-c-nav__label">$Text->sidebar_header_global</span>
									<span class="am-c-nav__item">
										<a href="{$fn(AM_BASE_URL)}" class="am-c-nav__link">
											<i class="bi bi-bookmark"></i>
											{$this->Automad->Shared->get(AM_KEY_SITENAME)}
										</a>
									</span>
									<am-nav-item view="Home" icon="window-sidebar" text="dashboard_title"></am-nav-item>
									<am-nav-item view="System" icon="sliders" text="sys_title"></am-nav-item>
									<am-nav-item view="Shared" icon="file-earmark-medical" text="shared_title"></am-nav-item>
									<am-nav-item view="Packages" icon="box-seam" text="packages_title"></am-nav-item>
								</nav>
								<am-nav-tree></am-nav-tree>
							</div>
						</am-sidebar>
					</nav>
					<nav class="am-l-navbar am-l-navbar--sidebar">
						<div class="am-l-navbar__logo">
							Logo
						</div>
						<div class="am-l-navbar__jump">
							<am-jumpbar placeholder="$Text->jumpbar_placeholder"></am-jumpbar>
						</div>
						<div class="am-l-navbar__buttons">
							{$fn($this->saveButton())}
							<am-toggle target="body" cls="am-l-page--sidebar-open">Open</am-toggle>
						</div>
					</nav>
					<main class="am-l-main am-l-main--sidebar">
						{$fn($this->main())}
					</main>
					<footer class="am-l-footer">
						<div class="am-l-footer__content">
							Footer
						</div>
					</footer>
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
	 * Render a save button.
	 *
	 * @return string the rendered save button
	 */
	protected function saveButton() {
	}
}
