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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	Attr,
	ConfigController,
	createField,
	CSS,
	EventName,
	FieldTag,
	html,
	UserCollectionController,
	UserController,
} from '@/admin/core';

/**
 * Render the user section.
 *
 * @returns the rendered HTML
 */
export const renderUsersSection = (): string => {
	return html`
		<am-form
			${Attr.api}="${UserController.edit}"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.watch}
		>
			<h2 class="${CSS.marginTopNone}">
				${App.text('systemUsersYourAccountHeading')}
			</h2>
			<p>${App.text('systemUsersYourAccountText')}</p>
			<am-form-error></am-form-error>
			<span class="${CSS.card}">
				<span
					class="${CSS.cardForm} ${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
				>
					<span class="${CSS.flex} ${CSS.flexColumn}">
						<am-user-name value="${App.user.name}"></am-user-name>
						<am-user-email
							value="${App.user.email}"
						></am-user-email>
					</span>
					<span class="${CSS.cardFormButtons}">
						<am-submit class="${CSS.button}">
							<span>${App.text('save')}</span>
						</am-submit>
						<am-modal-toggle
							class="${CSS.button} ${CSS.buttonPrimary}"
							${Attr.modal}="#am-change-password-modal"
						>
							<span>
								${App.text('systemUsersChangePassword')}
							</span>
						</am-modal-toggle>
					</span>
				</span>
			</span>
		</am-form>

		<h2>${App.text('systemUsersTotpHeading')}</h2>
		<p>${App.text('systemUsersTotpText')}</p>
		<am-totp-config></am-totp-config>

		<h2>${App.text('systemUsersCollaborateHeading')}</h2>
		<p>${App.text('systemUsersCollaborateText')}</p>
		<span class="${CSS.card}">
			<span class="${CSS.cardForm}">
				<span class="${CSS.cardFormButtons}">
					<am-modal-toggle
						class="${CSS.button}"
						${Attr.modal}="#am-registered-users-modal"
					>
						<span
							class="${CSS.flex} ${CSS.flexAlignCenter} ${CSS.flexGap}"
						>
							<span> ${App.text('systemUsersRegistered')} </span>
							<span class="${CSS.badge}">
								<am-user-count-indicator></am-user-count-indicator>
							</span>
						</span>
					</am-modal-toggle>
				</span>
				<span class="${CSS.cardFormButtons}">
					<am-modal-toggle
						class="${CSS.button}"
						${Attr.modal}="#am-add-user-modal"
					>
						<span>${App.text('systemUsersAdd')}</span>
					</am-modal-toggle>
					<am-modal-toggle
						class="${CSS.button} ${CSS.buttonPrimary}"
						${Attr.modal}="#am-invite-user-modal"
					>
						<span>${App.text('systemUsersInvite')}</span>
					</am-modal-toggle>
				</span>
			</span>
		</span>

		<p>${App.text('systemUsersSignOutAllInfo')}</p>
		<am-form
			${Attr.api}="${ConfigController.update}"
			${Attr.confirm}="${App.text('systemUsersSignOutAllConfirm')}"
		>
			<input type="hidden" name="type" value="sessionCookieSalt" />
			<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
				<span>${App.text('systemUsersSignOutAll')}</span>
			</am-submit>
		</am-form>

		<!-- Modals -->
		<am-modal id="am-change-password-modal" ${Attr.clearForm}>
			<am-form
				class="${CSS.modalDialog}"
				${Attr.api}="${UserController.changePassword}"
			>
				<am-modal-header>
					${App.text('systemUsersChangePassword')}
				</am-modal-header>
				<am-modal-body>
					<am-form-error></am-form-error>
					<div>
						${createField(
							FieldTag.password,
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
							FieldTag.password,
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
							FieldTag.password,
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
				</am-modal-body>
				<am-modal-footer>
					<am-modal-close class="${CSS.button}">
						${App.text('close')}
					</am-modal-close>
					<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
						${App.text('save')}
					</am-submit>
				</am-modal-footer>
			</am-form>
		</am-modal>
		<am-modal id="am-registered-users-modal" nofocus>
			<am-modal-dialog>
				<am-modal-header>
					${App.text('systemUsersRegistered')}
				</am-modal-header>
				<am-modal-body>
					<am-delete-users-form
						${Attr.api}="${UserCollectionController.edit}"
						${Attr.event}="${EventName.appStateRequireUpdate}"
						${Attr.watch}
					></am-delete-users-form>
				</am-modal-body>
				<am-modal-footer>
					<am-modal-close class="${CSS.button}">
						${App.text('close')}
					</am-modal-close>
					<am-submit
						class="${CSS.button} ${CSS.buttonDanger}"
						${Attr.form}="${UserCollectionController.edit}"
						disabled
					>
						${App.text('deleteSelection')}
					</am-submit>
				</am-modal-footer>
			</am-modal-dialog>
		</am-modal>
		<am-modal id="am-add-user-modal" ${Attr.clearForm}>
			<am-form
				class="${CSS.modalDialog}"
				${Attr.api}="${UserCollectionController.createUser}"
				${Attr.event}="${EventName.appStateRequireUpdate}"
			>
				<am-modal-header>
					${App.text('systemUsersAdd')}
				</am-modal-header>
				<am-modal-body>
					<am-form-error></am-form-error>
					<div>
						<am-user-name></am-user-name>
						<am-user-email></am-user-email>
						${createField(
							FieldTag.password,
							null,
							{
								key: 'password1',
								id: 'am-field__create-password1',
								value: '',
								name: 'password1',
								label: App.text('password'),
							},
							[],
							{
								required: '',
							}
						).outerHTML}
						${createField(
							FieldTag.password,
							null,
							{
								key: 'password2',
								id: 'am-field__create-password2',
								value: '',
								name: 'password2',
								label: App.text('repeatPassword'),
							},
							[],
							{
								required: '',
							}
						).outerHTML}
					</div>
				</am-modal-body>
				<am-modal-footer>
					<am-modal-close class="${CSS.button}">
						${App.text('close')}
					</am-modal-close>
					<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
						${App.text('systemUsersAdd')}
					</am-submit>
				</am-modal-footer>
			</am-form>
		</am-modal>
		<am-modal id="am-invite-user-modal" ${Attr.clearForm}>
			<am-form
				class="${CSS.modalDialog}"
				${Attr.api}="${UserCollectionController.inviteUser}"
				${Attr.event}="${EventName.appStateRequireUpdate}"
			>
				<am-modal-header>
					${App.text('systemUsersInvite')}
				</am-modal-header>
				<am-modal-body>
					<am-form-error></am-form-error>
					<div>
						<am-user-name></am-user-name>
						<am-user-email></am-user-email>
					</div>
				</am-modal-body>
				<am-modal-footer>
					<am-modal-close class="${CSS.button}">
						${App.text('close')}
					</am-modal-close>
					<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
						${App.text('systemUsersSendInvitation')}
					</am-submit>
				</am-modal-footer>
			</am-form>
		</am-modal>
	`;
};
