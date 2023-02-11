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
	CSS,
	fire,
	FormDataProviders,
	listen,
	resolveFileUrl,
	resolvePageUrl,
} from '../../core';
import { BaseFieldComponent } from './BaseField';
import Editor, { LinkMdNode } from '@toast-ui/editor';
import { ToolbarCustomOptions } from '@toast-ui/editor/types/ui';
import { Context, OpenTagToken } from '@toast-ui/editor/types/toastmark';
import { CustomHTMLRenderer } from '@toast-ui/editor/dist/toastui-editor-viewer';

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
	const el = create('button', ['toastui-editor-toolbar-icons', icon], {});

	listen(el, 'click', onClick);

	return { el, name: label, tooltip: label };
};

/**
 * Use a custom renderer to correctly resolve page and image links.
 *
 * @see {@link docs https://github.com/nhn/tui.editor/blob/master/docs/en/custom-html-renderer.md}
 */
const htmlRenderer: CustomHTMLRenderer = {
	image(node: LinkMdNode, context: Context) {
		const { origin, entering } = context;
		const result = origin() as OpenTagToken;
		if (entering) {
			result.attributes = {
				src: resolveFileUrl(node.destination),
			};
		}
		return result;
	},
	link(node: LinkMdNode, context: Context) {
		const { origin, entering } = context;
		const result = origin() as OpenTagToken;
		if (entering) {
			result.attributes = {
				href: resolvePageUrl(node.destination),
				target: '_blank',
			};
		}
		return result;
	},
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
		const container = create('div', [CSS.mdEditor], { id }, this);

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
			initialValue: value as string,
			usageStatistics: false,
			height: 'auto',
			hideModeSwitch: true,
			initialEditType: 'markdown',
			previewStyle: 'vertical',
			placeholder: placeholder as string,
			toolbarItems: [
				['heading', 'bold', 'italic', 'strike'],
				['hr', 'quote'],
				['ul', 'ol'],
				['table', imageSelection, linkSelection],
				['code', 'codeblock'],
			],
			customHTMLRenderer: htmlRenderer,
			events: {
				change: () => {
					this.value = editor.getMarkdown();
					fire('input', this);
				},
				focus: () => {
					container.classList.add(CSS.mdEditorFocus);
				},
				blur: () => {
					container.classList.remove(CSS.mdEditorFocus);
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
	}
}

FormDataProviders.add(MarkdownComponent.TAG_NAME);
customElements.define(MarkdownComponent.TAG_NAME, MarkdownComponent);
