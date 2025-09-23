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

use Automad\Blocks\Snippet;
use Automad\Core\Debug;
use Automad\Engine\Collections\SnippetCollection;
use Automad\Engine\Delimiters;
use Automad\Engine\Processors\TemplateProcessor;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet definition processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SnippetDefinitionProcessor extends AbstractFeatureProcessor {
	/**
	 * Process snippet definitions and add them to the snippet collection.
	 *
	 * @param array $matches
	 * @param string $directory
	 */
	public function process(array $matches, string $directory): string {
		if (empty($matches['snippet'])) {
			return '';
		}

		$name = $matches['snippet'];
		$body = $matches['snippetSnippet'];
		$collection = SnippetCollection::getCollection();

		// It is very important to differentiate between the first time a template is processed (pre-processed),
		// with TemplateProcessor::$isPreProcessing == true, and the final one where the actual output is generated.
		// The first run only collects definitions and also allows for overriding them based on other definitions
		// using the same name and that occur after their actual evaluation in a template.
		if (TemplateProcessor::$isPreProcessing || Snippet::$snippetIsRendering) {
			SnippetCollection::add($name, $body, $directory);
			Debug::log(SnippetCollection::getCollection(), 'Registered snippet "' . $name . '"');

			return '';
		}

		// Now the point is that in the second run, when TemplateProcessor::$isPreProcessing == false,
		// definitions that are already part of the collection must (!) not be overriden because
		// that would revert the overrides that have been collected in the first run back to the
		// original version right before they are evaluated. However, since the overrides have not been evaluated
		// yet, snippets that are defined within another overriding snippet also must be added to the collection
		// before the engine is able the evaluate them. Therefore in any case undefined (!) snippet entries always
		// have to be registered.
		if (empty($collection[$name])) {
			SnippetCollection::add($name, $body, $directory);
			Debug::log(SnippetCollection::getCollection(), 'Registered snippet "' . $name . '"');
		}

		return '';
	}

	/**
	 * The pattern that is used to match snippet definitions.
	 *
	 * @return string the snippet definition pattern
	 */
	public static function syntaxPattern(): string {
		$statementOpen = preg_quote(Delimiters::STATEMENT_OPEN);
		$statementClose = preg_quote(Delimiters::STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				Delimiters::OUTER_STATEMENT_MARKER . '\s*' .
				'snippet\s+(?P<snippet>[\w\-]+)' .
				'\s*' . $statementClose .
				'(?P<snippetSnippet>.*?)' .
				$statementOpen . Delimiters::OUTER_STATEMENT_MARKER . '\s*end' .
				'\s*' . $statementClose;
	}
}
