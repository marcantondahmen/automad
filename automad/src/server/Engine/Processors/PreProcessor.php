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

namespace Automad\Engine\Processors;

use Automad\Engine\PatternAssembly;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The pre-processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PreProcessor {
	/**
	 * Preprocess recursive statements to identify the top-level (outer) statements within a parsed string.
	 *
	 * @param string $str
	 * @return string The preprocessed string where all outer opening statement delimiters get an additional marker appended.
	 */
	public static function prepareWrappingStatements(string $str) {
		$depth = 0;
		$regex = 	'/(' .
					'(?P<begin>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*(?:if|for|foreach|with|snippet)\s.*?' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
					'(?P<else>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*else\s*' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
					'(?P<end>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*end\s*' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')' .
					')/is';

		return 	preg_replace_callback($regex, function ($match) use (&$depth) {
			// Convert $match to the actually needed string.
			$return = array_unique($match);
			$return = array_filter($return);
			$return = implode($return);

			// Decrease depth in case the match is else or end.
			if (!empty($match['end']) || !empty($match['else'])) {
				$depth--;
			}

			// Append a marker to the opening delimiter in case depth === 0.
			if ($depth === 0) {
				$return = str_replace(AM_DEL_STATEMENT_OPEN, AM_DEL_STATEMENT_OPEN . PatternAssembly::$outerStatementMarker, $return);
			}

			// Increase depth after (!) return was possible modified (in case depth === 0) in case the match is begin or else.
			if (!empty($match['begin']) || !empty($match['else'])) {
				$depth++;
			}

			return $return;
		}, $str);
	}

	/**
	 * Strip whitespace before or after delimiters when using `<@~` or `~@>`.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	public static function stripWhitespace(string $str) {
		$str = preg_replace('/\s*(' . preg_quote(AM_DEL_STATEMENT_OPEN) . ')~/is', '$1', $str);
		$str = preg_replace('/~(' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')\s*/is', '$1', $str);

		return $str;
	}
}
