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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Binding,
	classes,
	createField,
	eventNames,
	html,
	listen,
} from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Create bindings for the form elements in the feed section.
 *
 * @param listeners
 */
const createBindings = (listeners: Listener[]): void => {
	const username = new Binding('username', null, null, App.user.name);
	const email = new Binding('email', null, null, App.user.email);
	const userSubmit = new Binding('userSubmitButton', null, null, true);
	const userCount = new Binding(
		'userCount',
		null,
		null,
		App.system.users.length
	);

	listeners.push(
		listen(window, eventNames.appStateChange, () => {
			username.value = App.user.name;
			email.value = App.user.email;
			userSubmit.value = true;
			userCount.value = App.system.users.length;
		})
	);
};

/**
 * Render the user section.
 *
 * @param listeners
 * @returns the rendered HTML
 */
export const renderUsersSection = (listeners: Listener[]): string => {
	createBindings(listeners);

	return html`
		<am-form
			api="User/edit"
			event="${eventNames.appStateRequireUpdate}"
			watch
		>
			<p>$${App.text('systemUsersInfo')}</p>
			${createField(
				'am-input',
				null,
				{
					key: 'username',
					value: App.user.name,
					name: 'username',
					label: App.text('username'),
				},
				[],
				{ bind: 'username', bindto: 'value' }
			).outerHTML}
			${createField(
				'am-email',
				null,
				{
					key: 'email',
					value: App.user.email,
					name: 'email',
					label: App.text('email'),
				},
				[],
				{ bind: 'email', bindto: 'value' }
			).outerHTML}
			<am-submit
				class="${classes.button}"
				bind="userSubmitButton"
				bindto="disabled"
			>
				<am-icon-text
					icon="check"
					text="${App.text('save')}"
				></am-icon-text>
			</am-submit>
			<am-modal-toggle
				class="${classes.button}"
				modal="#am-change-password-modal"
			>
				${App.text('systemUsersChangePassword')}
			</am-modal-toggle>
		</am-form>
		<am-modal id="am-change-password-modal">
			<am-form class="${classes.modalDialog}" api="User/changePassword">
				<div class="${classes.modalHeader}">
					<span>${App.text('systemUsersChangePassword')}</span>
					<am-modal-close
						class="${classes.modalClose}"
					></am-modal-close>
				</div>
				${createField(
					'am-password',
					null,
					{
						key: 'currentPassword',
						value: '',
						name: 'currentPassword',
						label: App.text('currentPassword'),
					},
					[],
					{}
				).outerHTML}
				${createField(
					'am-password',
					null,
					{
						key: 'newPassword1',
						value: '',
						name: 'newPassword1',
						label: App.text('newPassword'),
					},
					[],
					{}
				).outerHTML}
				${createField(
					'am-password',
					null,
					{
						key: 'newPassword2',
						value: '',
						name: 'newPassword2',
						label: App.text('repeatPassword'),
					},
					[],
					{}
				).outerHTML}
				<div class="${classes.modalFooter}">
					<am-submit class="${classes.button}">
						<am-icon-text
							icon="check"
							text="${App.text('save')}"
						></am-icon-text>
					</am-submit>
				</div>
			</am-form>
		</am-modal>
		<p>$${App.text('systemUsersRegisteredInfo')}</p>
		<div>
			<am-modal-toggle
				class="${classes.button}"
				modal="#am-registered-users-modal"
			>
				<span class="${classes.iconText}">
					<i class="bi bi-people"></i>
					<span
						class="${classes.flex} ${classes.flexAlignCenter} ${classes.flexGap}"
					>
						<span>${App.text('systemUsersRegistered')}</span>
						<span
							class="${classes.badge}"
							bind="userCount"
							bindto="textContent"
						></span>
					</span>
				</span>
			</am-modal-toggle>
			<am-modal id="am-registered-users-modal" nofocus>
				<div class="${classes.modalDialog}">
					<div class="${classes.modalHeader}">
						<span>${App.text('systemUsersRegistered')}</span>
						<am-modal-close
							class="${classes.modalClose}"
						></am-modal-close>
					</div>
					<am-delete-users-form
						api="UserCollection/edit"
						event="${eventNames.appStateRequireUpdate}"
					></am-delete-users-form>
					<div class="${classes.modalFooter}">
						<am-modal-close class="${classes.button}">
							${App.text('close')}
						</am-modal-close>
						<am-submit
							class="${classes.button}"
							form="UserCollection/edit"
						>
							${App.text('deleteSelected')}
						</am-submit>
					</div>
				</div>
			</am-modal>
		</div>
		<div>
			<am-modal-toggle
				class="${classes.button}"
				modal="#am-add-user-modal"
			>
				${App.text('systemUsersAdd')}
			</am-modal-toggle>
			<am-modal id="am-add-user-modal">
				<am-form
					class="${classes.modalDialog}"
					api="UserCollection/createUser"
					event="${eventNames.appStateRequireUpdate}"
				>
					<div class="${classes.modalHeader}">
						<span>${App.text('systemUsersAdd')}</span>
						<am-modal-close
							class="${classes.modalClose}"
						></am-modal-close>
					</div>
					${createField(
						'am-input',
						null,
						{
							key: 'username',
							value: '',
							name: 'username',
							label: App.text('username'),
						},
						[],
						{}
					).outerHTML}
					${createField(
						'am-email',
						null,
						{
							key: 'email',
							value: '',
							name: 'email',
							label: App.text('email'),
						},
						[],
						{}
					).outerHTML}
					${createField(
						'am-password',
						null,
						{
							key: 'password1',
							value: '',
							name: 'password1',
							label: App.text('password'),
						},
						[],
						{}
					).outerHTML}
					${createField(
						'am-password',
						null,
						{
							key: 'password2',
							value: '',
							name: 'password2',
							label: App.text('repeatPassword'),
						},
						[],
						{}
					).outerHTML}
					<div class="${classes.modalFooter}">
						<am-modal-close class="${classes.button}">
							${App.text('close')}
						</am-modal-close>
						<am-submit class="${classes.button}">
							${App.text('systemUsersAdd')}
						</am-submit>
					</div>
				</am-form>
			</am-modal>
			<am-modal-toggle
				class="${classes.button}"
				modal="#am-invite-user-modal"
			>
				${App.text('systemUsersInvite')}
			</am-modal-toggle>
			<am-modal id="am-invite-user-modal">
				<am-form
					class="${classes.modalDialog}"
					api="UserCollection/inviteUser"
					event="${eventNames.appStateRequireUpdate}"
				>
					<div class="${classes.modalHeader}">
						<span>${App.text('systemUsersInvite')}</span>
						<am-modal-close
							class="${classes.modalClose}"
						></am-modal-close>
					</div>
					${createField(
						'am-input',
						null,
						{
							key: 'username',
							value: '',
							name: 'username',
							label: App.text('username'),
						},
						[],
						{}
					).outerHTML}
					${createField(
						'am-email',
						null,
						{
							key: 'email',
							value: '',
							name: 'email',
							label: App.text('email'),
						},
						[],
						{}
					).outerHTML}
					<div class="${classes.modalFooter}">
						<am-modal-close class="${classes.button}">
							${App.text('close')}
						</am-modal-close>
						<am-submit class="${classes.button}">
							${App.text('systemUsersSendInvitation')}
						</am-submit>
					</div>
				</am-form>
			</am-modal>
		</div>
	`;
};
