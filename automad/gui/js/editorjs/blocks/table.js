/*
 *	This EditorJS block is based on the original table by CodeX and
 *	is extended to support the Automad block grid layout.
 *	https://github.com/editor-js/table
 *
 *	Copyright (c) 2018 CodeX (team@ifmo.su)
 *	Copyright (c) 2021 Marc Anton Dahmen
 *	MIT License
 */


+function (Automad) {

	Automad.tableUtils = {

		isNotMissed: function(elem) {
			return (!(elem === undefined || elem === null));
		},

		create: function(tagName, cssClasses = null, attrs = null, children = null) {
			const elem = document.createElement(tagName);

			if (Automad.tableUtils.isNotMissed(cssClasses)) {
				for (let i = 0; i < cssClasses.length; i++) {
					if (Automad.tableUtils.isNotMissed(cssClasses[i])) {
						elem.classList.add(cssClasses[i]);
					}
				}
			}
			if (Automad.tableUtils.isNotMissed(attrs)) {
				for (let key in attrs) {
					elem.setAttribute(key, attrs[key]);
				}
			}
			if (Automad.tableUtils.isNotMissed(children)) {
				for (let i = 0; i < children.length; i++) {
					if (Automad.tableUtils.isNotMissed(children[i])) {
						elem.appendChild(children[i]);
					}
				}
			}
			return elem;
		},

		getCoords: function(elem) {
			const rect = elem.getBoundingClientRect();

			return {
				y1: Math.floor(rect.top + window.pageYOffset),
				x1: Math.floor(rect.left + window.pageXOffset),
				x2: Math.floor(rect.right + window.pageXOffset),
				y2: Math.floor(rect.bottom + window.pageYOffset)
			};
		},

		getSideByCoords: function(coords, x, y) {
			let side;
			const sizeArea = 10;

			// a point is close to the boundary if the distance between them is less than the allowed distance.
			// +1px on each side due to fractional pixels
			if (x - coords.x1 >= -1 && x - coords.x1 <= sizeArea + 1) {
				side = 'left';
			}
			if (coords.x2 - x >= -1 && coords.x2 - x <= sizeArea + 1) {
				side = 'right';
			}
			if (y - coords.y1 >= -1 && y - coords.y1 <= sizeArea + 1) {
				side = 'top';
			}
			if (coords.y2 - y >= -1 && coords.y2 - y <= sizeArea + 1) {
				side = 'bottom';
			}

			return side;
		},

		CSS: {
			highlightingLine: 'tc-toolbar',
			hidden: 'tc-toolbar--hidden',
			horizontalHighlightingLine: 'tc-toolbar__shine-line--hor',
			verticalHighlightingLine: 'tc-toolbar__shine-line--ver',
			plusButton: 'tc-toolbar__plus',
			horizontalPlusButton: 'tc-toolbar__plus--hor',
			verticalPlusButton: 'tc-toolbar__plus--ver',
			area: 'tc-table__area',
			table: 'tc-table',
			input: 'tc-table__inp',
			cell: 'tc-table__cell',
			wrapper: 'tc-table__wrap',
			editor: 'tc-editor',
			toolBarHor: 'tc-toolbar--hor',
			toolBarVer: 'tc-toolbar--ver'
		}

	}

}(window.Automad = window.Automad || {});


/**
 * An item with a menu that appears when you hover over a _table border
 */

class BorderToolBar {

	constructor() {
		this._plusButton = this._generatePlusButton();
		this._highlightingLine = this._generateHighlightingLine();
		this._toolbar = this._generateToolBar([this._plusButton, this._highlightingLine]);
	}

	hide() {
		this._toolbar.classList.add(Automad.tableUtils.CSS.hidden);
	}

	show() {
		this._toolbar.classList.remove(Automad.tableUtils.CSS.hidden);
		this._highlightingLine.classList.remove(Automad.tableUtils.CSS.hidden);
	};

	hideLine() {
		this._highlightingLine.classList.add(Automad.tableUtils.CSS.hidden);
	};

	get htmlElement() {
		return this._toolbar;
	}

