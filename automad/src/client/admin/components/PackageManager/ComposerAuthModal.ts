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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	createField,
	createFormModal,
	CSS,
	FieldTag,
	html,
	listen,
	requestAPI,
} from '@/admin/core';
import { ComposerAuth } from '@/admin/types';
import { create, PackageManagerController } from '@/common';

/**
 * The Composer auth config modal component.
 *
 * @extends BaseComponent
 */
export class ComposerAuthModalComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-composer-auth-modal';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const authButton = create(
			'button',
			[CSS.button],
			{},
			this,
			App.text('composerAuth')
		);

		listen(authButton, 'click', () => {
			const { modal, form } = createFormModal(
				PackageManagerController.saveAuth,
				'',
				App.text('composerAuth'),
				App.text('save')
			);

			setTimeout(async () => {
				form.innerHTML = '<am-spinner></am-spinner>';
				form.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGapLarge);

				modal.open();

				create('div', [], {}, form, App.text('composerAuthInfo'));

				const defaultAuth: ComposerAuth = {
					githubToken: '',
					githubTokenIsSet: false,
					gitlabUrl: '',
					gitlabToken: '',
					gitlabTokenIsSet: false,
				};

				let auth: ComposerAuth = {
					...defaultAuth,
					...(await requestAPI(PackageManagerController.getSafeAuth))
						.data,
				};

				const github = create(
					'div',
					[CSS.card],
					{},
					form,
					html`
						<div class="${CSS.cardIcon}">
							<i class="bi bi-github"></i>
						</div>
						<div class="${CSS.cardTitle}">GitHub</div>
					`
				);

				const githubForm = create('div', [CSS.cardForm], {}, github);

				createField(
					FieldTag.input,
					githubForm,
					{
						key: 'githubToken',
						value: '',
						name: 'githubToken',
						placeholder: auth.githubTokenIsSet ? '**********' : '',
						label: 'Fine-grained access token "Contents: Read"',
					},
					[]
				);

				const gitlab = create(
					'div',
					[CSS.card],
					{},
					form,
					html`
						<div class="${CSS.cardIcon}">
							<i class="bi bi-gitlab"></i>
						</div>
						<div class="${CSS.cardTitle}">GitLab</div>
					`
				);

				const gitlabForm = create('div', [CSS.cardForm], {}, gitlab);

				createField(
					FieldTag.input,
					gitlabForm,
					{
						key: 'gitlabToken',
						value: '',
						name: 'gitlabToken',
						placeholder: auth.gitlabTokenIsSet ? '**********' : '',
						label: 'Personal access token "read_api"',
					},
					[]
				);

				createField(
					FieldTag.input,
					gitlabForm,
					{
						key: 'gitlabUrl',
						value: auth.gitlabUrl || 'gitlab.com',
						name: 'gitlabUrl',
						placeholder: 'gitlab.com',
						label: 'GitLab URL',
					},
					[]
				);
			}, 0);
		});
	}
}

customElements.define(
	ComposerAuthModalComponent.TAG_NAME,
	ComposerAuthModalComponent
);
