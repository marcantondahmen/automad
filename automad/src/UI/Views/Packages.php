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
 * The package manager page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Packages extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$fn = $this->fn;

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled uk-hidden-small"><i class="uk-icon-download"></i></li>
				<li><a href="">{$fn(Text::get('packages_title'))}</a></li>
			</ul>
			<div class="am-sticky">
				<div class="uk-form">
					<input 
					class="uk-width-1-1" 
					type="search" 
					name="filter" 
					placeholder="{$fn(Text::get('packages_filter'))}"
					data-am-packages-filter 
					/>
				</div>
			</div>
			<div class="uk-margin-large-top" data-am-packages>
				<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small"></i>
			</div>
			{$fn($this->progress())}
		HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('packages_title');

		return "$title &mdash; Automad";
	}

	/**
	 * Render the progress modals.
	 *
	 * @return string the rendered modals
	 */
	private function progress() {
		$progressModals = array(
			'am-modal-update-all-packages-progress' => array(
				'icon' => 'uk-icon-refresh uk-icon-spin',
				'text' => Text::get('packages_updating_all')
			),
			'am-modal-update-package-progress' => array(
				'icon' => 'uk-icon-refresh uk-icon-spin',
				'text' => Text::get('packages_updating')
			),
			'am-modal-remove-package-progress' => array(
				'icon' => 'uk-icon-close',
				'text' => Text::get('packages_removing')
			),
			'am-modal-install-package-progress' => array(
				'icon' => 'uk-icon-download',
				'text' => Text::get('packages_installing')
			)
		);

		$modals = '';

		foreach ($progressModals as $id => $content) {
			$modals .= <<< HTML
				<div id="$id" class="uk-modal">
					<div class="uk-modal-dialog uk-padding-remove">
						<div class="am-progress-panel uk-progress uk-progress-striped uk-active">
							<div class="uk-progress-bar uk-margin-remove" style="width: 100%;">			
								<i class="{$content['icon']}"></i>&nbsp;
								{$content['text']}
							</div>
						</div>
					</div>
				</div>
			HTML;
		}

		return $modals;
	}
}
