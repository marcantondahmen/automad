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
	Attr,
	Binding,
	createField,
	CSS,
	EventName,
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
		listen(window, EventName.appStateChange, () => {
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
		<div class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}">
			<am-form
				${Attr.api}="User/edit"
				${Attr.event}="${EventName.appStateRequireUpdate}"
				${Attr.watch}
			>
				<span class="${CSS.card}">
					<span class="${CSS.cardBody} ${CSS.cardBodyLarge}">
						${App.text('systemUsersInfo')}
					</span>
					<span
						class="${CSS.cardForm} ${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
					>
						<span>
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
								{
									[Attr.bind]: 'username',
									[Attr.bindTo]: 'value',
								}
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
								{ [Attr.bind]: 'email', [Attr.bindTo]: 'value' }
							).outerHTML}
						</span>
						<span class="${CSS.cardFormButtons}">
							<am-submit
								class="${CSS.button}"
								${Attr.bind}="userSubmitButton"
								${Attr.bindTo}="disabled"
							>
								<span>${App.text('save')}</span>
							</am-submit>
							<am-modal-toggle
								class="${CSS.button} ${CSS.buttonAccent}"
								${Attr.modal}="#am-change-password-modal"
							>
								<span
									>${App.text(
										'systemUsersChangePassword'
									)}</span
								>
							</am-modal-toggle>
						</span>
					</span>
				</span>
			</am-form>
			<span class="${CSS.card}">
				<span class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${App.text('systemUsersRegisteredInfo')}
				</span>
				<span class="${CSS.cardForm}">
					<span class="${CSS.cardFormButtons}">
						<am-modal-toggle
							class="${CSS.button}"
							${Attr.modal}="#am-registered-users-modal"
						>
							<span class="${CSS.iconText}">
								<span
									class="${CSS.flex} ${CSS.flexAlignCenter} ${CSS.flexGap}"
								>
									<span
										>${App.text(
											'systemUsersRegistered'
										)}</span
									>
									<span
										class="${CSS.badge}"
										${Attr.bind}="userCount"
										${Attr.bindTo}="textContent"
									></span>
								</span>
							</span>
						</am-modal-toggle>
					</span>
					<span class="${CSS.cardFormButtons}">
						<am-modal-toggle
							class="${CSS.button}"
							${Attr.modal}="#am-add-user-modal"
						>
							${App.text('systemUsersAdd')}
						</am-modal-toggle>
						<am-modal-toggle
							class="${CSS.button}"
							${Attr.modal}="#am-invite-user-modal"
						>
							${App.text('systemUsersInvite')}
						</am-modal-toggle>
					</span>
				</span>
			</span>
		</div>
		<!-- Modals -->
		<am-modal id="am-change-password-modal">
			<am-form
				class="${CSS.modalDialog}"
				${Attr.api}="User/changePassword"
			>
				<div class="${CSS.modalHeader}">
					<span>${App.text('systemUsersChangePassword')}</span>
					<am-modal-close class="${CSS.modalClose}"></am-modal-close>
				</div>
				<div class="${CSS.modalBody}">
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
				</div>
				<div class="${CSS.modalFooter}">
					<am-modal-close class="${CSS.button} ${CSS.buttonLink}">
						${App.text('close')}
					</am-modal-close>
					<am-submit class="${CSS.button} ${CSS.buttonAccent}">
						${App.text('save')}
					</am-submit>
				</div>
			</am-form>
		</am-modal>
		<am-modal id="am-registered-users-modal" nofocus>
			<div class="${CSS.modalDialog}">
				<div class="${CSS.modalHeader}">
					<span>${App.text('systemUsersRegistered')}</span>
					<am-modal-close class="${CSS.modalClose}"></am-modal-close>
				</div>
				<div class="${CSS.modalBody}">
					<am-delete-users-form
						${Attr.api}="UserCollection/edit"
						${Attr.event}="${EventName.appStateRequireUpdate}"
					></am-delete-users-form>
				</div>
				<div class="${CSS.modalFooter}">
					<am-modal-close class="${CSS.button} ${CSS.buttonLink}">
						${App.text('close')}
					</am-modal-close>
					<am-submit
						class="${CSS.button} ${CSS.buttonAccent}"
						${Attr.form}="UserCollection/edit"
					>
						${App.text('deleteSelected')}
					</am-submit>
				</div>
			</div>
		</am-modal>
		<am-modal id="am-add-user-modal">
			<am-form
				class="${CSS.modalDialog}"
				${Attr.api}="UserCollection/createUser"
				${Attr.event}="${EventName.appStateRequireUpdate}"
			>
				<div class="${CSS.modalHeader}">
					<span>${App.text('systemUsersAdd')}</span>
					<am-modal-close class="${CSS.modalClose}"></am-modal-close>
				</div>
				<div class="${CSS.modalBody}">
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
				</div>
				<div class="${CSS.modalFooter}">
					<am-modal-close class="${CSS.button} ${CSS.buttonLink}">
						${App.text('close')}
					</am-modal-close>
					<am-submit class="${CSS.button} ${CSS.buttonAccent}">
						${App.text('systemUsersAdd')}
					</am-submit>
				</div>
			</am-form>
		</am-modal>
		<am-modal id="am-invite-user-modal">
			<am-form
				class="${CSS.modalDialog}"
				${Attr.api}="UserCollection/inviteUser"
				${Attr.event}="${EventName.appStateRequireUpdate}"
			>
				<div class="${CSS.modalHeader}">
					<span>${App.text('systemUsersInvite')}</span>
					<am-modal-close class="${CSS.modalClose}"></am-modal-close>
				</div>
				<div class="${CSS.modalBody}">
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
				</div>
				<div class="${CSS.modalFooter}">
					<am-modal-close class="${CSS.button} ${CSS.buttonLink}">
						${App.text('close')}
					</am-modal-close>
					<am-submit class="${CSS.button} ${CSS.buttonAccent}">
						${App.text('systemUsersSendInvitation')}
					</am-submit>
				</div>
			</am-form>
		</am-modal>
	`;
};
