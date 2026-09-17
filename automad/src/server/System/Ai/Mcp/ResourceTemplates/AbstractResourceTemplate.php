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

namespace Automad\System\Ai\Mcp\ResourceTemplates;

use Mcp\Schema\Annotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract class for MCP resource templates. Derived classes are discovered automatically by
 * Automad\System\Ai\Mcp\Provider from the Automad\System\Ai\Mcp\ResourceTemplates namespace and
 * registered with the MCP server.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
abstract class AbstractResourceTemplate {
	/**
	 * The template's annotations, hinting at its intended audience, priority and last modification.
	 *
	 * @return Annotations|null
	 */
	public function getAnnotations(): Annotations|null {
		return null;
	}

	/**
	 * The template's description.
	 *
	 * @return string
	 */
	abstract public function getDescription(): string|null;

	/**
	 * The template's main handler, returning its content when read.
	 *
	 * @return callable
	 */
	abstract public function getHandler(): callable;

	/**
	 * The template's MIME type.
	 *
	 * @return string
	 */
	public function getMimeType(): string {
		return 'application/json';
	}

	/**
	 * The template's name.
	 *
	 * @return string
	 */
	abstract public function getName(): string;

	/**
	 * The template's human-readable title.
	 *
	 * @return string
	 */
	abstract public function getTitle(): string;

	/**
	 * The template's uri.
	 *
	 * @return string
	 */
	abstract public function getUriTemplate(): string;
}
