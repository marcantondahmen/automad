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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, getPageURL } from '@/admin/core';
import { PublishControllers } from '@/admin/types';
import { KeyValueMap, PageController } from '@/common';
import { BasePublishFormComponent } from './BasePublishForm';

class PagePublishFormComponent extends BasePublishFormComponent {
	/**
	 * Data that is added to the update request.
	 *
	 * @abstract
	 */
	protected additionalRequestData(): KeyValueMap {
		return { url: getPageURL() };
	}

	/**
	 * Initial state.
	 *
	 * @abstract
	 */
	protected initialState(): string {
		return App.pages[getPageURL()].publicationState;
	}

	/**
	 * The controllers configuration.
	 *
	 * @abstract
	 */
	protected controllers(): PublishControllers {
		return {
			state: PageController.getPublicationState,
			discard: PageController.discardDraft,
			publish: PageController.publish,
		};
	}
}

customElements.define('am-page-publish-form', PagePublishFormComponent);
