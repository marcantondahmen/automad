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

namespace Automad\System\Ai\Mcp\Resources;

use Automad\Core\Str;
use Mcp\Schema\Annotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract class for MCP resources. Derived classes are discovered automatically by
 * Automad\System\Ai\Mcp\Provider from the Automad\System\Ai\Mcp\Resources namespace and
 * registered with the MCP server.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
abstract class AbstractResource {
	/**
	 * The resource's annotations, hinting at its intended audience, priority and last modification.
	 *
	 * @return Annotations|null
	 */
	public function getAnnotations(): Annotations|null {
		return null;
	}

	/**
	 * The resource's description.
	 *
	 * @return string
	 */
	abstract public function getDescription(): string|null;

	/**
	 * The resource's main handler, returning its content when read.
	 *
	 * @return callable
	 */
	abstract public function getHandler(): callable;

	/**
	 * The resource's MIME type.
	 *
	 * @return string
	 */
	public function getMimeType(): string {
		return 'application/json';
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return Str::sanitize($this->getTitle());
	}

	/**
	 * The resource's human-readable title.
	 *
	 * @return string
	 */
	abstract public function getTitle(): string;
}
