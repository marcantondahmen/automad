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

use Automad\Blocks\Schema\AgentFieldSchema;
use Automad\Blocks\Utils\Attr;
use Automad\Blocks\Utils\EmbedResolver;
use Automad\Core\Automad;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The embed block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Embed extends AbstractBlock {
	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return 'A wrapper for 3rd-party embedded content such as Twitter posts, YouTube video, or SoundCloud tracks. The following platforms are supported: ' .
			'CodePen, Dailymotion, Facebook, Giphy, Imgur, Instagram, Mixcloud, Soundcloud, Twitter / X, Vimeo, YouTube';
	}

	/**
	 * Render a embed block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$data = $block['data'];
		$html = EmbedResolver::getHtml($data['source'] ?? '');

		if (empty($html)) {
			return '';
		}

		if (AM_CONSENT_CHECK_ENABLED) {
			// Only replace either iframe or script tag. Not both since for example the GitHub provider
			// HTML contains an iframe with a nested script tag that should not be processed.
			if (str_contains($html, '<iframe')) {
				$html = str_replace(array('<iframe', '</iframe'), array('<am-consent type="iframe"', '</am-consent'), $html);
			} else {
				$html = str_replace(array('<script', '</script'), array('<am-consent type="script"', '</am-consent'), $html);
			}
		}

		if (!empty($data['caption'])) {
			$html .= "<figcaption>{$data['caption']}</figcaption>";
		}

		$attr = Attr::render($block['tunes']);

		return "<am-embed $attr><figure>$html</figure></am-embed>";
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
			array('caption'),
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
		return $block['data']['caption'] ?? '';
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'source' => new AgentFieldSchema(
				'string',
				'The 3rd-party embed provider source URL.',
				enum: EmbedResolver::getServiceKeys()
			),
			'caption' => new AgentFieldSchema(
				'string',
				'The caption that is desplayed below the embedded element',
				true
			)
		);
	}

	/**
	 * Defines whether a block can be stretched.
	 *
	 * @return bool
	 */
	protected static function isStretchable(): bool {
		return true;
	}
}
