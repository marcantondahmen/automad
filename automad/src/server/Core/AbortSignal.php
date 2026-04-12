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

namespace Automad\Core;

use RuntimeException;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The AbortException class is used to immediatly stop execution in a safe way.
 * It can be used in order to end a request based on application logic in
 * contrast to a server error. Therefore this exception will trigger
 * a custom exception handler without returning a back trace.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AbortSignal extends RuntimeException {
	/**
	 * The body for more details that can be used when printing error pages.
	 */
	private string $details;

	/**
	 * The constructor.
	 *
	 * @param string $message
	 * @param string $details
	 * @param int $code
	 */
	public function __construct(
		string $message = '',
		string $details = '',
		int $code = 400
	) {
		parent::__construct($message, $code);
		$this->details = $details;
	}

	/**
	 * The details getter.
	 *
	 * @return string
	 */
	public function getDetails(): string {
		return $this->details;
	}
}
