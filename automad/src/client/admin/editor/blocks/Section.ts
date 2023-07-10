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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	Bindings,
	collectFieldData,
	create,
	createEditor,
	createField,
	createSelect,
	CSS,
	FieldTag,
	html,
	listen,
	query,
	queryAll,
	resolveFileUrl,
	uniqueId,
} from '@/core';
import {
	EditorOutputData,
	SectionAlignItemsOption,
	SectionBlockData,
	SectionJustifyContentOption,
	SectionStyle,
	SelectComponentOption,
} from '@/types';
import { BaseBlock } from './BaseBlock';
import iconFlexGap from '@/svg/icons/flex-gap.svg';
import iconMinWidth from '@/svg/icons/min-width.svg';
import iconFlexJustyifyContent from '@/svg/icons/flex-justify-content.svg';
import iconFlexAlignItems from '@/svg/icons/flex-align-items.svg';
import { EditorJSComponent } from '@/components/Editor/EditorJS';
import { ModalComponent } from '@/components/Modal/Modal';
import { EditorPortalComponent } from '@/components/Editor/EditorPortal';

/**
 * The flexbox option for "justify-content".
 */
export const SectionJustifyContentOptions = {
	start: 'Start',
	end: 'End',
	center: 'Center',
	'space-between': 'Space Between',
	'space-evenly': 'Space Evenly',
	'fill-row': 'Fill Row',
} as const;

/**
 * The flexbox option for "align-items".
 */
export const SectionAlignItemsOptions = {
	normal: 'Normal',
	stretch: 'Stretch',
	center: 'Center',
	start: 'Start',
	end: 'End',
} as const;

/**
 * Background blend modes for the section's background image.
 */