	_generatePlusButton() {
		const button = Automad.tableUtils.create('div', [Automad.tableUtils.CSS.plusButton]);

		button.innerHTML = '<svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="10" fill="#1070ff"/><path fill="#FFF" d="M10.9 9.1h3.7a.9.9 0 1 1 0 1.8h-3.7v3.7a.9.9 0 1 1-1.8 0v-3.7H5.4a.9.9 0 0 1 0-1.8h3.7V5.4a.9.9 0 0 1 1.8 0v3.7z"/></svg>';
		button.addEventListener('click', (event) => {
			event.stopPropagation();
			const e = new CustomEvent('click', { 'detail': { 'x': event.pageX, 'y': event.pageY }, 'bubbles': true });

			this._toolbar.dispatchEvent(e);
		});
		return button;
	}

	_generateHighlightingLine() {
		const line = Automad.tableUtils.create('div', [Automad.tableUtils.CSS.highlightingLine]);

		line.addEventListener('click', (event) => {
			event.stopPropagation();
			const e = new CustomEvent('click', { 'bubbles': true });

			this._toolbar.dispatchEvent(e);
		});
		return line;
	}

	_generateToolBar(children) {
		const bar = Automad.tableUtils.create('div', [Automad.tableUtils.CSS.hidden], null, children);

		bar.addEventListener('mouseleave', (event) => {
			this._recalcMousePos(event);
		}
		);

		return bar;
	}

	_recalcMousePos(event) {
		this.hide();
		const area = document.elementFromPoint(event.pageX, event.pageY);

		if (area !== null && area.classList.contains(Automad.tableUtils.CSS.area)) {
			const e = new MouseEvent('mouseover', { clientX: event.pageX, clientY: event.pageY });
			area.dispatchEvent(e);
		}
	}
}

/**
 * An item with a menu that appears when you hover over a _table border horizontally
 */

class HorizontalBorderToolBar extends BorderToolBar {
	
	constructor() {
		super();

		this._toolbar.classList.add(Automad.tableUtils.CSS.toolBarHor);
		this._plusButton.classList.add(Automad.tableUtils.CSS.horizontalPlusButton);
		this._highlightingLine.classList.add(Automad.tableUtils.CSS.horizontalHighlightingLine);
	}

	showIn(y) {
		const halfHeight = Math.floor(Number.parseInt(window.getComputedStyle(this._toolbar).height) / 2);

		this._toolbar.style.top = (y - halfHeight) + 'px';
		this.show();
	}
}

/**
 * An item with a menu that appears when you hover over a _table border vertically
 */

class VerticalBorderToolBar extends BorderToolBar {
	
	constructor() {
		super();

		this._toolbar.classList.add(Automad.tableUtils.CSS.toolBarVer);
		this._plusButton.classList.add(Automad.tableUtils.CSS.verticalPlusButton);
		this._highlightingLine.classList.add(Automad.tableUtils.CSS.verticalHighlightingLine);
	}

	showIn(x) {
		const halfWidth = Math.floor(Number.parseInt(window.getComputedStyle(this._toolbar).width) / 2);

		this._toolbar.style.left = (x - halfWidth) + 'px';
		this.show();
	}

}

/**
 * Tool for table's creating
 */

class AutomadBlockTable {
	
	static get isReadOnlySupported() {
		return true;
	}

	static get enableLineBreaks() {
		return true;
	}

	static get toolbox() {
		return {
			icon: '<svg width="18px" height="16px" viewBox="0 0 18 16"><path d="M14,0H4C1.8,0,0,1.8,0,4v8c0,2.2,1.8,4,4,4h10c2.2,0,4-1.8,4-4V4C18,1.8,16.2,0,14,0z M4,2h4v5H2V4C2,2.9,2.9,2,4,2z M4,14 c-1.1,0-2-0.9-2-2V9h6v5H4z M16,12c0,1.1-0.9,2-2,2h-4V9h6V12z M16,7h-6V2h4c1.1,0,2,0.9,2,2V7z"/></svg>',
			title: AutomadEditorTranslation.get('table_toolbox'),
		};
	}

