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

import { PublishControllers } from '@/admin/types';
import { KeyValueMap, SharedController } from '@/common';
import { BasePublishFormComponent } from './BasePublishForm';

class SharedPublishFormComponent extends BasePublishFormComponent {
	/**
	 * Data that is added to the update request.
	 *
	 * @abstract
	 */
	protected additionalRequestData(): KeyValueMap {
		return {};
	}

	/**
	 * Initial state.
	 *
	 * @abstract
	 */
	protected initialState(): string {
		return '';
	}

	/**
	 * The controllers configuration.
	 *
	 * @abstract
	 */
	protected controllers(): PublishControllers {
		return {
			state: SharedController.getPublicationState,
			discard: SharedController.discardDraft,
			publish: SharedController.publish,
		};
	}
}

customElements.define('am-shared-publish-form', SharedPublishFormComponent);
