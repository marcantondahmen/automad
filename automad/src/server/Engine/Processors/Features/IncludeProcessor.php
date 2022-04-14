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
use Automad\Engine\Processors\TemplateProcessor;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The include processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class IncludeProcessor extends AbstractFeatureProcessor {
	/**
	 * Process include statements.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @param bool $collectSnippetDefinitions
	 * @return string the processed string
	 */
	public function process(array $matches, string $directory, bool $collectSnippetDefinitions) {
		if (!empty($matches['file'])) {
			Debug::log($matches['file'], 'Matched include');
			$file = $directory . '/' . $matches['file'];

			if (file_exists($file)) {
				Debug::log($file, 'Including');
				$TemplateProcessor = $this->initTemplateProcessor();

				return $TemplateProcessor->process($this->Automad->loadTemplate($file), dirname($file), $collectSnippetDefinitions);
			} else {
				Debug::log($file, 'File not found');
			}
		}
	}

	/**
	 * The pattern that is used to match include statements.
	 *
	 * @return string the regex pattern that matches include statements
	 */
	public static function syntaxPattern() {
		$statementOpen = preg_quote(AM_DEL_STATEMENT_OPEN);
		$statementClose = preg_quote(AM_DEL_STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				'(?P<file>[\w\/\-\.]+\.[a-z0-9]{2,5})' .
				'\s*' . $statementClose;
	}
}