	constructor({ data, config, api, readOnly }) {
		this.api = api;
		this.readOnly = readOnly;
		this.data = data;
		this._tableConstructor = new TableConstructor(this.data, config, api, readOnly);
		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);
	}

	render() {
		return this._tableConstructor.htmlElement;
	}

	save(toolsContent) {

		const table = toolsContent.querySelector('table');
		const data = [];
		const rows = table.rows;

		for (let i = 0; i < rows.length; i++) {
			const row = rows[i];
			const cols = Array.from(row.cells);
			const inputs = cols.map(cell => cell.querySelector('.' + Automad.tableUtils.CSS.input));
			const isWorthless = inputs.every(this._isEmpty);

			if (isWorthless) {
				continue;
			}
			data.push(inputs.map(input => input.innerHTML));
		}

		return Object.assign(this.data, {
			content: data,
		});
	}

	renderSettings() {

		return this.layoutSettings;

	}

	_isEmpty(input) {
		return !input.textContent.trim();
	}
}


class Table {

	constructor(readOnly) {
		this.readOnly = readOnly;
		this._numberOfColumns = 0;
		this._numberOfRows = 0;
		this._element = this._createTableWrapper();
		this._table = this._element.querySelector('table');

		if (!this.readOnly) {
			this._hangEvents();
		}
	}

	addColumn(index = -1) {
		this._numberOfColumns++;
		/** Add cell in each row */
		const rows = this._table.rows;

		for (let i = 0; i < rows.length; i++) {
			const cell = rows[i].insertCell(index);

			this._fillCell(cell);
		}
	};

	addRow(index = -1) {
		this._numberOfRows++;
		const row = this._table.insertRow(index);

		this._fillRow(row);

		return row;
	};

	get htmlElement() {
		return this._element;
	}

	get body() {
		return this._table;
	}

	get selectedCell() {
		return this._selectedCell;
	}

	_createTableWrapper() {
		return Automad.tableUtils.create('div', [Automad.tableUtils.CSS.wrapper], null, [Automad.tableUtils.create('table', [Automad.tableUtils.CSS.table])]);
	}

	_createContenteditableArea() {
		return Automad.tableUtils.create('div', [Automad.tableUtils.CSS.input], { contenteditable: !this.readOnly });
	}

	_fillCell(cell) {
		cell.classList.add(Automad.tableUtils.CSS.cell);
		const content = this._createContenteditableArea();

		cell.appendChild(Automad.tableUtils.create('div', [Automad.tableUtils.CSS.area], null, [content]));
	}

	_fillRow(row) {
		for (let i = 0; i < this._numberOfColumns; i++) {
			const cell = row.insertCell();

			this._fillCell(cell);
		}
	}

	_hangEvents() {
		this._table.addEventListener('focus', (event) => {
			this._focusEditField(event);
		}, true);

		this._table.addEventListener('blur', (event) => {
			this._blurEditField(event);
		}, true);

		this._table.addEventListener('keydown', (event) => {
			this._pressedEnterInEditField(event);
		});

		this._table.addEventListener('click', (event) => {
			this._clickedOnCell(event);
		});

		this._table.addEventListener('mouseover', (event) => {
			this._mouseEnterInDetectArea(event);
			event.stopPropagation();
		}, true);
	}

	_focusEditField(event) {
		if (!event.target.classList.contains(Automad.tableUtils.CSS.input)) {
			return;
		}
		this._selectedCell = event.target.closest('.' + Automad.tableUtils.CSS.cell);
	}

	_blurEditField(event) {
		if (!event.target.classList.contains(Automad.tableUtils.CSS.input)) {
			return;
		}
		this._selectedCell = null;
	}

	_pressedEnterInEditField(event) {
		if (!event.target.classList.contains(Automad.tableUtils.CSS.input)) {
			return;
		}
		if (event.keyCode === 13 && !event.shiftKey) {
			event.preventDefault();
		}
	}

	_clickedOnCell(event) {
		if (!event.target.classList.contains(Automad.tableUtils.CSS.cell)) {
			return;
		}
		const content = event.target.querySelector('.' + Automad.tableUtils.CSS.input);

		content.focus();
	}

	_mouseEnterInDetectArea(event) {
		if (!event.target.classList.contains(Automad.tableUtils.CSS.area)) {
			return;
		}

		const coordsCell = Automad.tableUtils.getCoords(event.target.closest('TD'));
		const side = Automad.tableUtils.getSideByCoords(coordsCell, event.pageX, event.pageY);

		event.target.dispatchEvent(new CustomEvent('mouseInActivatingArea', {
			detail: {
				side: side,
			},
			bubbles: true,
		}));
	}
}


