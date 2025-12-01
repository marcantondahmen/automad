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

import {
	Editor,
	LinkMdNode,
	ToolbarCustomOptions,
	Context,
	OpenTagToken,
	CustomHTMLRenderer,
	codeSyntaxHighlight,
} from '@/admin/vendor/toastui';
import {
	App,
	Binding,
	create,
	createImagePickerModal,
	createLinkModal,
	CSS,
	FieldTag,
	fire,
	FormDataProviders,
	query,
	queryAll,
	resolveFileUrl,
	resolvePageUrl,
} from '@/admin/core';
import { BaseFieldComponent } from './BaseField';
import { Prism } from '@/prism/prism';

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
 * Register translation.
 *
 * @see {@link docs https://github.com/nhn/tui.editor/blob/master/docs/en/i18n.md#use-case-2--some-value-overrides}
 * @see {@link en-US https://github.com/nhn/tui.editor/blob/master/apps/editor/src/i18n/en-us.ts}
 */
const setMdEditorLanguage = (): void => {
	Editor.setLanguage('en-US', {
		Write: App.text('write'),
		Preview: App.text('preview'),
		Headings: App.text('headings'),
		Heading: App.text('heading'),
		Paragraph: App.text('paragraph'),
		Strike: App.text('strikeThrough'),
		Bold: App.text('bold'),
		Italic: App.text('italic'),
		Code: App.text('inlineCode'),
		'Insert CodeBlock': App.text('insertCodeBlock'),
		Blockquote: App.text('blockquote'),
		'Unordered list': App.text('unorderedList'),
		'Ordered list': App.text('orderedList'),
		Line: App.text('insertHorizontalLine'),
		'Insert table': App.text('insertTable'),
		Indent: App.text('indent'),
		Outdent: App.text('outdent'),
	});
};

/**
 * A Markdown editor field.
 *
 * @see {@link tui-editor https://github.com/nhn/tui.editor/tree/master/apps/editor}
 * @extends BaseFieldComponent
 */
class MarkdownFieldComponent extends BaseFieldComponent {
	/**
	 * The editor instance.
	 */
	private editor: Editor;

	/**
	 * Don't link the label.
	 */
	protected linkLabel = false;

	/**
	 * The editor value that serves a input value for the parent form.
	 */
	value: string;

	/**
	 * Create a custom toolbar button.
	 *
	 * @param icon
	 * @param label
	 * @param onClick
	 * @returns the toolbar item options
	 */
	private createCustomButton(
		icon: string,
		label: string,
		onClick: (event: Event) => void
	): ToolbarCustomOptions {
		const el = create('button', ['toastui-editor-toolbar-icons', icon], {});

		this.listen(el, 'click', onClick);

		return {
			el,
			name: label,
			tooltip: label,
			onUpdated({ disabled }: { disabled: boolean }) {
				disabled
					? el.setAttribute('disabled', '')
					: el.removeAttribute('disabled');
			},
		};
	}

	/**
	 * Create an input field.
	 */
	createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const container = create(
			'div',
			[CSS.mdEditor, CSS.contents],
			{ id },
			this
		);

		this.setAttribute('name', name);
		this.value = value as string;

		const linkBindingName = `markdownComponents_link_${id}`;

		const imageSelection = this.createCustomButton(
			'image',
			App.text('insertImage'),
			() => {
				createImagePickerModal((value) => {
					if (value) {
						this.editor.insertText(`\r\n![](${value})`);
					}
				}, App.text('insertImage'));
			}
		);

		const linkSelection = this.createCustomButton(
			'link',
			App.text('insertLink'),
			() => {
				createLinkModal(linkBindingName, 'insertLink');
			}
		);

		setMdEditorLanguage();

		this.editor = new Editor({
			el: container,
			initialValue: value as string,
			autofocus: false,
			usageStatistics: false,
			height: 'auto',
			hideModeSwitch: true,
			initialEditType: 'markdown',
			previewStyle: 'tab',
			placeholder: placeholder as string,
			previewHighlight: true,
			toolbarItems: [
				['heading', 'bold', 'italic', 'strike', 'code', 'quote'],
				['ul', 'ol', 'indent', 'outdent'],
				['table', imageSelection, linkSelection, 'hr', 'codeblock'],
			],
			customHTMLRenderer: htmlRenderer,
			events: {
				change: () => {
					this.value = this.editor.getMarkdown();
					fire('input', this);
				},
				focus: () => {
					container.classList.add(CSS.mdEditorFocus);
				},
				blur: () => {
					container.classList.remove(CSS.mdEditorFocus);
				},
			},
			plugins: [[codeSyntaxHighlight, { highlighter: Prism }]],
		});

		new Binding(linkBindingName, {
			onChange: (value) => {
				if (value) {
					this.editor.insertText(
						`[${this.editor.getSelectedText() || value}](${value})`
					);
				}
			},
		});

		const tabs = query('.toastui-editor-tabs', this);
		const verticaToggle = create('div', ['tab-item'], {}, tabs);
		const tabItems = queryAll('div', tabs);

		verticaToggle.textContent = App.text('vertical');

		this.listen(
			tabs,
			'click',
			(event: Event) => {
				const target = event.target as HTMLElement;
				let activeIndex = 0;

				tabItems.forEach((item, index) => {
					if (item.isSameNode(target)) {
						activeIndex = index;
					}

					item.classList.toggle('active', item.isSameNode(target));
				});

				this.editor.changePreviewStyle(
					activeIndex === 2 ? 'vertical' : 'tab'
				);

				target.click();
			},
			'div'
		);
	}

	/**
	 * Return the field that is observed for changes.
	 *
	 * @return the input field
	 */
	getValueProvider(): HTMLElement {
		return this;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	query() {
		return this.value;
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	mutate(value: any): void {
		this.editor.setMarkdown(value);
	}
}

FormDataProviders.add(FieldTag.markdown);
customElements.define(FieldTag.markdown, MarkdownFieldComponent);
