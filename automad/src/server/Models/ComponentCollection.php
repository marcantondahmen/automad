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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\PublicationState;
use Automad\Core\Session;
use Automad\Stores\ComponentStore;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The component collection model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-type Component = array{
 *   id: string,
 *	 name: string,
 *	 blocks: array,
 *	 collapsed: bool
 * }
 */
class ComponentCollection {
	/**
	 * The collection.
	 *
	 * @var array<Component>
	 */
	private array $collection;

	/**
	 * The publication state.
	 */
	private string $publicationState;

	/**
	 * The collection constructor.
	 */
	public function __construct() {
		$ComponentStore = new ComponentStore();

		$state = $ComponentStore->getState(empty(Session::getUsername())) ?? array('components' => array());

		$this->collection = $state['components'];
		$this->publicationState = $ComponentStore->isPublished() ? PublicationState::PUBLISHED->value : PublicationState::DRAFT->value;
	}

	/**
	 * Get the collection.
	 *
	 * @return array<Component>
	 */
	public function get(): array {
		return $this->collection;
	}

	/**
	 * Find a component by id.
	 *
	 * @param string $id
	 * @return Component|null
	 */
	public function getComponent(string $id): array|null {
		$filtered = array_filter($this->collection, function (array $item) use ($id) {
			return $item['id'] === $id;
		});

		if (empty($filtered)) {
			return null;
		}

		return reset($filtered);
	}

	/**
	 * Return the publication state.
	 *
	 * @return string
	 */
	public function getPublicationState(): string {
		return $this->publicationState;
	}
}
