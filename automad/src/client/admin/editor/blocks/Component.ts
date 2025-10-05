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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { ModalComponent } from '@/admin/components/Modal/Modal';
import EditorJS from 'automad-editorjs';
import {
	App,
	Attr,
	create,
	createSelect,
	CSS,
	EventName,
	getComponentTargetContainer,
	html,
	query,
	Route,
} from '@/admin/core';
import { ComponentBlockData } from '@/admin/types';
import { getBlockTools } from '../blocks';
import { BaseBlock } from './BaseBlock';
import { baseTunes, getBlockTunes } from '../tunes';
import { convertLegacyBlocks } from '../utils';

const getComponent = (id: string) => {
	return App.components.find((c) => c.id === id);
};

export class ComponentBlock extends BaseBlock<ComponentBlockData> {
	/**
	 * Sanitizer rules
	 */
	static get sanitize() {
		return {
			id: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('componentBlockTitle'),
			icon: '<i class="bi bi-boxes"></i>',
		};
	}

	/**
	 * The editor instance.
	 */
	private editor: EditorJS;

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: ComponentBlockData): ComponentBlockData {
		return { id: data.id || '' };
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		if (!this.data.id) {
			return this.wrapper;
		}

		this.renderWrapper();

		return this.wrapper;
	}

	/**
	 * Render the actual wrapper content.
	 */
	private renderWrapper(): void {
		const component = getComponent(this.data.id ?? '');

		if (!component) {
			return;
		}

		this.wrapper.classList.add(
			CSS.editorBlockComponent,
			CSS.userSelectNone
		);

		this.wrapper.innerHTML = html`
			<div class="${CSS.editorBlockComponentLabel}">
				${ComponentBlock.toolbox.icon}
				<span>${component.name}</span>
			</div>
			<div class="${CSS.editorBlockComponentOverlay}">
				<am-link
					class="${CSS.button} ${CSS.buttonPrimary}"
					${Attr.target}="${Route.components}"
				>
					${App.text('openComponentEditor')}
				</am-link>
			</div>
		`;

		this.editor = new EditorJS({
			data: convertLegacyBlocks({ blocks: component.blocks }),
			holder: this.wrapper,
			minHeight: 0,
			autofocus: false,
			tools: { ...getBlockTools(true), ...getBlockTunes(false) },
			inlineToolbar: [],
			tunes: baseTunes,
			readOnly: true,
		});
	}

	/**
	 * Called when block is added.
	 */
	appendCallback(): void {
		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{},
			getComponentTargetContainer(),
			html`
				<am-modal-dialog>
					<am-modal-header>
						${App.text('selectComponent')}
					</am-modal-header>
					<am-modal-body></am-modal-body>
					<am-modal-footer>
						<a
							href="${App.dashboardURL}/${Route.components}"
							class="${CSS.button}"
						>
							${App.text('openComponentEditor')}
						</a>
						<button class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('add')}
						</button>
					</am-modal-footer>
				</am-modal-dialog>
			`
		) as ModalComponent;

		const body = query('am-modal-body', modal);
		const button = query('am-modal-footer button', modal);

		const options = App.components.map((component) => {
			return {
				value: component.id,
				text: component.name,
			};
		});

		if (options.length > 0) {
			const select = createSelect(options, options[0].value, body);

			this.listen(button, 'click', () => {
				this.data.id = select.value;
				modal.close();
			});
		} else {
			button.setAttribute('disabled', '');
			body.textContent = App.text('noComponentsFound');
		}

		modal.open();

		this.listen(modal, EventName.modalClose, () => {
			if (!this.data.id) {
				this.api.blocks.delete(
					this.api.blocks.getBlockIndex(this.blockAPI.id)
				);

				return;
			}

			this.renderWrapper();
		});
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): ComponentBlockData {
		return this.data;
	}

	/**
	 * Clean up on destroy.
	 */
	destroy(): void {
		this.editor.destroy();

		super.destroy();
	}
}
