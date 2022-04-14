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

namespace Automad\Engine\Processors\Features;

use Automad\Core\Debug;
use Automad\Core\FileUtils;
use Automad\Core\Parse;
use Automad\Core\Selection;
use Automad\Engine\PatternAssembly;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The context change processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ContextProcessor extends AbstractFeatureProcessor {
	/**
	 * Process `with` and `with ... else` statements.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @param bool $collectSnippetDefinitions
	 * @return sting the processed template string
	 */
	public function process(array $matches, string $directory, bool $collectSnippetDefinitions) {
		if (!empty($matches['with'])) {
			$Context = $this->Automad->Context;
			$url = $this->ContentProcessor->processVariables(trim($matches['with'], '\'"'));
			$TemplateProcessor = $this->initTemplateProcessor();

			// Previous or next page. Use lowercase matches to be case insensitive.
			if (strtolower($matches['with']) == 'prev' || strtolower($matches['with']) == 'next') {
				// Cache the current pagelist config and temporary disable the excludeHidden parameter to also
				// get the neighbors of a hidden page.
				$pagelistConfigShelf = $this->Automad->getPagelist()->config();
				$this->Automad->getPagelist()->config(array('excludeHidden' => false));

				$Selection = new Selection($this->Automad->getPagelist()->getPages(true));
				$Selection->filterPrevAndNextToUrl($Context->get()->url);
				$pages = $Selection->getSelection();

				// Restore the original pagelist config.
				$this->Automad->getPagelist()->config($pagelistConfigShelf);

				if (array_key_exists(strtolower($matches['with']), $pages)) {
					$Page = $pages[strtolower($matches['with'])];
				}
			}

			// Any existing page.
			// To avoid overriding $Page (next/prev), it has to be tested explicitly whether
			// the URL actually exists.
			if (array_key_exists($url, $this->Automad->getCollection())) {
				$Page = $this->Automad->getPage($url);
			}

			// Process snippet for $Page.
			if (!empty($Page)) {
				Debug::log($Page->url, 'With page');

				// Save original context and pagelist.
				$contextShelf = $Context->get();
				$pagelistConfigShelf = $this->Automad->getPagelist()->config();

				// Set context to $url.
				$Context->set($Page);

				// Parse snippet.
				$html = $TemplateProcessor->process($matches['withSnippet'], $directory, $collectSnippetDefinitions);

				// Restore original context and pagelist.
				$Context->set($contextShelf);
				$this->Automad->getPagelist()->config($pagelistConfigShelf);

				return $html;
			}

			// If no matching page exists, check for a file.
			$files = FileUtils::fileDeclaration($url, $Context->get(), true);

			if (!empty($files)) {
				$file = $files[0];
				Debug::log($file, 'With file');

				return $this->ContentProcessor->processFileSnippet(
					$file,
					Parse::jsonOptions(
						$this->ContentProcessor->processVariables(
							$matches['withOptions'],
							true
						)
					),
					$matches['withSnippet'],
					$directory,
					$collectSnippetDefinitions
				);
			}

			// In case $url is not a page and also not a file (no 'return' was called before), process the 'withElseSnippet'.
			Debug::log($url, 'With: No matching page or file found for');

			if (!empty($matches['withElseSnippet'])) {
				return $TemplateProcessor->process($matches['withElseSnippet'], $directory, $collectSnippetDefinitions);
			}
		}
	}

	/**
	 * The pattern that is used to match context change statements in a template string.
	 *
	 * @return string the regex pattern for context change statements
	 */
	public static function syntaxPattern() {
		$statementOpen = preg_quote(AM_DEL_STATEMENT_OPEN);
		$statementClose = preg_quote(AM_DEL_STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				PatternAssembly::$outerStatementMarker . '\s*' .
				'with\s+(?P<with>' .
					'"[^"]*"|' . "'[^']*'|" . PatternAssembly::variable() . '|prev|next' .
				')' .
				'\s*(?P<withOptions>\{.*?\})?' .
				'\s*' . $statementClose .
				'(?P<withSnippet>.*?)' .
				'(?:' . $statementOpen . PatternAssembly::$outerStatementMarker .
					'\s*else\s*' .
				$statementClose . '(?P<withElseSnippet>.*?)' . ')?' .
				$statementOpen . PatternAssembly::$outerStatementMarker . '\s*end' .
				'\s*' . $statementClose;
	}
}
