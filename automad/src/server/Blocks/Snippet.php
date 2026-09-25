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
use Automad\Core\Automad;
use Automad\Engine\Processors\TemplateProcessor;
use Automad\Models\ComponentCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Snippet extends AbstractBlock {
	/**
	 * This variable tracks whether a snippet is called by another snippet to prevent inifinte recursive loops.
	 */
	public static bool $snippetIsRendering = false;

	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return <<< TXT
			Renders Automad template language code, either from a snippet file or from inline code.
			Use it to add dynamic, template-driven content that no other block type provides. Read the
			`automad://snippets` resource to find existing snippet files and prefer those over writing
			inline template code.
			TXT;
	}

	/**
	 * Render a snippet block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		// Prevent infinite recursion.
		if (self::$snippetIsRendering) {
			return '';
		}

		self::$snippetIsRendering = true;

		$output = '';
		$data = $block['data'];
		$TemplateProcessor = TemplateProcessor::create($Automad);

		if (!empty($data['snippet'])) {
			$output .= $TemplateProcessor->process($data['snippet'], AM_BASE_DIR . AM_DIR_PACKAGES);
		}

		if (!empty($data['file'])) {
			// Test for files with or without leading slash.
			$file = AM_BASE_DIR . '/' . trim($data['file'], '/');

			if (!is_readable($file)) {
				// Test also path without packages directory.
				$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . trim($data['file'], '/');
			}

			if (is_readable($file)) {
				$template = $Automad->loadTemplate($file);
				$output .= $TemplateProcessor->process($template, dirname($file));
			}
		}

		self::$snippetIsRendering = false;

		return $output;
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
		return '';
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'file' => new AgentFieldSchema(
				'string',
				<<< TXT
					The snippet file that is rendered. Before selecting a snippet, read the available snippets
					from the `automad://snippets` resource and use one of the returned snippet files. Use an
					empty string when defining inline code in "snippet" instead.
					TXT
			),
			'snippet' => new AgentFieldSchema(
				'string',
				<<< TXT
					Inline Automad template language code that can be used as an alternative to a snippet
					file. Use an empty string when a snippet file is selected.
					TXT
			)
		);
	}
}