class TableConstructor {
	
	constructor(data, config, api, readOnly) {
		this.readOnly = readOnly;

		/** creating table */
		this._table = new Table(readOnly);
		const size = this._resizeTable(data, config);

		this._fillTable(data, size);

		/** creating container around table */
		this._container = Automad.tableUtils.create('div', [Automad.tableUtils.CSS.editor, api.styles.block], null, [this._table.htmlElement]);

		/** creating ToolBars */
		this._verticalToolBar = new VerticalBorderToolBar();
		this._horizontalToolBar = new HorizontalBorderToolBar();
		this._table.htmlElement.appendChild(this._horizontalToolBar.htmlElement);
		this._table.htmlElement.appendChild(this._verticalToolBar.htmlElement);

		/** Activated elements */
		this._hoveredCell = null;
		this._activatedToolBar = null;
		this._hoveredCellSide = null;

		/** Timers */
		this._plusButDelay = null;
		this._toolbarShowDelay = null;

		if (!this.readOnly) {
			this._hangEvents();
		}
	}

	get htmlElement() {
		return this._container;
	}

	_fillTable(data, size) {
		if (data.content !== undefined) {
			for (let i = 0; i < size.rows && i < data.content.length; i++) {
				for (let j = 0; j < size.cols && j < data.content[i].length; j++) {
					// get current cell and her editable part
					const input = this._table.body.rows[i].cells[j].querySelector('.' + Automad.tableUtils.CSS.input);

					input.innerHTML = data.content[i][j];
				}
			}
		}
	}

	_resizeTable(data, config) {
		const isValidArray = Array.isArray(data.content);
		const isNotEmptyArray = isValidArray ? data.content.length : false;
		const contentRows = isValidArray ? data.content.length : undefined;
		const contentCols = isNotEmptyArray ? data.content[0].length : undefined;
		const parsedRows = Number.parseInt(config.rows);
		const parsedCols = Number.parseInt(config.cols);
		// value of config have to be positive number
		const configRows = !isNaN(parsedRows) && parsedRows > 0 ? parsedRows : undefined;
		const configCols = !isNaN(parsedCols) && parsedCols > 0 ? parsedCols : undefined;
		const defaultRows = 2;
		const defaultCols = 2;
		const rows = contentRows || configRows || defaultRows;
		const cols = contentCols || configCols || defaultCols;

		for (let i = 0; i < rows; i++) {
			this._table.addRow();
		}
		for (let i = 0; i < cols; i++) {
			this._table.addColumn();
		}

		return {
			rows: rows,
			cols: cols
		};
	}

	_showToolBar(toolBar, coord) {
		this._hideToolBar();
		this._activatedToolBar = toolBar;
		toolBar.showIn(coord);
	}

	_hideToolBar() {
		if (this._activatedToolBar !== null) {
			this._activatedToolBar.hide();
		}
	}

	_hangEvents() {
		this._container.addEventListener('mouseInActivatingArea', (event) => {
			this._toolbarCalling(event);
		});

		this._container.addEventListener('click', (event) => {
			this._clickToolbar(event);
		});

		this._container.addEventListener('input', () => {
			this._hideToolBar();
		});

		this._container.addEventListener('keydown', (event) => {
			this._containerKeydown(event);
		});

		this._container.addEventListener('mouseout', (event) => {
			this._leaveDetectArea(event);
		});

		this._container.addEventListener('mouseover', (event) => {
			this._mouseEnterInDetectArea(event);
		});
	}

	_mouseInActivatingAreaListener(event) {
		this._hoveredCellSide = event.detail.side;
		const areaCoords = Automad.tableUtils.getCoords(event.target);
		const containerCoords = Automad.tableUtils.getCoords(this._table.htmlElement);

		this._hoveredCell = event.target.closest('TD');

		if (this._hoveredCell === null) {
			const paddingContainer = 11;
			this._hoveredCell = this._container;
			areaCoords.x1 += paddingContainer;
			areaCoords.y1 += paddingContainer;
			areaCoords.x2 -= paddingContainer;
			areaCoords.y2 -= paddingContainer;
		}

		if (this._hoveredCellSide === 'top') {
			this._showToolBar(this._horizontalToolBar, areaCoords.y1 - containerCoords.y1 - 2);
		}
		if (this._hoveredCellSide === 'bottom') {
			this._showToolBar(this._horizontalToolBar, areaCoords.y2 - containerCoords.y1 - 1);
		}
		if (this._hoveredCellSide === 'left') {
			this._showToolBar(this._verticalToolBar, areaCoords.x1 - containerCoords.x1 - 2);
		}
		if (this._hoveredCellSide === 'right') {
			this._showToolBar(this._verticalToolBar, areaCoords.x2 - containerCoords.x1 - 1);
		}
	}

