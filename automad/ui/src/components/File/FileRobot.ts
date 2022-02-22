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

import FilerobotImageEditor from 'filerobot-image-editor';
import { FilerobotImageEditorConfig } from 'react-filerobot-image-editor';
import { KeyValueMap } from '../../types';
import { App, classes, create, fire, listen } from '../../core';
import { modalCloseEventName, ModalComponent } from '../Modal/Modal';
import { BaseComponent } from '../Base';
import { FormComponent } from '../Forms/Form';
import { FilesChangedOnServerEventName } from '../Forms/FileCollectionList';

/**
 * A file robot wrapper component.
 *
 * @example
 * <am-file-robot file="url"></am-file-robot>
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
		return ['file'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		listen(this, 'click', () => {
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
			{ destroy: '', noesc: '' },
			document.body
		);
		const dialog = create(
			'am-form',
			[classes.modalDialog, classes.modalDialogFullscreen],
			{ api: 'Image/save' },
			modal
		);

		this.initFileRobot(dialog, modal);

		return modal;
	}

	/**
	 * Init FileRobot.
	 *
	 * @see {@link Config https://github.com/scaleflex/filerobot-image-editor/tree/v4#vanillajs-example}
	 * @see {@link Defaults https://github.com/scaleflex/filerobot-image-editor/blob/v4/packages/react-filerobot-image-editor/src/context/defaultConfig.js}
	 * @see {@link ThemeColors https://github.com/scaleflex/ui/blob/master/packages/ui/src/utils/types/palette/color.ts#L1}
	 * @see {@link Translations https://github.com/scaleflex/filerobot-image-editor/blob/v4/packages/react-filerobot-image-editor/src/context/defaultTranslations.js}
	 * @param form
	 * @param modal
	 */
	private initFileRobot(form: FormComponent, modal: ModalComponent): void {
		const config = {
			img: this.elementAttributes.file,
			savingPixelRatio: 1,
			previewPixelRatio: window.devicePixelRatio,
			useBackendTranslations: false,
			theme: {
				// More about themes:
				// https://github.com/scaleflex/filerobot-image-editor/tree/v4#theme
				palette: {
					// Add palette colors here:
					// https://github.com/scaleflex/ui/blob/master/packages/ui/src/utils/types/palette/color.ts#L1
					'bg-primary-active': 'var(--am-input-bg)',
				},
				typography: {
					fontFamily: 'Inter',
				},
			},
			// https://github.com/scaleflex/filerobot-image-editor/blob/v4/packages/react-filerobot-image-editor/src/context/defaultTranslations.js
			translations: App.state.text,
			onSave: (savedImageData: KeyValueMap, designState: any) => {
				delete savedImageData.imageCanvas;
				this.save(form, savedImageData);
			},
			onClose: (closingReason: string) => {
				filerobotImageEditor.terminate();
				modal.close();
			},
		};

		const filerobotImageEditor = new FilerobotImageEditor(
			form,
			config as FilerobotImageEditorConfig
		);

		filerobotImageEditor.render();

		listen(modal, modalCloseEventName, () => {
			filerobotImageEditor.terminate();
		});
	}

	/**
	 * Save the image.
	 *
	 * @param form
	 * @param savedImageData
	 * @async
	 */
	private async save(
		form: FormComponent,
		savedImageData: KeyValueMap
	): Promise<void> {
		form.additionalData = savedImageData;
		await form.submit();
		fire(FilesChangedOnServerEventName);
	}
}

customElements.define('am-file-robot', FileRobotComponent);
