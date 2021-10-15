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
use Automad\Engine\Collections\SnippetCollection;
use Automad\Engine\PatternAssembly;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet definition processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SnippetDefinitionProcessor extends AbstractFeatureProcessor {
	/**
	 * Process snippet definitions and add them to the snippet collection.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @param bool $collectSnippetDefinitions
	 */
	public function process(array $matches, string $directory, bool $collectSnippetDefinitions) {
		if (empty($matches['snippet'])) {
			return null;
		}

		$name = $matches['snippet'];
		$body = $matches['snippetSnippet'];

		// Always add and also override snippet definition while collecting snippets in order to enable inheritance.
		if ($collectSnippetDefinitions) {
			SnippetCollection::add($name, $body);
			Debug::log(SnippetCollection::getCollection(), 'Registered snippet "' . $name . '"');

			return null;
		}

		$collection = SnippetCollection::getCollection();

		// In case a snippet definition doesn't exist in the collection yet, add it also
		// during render time in order to catch also snippets that are nested in other snippets that
		// are overriding even other snippets.
		if (empty($collection[$name])) {
			SnippetCollection::add($name, $body);
			Debug::log(SnippetCollection::getCollection(), 'Registered snippet "' . $name . '"');
		}
	}

	/**
	 * The pattern that is used to match snippet definitions.
	 *
	 * @return string the snippet definition pattern
	 */
	public static function syntaxPattern() {
		$statementOpen = preg_quote(AM_DEL_STATEMENT_OPEN);
		$statementClose = preg_quote(AM_DEL_STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				PatternAssembly::$outerStatementMarker . '\s*' .
				'snippet\s+(?P<snippet>[\w\-]+)' .
				'\s*' . $statementClose .
				'(?P<snippetSnippet>.*?)' .
				$statementOpen . PatternAssembly::$outerStatementMarker . '\s*end' .
				'\s*' . $statementClose;
	}
}