export const SectionBackgroundBlendModes = [
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
export const SectionBorderStyles = [
	'solid',
	'dashed',
	'dotted',
	'double',
	'groove',
	'ridge',
];

/**
 * Section style defaults.
 */
export const styleDefaults: SectionStyle = {
	card: false,
	shadow: false,
	matchRowHeight: false,
	color: '',
	backgroundColor: '',
	backgroundBlendMode: 'normal',
	borderColor: '',
	borderWidth: '0',
	borderRadius: '0',
	borderStyle: 'solid',
	backgroundImage: '',
	paddingTop: '0',
	paddingBottom: '0',
	overflowHidden: false,
} as const;

/**
 * Section style defaults that are used for the editor UI.
 */
const editorStyleDefaults = Object.assign({}, styleDefaults, {
	color: 'inherit',
	backgroundColor: 'transparent',
	borderColor: 'transparent',
});

/**
 * The Section block that create a new editor inside a parent editor
 * in order to create nested flexbox layouts.
 *
 * @extends BaseBlock
 */
export class SectionBlock extends BaseBlock<SectionBlockData> {
	private holder: EditorJSComponent = null;

	static get enableLineBreaks() {
		return true;
	}

	static get sanitize() {
		return {
			content: true,
			style: false,
		};
	}

	static get toolbox() {
		return {
			title: App.text('editorBlockSection'),
			icon: '<i class="bi bi-plus-square-dotted"></i>',
		};
	}

	protected prepareData(data: SectionBlockData): SectionBlockData {
		return {
			content: data.content || {},
			style: Object.assign({}, styleDefaults, data.style),
			justify: data.justify || 'start',
			align: data.align || 'normal',
			gap: data.gap !== undefined ? data.gap : '',
			minBlockWidth:
				data.minBlockWidth !== undefined ? data.minBlockWidth : '',
		};
	}

	render(): HTMLElement {
		this.wrapper.classList.add(CSS.editorBlockSection);

		create(
			'span',
			[CSS.flex],
			{},
			this.wrapper,
			html`
				<span class="${CSS.editorBlockSectionLabel}">
					${App.text('editorBlockSection')}
				</span>
			`
		);

		const portal = create(
			EditorPortalComponent.TAG_NAME,
			[],
			{},
			this.wrapper
		);

		this.holder = createEditor(
			portal,
			this.data.content as EditorOutputData,
			{
				onChange: async (api, event) => {
					const { blocks } = await api.saver.save();

					this.data.content = { blocks };
					this.blockAPI.dispatchChange();
				},
			},
			true
		);

		this.renderToolbar();
		this.setStyle();

		return this.wrapper;
	}

	save(): SectionBlockData {
		return this.data;
	}

	private renderToolbar(): void {
		const toolbar = create(
			'div',
			[CSS.editorBlockSectionToolbar],
			{},
			this.wrapper
		);

		this.renderStylesModal(toolbar);
		this.renderJustifySelect(toolbar);
		this.renderAlignSelect(toolbar);
		this.renderNumberUnitInput(toolbar, 'gap', iconFlexGap);
		this.renderNumberUnitInput(toolbar, 'minBlockWidth', iconMinWidth);

		listen(toolbar, 'change', () => {
			this.setStyle();
		});

		listen(this.wrapper, 'click', (event: Event) => {
			event.stopPropagation();

			queryAll(`.${CSS.editorBlockSectionToolbar}`).forEach(
				(_toolbar) => {
					_toolbar.classList.toggle(CSS.active, _toolbar == toolbar);
				}
			);
		});
	}

	private renderJustifySelect(toolbar: HTMLElement): void {
		const justifySelectOptions = Object.keys(
			SectionJustifyContentOptions
		).reduce((result, key: SectionJustifyContentOption) => {
			result.push({
				text: SectionJustifyContentOptions[key],
				value: key,
			});

			return result;
		}, []);
		const formGroup = create(
			'span',
			[CSS.formGroup],
			{},
			toolbar,
			html`
				<span class="${CSS.formGroupItem} ${CSS.formGroupIcon}">
					${iconFlexJustyifyContent}
				</span>
			`
		);

		const justify = createSelect(
			justifySelectOptions,
			this.data.justify,
			formGroup,
			null,
			null,
			'',
			[CSS.formGroupItem]
		);

		listen(justify.select, 'change', () => {
			this.data.justify = justify.value as SectionJustifyContentOption;
			this.blockAPI.dispatchChange();
		});
	}

	private renderAlignSelect(toolbar: HTMLElement): void {
		const alignSelectOptions = Object.keys(SectionAlignItemsOptions).reduce(
			(result, key: SectionAlignItemsOption) => {
				result.push({
					text: SectionAlignItemsOptions[key],
					value: key,
				});

				return result;
			},
			[]
		);
		const formGroup = create(
			'span',
			[CSS.formGroup],
			{},
			toolbar,
			html`
				<span class="${CSS.formGroupItem} ${CSS.formGroupIcon}">
					${iconFlexAlignItems}
				</span>
			`
		);

		const align = createSelect(
			alignSelectOptions,
			this.data.align,
			formGroup,
			null,
			null,
			'',
			[CSS.formGroupItem]
		);

		listen(align.select, 'change', () => {
			this.data.align = align.value as SectionAlignItemsOption;
			this.blockAPI.dispatchChange();
		});
	}

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

		listen(input, 'change', () => {
			this.data[key] = input.value;
			this.blockAPI.dispatchChange();
		});
	}

	private renderStylesModal(toolbar: HTMLElement): void {
		const id = uniqueId();

		create(
			'am-modal-toggle',
			[CSS.button, CSS.buttonIcon, CSS.buttonAccent],
			{ [Attr.modal]: `#${id}`, [Attr.tooltip]: App.text('editStyle') },
			toolbar,
			'<i class="bi bi-palette2"></i>'
		);

		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{ id },
			this.wrapper,
			html`
				<div class="${CSS.modalDialog}">
					<div class="${CSS.modalHeader}">
						<span>${App.text('editStyle')}</span>
						<am-modal-close
							class="${CSS.modalClose}"
						></am-modal-close>
					</div>
					<div class="${CSS.modalBody}"></div>
					<div class="${CSS.modalFooter}">
						<button class="${CSS.button} ${CSS.buttonAccent}">
							${App.text('ok')}
						</button>
					</div>
				</div>
			`
		);

		const body = query(`.${CSS.modalBody}`, modal);
		const button = query(`.${CSS.modalFooter} button`, modal);

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
		field(FieldTag.toggle, 'matchRowHeight', 'matchRowHeight', body);

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
			SectionBorderStyles.reduce(
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

		field(FieldTag.imageSelect, 'backgroundImage', 'backgroundImage', body);

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
			SectionBackgroundBlendModes.reduce(
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

		listen(button, 'click', () => {
			this.data.style = collectFieldData(body) as SectionStyle;
			this.setStyle();
			this.blockAPI.dispatchChange();

			modal.close();
		});
	}

	private setStyle(): void {
		const { style, gap, justify, align, minBlockWidth } = this.data;
		const baseClass = CSS.editorStyleBase;
		const classes: string[] = [CSS.editorBlockSectionEditor];

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

		if (style.matchRowHeight) {
			classes.push(`${baseClass}--match-height`);
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

			inline.push(
				`--${prop}: ${
					value || editorStyleDefaults[prop as keyof SectionStyle]
				};`
			);
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
