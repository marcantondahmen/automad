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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;
use Automad\System\Mail as SystemMail;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Mail extends AbstractBlock {
	/**
	 * Render a mail form block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		if (empty($data->to)) {
			return '';
		}

		$defaults = array(
			'error' => '',
			'success' => '',
			'placeholderEmail' => '',
			'placeholderSubject' => '',
			'placeholderMessage' => '',
			'textButton' => ''
		);

		$options = array_merge($defaults, (array) $data);
		$data = (object) $options;

		$status = SystemMail::send($data, $Automad);

		if ($status) {
			$status = "<h3>$status</h3>";
		}

		$class = self::classAttr();

		return <<< HTML
			<am-mail $class>
				$status
				<form action="" method="post">	
					<input type="text" name="human" value="">	
					<input class="am-input" type="text" name="from" value="" placeholder="$data->placeholderEmail">
					<input class="am-input" type="text" name="subject" value="" placeholder="$data->placeholderSubject">
					<textarea class="am-input" name="message" placeholder="$data->placeholderMessage"></textarea>
					<button class="am-button" type="submit">$data->textButton</button>	
				</form>
			</am-mail>
		HTML;
	}
}
