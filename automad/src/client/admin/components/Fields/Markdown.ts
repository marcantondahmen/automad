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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	Binding,
	create,
	createImagePickerModal,
	createLinkModal,
	fire,
	FormDataProviders,
	html,
	listen,
	resolveFileUrl,
	resolvePageUrl,
} from '../../core';
import { BaseFieldComponent } from './BaseField';
import Editor from '@toast-ui/editor';
import { ToolbarCustomOptions } from '@toast-ui/editor/types/ui';

/**
 * Create a custom toolbar button.
 *
 * @param icon
 * @param label
 * @param onClick
 * @returns the toolbar item options
 */
const createCustomButton = (
	icon: string,
	label: string,
	onClick: Function
): ToolbarCustomOptions => {
	const el = create('button', [], {});

	el.innerHTML = html`<i class="bi bi-${icon}"></i>`;
	listen(el, 'click', onClick);

	return { el, name: label, tooltip: label };
};

/**
 * A Markdown editor field.
 *
 * @see {@link tui-editor https://github.com/nhn/tui.editor/tree/master/apps/editor}
 * @extends BaseFieldComponent
 */
class MarkdownComponent extends BaseFieldComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-markdown';

	/**
	 * The editor value that serves a input value for the parent form.
	 */
	value: string;

	/**
	 * Create an input field.
	 */
	createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const container = create('div', [], { id }, this);

		this.setAttribute('name', name);
		this.value = value as string;

		const imageBindingName = `image_${id}`;
		const linkBindingName = `link_${id}`;

		const imageSelection = createCustomButton('image', 'Image', () => {
			createImagePickerModal(imageBindingName, 'Image');
		});

		const linkSelection = createCustomButton('link', 'Link', () => {
			createLinkModal(linkBindingName, 'Link');
		});

		const editor = new Editor({
			el: container,
			usageStatistics: false,
			height: 'auto',
			hideModeSwitch: true,
			initialEditType: 'markdown',
			previewStyle: 'vertical',
			placeholder: placeholder as string,
			toolbarItems: [
				['heading', 'bold', 'italic', 'strike'],
				['hr', 'quote'],
				['ul', 'ol', 'task', 'indent', 'outdent'],
				['table', imageSelection, linkSelection],
				['code', 'codeblock'],
			],
			events: {
				change: () => {
					this.value = editor.getMarkdown();
					fire('input', this);
				},
				beforePreviewRender: (html) => {
					return html
						.replace(/src="([^"]+)"/g, (tag, image) => {
							return `src="${resolveFileUrl(image)}"`;
						})
						.replace(/href="([^"]+)"/g, (tag, link) => {
							return `href="${resolvePageUrl(
								link
							)}" target="_blank"`;
						});
				},
			},
		});

		new Binding(imageBindingName, {
			onChange: (value) => {
				if (value) {
					editor.insertText(`\r\n![](${value})`);
				}
			},
		});

		new Binding(linkBindingName, {
			onChange: (value) => {
				if (value) {
					editor.insertText(
						`[${editor.getSelectedText() || value}](${value})`
					);
				}
			},
		});

		editor.setMarkdown(value as string, false);
	}
}

FormDataProviders.add(MarkdownComponent.TAG_NAME);
customElements.define(MarkdownComponent.TAG_NAME, MarkdownComponent);
