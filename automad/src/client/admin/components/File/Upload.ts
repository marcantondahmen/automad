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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import Dropzone, { DropzoneFile, DropzoneOptions } from 'dropzone';
import {
	App,
	Attr,
	controllerRoute,
	create,
	CSS,
	EventName,
	FileCollectionController,
	fire,
	getCsrfToken,
	getPageURL,
	html,
	notifyError,
	notifySuccess,
	query,
	RequestKey,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { FileCollectionListFormComponent } from '@/admin/components/Forms/FileCollection/ListForm';
import { ModalComponent } from '@/admin/components/Modal/Modal';

/**
 * An upload component.
 *
 * @example
 * <am-upload></am-upload>
 *
 * @see {@link dropzone https://github.com/dropzone/dropzone}
 * @see {@link options https://github.com/dropzone/dropzone/blob/main/src/options.js}
 * @extends BaseComponent
 */
class UploadComponent extends BaseComponent {
	/**
	 * The dropzone instance.
	 */
	dropzone: Dropzone = null;

	/**
	 * The actual form that is also used as the dropzone.
	 */
	form: HTMLFormElement = null;

	/**
	 * The preview queue window.
	 */
	modal: ModalComponent = null;

	/**
	 * The preview container.
	 */
	queue: HTMLElement = null;

	/**
	 * The dropzone instance options.
	 *
	 * @see {@link options https://github.com/dropzone/dropzone/blob/main/src/options.js}
	 */
	get options(): DropzoneOptions {
		const acceptedFiles = App.allowedFileTypes
			.map((extension) => `.${extension}`)
			.join(', ');

		return {
			clickable: [this.form],
			paramName: 'file',
			hiddenInputContainer: this,
			uploadMultiple: false,
			parallelUploads: 2,
			maxFilesize: 512,
			acceptedFiles: acceptedFiles,
			previewsContainer: this.queue,
			addRemoveLinks: true,
			dictInvalidFileType: App.text('unsupportedFileTypeError'),
			dictCancelUpload: App.text('cancel'),
			dictUploadCanceled: App.text('uploadCancelled'),
			dictCancelUploadConfirmation: App.text('confirmCancelUpload'),
			chunking: true,
			forceChunking: true,
			parallelChunkUploads: false,
			thumbnailWidth: 150,
			thumbnailHeight: 150,
		};
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.upload);

		this.form = this.createForm();
		this.modal = create(
			'am-modal',
			[],
			{ [Attr.noClick]: '', [Attr.noEsc]: '' },
			this.form
		);
		this.queue = create(
			'am-modal-dialog',
			[CSS.uploadPreviews],
			{},
			this.modal
		);

		this.dropzone = new Dropzone(this.form, this.options);
		this.dropzone.on('success', this.onSuccess.bind(this));
		this.dropzone.on('error', this.onError.bind(this));
		this.dropzone.on('addedfiles', this.onAddedFiles.bind(this));
		this.dropzone.on('queuecomplete', this.onQueueComplete.bind(this));
	}

	/**
	 * The error event handler.
	 *
	 * @param file
	 * @param message
	 */
	private onError(file: DropzoneFile, message: any): void {
		this.dropzone.removeFile(file);

		if (typeof message !== 'string' && message.error) {
			message = message.error;
		}

		notifyError(html`${message}`);
	}

	/**
	 * The success event handler.
	 *
	 * @param file
	 */
	private onSuccess(file: DropzoneFile): void {
		this.dropzone.removeFile(file);

		notifySuccess(html`${App.text('uploadedSuccess')}:<br />$${file.name}`);
		fire(EventName.filesChangeOnServer);

		query<FileCollectionListFormComponent>(
			'am-file-collection-list-form'
		)?.refresh();
	}

	/**
	 * The files added event handler.
	 */
	private onAddedFiles(): void {
		this.modal.open();
	}

	/**
	 * The completed all event handler.
	 */
	private onQueueComplete(): void {
		this.modal.close();
	}

	/**
	 * Create a form inlcuding a hidden url input in case of a page.
	 *
	 * @returns the form element
	 */
	private createForm(): HTMLFormElement {
		const form = create(
			'form',
			[CSS.uploadDropzone],
			{
				action: `${App.apiURL}/${controllerRoute(
					FileCollectionController.upload
				)}`,
			},
			this
		);

		form.innerHTML = html`<span>${App.text('dropFilesOrClick')}</span>`;

		const page = getPageURL();

		if (page) {
			create(
				'input',
				[],
				{
					type: 'hidden',
					name: 'url',
					value: page,
				},
				form
			);
		}

		create(
			'input',
			[],
			{
				type: 'hidden',
				name: RequestKey.csrf,
				value: getCsrfToken(),
			},
			form
		);

		return form;
	}

	disconnectedCallback(): void {
		this.dropzone.destroy();
		super.disconnectedCallback();
	}
}

customElements.define('am-upload', UploadComponent);
