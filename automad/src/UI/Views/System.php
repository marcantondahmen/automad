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

use Automad\UI\Components\Modal\EditConfig;
use Automad\UI\Components\Nav\Switcher;
use Automad\UI\Components\System\Cache;
use Automad\UI\Components\System\Debug;
use Automad\UI\Components\System\Headless;
use Automad\UI\Components\System\Language;
use Automad\UI\Components\System\Update;
use Automad\UI\Components\System\Users;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The package manager page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class System extends AbstractView {
	/**
	 * Render body.
	 *
	 * @return string the rendered items
	 */
	protected function body() {
		$fn = $this->fn;

		return <<< HTML
			<ul class="uk-subnav uk-subnav-pill uk-margin-top">
				<li class="uk-disabled uk-hidden-small"><i class="uk-icon-sliders"></i></li>
				<li><a href="">{$fn(Text::get('sys_title'))}</a></li>
			</ul>
			{$fn($this->switcher())}
			{$fn(EditConfig::render('am-edit-config-modal'))}
			<ul id="am-sys-content" class="uk-switcher">
				<li>{$fn(Cache::render())}</li>
				<li>{$fn(Users::render())}</li>
				<li>{$fn(Update::render())}</li>
				<li>{$fn(Language::render())}</li>
				<li>{$fn(Headless::render())}</li>
				<li>{$fn(Debug::render())}</li>
			</ul>
		HTML;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	protected function title() {
		$title = Text::get('sys_title');

		return "$title &mdash; Automad";
	}

	/**
	 * Render the switcher menu.
	 *
	 * @return string the switcher markup
	 */
	private function switcher() {
		return Switcher::render(
			'#am-sys-content',
			array(
				array(
					'icon' => '<i class="uk-icon-rocket"></i>',
					'text' => Text::get('sys_cache')
				),
				array(
					'icon' => '<i class="uk-icon-user"></i>',
					'text' => Text::get('sys_user')
				),
				array(
					'icon' => '<i class="uk-icon-refresh"></i>',
					'text' => Text::get('sys_update')
				),
				array(
					'icon' => '<i class="uk-icon-flag"></i>',
					'text' => Text::get('sys_language')
				),
				array(
					'icon' => '<span class="am-icon-headless"></span>',
					'text' => Text::get('sys_headless')
				),
				array(
					'icon' => '<i class="uk-icon-bug"></i>',
					'text' => Text::get('sys_debug')
				)
			),
			array(
				'<a href="#am-edit-config-modal" data-uk-modal>' .
					'<i class="uk-icon-file-text-o uk-icon-justify"></i>&nbsp;&nbsp;' .
					Text::get('sys_config') .
				'</a>'
			)
		);
	}
}
