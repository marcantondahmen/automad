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

import { UndoCapableField, UndoEntry, UndoValue } from '@/admin/types';
import { App, debounce, EventName, fire, keyCombo, listen } from '.';

/**
 * The Undo class provides undo/redo-functionality for all kinds of fields.
 * In order to work, the root element has to attach the required window listener on load
 * as well as clear the undo stack on every view update.
 *
 * In order to actually enable undo for fields,
 * a field has to implement the Undoable interface and call Undo.attach(this) during init.
 */
export class Undo {
	/**
	 * The stack of undo actions.
	 *
	 * @static
	 */
	private static undoStack: UndoStack;

	/**
	 * The stack of redo actions.
	 *
	 * @static
	 */
	private static redoStack: UndoStack;

	/**
	 * The size of the undo stacks.
	 *
	 * @static
	 */
	static get size(): { undo: number; redo: number } {
		return { undo: Undo.undoStack.length, redo: Undo.redoStack.length };
	}

	/**
	 * The undo handler.
	 *
	 * @static
	 */
	static undoHandler(): void {
		const entry = Undo.undoStack.pop();

		if (!entry) {
			return;
		}

		entry.undo();
		Undo.redoStack.push(entry);
	}

	/**
	 * The redo handler.
	 *
	 * @static
	 */
	static redoHandler(): void {
		const entry = Undo.redoStack.pop();

		if (!entry) {
			return;
		}

		entry.redo();
		Undo.undoStack.push(entry);
	}

	/**
	 * Register keycombos.
	 *
	 * @static
	 */
	static addListeners(): void {
		App.root.addListener(keyCombo('z', Undo.undoHandler));
		App.root.addListener(keyCombo('y', Undo.redoHandler));
	}

	/**
	 * Create new or reset the undo/redo stacks.
	 *
	 * @static
	 */
	static new(): void {
		Undo.undoStack = new UndoStack();
		Undo.redoStack = new UndoStack();

		fire(EventName.undoStackUpdate);
	}

	/**
	 * Add an entry to the undo stack.
	 *
	 * @static
	 */
	static addEntry(entry: UndoEntry): void {
		Undo.redoStack = new UndoStack();
		Undo.undoStack.push(entry);
	}

	/**
	 * Attach the UndoProvider to a field component.
	 *
	 * @static
	 */
	static attach(field: UndoCapableField): void {
		setTimeout(() => {
			new UndoProvider(field);
		}, 0);
	}
}

/**
 * The stack of undo entries that will automatically be limited to a fixes maximum.
 */
class UndoStack {
	/**
	 * The maximum amount of items on a stack.
	 *
	 * @static
	 */
	private static LIMIT = 50;

	/**
	 * The actual array of entries.
	 */
	private _stack: UndoEntry[];

	/**
	 * The stack length.
	 */
	get length(): number {
		return this._stack.length;
	}

	/**
	 * The constructor.
	 */
	constructor() {
		this._stack = [];
	}

	/**
	 * Push an entry on top of the stack.
	 *
	 * @param entry
	 */
	push(entry: UndoEntry): void {
		this._stack.push(entry);
		this._stack = this._stack.slice(-UndoStack.LIMIT);

		fire(EventName.undoStackUpdate);
	}

	/**
	 * Pop an entry from the stack.
	 *
	 * @return the latest added entry
	 */
	pop(): UndoEntry {
		const entry = this._stack.pop();

		fire(EventName.undoStackUpdate);

		return entry;
	}
}

/**
 * An UndoProvider can be attached to any field component that implements the Undoable interface.
 */
class UndoProvider {
	/**
	 * The last original state before a change event was triggered.
	 * That value will be used as the undo-value.
	 */
	private undoState: UndoValue;

	/**
	 * Undoing or redoing changes trigger change events. In order to ignore those feeback-loops
	 * this property can be set to true.
	 */
	private ignoreEventFeedback = false;

	/**
	 * The constructor
	 *
	 * @param field
	 */
	constructor(field: UndoCapableField) {
		const fireEvent = () => {
			this.ignoreEventFeedback = true;
			fire('change', field.getValueProvider());
		};

		const onChange = debounce(() => {
			if (this.ignoreEventFeedback) {
				this.ignoreEventFeedback = false;
				return;
			}

			const value = field.query();
			const undoState = this.undoState;

			if (JSON.stringify(value) === JSON.stringify(undoState)) {
				return;
			}

			Undo.addEntry({
				undo: () => {
					field.mutate(undoState);
					fireEvent();
				},
				redo: () => {
					field.mutate(value);
					fireEvent();
				},
			});

			this.undoState = value;
		}, 500);

		this.undoState = field.query();

		App.root.listen(
			field.getValueProvider(),
			'change input',
			onChange.bind(this)
		);
	}
}
