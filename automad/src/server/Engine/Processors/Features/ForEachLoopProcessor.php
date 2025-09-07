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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors\Features;

use Automad\Core\Debug;
use Automad\Core\FileUtils;
use Automad\Core\Parse;
use Automad\Engine\Delimiters;
use Automad\Engine\PatternAssembly;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The foreach loop processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ForEachLoopProcessor extends AbstractFeatureProcessor {
	/**
	 * Process `foreach` and `foreach ... else` loops.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @return string the processed string
	 */
	public function process(array $matches, string $directory): string {
		if (empty($matches['foreach'])) {
			return '';
		}
		$Context = $this->Automad->Context;
		$TemplateProcessor = $this->initTemplateProcessor();

		$foreachSnippet = $matches['foreachSnippet'];
		$foreachElseSnippet = '';

		if (!empty($matches['foreachElseSnippet'])) {
			$foreachElseSnippet = $matches['foreachElseSnippet'];
		}

		$html = '';
		$i = 0;

		// Shelve the runtime objetc before any loop.
		// The index will be overwritten when iterating over filter, tags and files and must be restored after the loop.
		$runtimeShelf = $this->Automad->Runtime->shelve();

		if (strtolower($matches['foreach']) == 'pagelist') {
			// Pagelist

			// Get pages.
			$pages = $this->Automad->getPagelist()->getPages();
			Debug::log($pages, 'Foreach in pagelist loop');

			// Shelve context page and pagelist config.
			$contextShelf = $Context->get();
			$pagelistConfigShelf = $this->Automad->getPagelist()->config();

			// Calculate offset for index.
			if ($pagelistPage = intval($pagelistConfigShelf['page'])) {
				$offset = ($pagelistPage - 1) * intval($pagelistConfigShelf['limit']);
			} else {
				$offset = intval($pagelistConfigShelf['offset']);
			}

			foreach ($pages as $Page) {
				// Set context to the current page in the loop.
				$Context->set($Page);
				// Set index for current page. The index can be used as @{:i}.
				$this->Automad->Runtime->set(Fields::LOOP_INDEX, ++$i + $offset);
				// Parse snippet.
				Debug::log($Page, 'Processing snippet in loop for page: "' . $Page->url . '"');
				$html .= $TemplateProcessor->process($foreachSnippet, $directory);
				// Note that the config only has to be shelved once before starting the loop,
				// but has to be restored after each snippet to provide the correct data (like :pagelistCount)
				// for the next iteration, since a changed config would generate incorrect values in
				// recursive loops.
				$this->Automad->getPagelist()->config($pagelistConfigShelf);
			}

			// Restore context.
			$Context->set($contextShelf);
		} elseif (strtolower($matches['foreach']) == 'filters') {
			// Filters (tags of the pages in the pagelist)
			// Each filter can be used as @{:filter} within a snippet.

			foreach ($this->Automad->getPagelist()->getTags() as $filter) {
				Debug::log($filter, 'Processing snippet in loop for filter');
				// Store current filter in the system variable buffer.
				$this->Automad->Runtime->set(Fields::FILTER, $filter);
				// Set index. The index can be used as @{:i}.
				$this->Automad->Runtime->set(Fields::LOOP_INDEX, ++$i);
				$html .= $TemplateProcessor->process($foreachSnippet, $directory);
			}
		} elseif (strtolower($matches['foreach']) == 'tags') {
			// Tags (of the current page)
			// Each tag can be used as @{:tag} within a snippet.
			foreach ($Context->get()->tags as $tag) {
				Debug::log($tag, 'Processing snippet in loop for tag');
				// Store current tag in the system variable buffer.
				$this->Automad->Runtime->set(Fields::TAG, $tag);
				// Set index. The index can be used as @{:i}.
				$this->Automad->Runtime->set(Fields::LOOP_INDEX, ++$i);
				$html .= $TemplateProcessor->process($foreachSnippet, $directory);
			}
		} else {
			// Files
			// The file path and the basename can be used like @{:file} and @{:basename} within a snippet.
			if (strtolower($matches['foreach']) == 'filelist') {
				// Use files from filelist.
				$files = $this->Automad->getFilelist()->getFiles();
			} else {
				// Parse given glob pattern within any kind of quotes or from a variable value.
				$files = FileUtils::fileDeclaration(
					$this->ContentProcessor->processVariables(trim($matches['foreach'], '\'"')),
					$Context->get(),
					true
				);
			}

			foreach ($files as $file) {
				Debug::log($file, 'Processing snippet in loop for file');
				// Set index. The index can be used as @{:i}.
				$this->Automad->Runtime->set(Fields::LOOP_INDEX, ++$i);
				$html .= $this->ContentProcessor->processFileSnippet(
					$file,
					Parse::jsonOptions(
						$this->ContentProcessor->processVariables(
							$matches['foreachOptions'],
							true
						)
					),
					$foreachSnippet,
					$directory,
				);
			}
		}

		// Restore runtime.
		$this->Automad->Runtime->unshelve($runtimeShelf);

		// If the counter ($i) is 0 (false), process the "else" snippet.
		if (!$i) {
			Debug::log('foreach in ' . strtolower($matches['foreach']), 'No elements array. Processing else statement for');
			$html .= $TemplateProcessor->process($foreachElseSnippet, $directory);
		}

		return $html;
	}

	/**
	 * The pattern that is used to match foreach loops.
	 *
	 * @return string the foreach loop pattern
	 */
	public static function syntaxPattern(): string {
		$statementOpen = preg_quote(Delimiters::STATEMENT_OPEN);
		$statementClose = preg_quote(Delimiters::STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				Delimiters::OUTER_STATEMENT_MARKER . '\s*' .
				'foreach\s+in\s+(?P<foreach>' .
					'pagelist|' .
					'filters|' .
					'tags|' .
					'filelist|' .
					'"[^"]*"|' . "'[^']*'|" . PatternAssembly::variable() .
				')' .
				'\s*(?P<foreachOptions>\{.*?\})?' .
				'\s*' . $statementClose .
				'(?P<foreachSnippet>.*?)' .
				'(?:' . $statementOpen . Delimiters::OUTER_STATEMENT_MARKER .
					'\s*else\s*' .
				$statementClose . '(?P<foreachElseSnippet>.*?)' . ')?' .
				$statementOpen . Delimiters::OUTER_STATEMENT_MARKER . '\s*end' .
				'\s*' . $statementClose;
	}
}
