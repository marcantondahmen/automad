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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { API } from '@/vendor/editorjs';
import {
	App,
	Attr,
	Bindings,
	collectFieldData,
	create,
	createEditor,
	createField,
	createGenericModal,
	createSelect,
	CSS,
	FieldTag,
	html,
	queryAll,
	resolveFileUrl,
	uniqueId,
} from '@/admin/core';
import {
	SectionAlignItemsOption,
	LayoutSectionBlockData,
	SectionJustifyContentOption,
	SectionStyle,
	SectionToolbarRadioOptions,
	SelectComponentOption,
} from '@/admin/types';
import { BaseBlock } from './BaseBlock';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import iconAlignStart from '@/common/svg/flex/align-start.svg';
import iconAlignCenter from '@/common/svg/flex/align-center.svg';
import iconAlignEnd from '@/common/svg/flex/align-end.svg';
import iconAlignStretch from '@/common/svg/flex/align-stretch.svg';
import iconFillRow from '@/common/svg/flex/fill-row.svg';
import iconJustifyStart from '@/common/svg/flex/justify-start.svg';
import iconJustifyCenter from '@/common/svg/flex/justify-center.svg';
import iconJustifyEnd from '@/common/svg/flex/justify-end.svg';
import iconJustifyBetween from '@/common/svg/flex/justify-between.svg';
import iconJustifyEvenly from '@/common/svg/flex/justify-evenly.svg';
import iconGap from '@/common/svg/flex/gap.svg';
import iconMin from '@/common/svg/flex/min.svg';

/**
 * The flexbox option for "justify-content".
 */
export const sectionJustifyContentOptions: SectionToolbarRadioOptions<SectionJustifyContentOption> =
	{
		start: {
			icon: iconJustifyStart,
			tooltip: 'flexJustifyStart',
		},
		center: {
			icon: iconJustifyCenter,
			tooltip: 'flexJustifyCenter',
		},
		end: { icon: iconJustifyEnd, tooltip: 'flexJustifyEnd' },
		'space-between': {
			icon: iconJustifyBetween,
			tooltip: 'flexJustifyBetween',
		},
		'space-evenly': {
			icon: iconJustifyEvenly,
			tooltip: 'flexJustifyEvenly',
		},
		'fill-row': { icon: iconFillRow, tooltip: 'flexFillRow' },
	} as const;

/**
 * The flexbox option for "align-items".
 */
export const sectionAlignItemsOptions: SectionToolbarRadioOptions<SectionAlignItemsOption> =
	{
		start: { icon: iconAlignStart, tooltip: 'flexAlignStart' },
		center: { icon: iconAlignCenter, tooltip: 'flexAlignCenter' },
		end: { icon: iconAlignEnd, tooltip: 'flexAlignEnd' },
		stretch: {
			icon: iconAlignStretch,
			tooltip: 'flexAlignStretch',
		},
	} as const;

/**
 * Background blend modes for the section's background image.
 */
export const sectionBackgroundBlendModes = [
	'normal',
	'multiply',
	'screen',
	'overlay',
	'darken',
	'lighten',
	'color-dodge',
	'color-burn',
	'hard-light',
	'soft-light',
	'difference',
	'exclusion',
	'hue',
	'saturation',
	'color',
	'luminosity',
] as const;

/**
 * Border styles for sections.
 */
export const sectionBorderStyles = [
	'none',
	'solid',
	'dashed',
	'dotted',
	'double',
	'groove',
	'ridge',
] as const;

/**
 * Section style defaults.
 */
export const styleDefaults: SectionStyle = {
	card: false,
	shadow: false,
	color: '',
	backgroundColor: '',
	backgroundBlendMode: 'normal',
	borderColor: '',
	borderWidth: '',
	borderRadius: '',
	borderStyle: 'none',
	backgroundImage: '',
	paddingTop: '',
	paddingBottom: '',
	overflowHidden: false,
} as const;

/**
 * Create a radio button input.
 *
 * @param value
 * @param icon
 * @param title
 * @param selected
 * @param container
 *
 */
