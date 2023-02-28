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
	createField,
	createSelect,
	CSS,
	html,
	listen,
	query,
	queryAll,
} from '../../../../core';
import {
	EditorOutputData,
	FieldType,
	SectionBlockData,
	SectionJustifyContentOption,
	SectionStyle,
	SelectComponentOption,
} from '../../../../types';
import { BaseBlock } from './BaseBlock';
import { createEditor } from '../../../../core/editor';
import { ModalComponent } from '../../../Modal/Modal';
import { uniqueId } from '../../../../core';
import iconFlexGap from '../../../../svg/icons/flex-gap.svg';
import iconMinWidth from '../../../../svg/icons/min-width.svg';
import iconFlexJustyifyContent from '../../../../svg/icons/flex-justify-content.svg';

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
	borderWidth: '',
	borderRadius: '',
	backgroundImage: '',
	paddingTop: '',
	paddingBottom: '',
	overflowHidden: false,
} as const;

/**
 * The Section block that create a new editor inside a parent editor
 * in order to create nested flexbox layouts.
 *
 * @extends BaseBlock
 */
export class SectionBlock extends BaseBlock<SectionBlockData> {
	private holder: HTMLElement = null;

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
			title: 'Section',
			icon: '<i class="bi bi-plus-square-dotted"></i>',
		};
	}

	protected prepareData(data: SectionBlockData): SectionBlockData {
		return {
			content: data.content || {},
			style: Object.assign({}, styleDefaults, data.style),
			justify: data.justify || 'start',
			gap: data.gap !== undefined ? data.gap : '',
			minBlockWidth:
				data.minBlockWidth !== undefined ? data.minBlockWidth : '',
		};
	}

	render(): HTMLElement {
		this.wrapper.classList.add(CSS.editorBlockSection);
		this.holder = create('div', [], {}, this.wrapper);

		this.renderFlexSettings();
		this.setStyle();

		const editor = createEditor(
			this.data.content as EditorOutputData,
			{
				holder: this.holder,
				onChange: async (api, event) => {
					this.data.content = await editor.save();
					this.blockAPI.dispatchChange();
				},
			},
			true
		);

		listen(this.wrapper, 'keydown', (event: KeyboardEvent) => {
			if (event.key === 'Tab' || event.keyCode === 9) {
				event.stopImmediatePropagation();
			}
		});

		return this.wrapper;
	}

	save(): SectionBlockData {
		return this.data;
	}

	private renderFlexSettings(): void {
		const settings = create(
			'div',
			[CSS.editorBlockSectionSettings],
			{},
			this.wrapper
		);

		this.renderStylesModal(settings);
		this.renderJustifySelect(settings);
		this.renderNumberUnitInput(settings, 'gap', iconFlexGap);
		this.renderNumberUnitInput(settings, 'minBlockWidth', iconMinWidth);

		listen(settings, 'change', () => {
			this.setStyle();
		});

		listen(this.holder, 'click', (event: Event) => {
			event.stopPropagation();

			queryAll(`.${CSS.editorBlockSectionSettings}`).forEach(
				(_settings) => {
					_settings.classList.toggle(
						CSS.active,
						_settings == settings
					);
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
			[CSS.formGroup, CSS.flex],
			{},
			toolbar,
			html`
				<span class="${CSS.formGroupItem} ${CSS.formGroupIcon} "
					>${iconFlexJustyifyContent}</span
				>
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
			App.root,
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
			type: FieldType,
			name: keyof SectionStyle,
			text: string,
			parent: HTMLElement
		): void => {
			createField(type, parent, {
				name: name,
				value: this.data.style[name],
				key: uniqueId(''),
				label: App.text(text),
			});
		};

		field('am-toggle', 'card', 'optimizeContentForCards', body);
		field('am-toggle', 'matchRowHeight', 'matchRowHeight', body);

		const group1 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		field('am-toggle', 'overflowHidden', 'overflowHidden', group1);
		field('am-toggle', 'shadow', 'addShadow', group1);
		const group2 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		field('am-color', 'color', 'textColor', group2);
		field('am-color', 'backgroundColor', 'backgroundColor', group2);
		field('am-color', 'borderColor', 'borderColor', group2);

		field('am-image-select', 'backgroundImage', 'backgroundImage', body);

		const blendModeId = uniqueId();
		const blendMode = create(
			'div',
			[CSS.field],
			{},
			body,
			html`<label for="${blendModeId}">Background Blendmode</label>`
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

		const group3 = create('div', [CSS.grid, CSS.gridAuto], {}, body);
		field('am-number-unit', 'borderWidth', 'borderWidth', group3);
		field('am-number-unit', 'borderRadius', 'borderRadius', group3);
		const group4 = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		field('am-number-unit', 'paddingTop', 'paddingTop', group4);
		field('am-number-unit', 'paddingBottom', 'paddingBottom', group4);

		Bindings.connectElements(body);

		listen(button, 'click', () => {
			this.data.style = collectFieldData(body) as SectionStyle;
			this.setStyle();
			this.blockAPI.dispatchChange();

			modal.close();
		});
	}

	private setStyle(): void {
		const { style, gap, justify, minBlockWidth } = this.data;
		const baseClass = CSS.editorLayoutBase;
		const classes: string[] = [CSS.editorBlockSectionEditor];

		classes.push(`${baseClass}--justify-${justify}`);

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
			'backgroundImage',
			'backgroundBlendMode',
			'borderColor',
			'borderWidth',
			'borderRadius',
			'paddingTop',
			'paddingBottom',
		].forEach((prop: string) => {
			const value = style[prop as keyof SectionStyle];

			if (value) {
				inline.push(`--${prop}: ${value};`);
			}
		});

		if (gap) {
			inline.push(`--gap: ${gap};`);
		}

		if (minBlockWidth) {
			inline.push(`--minBlockWidth: ${minBlockWidth};`);
		}

		this.holder.setAttribute('style', inline.join(' '));
	}
}
