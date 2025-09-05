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
use Automad\Core\Parse;
use Automad\Engine\Collections\AssetCollection;
use Automad\Engine\Collections\SnippetCollection;
use Automad\Engine\CustomFunction;
use Automad\Engine\Delimiters;
use Automad\Engine\Extension;
use Automad\Engine\Toolbox;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet, Toolbox method, function or extension method invocation processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class InvocationProcessor extends AbstractFeatureProcessor {
	/**
	 * Process invocations of Toolbox methods, snippets or extensions.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @return string the processed template string
	 */
	public function process(array $matches, string $directory): string {
		if (empty($matches['call'])) {
			return '';
		}

		$call = $matches['call'];
		Debug::log($call, 'Matched call');

		// Check if options exist.
		if (isset($matches['callOptions'])) {
			// Parse the options JSON and also find and replace included variables within the JSON string.
			$options = Parse::jsonOptions(
				$this->ContentProcessor->processVariables($matches['callOptions'], true)
			);
		} else {
			$options = array();
		}

		$Toolbox = new Toolbox($this->Automad);

		// Call snippet or method in order of priority: Snippets, Toolbox methods, custom functions and extensions.
		if ($Snippet = SnippetCollection::get($call)) {
			// Process a registered snippet.
			Debug::log($call, 'Process registered snippet');
			$TemplateProcessor = $this->initTemplateProcessor();

			return $TemplateProcessor->process($Snippet->body, $Snippet->path);
		}

		if (method_exists($Toolbox, $call)) {
			// Call a toolbox method, in case there is no matching snippet.
			Debug::log($options, 'Calling method ' . $call . ' and passing the following options');

			return $Toolbox->$call($options) ?? '';
		}

		if (CustomFunction::exists($call)) {
			// Call a custom function.
			Debug::log($options, 'Calling custom function ' . $call);

			return CustomFunction::call($call, $options, $this->Automad);
		}

		// Try an extension, if no snippet or toolbox method was found.
		Debug::log($call . ' is not a snippet core method or custom function. Will look for a matching extension ...');
		$Extension = new Extension($call, $options, $this->Automad);
		AssetCollection::merge($Extension->getAssets());

		return $Extension->getOutput();
	}

	/**
	 * The pattern that is used to match invocations.
	 *
	 * @return string the invocation regex pattern
	 */
	public static function syntaxPattern(): string {
		$statementOpen = preg_quote(Delimiters::STATEMENT_OPEN);
		$statementClose = preg_quote(Delimiters::STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				'(?P<call>[\w\/\-]+)\s*(?P<callOptions>\{.*?\})?' .
				'\s*' . $statementClose;
	}
}