const createRadioInput = (
	name: string,
	value: string,
	icon: string,
	tooltip: string,
	selected: string,
	container: HTMLElement
): void => {
	create<HTMLInputElement>(
		'input',
		[],
		{
			type: 'radio',
			name,
			value,
		},
		create(
			'label',
			[
				CSS.editorBlockLayoutSectionRadio,
				...(value === selected
					? [CSS.editorBlockLayoutSectionRadioActive]
					: []),
			],
			{ [Attr.tooltip]: App.text(tooltip) },
			container,
			icon
		)
	).checked = value === selected;
};

/**
 * Toggle the active class on the checked radio.
 *
 * @param wrapper
 */
const toggleActiveRadio = (wrapper: HTMLElement): void => {
	queryAll<HTMLInputElement>('input', wrapper).forEach((input) => {
		input.parentElement.classList.toggle(
			CSS.editorBlockLayoutSectionRadioActive,
			input.checked
		);
	});
};

/**
 * The Layout-Section block that create a new editor inside a parent editor
 * in order to create nested flexbox layouts.
 *
 * @extends BaseBlock
 */
export class LayoutSectionBlock extends BaseBlock<LayoutSectionBlockData> {
	/**
	 * The editor holder element.
	 */
	private holder: EditorJSComponent = null;

	/**
	 * The actual section element.
	 * Must be contained inside the wrapper in order to apply spacing correctly.
	 */
	private section: HTMLElement = null;

