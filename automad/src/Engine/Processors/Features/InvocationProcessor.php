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
use Automad\Core\Parse;
use Automad\Engine\Extension;
use Automad\Engine\Toolbox;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet, Toolbox method or extension method invocation processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class InvocationProcessor extends AbstractFeatureProcessors {
	public function process(array $matches, string $directory) {
		if (!empty($matches['call'])) {
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
			$snippet = $this->SnippetCollection->get($call);

			// Call snippet or method in order of priority: Snippets, Toolbox methods and extensions.
			if ($snippet) {
				// Process a registered snippet.
				Debug::log($call, 'Process registered snippet');
				$TemplateProcessor = $this->initTemplateProcessor();

				return $TemplateProcessor->process($snippet, $directory);
			} elseif (method_exists($Toolbox, $call)) {
				// Call a toolbox method, in case there is no matching snippet.
				Debug::log($options, 'Calling method ' . $call . ' and passing the following options');

				return $Toolbox->$call($options);
			} else {
				// Try an extension, if no snippet or toolbox method was found.
				Debug::log($call . ' is not a snippet or core method. Will look for a matching extension ...');
				$Extension = new Extension($call, $options, $this->Automad);
				$this->AssetCollection->merge($Extension->getAssets());

				return $Extension->getOutput();
			}
		}
	}

	public static function syntaxPattern() {
		$statementOpen = preg_quote(AM_DEL_STATEMENT_OPEN);
		$statementClose = preg_quote(AM_DEL_STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				'(?P<call>[\w\/\-]+)\s*(?P<callOptions>\{.*?\})?' .
				'\s*' . $statementClose;
	}
}