	_isToolbar(elem) {
		return !!(elem.closest('.' + Automad.tableUtils.CSS.toolBarHor) || elem.closest('.' + Automad.tableUtils.CSS.toolBarVer));
	}

	_leaveDetectArea(event) {
		if (this._isToolbar(event.relatedTarget)) {
			return;
		}
		clearTimeout(this._toolbarShowDelay);
		this._hideToolBar();
	}

	_toolbarCalling(event) {
		if (this._isToolbar(event.target)) {
			return;
		}
		clearTimeout(this._toolbarShowDelay);
		this._toolbarShowDelay = setTimeout(() => {
			this._mouseInActivatingAreaListener(event);
		}, 125);
	}

	_clickToolbar(event) {
		if (!this._isToolbar(event.target)) {
			return;
		}
		let typeCoord;

		if (this._activatedToolBar === this._horizontalToolBar) {
			this._addRow();
			typeCoord = 'y';
		} else {
			this._addColumn();
			typeCoord = 'x';
		}
		/** If event has transmitted data (coords of mouse) */
		const detailHasData = isNaN(event.detail) && event.detail !== null;

		if (detailHasData) {
			const containerCoords = Automad.tableUtils.getCoords(this._table.htmlElement);
			let coord;

			if (typeCoord === 'x') {
				coord = event.detail.x - containerCoords.x1;
			} else {
				coord = event.detail.y - containerCoords.y1;
			}
			this._delayAddButtonForMultiClickingNearMouse(coord);
		} else {
			this._hideToolBar();
		}
	}

	_containerKeydown(event) {
		if (event.keyCode === 13) {
			this._containerEnterPressed(event);
		}
	}

	_delayAddButtonForMultiClickingNearMouse(coord) {
		this._showToolBar(this._activatedToolBar, coord);
		this._activatedToolBar.hideLine();
		clearTimeout(this._plusButDelay);
		this._plusButDelay = setTimeout(() => {
			this._hideToolBar();
		}, 500);
	}

	_getHoveredSideOfContainer() {
		if (this._hoveredCell === this._container) {
			return this._isBottomOrRight() ? 0 : -1;
		}
		return 1;
	}

	_isBottomOrRight() {
		return this._hoveredCellSide === 'bottom' || this._hoveredCellSide === 'right';
	}

	_addRow() {
		const indicativeRow = this._hoveredCell.closest('TR');
		let index = this._getHoveredSideOfContainer();

		if (index === 1) {
			index = indicativeRow.sectionRowIndex;
			// if inserting after hovered cell
			index = index + this._isBottomOrRight();
		}

		this._table.addRow(index);
	}

	_addColumn() {
		let index = this._getHoveredSideOfContainer();

		if (index === 1) {
			index = this._hoveredCell.cellIndex;
			// if inserting after hovered cell
			index = index + this._isBottomOrRight();
		}

		this._table.addColumn(index);
	}

	_containerEnterPressed(event) {
		if (!(this._table.selectedCell !== null && !event.shiftKey)) {
			return;
		}
		const indicativeRow = this._table.selectedCell.closest('TR');
		let index = this._getHoveredSideOfContainer();

		if (index === 1) {
			index = indicativeRow.sectionRowIndex + 1;
		}
		const newstr = this._table.addRow(index);

		newstr.cells[0].click();
	}

	_mouseEnterInDetectArea(event) {
		const coords = Automad.tableUtils.getCoords(this._container);
		let side = Automad.tableUtils.getSideByCoords(coords, event.pageX, event.pageY);

		event.target.dispatchEvent(new CustomEvent('mouseInActivatingArea', {
			'detail': {
				'side': side
			},
			'bubbles': true
		}));
	}
}