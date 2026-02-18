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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Test;

use Automad\Models\Search\Replacement;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The block test helper class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Block {
	/**
	 * A test wrapper for block testing.
	 *
	 * @param string $blockClass
	 * @param string $search
	 * @param string $replace
	 * @param bool $isRegex
	 * @param bool $isCaseInsensitive
	 * @param string $blockJson
	 * @param string $expectedReplacedJson
	 * @param string $expectedString
	 * @param mixed $Test
	 */
	public static function test(
		mixed $Test,
		string $blockClass,
		string $search,
		string $replace,
		bool $isRegex,
		bool $isCaseInsensitive,
		string $blockJson,
		string $expectedReplacedJson,
		string $expectedString
	) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();
		$block = json_decode($blockJson, true);
		$expectedReplaced = json_decode($expectedReplacedJson, true);
		$flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
		$class = "\\Automad\\Blocks\\$blockClass";

		/** @disregard */
		$Test->assertSame(
			$expectedString,
			$class::toString(
				$block,
				$Automad->ComponentCollection
			)
		);

		/** @disregard */
		$Test->assertSame(
			json_encode($expectedReplaced, $flags),
			json_encode(
				$class::replace(
					$block,
					$Automad->ComponentCollection,
					Replacement::buildRegex($search, $isRegex, $isCaseInsensitive),
					$replace,
					false
				),
				$flags
			)
		);
	}
}
