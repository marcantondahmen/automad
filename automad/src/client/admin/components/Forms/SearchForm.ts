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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createLabelFromField,
	CSS,
	debounce,
	fire,
	collectFieldData,
	getSearchParam,
	html,
	listen,
	queryAll,
	requestAPI,
	Route,
	SearchController,
} from '@/admin/core';
import { FieldResults, FileResults, KeyValueMap } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

/**
 * Render the inner content for a given field in the results card.
 *
 * @param fieldResultsArray
 * @returns the rendered field result
 */
const renderFieldResults = (fieldResultsArray: FieldResults[]): string => {
	return fieldResultsArray.reduce((output, fieldResults): string => {
		return html`
			${output}
			<div
				class="${CSS.cardListItem} ${CSS.flex} ${CSS.flexColumn} ${CSS.flexGap}"
			>
				<span>
					<span class="${CSS.badge}">
						${createLabelFromField(fieldResults.field)}
					</span>
				</span>
				<span>${fieldResults.context}</span>
			</div>
		`;
	}, '');
};

/**
 * Render a card for all results for a given file.
 *
 * @param fileResults
 * @returns the rendered file card
 */
const renderFileCard = (fileResults: FileResults): string => {
	const { path, fieldResultsArray, url } = fileResults;
	const fields: string[] = fieldResultsArray.reduce(
		(fields, fieldResults): string[] => {
			return fields.concat([fieldResults.field]);
		},
		[]
	);

	return html`
		<div class="${CSS.card}">
			<div class="${CSS.cardList}">
				<div
					class="${CSS.cardListItem} ${CSS.cardListItemFaded} ${CSS.flex} ${CSS.flexBetween} ${CSS.flexAlignCenter}"
				>
					<am-link
						${Attr.target}="${path
							? `${Route.page}?url=${url}`
							: Route.shared}"
						class="${CSS.iconText} ${CSS.textLink}"
					>
						<i class="bi bi-file-earmark-text"></i>
						<span>${url || App.text('sharedTitle')}</span>
					</am-link>
					<am-checkbox
						name="${path}"
						value="${fields.join()}"
					></am-checkbox>
				</div>
				${renderFieldResults(fieldResultsArray)}
			</div>
		</div>
	`;
};

/**
 * The search form.
 *
 * @extends BaseComponent
 */
export class SearchFormComponent extends BaseComponent {
	/**
	 * The search parameter name.
	 *
	 * @static
	 */
	private static SEARCH_PARAM = 'search';

	/**
	 * Get the api attribute already before attributes are observed.
	 */
	protected get api(): string {
		return SearchController.searchReplace;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.flex, CSS.flexGapLarge, CSS.flexColumn);

		const formContainer = create(
			'section',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			this
		);
		const { searchBar, search, isRegex, isCaseSensitive, replace } =
			this.createForm(formContainer);
		const { replaceButton, checkAll, unCheckAll } =
			this.createButtons(formContainer);
		const resultsContainer = create(
			'section',
			[CSS.flex, CSS.flexColumn, CSS.flexGapLarge],
			{},
			this
		);

		const performRequest = async (replaceData: KeyValueMap = {}) => {
			const data: KeyValueMap = Object.assign(
				{
					searchValue: search.value,
					isRegex: isRegex.checked,
					isCaseSensitive: isCaseSensitive.checked,
				},
				replaceData
			);

			const response = await requestAPI(this.api, data);

			this.createResults(response, resultsContainer);
		};

		const performSearch = debounce(async () => {
			const url = new URL(window.location.href);

			url.searchParams.set(
				SearchFormComponent.SEARCH_PARAM,
				search.value
			);
			window.history.replaceState(null, null, url);

			performRequest();
		}, 200);

		const performReplace = () => {
			const files = JSON.stringify(collectFieldData(resultsContainer));

			performRequest({
				replaceValue: replace.value,
				replaceSelected: true,
				files,
			});
		};

		listen(searchBar, 'input', performSearch, 'input');
		listen(replaceButton, 'click', performReplace);

		const toggle = (state: boolean) => {
			queryAll<HTMLInputElement>('input', resultsContainer).forEach(
				(checkbox) => {
					checkbox.checked = state;
					fire('input', checkbox);
				}
			);
		};

		listen(checkAll, 'click', () => {
			toggle(true);
		});

		listen(unCheckAll, 'click', () => {
			toggle(false);
		});

		performSearch();
	}

	/**
	 * Create the list of results.
	 *
	 * @returns the created results list
	 */
	private createResults(response: KeyValueMap, container: HTMLElement): void {
		const fileResultsArray: FileResults[] = response.data;

		if (fileResultsArray.length) {
			container.innerHTML = fileResultsArray.reduce(
				(output, fileResults): string => {
					return html`${output} ${renderFileCard(fileResults)}`;
				},
				''
			);

			return;
		}

		container.innerHTML = !!getSearchParam(SearchFormComponent.SEARCH_PARAM)
			? html`
					<am-alert
						${Attr.icon}="slash-circle"
						${Attr.text}="searchNoResults"
					></am-alert>
				`
			: '';
	}

	/**
	 * Create all form inputs.
	 *
	 * @param container
	 * @returns the created form input elements
	 */
	private createForm(container: HTMLElement): KeyValueMap {
		const searchBar = create('div', [CSS.flex, CSS.flexGap], {}, container);

		const search = create(
			'input',
			[CSS.input],
			{
				type: 'text',
				name: 'searchValue',
				value: getSearchParam(SearchFormComponent.SEARCH_PARAM),
				placeholder: App.text('searchPlaceholder'),
			},
			searchBar
		);

		const isRegex = create(
			'am-custom-icon-checkbox',
			[],
			{
				name: 'isRegex',
				[Attr.icon]: 'regex',
				[Attr.tooltip]: App.text('searchIsRegex'),
			},
			searchBar
		);

		const isCaseSensitive = create(
			'am-custom-icon-checkbox',
			[],
			{
				name: 'isCaseSensitive',
				[Attr.icon]: 'type',
				[Attr.tooltip]: App.text('searchIsCaseSensitive'),
			},
			searchBar
		);

		const replace = create(
			'input',
			[CSS.input],
			{
				type: 'text',
				name: 'replaceValue',
				placeholder: App.text('searchReplacePlaceholder'),
			},
			container
		);

		return {
			searchBar,
			search,
			isRegex,
			isCaseSensitive,
			replace,
		};
	}

	/**
	 * Create all button.
	 *
	 * @param container
	 * @returns the created button elements
	 */
	private createButtons(container: HTMLElement): KeyValueMap {
		const wrapper = create(
			'div',
			[CSS.flex, CSS.flexBetween],
			{},
			container
		);

		const replaceButton = create(
			'span',
			[CSS.button, CSS.buttonPrimary],
			{},
			wrapper
		);

		replaceButton.textContent = App.text('searchReplaceSelected');

		const toggles = create('div', [CSS.formGroup], {}, wrapper);
		const checkAll = create(
			'span',
			[CSS.button, CSS.formGroupItem],
			{},
			toggles
		);

		checkAll.textContent = App.text('searchReplaceCheckAll');

		const unCheckAll = create(
			'span',
			[CSS.button, CSS.formGroupItem],
			{},
			toggles
		);

		unCheckAll.textContent = App.text('searchReplaceUncheckAll');

		return {
			replaceButton,
			checkAll,
			unCheckAll,
		};
	}
}

customElements.define('am-search-form', SearchFormComponent);
