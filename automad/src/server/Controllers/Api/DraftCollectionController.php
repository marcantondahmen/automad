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

namespace Automad\Controllers\Api;

use Automad\Api\Response;
use Automad\Core\Automad;
use Automad\Core\Text;
use Automad\Models\Page;
use Automad\Stores\ComponentStore;
use Automad\Stores\DataStore;
use Automad\Stores\PublicationState;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The DraftCollectionController handles all bulk methods related to page drafts.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-type Draft = array{
 *		type: 'page'|'shared'|'components',
 *		lastModified: string,
 *		lastPublished: string|null,
 *		url?: string,
 *		title?: string
 *	}
 */
class DraftCollectionController {
	/**
	 * Discard unpublished changes..
	 *
	 * @return Response
	 */
	public static function discardComponents(): Response {
		return ComponentController::discardDraft();
	}

	/**
	 * Discard unpublished changes..
	 *
	 * @return Response
	 */
	public static function discardPage(): Response {
		return PageController::discardDraft()->setReload(false);
	}

	/**
	 * Discard unpublished changes..
	 *
	 * @return Response
	 */
	public static function discardShared(): Response {
		return SharedController::discardDraft();
	}

	/**
	 * Get the draft collection data.
	 *
	 * @return Response
	 */
	public static function get(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();

		/** @var Draft[] */
		$drafts = array();

		if ($Automad->ComponentCollection->publicationState == PublicationState::DRAFT->value) {
			/** @var Draft */
			$drafts[] = array(
				'type' => 'components',
				'lastPublished' => $Automad->ComponentCollection->lastPublished,
				'lastModified' => $Automad->ComponentCollection->lastModified
			);
		}

		if ($Automad->Shared->publicationState == PublicationState::DRAFT->value) {
			/** @var Draft */
			$drafts[] = array(
				'type' => 'shared',
				'lastPublished' => $Automad->Shared->lastPublished,
				'lastModified' => $Automad->Shared->lastModified
			);
		}

		foreach ($Automad->getPages() as $Page) {
			if (!$Page->isPublished()) {
				$DataStore = new DataStore($Page->path);

				/** @var Draft */
				$draft = array(
					'type' => 'page',
					'url' => $Page->get(Fields::ORIG_URL),
					'title' => $Page->get(Fields::TITLE),
					'lastModified' => $Page->get(Fields::TIME_LAST_MODIFIED),
					'lastPublished' => $DataStore->lastPublished()
				);

				$drafts[] = $draft;
			}
		}

		return $Response->setData(array('drafts' => $drafts));
	}

	/**
	 * Publish all pages, components and shared data that are currently unpublished.
	 *
	 * @return Response
	 */
	public static function publishAll(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();

		if ($Automad->ComponentCollection->publicationState === PublicationState::DRAFT->value) {
			$CompomentStore = new ComponentStore();

			if (!$CompomentStore->publish()) {
				return $Response->setError(Text::get('publishError'));
			}
		}

		if ($Automad->Shared->publicationState === PublicationState::DRAFT->value) {
			if (!$Automad->Shared->publish()) {
				return $Response->setError(Text::get('publishError'));
			}
		}

		foreach ($Automad->getPages() as $Page) {
			if (!$Page->isPublished()) {
				if (!$Page->publish()) {
					return $Response->setError(Text::get('publishError'));
				}
			}
		}

		return $Response->setSuccess(Text::get('publishedSuccessfully'))->setReload(true);
	}

	/**
	 * Publish a page that currently is an unpublished draft.
	 *
	 * @return Response
	 */
	public static function publishComponents(): Response {
		return ComponentController::publish();
	}

	/**
	 * Publish a page that currently is an unpublished draft.
	 *
	 * @return Response
	 */
	public static function publishPage(): Response {
		return PageController::publish()->setRedirect('');
	}

	/**
	 * Publish a page that currently is an unpublished draft.
	 *
	 * @return Response
	 */
	public static function publishShared(): Response {
		return SharedController::publish();
	}
}
