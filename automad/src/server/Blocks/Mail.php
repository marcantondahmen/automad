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
 * Copyright (c) 2020-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Blocks;

use Automad\Api\Response;
use Automad\Blocks\Schema\AgentFieldSchema;
use Automad\Blocks\Utils\Attr;
use Automad\Core\Automad;
use Automad\Core\Text;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;
use Automad\System\Mail as SystemMail;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Mail extends AbstractBlock {
	const FIELDS = array(
		'to',
		'labelAddress',
		'errorAddress',
		'labelSubject',
		'errorSubject',
		'labelBody',
		'errorBody',
		'labelSend',
		'error',
		'success'
	);

	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return <<< TXT
			A contact form that lets visitors send an email with their address, a subject and a
			message to the given recipient address. Use it on contact pages. Labels and messages fall
			back to sensible defaults.
			TXT;
	}

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

	/**
	 * Search and replace inside block data.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @param string $searchRegex
	 * @param string $replace
	 * @param bool $replaceInPublishedComponent
	 * @return BlockData
	 */
	public static function replace(
		array $block,
		ComponentCollection $ComponentCollection,
		string $searchRegex,
		string $replace,
		bool $replaceInPublishedComponent
	): array {
		$block['data'] = Replacement::replaceInBlockFields(
			$block['data'],
			self::FIELDS,
			$searchRegex,
			$replace
		);

		return $block;
	}

	/**
	 * Return a searchable string representation of a block.
	 *
	 * @param BlockData $block
	 * @param ComponentCollection $ComponentCollection
	 * @return string
	 */
	public static function toString(array $block, ComponentCollection $ComponentCollection): string {
		return join(' ', array_map(fn (string $field): string => $block['data'][$field] ?? '', self::FIELDS));
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'labelSend' => new AgentFieldSchema(
				'string',
				<<< TXT
					The label of the submit button. Skip it in order to use the default label.
					TXT,
				true
			),
			'to' => new AgentFieldSchema(
				'string',
				<<< TXT
					The email address of the recipient that receives the form submissions, usually the address
					of the site owner.
					TXT
			),
		);
	}
}
