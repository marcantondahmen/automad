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
	Attr,
	confirm,
	createField,
	createFormModal,
	CSS,
	FieldTag,
	html,
	notifySuccess,
	requestAPI,
} from '@/admin/core';
import { ComposerAuth } from '@/admin/types';
import { create, PackageManagerController } from '@/common';

/**
 * Create the config modal.
 */
const configModal = (): void => {
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
			...(await requestAPI(PackageManagerController.getSafeAuth)).data,
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
};

/**
 * Render the auth reset button and modal.
 */
const resetAuth = async (): Promise<void> => {
	if (!(await confirm(App.text('composerAuthResetConfirm')))) {
		return;
	}

	const { success } = await requestAPI(
		PackageManagerController.resetAuth,
		{},
		true
	);

	if (success) {
		notifySuccess(success);
	}
};

/**
 * The Composer auth config component.
 *
 * @extends BaseComponent
 */
export class ComposerAuthComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-composer-auth';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.formGroup);

		const button = create(
			'button',
			[CSS.button, CSS.formGroupItem],
			{},
			this,
			App.text('composerAuth')
		);

		const dropdown = create(
			'am-dropdown',
			[CSS.button, CSS.buttonIcon, CSS.formGroupItem],
			{ [Attr.right]: '' },
			this,
			'<i class="bi bi-three-dots-vertical"></i>'
		);

		const items = create('div', [CSS.dropdownItems], {}, dropdown);

		const configLink = create(
			'button',
			[CSS.dropdownLink],
			{},
			items,
			html`
				<am-icon-text
					${Attr.icon}="key"
					${Attr.text}="${App.text('composerAuthConfig')}"
				></am-icon-text>
			`
		);

		const resetLink = create(
			'button',
			[CSS.dropdownLink],
			{},
			items,
			html`
				<am-icon-text
					${Attr.icon}="x-lg"
					${Attr.text}="${App.text('composerAuthReset')}"
				></am-icon-text>
			`
		);

		this.listen(button, 'click', configModal.bind(this));
		this.listen(configLink, 'click', configModal.bind(this));
		this.listen(resetLink, 'click', resetAuth.bind(this));
	}
}

customElements.define(ComposerAuthComponent.TAG_NAME, ComposerAuthComponent);
