/*
 * This EditorJS plugin is based on the original Undo plugin by kommit.
 * https://github.com/kommitters/editorjs-undo
 *
 * Copyright (c) 2020 kommit (info@kommit.co)
 * Copyright (c) 2021 Marc Anton Dahmen
 * MIT License
 */

/*
 * The original file by kommit can be found here:
 * https://github.com/kommitters/editorjs-undo/blob/master/src/index.js
 */

class AutomadEditorUndo {
	static get limit() {
		return 20;
	}

	constructor({ editor, data }) {
		const holder = editor.configuration.holder;

		this.editor = editor;
		this.holder =
			typeof holder === 'string'
				? document.getElementById(holder)
				: holder;

		const observer = new AutomadUndoObserver(
				() => this.registerChange(),
				this.holder
			),
			initialData = 'blocks' in data ? data.blocks : [],
			initialIndex = initialData.length - 1,
			firstElement = {
				index: initialIndex,
				state: initialData,
			};

		this.stack = [firstElement];
		this.initialItem = firstElement;
		this.position = 0;

		observer.setMutationObserver();
		this.setEventListeners();
	}

	defaultState(state) {
		if (!state.length) {
			state = [
				{
					type: 'paragraph',
					data: {},
				},
			];
		}

		return state;
	}

	renderState(position) {
		const { index, state } = this.stack[position];

		this.shouldSaveHistory = false;

		this.editor.blocks
			.render({ blocks: this.defaultState(state) })
			.then(() => {
				this.editor.caret.setToBlock(index, 'end');
				this.holder.dispatchEvent(new CustomEvent('undo'));
			});
	}

	undo() {
		if (this.position > 0) {
			this.renderState((this.position -= 1));
		}
	}

	redo() {
		if (this.position < this.stack.length - 1) {
			this.renderState((this.position += 1));
		}
	}

	save(state) {
		const index = this.editor.blocks.getCurrentBlockIndex();

		if (this.position >= this.maxLength) {
			this.truncate(this.stack, this.maxLength);
		}

		this.position = Math.min(this.position, this.stack.length - 1);
		this.stack = this.stack.slice(0, this.position + 1);
		this.stack.push({ index, state });
		this.position += 1;
	}

	truncate(stack, limit) {
		while (stack.length > limit) {
			stack.shift();
		}
	}

	editorDidUpdate(newData) {
		const { state } = this.stack[this.position];

		if (newData.length !== state.length) {
			return true;
		}

		return JSON.stringify(state) !== JSON.stringify(newData);
	}

	registerChange() {
		if (this.editor && this.editor.save && this.shouldSaveHistory) {
			this.editor.save().then((savedData) => {
				if (this.editorDidUpdate(savedData.blocks)) {
					this.save(savedData.blocks);
				}
			});
		}

		this.shouldSaveHistory = true;
	}

	setEventListeners() {
		const buttonKey = /(mac)/i.test(navigator.platform)
				? 'metaKey'
				: 'ctrlKey',
			handleUndo = (e) => {
				if (e[buttonKey] && e.key === 'z') {
					e.preventDefault();
					this.undo();
				}
			},
			handleRedo = (e) => {
				if (e[buttonKey] && e.key === 'y') {
					e.preventDefault();
					this.redo();
				}
			},
			handleDestroy = () => {
				this.holder.removeEventListener('keydown', handleUndo);
				this.holder.removeEventListener('keydown', handleRedo);
			};

		this.holder.addEventListener('keydown', handleUndo);
		this.holder.addEventListener('keydown', handleRedo);
		this.holder.addEventListener('destroy', handleDestroy);
	}
}

/*
 * The original file by kommit can be found here:
 * https://github.com/kommitters/editorjs-undo/blob/master/src/observer.js
 */

class AutomadUndoObserver {
	constructor(registerChange, holder) {
		this.holder = holder;
		this.observer = null;
		this.debounceTimer = 500;
		this.mutationDebouncer = this.debounce(() => {
			registerChange();
		}, this.debounceTimer);
	}

	mutationHandler(mutationList) {
		let contentMutated = false;

		mutationList.forEach((mutation) => {
			switch (mutation.type) {
				case 'childList':
					if (mutation.target === this.holder) {
						this.onDestroy();
					} else {
						contentMutated = true;
					}
					break;
				case 'characterData':
					contentMutated = true;
					break;
				case 'attributes':
					if (!mutation.target.classList.contains('ce-block')) {
						contentMutated = true;
					}
					break;
				default:
					break;
			}
		});

		if (contentMutated) {
			this.mutationDebouncer();
		}
	}

	setMutationObserver() {
		const observerOptions = {
				childList: true,
				attributes: true,
				subtree: true,
				characterData: true,
				characterDataOldValue: true,
			},
			target = this.holder;

		this.observer = new MutationObserver((mutationList) => {
			this.mutationHandler(mutationList);
		});

		this.observer.observe(target, observerOptions);
	}

	debounce(callback, wait) {
		let timeout;

		return (...args) => {
			const context = this;
			clearTimeout(timeout);
			timeout = setTimeout(() => callback.apply(context, args), wait);
		};
	}

	onDestroy() {
		const destroyEvent = new CustomEvent('destroy');

		this.holder.dispatchEvent(destroyEvent);
		this.observer.disconnect();
	}
}
