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
 * Copyright (c) 2020-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\API\Response;
use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Core\Text;
use Automad\System\Mail as SystemMail;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
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
		$id = $block['id'];
		$data = $block['data'];
		$honeypot = 'nickname';

		if (empty($data['to'])) {
			return '';
		}

		$defaults = array(
			'error' => Text::get('mailBlockDefaultError'),
			'errorAddress' => Text::get('mailBlockDefaultErrorAddress'),
			'errorBody' => Text::get('mailBlockDefaultErrorBody'),
			'errorSubject' => Text::get('mailBlockDefaultErrorSubject'),
			'labelAddress' => Text::get('mailBlockDefaultLabelAddress'),
			'labelBody' => Text::get('mailBlockDefaultLabelBody'),
			'labelSend' => Text::get('mailBlockDefaultLabelSend'),
			'labelSubject' => Text::get('mailBlockDefaultLabelSubject'),
			'success' => Text::get('mailBlockDefaultSuccess')
		);

		$data = array_merge($defaults, array_filter($data));
		$status = false;

		if (!empty($_POST) && $_POST['id'] == $id) {
			$status = SystemMail::sendForm($data, $Automad);
		}

		if (is_string($status) && !empty($status)) {
			header('Content-Type: application/json; charset=utf-8');
			$Response = new Response();

			exit($Response->setData(array('status' => $status))->json());
		}

		$attr = Attr::render($block['tunes'], array('am-form'));

		$idAddress = 'id_' . bin2hex(random_bytes(16));
		$idSubject = 'id_' . bin2hex(random_bytes(16));
		$idBody = 'id_' . bin2hex(random_bytes(16));

		return <<< HTML
			<am-mail $attr id="$id">
				<div class="am-field">
					<label for="$idAddress" class="am-label">{$data['labelAddress']}</label>
					<input 
						id="$idAddress"
						class="am-input" 
						type="email" 
						name="from" 
						value="" 
						required
					>
					<span class="am-error">{$data['errorAddress']}</span>
				</div>
				<input type="text" name="$honeypot" value="">	
				<div class="am-field">
					<label for="$idSubject" class="am-label">{$data['labelSubject']}</label>
					<input 
						id="$idSubject"
						class="am-input" 
						type="text" 
						name="subject" 
						value="" 
						required
					>
					<span class="am-error">{$data['errorSubject']}</span>
				</div>
				<div class="am-field">
					<label for="$idBody" class="am-label">{$data['labelBody']}</label>
					<textarea 
						id="$idBody"
						class="am-input" 
						name="message" 
						rows="8"
						required
					></textarea>
					<span class="am-error">{$data['errorBody']}</span>
				</div>
				<button class="am-button">{$data['labelSend']}</button>	
			</am-mail>
		HTML;
	}
}
