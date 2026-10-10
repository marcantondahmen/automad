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

namespace Automad\Blocks\Schema;

use JsonSerializable;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The field schema that is used by agent.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-type FieldType = 'array'|'boolean'|'number'|'object'|'string';
 */
class AgentFieldSchema implements JsonSerializable {
	/**
	 * The field description.
	 */
	public string $description;

	/**
	 * The list of allowed values.
	 */
	public array $enum;

	/**
	 * Whether the field contains blocks.
	 */
	public bool $hasBlocks;

	/**
	 * The list of allowed items.
	 */
	public array $items;

	/**
	 * Whether the field is optional or not.
	 */
	public bool $optional;

	/**
	 * The field type.
	 *
	 * @var FieldType
	 */
	public string $type;

	/**
	 * The contructor.
	 *
	 * @param FieldType $type
	 * @param string $description
	 * @param bool $optional
	 * @param array $enum
	 * @param array $items
	 * @param bool $hasBlocks
	 */
	public function __construct(
		string $type,
		string $description,
		bool $optional = false,
		array $enum = array(),
		array $items = array(),
		bool $hasBlocks = false
	) {
		$this->type = $type;
		$this->description = $description;
		$this->optional = $optional;
		$this->enum = $enum;
		$this->items = $items;
		$this->hasBlocks = $hasBlocks;
	}

	/**
	 * Return a default value based on its type.
	 *
	 * @return mixed
	 */
	public function getDefault(): mixed {
		if ($this->type === 'object' || $this->type === 'array') {
			return array();
		}

		if ($this->type === 'boolean') {
			return false;
		}

		if ($this->type === 'number') {
			return 0;
		}

		return '';
	}

	/**
	 * Get the filtered and optimized schema.
	 */
	public function jsonSerialize(): array {
		return array_intersect_key(
			array_filter(get_object_vars($this), fn ($item) => !empty($item)),
			array_flip(array('type', 'description', 'enum', 'items')),
		);
	}
}
