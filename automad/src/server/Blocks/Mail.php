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
 * Copyright (c) 2020-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Core\Text;
use Automad\System\Mail as SystemMail;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-import-type BlockData from AbstractBlock
 */
class Mail extends AbstractBlock {
	/**
	 * Render a mail form block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];

		if (empty($data['to'])) {
			return '';
		}

		$defaults = array(
			'error' => Text::get('mailBlockDefaultError'),
			'success' => Text::get('mailBlockDefaultSuccess'),
			'labelAddress' => Text::get('mailBlockDefaultLabelAddress'),
			'labelSubject' => Text::get('mailBlockDefaultLabelSubject'),
			'labelBody' => Text::get('mailBlockDefaultLabelBody'),
			'labelSend' => Text::get('mailBlockDefaultLabelSend')
		);

		$data = array_merge($defaults, array_filter($data));

		$status = SystemMail::sendForm($data, $Automad);

		if ($status) {
			$status = "<h3>$status</h3>";
		}

		$attr = Attr::render($block['tunes']);

		return <<< HTML
			<am-mail $attr>
				$status
				<form action="" method="post">	
					<input type="text" name="human" value="">	
					<input class="am-input" type="text" name="from" value="" placeholder="{$data['labelAddress']}">
					<input class="am-input" type="text" name="subject" value="" placeholder="{$data['labelSubject']}">
					<textarea class="am-input" name="message" placeholder="{$data['labelBody']}"></textarea>
					<button class="am-button" type="submit">{$data['labelSend']}</button>	
				</form>
			</am-mail>
		HTML;
	}
}
