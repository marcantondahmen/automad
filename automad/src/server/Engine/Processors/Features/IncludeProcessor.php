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
use Automad\Engine\Delimiters;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The include processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class IncludeProcessor extends AbstractFeatureProcessor {
	/**
	 * Process include statements.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @return string the processed string
	 */
	public function process(array $matches, string $directory): string {
		if (empty($matches['file'])) {
			return '';
		}

		Debug::log($matches['file'], 'Matched include');
		$file = $directory . '/' . $matches['file'];

		if (file_exists($file)) {
			Debug::log($file, 'Including');
			$TemplateProcessor = $this->initTemplateProcessor();

			return $TemplateProcessor->process($this->Automad->loadTemplate($file), dirname($file));
		}

		Debug::log($file, 'File not found');

		return '';
	}

	/**
	 * The pattern that is used to match include statements.
	 *
	 * @return string the regex pattern that matches include statements
	 */
	public static function syntaxPattern(): string {
		$statementOpen = preg_quote(Delimiters::STATEMENT_OPEN);
		$statementClose = preg_quote(Delimiters::STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				'(?P<file>[\w\/\-\.]+\.[a-z0-9]{2,5})' .
				'\s*' . $statementClose;
	}
}
