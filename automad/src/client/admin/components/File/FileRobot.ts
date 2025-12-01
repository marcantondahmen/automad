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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { FilerobotImageEditor, FilerobotImageEditorConfig } from '@/admin/vendor/filerobot';
import { KeyValueMap } from '@/admin/types';
import {
	App,
	Attr,
	confirm,
	create,
	CSS,
	EventName,
	fire,
	ImageController,
} from '@/admin/core';
import { ModalComponent } from '@/admin/components/Modal/Modal';
import { BaseComponent } from '@/admin/components/Base';
import { FormComponent } from '@/admin/components/Forms/Form';
import { fileRobotTheme } from './FileRobotTheme';

/**
 * A file robot wrapper component.
 *
 * @example
 * <am-file-robot ${Attr.file}="url"></am-file-robot>
 *
 * @see {@link FileRobot https://github.com/scaleflex/filerobot-image-editor/tree/v4}
 * @extends BaseComponent
 */
class FileRobotComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.file];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listen(this, 'click', () => {
			const modal = this.createModal();

			setTimeout(() => {
				modal.open();
			}, 0);
		});
	}

	/**
	 * Create a file edit modal component.
	 *
	 * @returns the created modal component
	 */
	private createModal(): ModalComponent {
		const modal = create(
			'am-modal',
			[],
			{ [Attr.destroy]: '', [Attr.noEsc]: '' },
			document.body
		);

		const dialog = create(
			'am-form',
			[CSS.modalDialog, CSS.modalDialogFullscreen],
			{ [Attr.api]: ImageController.save },
			modal
		);

		this.initFileRobot(dialog, modal);

		return modal;
	}

	/**
	 * Init FileRobot.
	 *
	 * @see {@link Config https://github.com/scaleflex/filerobot-image-editor/#config}
	 * @see {@link Example https://github.com/scaleflex/filerobot-image-editor/#vanillajs-example}
	 * @see {@link Theme https://github.com/scaleflex/filerobot-image-editor/tree/master#theme}
	 * @see {@link ThemeColors https://github.com/scaleflex/ui/blob/master/packages/ui/src/utils/types/palette/color.ts#L1}
	 * @see {@link Translations https://github.com/scaleflex/filerobot-image-editor/blob/master/packages/react-filerobot-image-editor/src/context/defaultTranslations.js}
	 * @param form
	 * @param modal
	 */
	private initFileRobot(form: FormComponent, modal: ModalComponent): void {
		const config = {
			source: this.elementAttributes[Attr.file],
			savingPixelRatio: 1,
			previewPixelRatio: window.devicePixelRatio,
			useBackendTranslations: false,
			theme: fileRobotTheme,
			translations: App.state.text,
			onSave: async (savedImageData: KeyValueMap, designState: any) => {
				form.additionalData = savedImageData;
				await form.submit();

				fire(EventName.filesChangeOnServer);
				close();
			},
			onClose: async (
				closingReason: string,
				haveNotSavedChanges: boolean
			) => {
				if (haveNotSavedChanges) {
					if (!(await confirm(App.text('discardImageChanges')))) {
						return;
					}
				}

				close();
			},
		};

		const filerobotImageEditor = new FilerobotImageEditor(
			form,
			config as FilerobotImageEditorConfig
		);

		const close = () => {
			filerobotImageEditor.terminate();
			modal.close();
		};

		filerobotImageEditor.render();
	}
}

customElements.define('am-file-robot', FileRobotComponent);
