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
use Automad\Core\Automad;
use Automad\Models\ComponentCollection;
use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The code block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-import-type AgentSchema from AbstractBlock
 * @psalm-import-type BlockData from AbstractBlock
 */
class Code extends AbstractBlock {
	/**
	 * The block description.
	 *
	 * @return string
	 */
	public static function getDescription(): string {
		return <<< TXT
			A preformatted code snippet with syntax highlighting and optional line numbers. Use it to
			display source code, shell commands, configuration files or installation instructions to
			visitors. The code is only displayed and never executed. Use the "raw" block to output
			HTML or the "snippet" block to execute Automad template code instead.
			TXT;
	}

	/**
	 * Render a code block.
	 *
	 * @param BlockData $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		$code = htmlspecialchars($block['data']['code']);
		$lang = 'language-' . ($block['data']['language'] ?? '');
		$lines = ($block['data']['lineNumbers'] ?? false) ? ' class="line-numbers"' : '';
		$attr = Attr::render($block['tunes']);

		return <<< HTML
			<div $attr>
				<pre $lines><code class="$lang" data-prismjs-copy="Copy">$code</code></pre>
			</div>
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
			array('code'),
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
		return $block['data']['code'] ?? '';
	}

	/**
	 * The collection of data fields that are passed on too the schema.
	 *
	 * @return AgentSchema
	 */
	protected static function agentDataSchema(): array {
		return array(
			'code' => new AgentFieldSchema(
				'string',
				<<< TXT
					The code that is displayed as plain text with its line breaks and indentation preserved.
					Do not escape HTML characters, the code is escaped automatically when rendered.
					TXT
			),
			'language' => new AgentFieldSchema(
				'string',
				<<< TXT
					The language that is used for syntax highlighting. Use "none" for plain text without
					highlighting.
					TXT,
				true,
				array(
					'apacheconf',
					'automad',
					'bash',
					'basic',
					'c',
					'clike',
					'csharp',
					'cpp',
					'css',
					'go',
					'graphql',
					'handlebars',
					'html',
					'java',
					'javascript',
					'jsx',
					'latex',
					'less',
					'lua',
					'markdown',
					'nginx',
					'none',
					'php',
					'powershell',
					'python',
					'ruby',
					'rust',
					'sass',
					'sql',
					'tsx',
					'typescript',
					'vim',
					'yaml',
				)
			),
			'lineNumbers' => new AgentFieldSchema(
				'boolean',
				<<< TXT
					If true, line numbers are displayed next to the code. This is useful for longer snippets.
					TXT,
				true
			)
		);
	}
}
