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
 */
class DraftCollectionController {
	/**
	 * Get the draft collection data.
	 *
	 * @psalm-type Draft = array{
	 *		url: string,
	 *		title: string,
	 *		lastModified: string,
	 *		lastPublished: string|null
	 *	}
	 *
	 * @return Response
	 */
	public static function get(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();

		/** @var Draft[] */
		$drafts = array();

		foreach ($Automad->getPages() as $Page) {
			if ($Page->get(Fields::PUBLICATION_STATE) === PublicationState::DRAFT->value) {
				$DataStore = new DataStore($Page->path);

				/** @var Draft */
				$draft = array(
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
	 * Publish all pages that currently have an unpublished draft.
	 *
	 * @return Response
	 */
	public static function publishAll(): Response {
		$Automad = Automad::fromCache();

		foreach ($Automad->getPages() as $Page) {
			if ($Page->get(Fields::PUBLICATION_STATE) === PublicationState::DRAFT->value) {
				$Page->publish();
			}
		}

		return (new Response())->setSuccess(Text::get('publishedSuccessfully'));
	}
}
