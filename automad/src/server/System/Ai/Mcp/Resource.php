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

namespace Automad\System\Ai\Mcp;

use Mcp\Schema\Annotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The interface for MCP resources. Implementing classes are discovered automatically by
 * Automad\System\Ai\Mcp\Provider from the Automad\System\Ai\Mcp\Resources namespace and
 * registered with the MCP server.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
interface Resource {
	/**
	 * The resource's annotations, hinting at its intended audience, priority and last modification.
	 *
	 * @return Annotations|null
	 */
	public function getAnnotations(): Annotations|null;

	/**
	 * The resource's description.
	 *
	 * @return string|null
	 */
	public function getDescription(): string|null;

	/**
	 * The resource's main handler, returning its content when read.
	 *
	 * @return callable
	 */
	public function getHandler(): callable;

	/**
	 * The resource's MIME type.
	 *
	 * @return string|null
	 */
	public function getMimeType(): string|null;

	/**
	 * The resource's name.
	 *
	 * @return string|null
	 */
	public function getName(): string|null;

	/**
	 * The resource's human-readable title.
	 *
	 * @return string|null
	 */
	public function getTitle(): string|null;
}
