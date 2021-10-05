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

namespace Automad\UI\Components\Card;

use Automad\Core\Str;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user card component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class User {
	/**
	 * Render a user card.
	 *
	 * @param string $user
	 * @return string the rendered user card
	 */
	public static function render(string $user) {
		$id = 'am-user-' . Str::slug($user);
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<li>
				<div id="$id" class="uk-panel uk-panel-box">
					<div class="uk-margin-small-bottom">
						<i class="uk-icon-user uk-icon-medium"></i>
					</div>
					<div class="uk-margin-small-bottom">
						$user
					</div>
					<div class="am-panel-bottom">
						<div class="am-panel-bottom-right">
							{$fn(self::checkbox($user, $id))}
						</div>
					</div>
				</div>
			</li>
		HTML;
	}

	/**
	 * Render a user selection checkbox.
	 *
	 * @param string $user
	 * @param string $id
	 * @return string the rendered checkbox
	 */
	private static function checkbox(string $user, string $id) {
		$fn = function ($expression) {
			return $expression;
		};

		if ($user == Session::getUsername()) {
			return <<< HTML
				<div class="am-panel-bottom-link uk-text-muted">
					{$fn(Text::get('sys_user_you'))}
				</div>
			HTML;
		}

		return <<< HTML
			<label 
			class="am-toggle-checkbox am-panel-bottom-link" 
			data-am-toggle="#$id">
				<input type="checkbox" name="delete[]" value="$user" />
			</label>
		HTML;
	}
}
