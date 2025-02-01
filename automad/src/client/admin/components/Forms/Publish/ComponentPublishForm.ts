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
 */

import { App } from '@/admin/core';
import { PublishControllers } from '@/admin/types';
import { KeyValueMap, ComponentController } from '@/common';
import { BasePublishFormComponent } from './BasePublishForm';

/**
 * The component publish button and form for the navbar.
 *
 * @extends BasePublishFormComponent
 */
class ComponentPublishFormComponent extends BasePublishFormComponent {
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
		return App.componentsPublicationState;
	}

	/**
	 * The controllers configuration.
	 *
	 * @abstract
	 */
	protected controllers(): PublishControllers {
		return {
			state: ComponentController.getPublicationState,
			discard: ComponentController.discardDraft,
			publish: ComponentController.publish,
		};
	}
}

customElements.define(
	'am-component-publish-form',
	ComponentPublishFormComponent
);
