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
//
// import { BaseComponent } from '@/components/Base';
// import {
// 	Attr,
// 	create,
// 	debounce,
// 	EventName,
// 	fire,
// 	listen,
// 	query,
// 	queryAll,
// 	queryParents,
// 	uniqueId,
// } from '@/core';
// import { EditorJSComponent } from './EditorJS';
// import { ModalFieldComponent } from '../Modal/ModalField';
// import { Listener } from '@/types';
//
// /**
//  * Get the most outer root block editor that is itself not a section.
//  *
//  * @param element
//  * @return the EditorJSComponent
//  */
// const getRootEditor = (element: HTMLElement): EditorJSComponent => {
// 	const editors = queryParents<EditorJSComponent>(
// 		EditorJSComponent.TAG_NAME,
// 		element
// 	);
//
// 	return editors.pop();
// };
//
// /**
//  * Garbage collect portal destination where the related portal no longer exists.
//  */
// const garbageCollectDestinations = (): void => {
// 	setTimeout(() => {
// 		const destinations = queryAll(
// 			EditorPortalDestinationComponent.TAG_NAME
// 		);
//
// 		destinations.forEach((destination) => {
// 			const portalId = destination.getAttribute(Attr.portal);
// 			const portal = query(`#${portalId}`);
//
// 			if (!portal) {
// 				destination.remove();
// 			}
// 		});
// 	}, 5000);
// };
//
// /**
//  * A editor portal component.
//  *
//  * @extends BaseComponent
//  */
// export class EditorPortalComponent extends BaseComponent {
// 	/**
// 	 * The components tag name.
// 	 *
// 	 * @static
// 	 */
// 	static TAG_NAME = 'am-editor-portal';
//
// 	/**
// 	 * The most outer parent EditorJSComponent that is itself not a section.
// 	 */
// 	private rootEditor: EditorJSComponent;
//
// 	/**
// 	 * The portal destination.
// 	 */
// 	private destination: HTMLElement;
//
// 	/**
// 	 * The mutation observer that is used for synchronizing the portal and its destination.
// 	 */
// 	private observer: MutationObserver;
//
// 	/**
// 	 * The change listener that listens to changes in other portals.
// 	 */
// 	private changeListener: Listener;
//
// 	/**
// 	 * The callback function used when an element is created in the DOM.
// 	 */
// 	connectedCallback(): void {
// 		if (!this.isConnected) {
// 			return;
// 		}
//
// 		this.rootEditor = getRootEditor(this);
//
// 		// In case the parent section block is moved inside of the editor,
// 		// just reconnect instead of initilializing it.
// 		if (!this.hasAttribute('id')) {
// 			this.init();
// 		} else {
// 			this.reconnect();
// 		}
//
// 		this.attachObservers();
// 	}
//
// 	/**
// 	 * Initilialize the portal when the component is connected the first time.
// 	 */
// 	init(): void {
// 		const portalId = uniqueId();
//
// 		this.setAttribute('id', portalId);
// 		this.destination = create(
// 			EditorPortalDestinationComponent.TAG_NAME,
// 			[],
// 			{ [Attr.portal]: portalId },
// 			this.rootEditor
// 		);
//
// 		this.style.flexGrow = '1';
// 		this.destination.style.display = 'flex';
// 		this.destination.style.flexDirection = 'column';
//
// 		setTimeout(() => {
// 			const section = Array.from(this.children).pop();
//
// 			this.destination.appendChild(section);
// 		}, 0);
// 	}
//
// 	/**
// 	 * Reconnect the portal and its destination after a section block has been moved.
// 	 */
// 	reconnect(): void {
// 		this.destination = query(
// 			`[${Attr.portal}="${this.getAttribute('id')}"]`
// 		);
//
// 		this.destination.style.display = 'flex';
// 	}
//
// 	/**
// 	 * Attach the mutation observer in order to detect changes in the portal and
// 	 * destination dimensions or position.
// 	 */
// 	attachObservers(): void {
// 		const field = this.closest(ModalFieldComponent.TAG_NAME);
//
// 		const config = {
// 			attributes: true,
// 			childList: true,
// 			subtree: true,
// 		};
//
// 		const observe = () => {
// 			this.observer.observe(this, config);
// 			this.observer.observe(this.destination, config);
// 			this.observer.observe(field, {
// 				attributes: true,
// 				childList: false,
// 				subtree: false,
// 			});
//
// 			try {
// 				this.changeListener.remove();
// 			} catch {}
//
// 			this.changeListener = listen(
// 				this.rootEditor,
// 				EventName.portalChange,
// 				update.bind(this)
// 			);
// 		};
//
// 		const update = () => {
// 			this.changeListener.remove();
// 			this.observer.disconnect();
//
// 			this.updateDimensions();
// 			fire(EventName.portalChange, this.rootEditor);
//
// 			observe();
// 		};
//
// 		this.observer = new MutationObserver(() => {
// 			update();
// 		});
//
// 		observe();
//
// 		this.addListener(
// 			listen(window, EventName.switcherChange, update.bind(this))
// 		);
//
// 		this.addListener(
// 			listen(window, 'resize', debounce(update.bind(this), 200))
// 		);
//
// 		this.addListener(
// 			listen(this.destination, 'click', () => {
// 				fire('click', this);
// 			})
// 		);
// 	}
//
// 	/**
// 	 * Synchronize portal and destination dimensions and position.
// 	 */
// 	updateDimensions(): void {
// 		const portalRect = this.getBoundingClientRect();
// 		const rootRect = this.rootEditor.getBoundingClientRect();
//
// 		const x = portalRect.x - rootRect.x;
// 		const y = portalRect.y - rootRect.y;
//
// 		this.destination.style.left = `${x}px`;
// 		this.destination.style.top = `${y}px`;
//
// 		// Handle nested portals inside of deleted section blocks.
// 		// If a parent section is deleted, the contained child portal moves
// 		// to the top-left corner of the window.
// 		if (portalRect.x === 0) {
// 			this.destination.style.left = '-90000px';
// 		}
//
// 		this.destination.style.width = `${this.offsetWidth}px`;
//
// 		this.destination.style.removeProperty('min-height');
//
// 		this.style.minHeight = `${this.destination.offsetHeight}px`;
// 		this.destination.style.minHeight = `${this.offsetHeight}px`;
// 	}
//
// 	/**
// 	 * Disconnect the mutation observer, hide the destination and trigger garbage collection
// 	 * on component disconnect.
// 	 */
// 	disconnectedCallback(): void {
// 		this.observer.disconnect();
// 		this.changeListener.remove();
// 		this.destination.style.display = 'none';
//
// 		garbageCollectDestinations();
// 	}
// }
//
// /**
//  * The portal destination component.
//  *
//  * @extends BaseComponent
//  */
// export class EditorPortalDestinationComponent extends BaseComponent {
// 	/**
// 	 * The components tag name.
// 	 *
// 	 * @static
// 	 */
// 	static TAG_NAME = 'am-editor-portal-destination';
//
// 	/**
// 	 * The callback function used when an element is created in the DOM.
// 	 */
// 	connectedCallback(): void {
// 		this.style.position = 'absolute';
// 	}
// }
//
// customElements.define(EditorPortalComponent.TAG_NAME, EditorPortalComponent);
// customElements.define(
// 	EditorPortalDestinationComponent.TAG_NAME,
// 	EditorPortalDestinationComponent
// );
