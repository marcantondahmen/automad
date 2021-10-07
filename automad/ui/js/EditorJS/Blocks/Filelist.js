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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadBlockFilelist {
	static get isReadOnlySupported() {
		return true;
	}

	static get sanitize() {
		return {
			file: false,
			glob: false,
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('filelist_toolbox'),
			icon: '<svg width="16px" height="18px" viewBox="0 0 16 18"><path d="M14.52,7.02c-1.57-1.57-0.99-0.99-2.5-2.5C10.89,3.39,10.25,3,8,3C7.5,3,7.19,3,7,3C4.79,3,3,4.79,3,7v7c0,2.21,1.79,4,4,4 h5c2.21,0,4-1.79,4-4c0,0,0-1.02,0-3C16,8.75,15.67,8.17,14.52,7.02z M14,14c0,1.1-0.9,2-2,2H7c-1.1,0-2-0.9-2-2V7c0-1.1,0.9-2,2-2 h2v1c0,2.21,1.79,4,4,4h1V14z"/><path d="M6,2h3.5C9.37,1.87,9.21,1.71,9.02,1.52C7.89,0.39,7.25,0,5,0C4.5,0,4.19,0,4,0C1.79,0,0,1.79,0,4v7 c0,1.48,0.81,2.75,2,3.45V11V4v0c0-1.1,0.9-2,2-2H6z"/></svg>',
		};
	}

	constructor({ data }) {
		var create = Automad.Util.create,
			t = AutomadEditorTranslation.get;

		this.data = {
			file: data.file || '',
			glob: data.glob || '*.*',
		};

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadBlockFilelist.toolbox.icon}</div>
			<div class="am-block-title">${AutomadBlockFilelist.toolbox.title}</div>
			<hr>
			${
				create.label(t('filelist_template'), [
					'am-block-label',
					'uk-margin-top-remove',
				]).outerHTML
			}
			<div class="am-block-file-select uk-form-select uk-button uk-text-left uk-width-1-1" data-uk-form-select>
				<i class="uk-icon-file"></i>&nbsp;
				<span></span>
				${
					create.select(
						['am-block-file'],
						[t('filelist_default')].concat(
							window.AutomadBlockTemplates.filelist
						),
						this.data.file
					).outerHTML
				}
			</div>
			${create.label(t('filelist_files')).outerHTML}
			${
				create.editable(
					['cdx-input', 'am-block-glob'],
					'*.pdf, *.zip',
					this.data.glob
				).outerHTML
			}
		`;

		this.inputs = {
			file: this.wrapper.querySelector('.am-block-file'),
			glob: this.wrapper.querySelector('.am-block-glob'),
		};
	}

	render() {
		return this.wrapper;
	}

	save() {
		var stripNbsp = Automad.Util.stripNbsp;

		return Object.assign(this.data, {
			file: this.inputs.file.value,
			glob: stripNbsp(this.inputs.glob.innerHTML),
		});
	}
}
