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

use Automad\Auth\Session;
use Automad\Core\Blocks;
use Automad\Core\PublicationState;
use Automad\Stores\ComponentStore;

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
	 * The collection.
	 *
	 * @var array<Component>
	 */
	private array $collection;

	/**
	 * The component store instance.
	 */
	private ComponentStore $ComponentStore;

	/**
	 * The publication state.
	 */
	private string $publicationState;

	/**
	 * The collection constructor.
	 */
	public function __construct() {
		$this->ComponentStore = new ComponentStore();

		$state = $this->ComponentStore->getState(empty(Session::getUsername())) ?? array('components' => array());

		$this->collection = $state['components'];
		$this->publicationState = $this->ComponentStore->isPublished() ? PublicationState::PUBLISHED->value : PublicationState::DRAFT->value;
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

	/**
	 * Search and replace inside a component.
	 *
	 * @param string $id
	 * @param string $searchRegex
	 * @param string $replace
	 * @param bool $replaceInPublished
	 */
	public function replaceInComponent(string $id, string $searchRegex, string $replace, bool $replaceInPublished): void {
		$replaceInState = function (PublicationState $PublicationState) use ($id, $searchRegex, $replace, $replaceInPublished): void {
			$state = $this->ComponentStore->getState($PublicationState);

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

			$this->ComponentStore->setState($PublicationState, $state);
		};

		$replaceInState(PublicationState::DRAFT);

		if ($replaceInPublished) {
			$replaceInState(PublicationState::PUBLISHED);
		}

		$this->ComponentStore->save();
		$this->collection = ($this->ComponentStore->getState(PublicationState::DRAFT) ?? array('components' => array()))['components'];
	}
}
