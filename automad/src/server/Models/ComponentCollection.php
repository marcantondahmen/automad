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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Models;

use Automad\Auth\Auth;
use Automad\Core\Blocks;
use Automad\Stores\ComponentStore;
use Automad\Stores\PublicationState;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The component collection model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
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
	 * The last modification date.
	 */
	public readonly string $lastModified;

	/**
	 * The last publication date.
	 */
	public readonly string $lastPublished;

	/**
	 * The publication state.
	 */
	public readonly string $publicationState;

	/**
	 * The collection.
	 *
	 * @var array<Component>
	 */
	private array $collection;

	/**
	 * The collection constructor.
	 */
	public function __construct() {
		$ComponentStore = new ComponentStore();

		$state = $ComponentStore->getState(!Auth::isAuthenticated()) ?? array('components' => array());

		$this->collection = $state['components'];
		$this->publicationState = $ComponentStore->isPublished() ? PublicationState::PUBLISHED->value : PublicationState::DRAFT->value;
		$this->lastPublished = $ComponentStore->lastPublished();
		$this->lastModified = $ComponentStore->lastModified();
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
	 * Search and replace inside a component.
	 *
	 * @param string $id
	 * @param string $searchRegex
	 * @param string $replace
	 * @param bool $replaceInPublished
	 */
	public function replaceInComponent(string $id, string $searchRegex, string $replace, bool $replaceInPublished): void {
		$ComponentStore = new ComponentStore();

		$replaceInState = function (PublicationState $PublicationState) use ($ComponentStore, $id, $searchRegex, $replace, $replaceInPublished): void {
			$state = $ComponentStore->getState($PublicationState);

			if (empty($state) || empty($state['components'])) {
				return;
			}

			$state['components'] = array_map(function (array $component) use ($id, $searchRegex, $replace, $replaceInPublished): array {
				if ($id !== $component['id']) {
					return $component;
				}

				$component['blocks'] = Blocks::replace($component['blocks'], $this, $searchRegex, $replace, $replaceInPublished);

				return $component;
			}, $state['components']);

			$ComponentStore->setState($PublicationState, $state);
		};

		$replaceInState(PublicationState::DRAFT);

		if ($replaceInPublished) {
			$replaceInState(PublicationState::PUBLISHED);
		}

		$ComponentStore->save();
		$this->collection = ($ComponentStore->getState(PublicationState::DRAFT) ?? array('components' => array()))['components'];
	}
}
