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

import SortableTree, {
	SortableTreeConfirmFunction,
	SortableTreeDropResultData,
	SortableTreeKeyValue,
	SortableTreeNodeComponent,
	SortableTreeOnChangeFunction,
	SortableTreeRenderLabelFunction,
} from 'sortable-tree';
import {
	App,
	Attr,
	create,
	CSS,
	debounce,
	EventName,
	getPageURL,
	html,
	listen,
	notifyError,
	PageController,
	query,
	queryAll,
	requestAPI,
	Route,
} from '@/core';
import { createSortableTreeNodes, treeStyles } from '@/core/tree';
import { KeyValueMap, PageMetaData } from '@/types';
import { BaseComponent } from '@/components/Base';

/**
 * Handle responses when moving pages.
 *
 * @param data
 */
const handleResponse = (data: KeyValueMap): void => {
	if (data.error) {
		notifyError(data.error);
	}

	if (data.reload) {
		App.reload();
	}

	if (data.redirect) {
		App.root.setView(data.redirect);
	}
};

/**
 * The render function that renders the label HTML.
 *
 * @param data
 * @returns the rendered HTML
 */
const renderLabelFunction: SortableTreeRenderLabelFunction = (
	data: SortableTreeKeyValue
): string => {
	const active = data.url == getPageURL() ? CSS.navItemActive : '';
	const icon =
		App.system.i18n && data.url === '/'
			? 'globe'
			: App.system.i18n && data.parentUrl === '/'
			? 'translate'
			: data.private
			? 'eye-slash-fill'
			: 'file-earmark-text';

	return html`
		<span class="${CSS.navItem} ${active}" title="${data.url}">
			<am-link
				class="${CSS.navLink}"
				${Attr.target}="${Route.page}?url=${encodeURIComponent(
					data.url as string
				)}"
			>
				<i class="bi bi-${icon}"></i>
				<span
					>${App.system.i18n && data.parentUrl === '/'
						? `${(data.url as string).replace('/', '')} &mdash; `
						: ''}$${data.title}</span
				>
			</am-link>
		</span>
	`;
};

/**
 * The onChange handler function.
 *
 * @param options
 */
const onChangeFunction: SortableTreeOnChangeFunction = async (
	options: SortableTreeDropResultData
): Promise<void> => {
	const { movedNode, targetParentNode, srcParentNode } = options;
	const newParent = targetParentNode.data as unknown as PageMetaData;
	const oldParent = srcParentNode.data as unknown as PageMetaData;
	const url = movedNode.data.url;
	const layout: string[] = [];
	const draggables = queryAll<SortableTreeNodeComponent>(
		'sortable-tree-node',
		query(NavTreeComponent.TAG_NAME)
	);
	const toggleDraggable = (state: 'true' | 'false') => {
		draggables.forEach((node) => {
			node.setAttribute('draggable', state);
		});
	};

	toggleDraggable('false');

	targetParentNode.subnodesData.forEach((data) => {
		layout.push(data.path as string);
	});

	if (newParent.path != oldParent.path) {
		const lockId = App.addNavigationLock();
		const targetPage = newParent.url;

		const data = await requestAPI(PageController.move, {
			url,
			targetPage,
			layout: JSON.stringify(layout),
		});

		toggleDraggable('true');
		App.removeNavigationLock(lockId);
		handleResponse(data);

		return;
	}

	const lockId = App.addNavigationLock();
	const parentPath = newParent.path;

	const data = await requestAPI(PageController.updateIndex, {
		url,
		parentPath,
		layout: JSON.stringify(layout),
	});

	toggleDraggable('true');
	App.removeNavigationLock(lockId);
	handleResponse(data);
};

/**
 * The confirmation handler function checks whether there is
 * no other ongoing request that blocks the navigation.
 *
 * @param movedNode
 * @param targetParentNode
 * @returns true when the navigation is not blocked by a previous request
 */
const confirmFunction: SortableTreeConfirmFunction = async (
	movedNode,
	targetParentNode
): Promise<boolean> => {
	return !App.navigationIsLocked;
};

/**
 * Return a debaounced function that reveals subnodes
 * when dragging over a node for more than 1200 miliseconds.
 */
const showSubnodesOnDragover = debounce((event: MouseEvent): void => {
	const node = (
		event.target as HTMLElement
	).closest<SortableTreeNodeComponent>('sortable-tree-node');

	node.collapse(false);
}, 1200);

/**
 * The navigation tree component.
 *
 * @example
 * <am-nav-tree></am-nav-tree>
 *
 * @extends BaseComponent
 */
export class NavTreeComponent extends BaseComponent {
	/**
	 * The tag name that is used for the custom web component.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-nav-tree';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.addListener(
			listen(window, EventName.appStateChange, this.init.bind(this))
		);

		this.init();

		listen(this, 'dragenter', showSubnodesOnDragover, 'sortable-tree-node');
	}

	/**
	 * Init the navTree.
	 */
	protected init(): void {
		this.classList.add(CSS.nav);
		this.innerHTML = '';

		const pages = Object.values(App.pages);

		create(
			'span',
			[CSS.navLabel],
			{},
			this,
			html`
				<span class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}">
					${App.text('sidebarPages')} &mdash; ${pages.length}
				</span>
			`
		);

		const current = getPageURL();
		const nodes = createSortableTreeNodes(pages);

		const tree = new SortableTree({
			nodes,
			element: this,
			initCollapseLevel: 1,
			styles: treeStyles,
			stateId: 'nav',
			renderLabel: renderLabelFunction,
			onChange: onChangeFunction,
			confirm: confirmFunction,
		});

		if (current) {
			const currentNode = tree.findNode('url', current);

			currentNode?.reveal();
			currentNode?.scrollIntoView({
				block: 'end',
			});
		}
	}
}

customElements.define(NavTreeComponent.TAG_NAME, NavTreeComponent);
