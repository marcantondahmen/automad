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

namespace Automad\Stores;

use Automad\Core\FileSystem;
use Automad\Core\PublicationState;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A store class handles the reading of JSON formatted data files.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractStore {
	const DATE_FORMAT = 'c';

	/**
	 * The full data store content.
	 */
	private array $data = array();

	/**
	 * The data store file path.
	 */
	private string $file = '';

	/**
	 * The constructor.
	 *
	 * @param ?string $optionalPath
	 */
	public function __construct(?string $optionalPath = null) {
		$this->file = $this->resolvePath($optionalPath);

		if (is_readable($this->file)) {
			$this->data = FileSystem::readJson($this->file, true);
		}
	}

	/**
	 * Return the data store file path.
	 *
	 * @return string
	 */
	public function getFile(): string {
		return $this->file;
	}

	/**
	 * Get a state.
	 *
	 * @param bool|PublicationState $state
	 * @return array|null
	 */
	public function getState(bool|PublicationState $state): array|null {
		if (empty($this->data)) {
			return null;
		}

		$pubState = is_bool($state) ? ($state ? PublicationState::PUBLISHED : PublicationState::DRAFT) : $state;
		$data = $this->data[$pubState->value] ?? null;

		if ($pubState == PublicationState::DRAFT && empty($data)) {
			$pubState = PublicationState::PUBLISHED;
			$data = $this->getState($pubState);
		}

		if (!is_null($data)) {
			$data[Fields::PUBLICATION_STATE] = $pubState->value;
		}

		return $data;
	}

	/**
	 * Returns true if there is no draft.
	 *
	 * @return bool
	 */
	public function isPublished(): bool {
		return empty($this->data[PublicationState::DRAFT->value]);
	}

	/**
	 * Return the last publication date.
	 *
	 * @return string
	 */
	public function lastPublished(): string {
		$published = $this->getState(PublicationState::PUBLISHED);

		return $published[Fields::TIME_LAST_PUBLISHED] ?? '';
	}

	/**
	 * Publish a draft.
	 *
	 * @return bool
	 */
	public function publish(): bool {
		$draft = $this->getState(PublicationState::DRAFT);
		$draft[Fields::TIME_LAST_PUBLISHED] = date(self::DATE_FORMAT);

		$this->setState(PublicationState::DRAFT, array());
		$this->setState(PublicationState::PUBLISHED, $draft);

		return $this->save();
	}

	/**
	 * Save the data store to disk.
	 *
	 * @return bool
	 */
	public function save(): bool {
		$data = array_merge($this->data, array('automadVersion' => AM_VERSION));

		return FileSystem::writeJson($this->file, $data);
	}

	/**
	 * Set the data for a publication state.
	 *
	 * @param PublicationState $state
	 * @param array $data
	 * @return AbstractStore
	 */
	public function setState(PublicationState $state, array $data): AbstractStore {
		$data = array_map(function ($value) {
			if (is_string($value)) {
				return trim($value);
			}

			return $value;
		}, $data);

		$data = array_filter($data, function ($value) {
			if (is_string($value)) {
				return strlen($value);
			}

			return true;
		});

		unset($data[Fields::PUBLICATION_STATE]);

		$this->data[$state->value] = $data;

		return $this;
	}

	/**
	 * This method is required to set the actual file path for the store on disk.
	 *
	 * @param ?string $optionalPath
	 * @return string
	 */
	abstract protected function resolvePath(?string $optionalPath = null): string;
}