	/**
	 * Enable linebreaks.
	 *
	 * @static
	 */
	static get enableLineBreaks() {
		return true;
	}

	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			content: true,
			style: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('layoutSectionBlockTitle'),
			icon: '<i class="bi bi-layout-three-columns"></i>',
		};
	}

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @return the section block data
	 */
	protected prepareData(
		data: LayoutSectionBlockData
	): LayoutSectionBlockData {
		return {
			content: data.content || { blocks: [] },
			style: Object.assign({}, styleDefaults, data.style),
			justify: data.justify || 'start',
			align: data.align || 'start',
			gap: data.gap !== undefined ? data.gap : '',
			minBlockWidth:
				data.minBlockWidth !== undefined ? data.minBlockWidth : '',
		};
	}

	/**
	 * Render the main block element.
	 *
	 * @return the rendered block
	 */
	render(): HTMLElement {
		this.section = create(
			'div',
			this.readOnly ? [] : [CSS.editorBlockLayoutSection],
			{},
			this.wrapper
		);

		if (!this.readOnly) {
			create(
				'span',
				[CSS.flex],
				{},
				this.section,
				html`
					<span class="${CSS.editorBlockLayoutSectionLabel}">
						${LayoutSectionBlock.toolbox.icon}
						<span>${App.text('layoutSectionBlockTitle')}</span>
					</span>
				`
			);
		}

		const renderEditor = async () => {
			this.holder = createEditor(
				this.section,
				this.data.content,
				{
					onChange: async (api: API) => {
						if (this.readOnly) {
							return;
						}

						const { blocks } = await api.saver.save();

						this.data.content = { blocks };
						this.blockAPI.dispatchChange();
					},
				},
				true,
				this.readOnly
			);

			await this.holder.editor.isReady;

			if (!this.readOnly) {
				this.listen(this.holder, 'paste', (event: Event) => {
					event.stopPropagation();
				});

				this.renderToolbar();
			}

			this.setStyle();
		};

		renderEditor();

		return this.wrapper;
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): LayoutSectionBlockData {
		return this.data;
	}

	/**
	 * Render the layout toolbox and append it to the main wrapper.
	 */
	private renderToolbar(): void {
		const toolbar = create(
			'div',
			[CSS.editorBlockLayoutSectionToolbar],
			{},
			this.section
		);

		const styleTools = create(
			'div',
			[CSS.editorBlockLayoutSectionToolbarSection],
			{},
			toolbar
		);

		const justifyTools = create(
			'div',
			[CSS.editorBlockLayoutSectionToolbarSection],
			{},
			toolbar
		);

		const alignTools = create(
			'div',
			[CSS.editorBlockLayoutSectionToolbarSection],
			{},
			toolbar
		);

		const sizeTools = create(
			'div',
			[CSS.editorBlockLayoutSectionToolbarSection],
			{},
			toolbar
		);

		this.renderStylesButton(styleTools);
		this.renderJustifySelect(justifyTools);
		this.renderAlignSelect(alignTools);
		this.renderNumberUnitInput(sizeTools, 'gap', iconGap);
		this.renderNumberUnitInput(sizeTools, 'minBlockWidth', iconMin);

		// Add this hidden input in order to catch the focus after a block has been dragged around.
		create('input', [CSS.displayNone], {}, toolbar);

		this.listen(toolbar, 'change', () => {
			this.setStyle();
		});

		const setToolbarPosition = () => {
			toolbar.style.transform = 'translateX(0)';

			setTimeout(() => {
				const rect = toolbar.getBoundingClientRect();
				const docWidth = document.body.offsetWidth;

				if (docWidth < rect.right) {
					const shift = docWidth - rect.right - 10;

					toolbar.style.transform = `translateX(${shift}px)`;
				}
			}, 0);
		};

		this.listen(this.wrapper, 'click', (event: Event) => {
			event.stopPropagation();

			queryAll(`.${CSS.editorBlockLayoutSectionToolbar}`).forEach(
				(_toolbar) => {
					_toolbar.classList.toggle(CSS.active, _toolbar == toolbar);
				}
			);

			setToolbarPosition();
		});

		this.listen(document, 'click', (event: Event) => {
			const target = event.target as HTMLElement;

			if (this.wrapper.contains(target)) {
				return;
			}

			toolbar.classList.remove(CSS.active);
		});
	}

	/**
	 * Render the styles button.
	 *
	 * @param toolbar
	 */
	private renderStylesButton(toolbar: HTMLElement): void {
		const button = create(
			'am-modal-toggle',
			[CSS.button, CSS.buttonIcon, CSS.buttonPrimary],
			{ [Attr.tooltip]: App.text('editStyle') },
			toolbar,
			'<i class="bi bi-palette2"></i>'
		);

		this.listen(button, 'click', () => {
			this.renderStylesModal();
		});
	}

	/**
	 * Render the justify setting select button.
	 *
	 * @param toolbar
	 */
	private renderJustifySelect(toolbar: HTMLElement): void {
		const wrapper = create(
			'div',
			[CSS.editorBlockLayoutSectionRadios],
			{},
			toolbar
		);

		for (const [value, { icon, tooltip }] of Object.entries(
			sectionJustifyContentOptions
		)) {
			createRadioInput(
				'justify',
				value,
				icon,
				tooltip,
				this.data.justify,
				wrapper
			);
		}

		this.listen(wrapper, 'change', () => {
			const { justify } = collectFieldData(wrapper);

			this.data.justify = justify as SectionJustifyContentOption;
			this.blockAPI.dispatchChange();

			toggleActiveRadio(wrapper);
		});
	}

	/**
	 * Render the align setting select button.
	 *
	 * @param toolbar
	 */
	private renderAlignSelect(toolbar: HTMLElement): void {
		const wrapper = create(
			'div',
			[CSS.editorBlockLayoutSectionRadios],
			{},
			toolbar
		);

		for (const [value, { icon, tooltip }] of Object.entries(
			sectionAlignItemsOptions
		)) {
			createRadioInput(
				'align',
				value,
				icon,
				tooltip,
				this.data.align,
				wrapper
			);
		}

		this.listen(wrapper, 'change', () => {
			const { align } = collectFieldData(wrapper);

			this.data.align = align as SectionAlignItemsOption;
			this.blockAPI.dispatchChange();

			toggleActiveRadio(wrapper);
		});
	}

	/**
	 * Render a number unit input.
	 *
	 * @param toolbar
	 * @param key
	 * @param icon
	 */
	private renderNumberUnitInput(
		toolbar: HTMLElement,
		key: 'gap' | 'minBlockWidth',
		icon: string
	): void {
		const group = create(
			'span',
			[CSS.formGroup],
			{},
			toolbar,
			html`
				<span class="${CSS.formGroupItem} ${CSS.formGroupIcon}">
					${icon}
				</span>
			`
		);

		const input = create(
			'am-number-unit-input',
			[CSS.formGroupItem],
			{ value: this.data[key] },
			group
		);

		this.listen(input, 'change', () => {
			this.data[key] = input.value;
			this.blockAPI.dispatchChange();
		});
	}

	/**
	 * Render the styles modal and append it to root.
	 */
	private renderStylesModal(): void {
		const { modal, body } = createGenericModal(App.text('editStyle'));

		const field = (
			type: FieldTag,
			name: keyof SectionStyle,
			text: string,
			parent: HTMLElement
		): void => {
			createField(type, parent, {
				name: name,
				value: this.data.style[name],
				key: uniqueId(),
				label: App.text(text),
			});
		};

		field(FieldTag.toggle, 'card', 'optimizeContentForCards', body);

		const group1 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		field(FieldTag.toggle, 'overflowHidden', 'overflowHidden', group1);
		field(FieldTag.toggle, 'shadow', 'addShadow', group1);
		const group2 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		field(FieldTag.color, 'color', 'textColor', group2);
		field(FieldTag.color, 'backgroundColor', 'backgroundColor', group2);

		const group3 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		const borderStyleId = uniqueId();
		const borderStyle = create(
			'div',
			[CSS.field],
			{},
			group3,
			html`<div>
				<label for="${borderStyleId}" class="${CSS.fieldLabel}">
					${App.text('borderStyle')}
				</label>
			</div>`
		);

		createSelect(
			sectionBorderStyles.reduce(
				(
					res: SelectComponentOption[],
					style: string
				): SelectComponentOption[] => {
					return [...res, { value: style }];
				},
				[]
			),
			this.data.style.borderStyle,
			borderStyle,
			'borderStyle',
			borderStyleId
		);

		field(FieldTag.color, 'borderColor', 'borderColor', group3);

		field(FieldTag.image, 'backgroundImage', 'backgroundImage', body);

		const blendModeId = uniqueId();
		const blendMode = create(
			'div',
			[CSS.field],
			{},
			body,
			html`
				<div>
					<label for="${blendModeId}" class="${CSS.fieldLabel}">
						Background Blendmode
					</label>
				</div>
			`
		);

		createSelect(
			sectionBackgroundBlendModes.reduce(
				(
					res: SelectComponentOption[],
					mode: string
				): SelectComponentOption[] => {
					return [...res, { value: mode }];
				},
				[]
			),
			this.data.style.backgroundBlendMode,
			blendMode,
			'backgroundBlendMode',
			blendModeId
		);

		const group4 = create('div', [CSS.grid, CSS.gridAuto], {}, body);
		field(FieldTag.numberUnit, 'borderWidth', 'borderWidth', group4);
		field(FieldTag.numberUnit, 'borderRadius', 'borderRadius', group4);
		const group5 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		field(FieldTag.numberUnit, 'paddingTop', 'paddingTop', group5);
		field(FieldTag.numberUnit, 'paddingBottom', 'paddingBottom', group5);

		Bindings.connectElements(body);

		this.listen(body, 'change', () => {
			this.data.style = collectFieldData(body) as SectionStyle;
			this.setStyle();
			this.blockAPI.dispatchChange();
		});

		setTimeout(() => {
			modal.open();
		});
	}

	/**
	 * Set the section styles.
	 */
	private setStyle(): void {
		const { style, gap, justify, align, minBlockWidth } = this.data;
		const baseClass = CSS.editorStyleBase;
		const classes: string[] = [CSS.editorBlockLayoutSectionEditor];

		classes.push(`${baseClass}--justify-${justify}`);
		classes.push(`${baseClass}--align-${align}`);

		if (style.card) {
			classes.push(`${baseClass}--card`);
		}

		if (style.shadow) {
			classes.push(`${baseClass}--shadow`);
		}

		if (style.overflowHidden) {
			classes.push(`${baseClass}--overflow-hidden`);
		}

		this.holder.className = classes.join(' ');

		const inline: string[] = [];

		[
			'color',
			'backgroundColor',
			'backgroundBlendMode',
			'borderColor',
			'borderWidth',
			'borderRadius',
			'borderStyle',
			'paddingTop',
			'paddingBottom',
		].forEach((prop: string) => {
			const value = style[prop as keyof SectionStyle];

			if (value.toString().length) {
				inline.push(`--${prop}: ${value};`);
			}
		});

		if (style['backgroundImage']) {
			const url = resolveFileUrl(style['backgroundImage']);

			inline.push(`--backgroundImage: url(${url});`);
		} else {
			inline.push(`--backgroundImage: none;`);
		}

		if (gap) {
			inline.push(`--gap: ${gap};`);
		}

		if (minBlockWidth) {
			inline.push(`--minBlockWidth: ${minBlockWidth};`);
		}

		this.holder.setAttribute('style', inline.join(' '));
	}
}
