/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */

/*!
 *  Markdown editor based in the htmleditor component of UIkit.
 * 
 *	UIkit 2.27.5 | http://www.getuikit.com | (c) 2014 YOOtheme | MIT License
 */

(function (addon) {

	var component;

	if (window.UIkit2) {
		component = addon(UIkit2);
	}

	if (typeof define == 'function' && define.amd) {
		define('uikit-markdowneditor', ['uikit'], function () {
			return component || addon(UIkit2);
		});
	}

})(function (UI) {

	"use strict";

	var editors = [];

	UI.component('markdowneditor', {

		defaults: {
			mode: 'split',
			autocomplete: true,
			enablescripts: false,
			height: 500,
			maxsplitsize: 1000,
			codemirror: {
				mode: 'gfm',
				lineWrapping: true,
				dragDrop: false,
				autoCloseTags: false,
				matchTags: false,
				autoCloseBrackets: true,
				matchBrackets: true,
				indentUnit: 4,
				indentWithTabs: true,
				tabSize: 4,
				hintOptions: {
					completionSingle: false
				},
				extraKeys: {
					"Enter": "newlineAndIndentContinueMarkdownList"
				},
				styleSelectedText: true
			},
			toolbar: ['bold', 'italic', 'link', 'image', 'blockquote', 'listUl', 'listOl']
		},

		boot: function () {

			// init code
			UI.ready(function (context) {

				UI.$('textarea[data-uk-markdowneditor]', context).each(function () {

					var editor = UI.$(this);

					if (!editor.data('markdowneditor')) {
						UI.markdowneditor(editor, UI.Utils.options(editor.attr('data-uk-markdowneditor')));
					}
				});
			});
		},

		init: function () {

			var $this = this, tpl = UI.components.markdowneditor.template;

			this.CodeMirror = this.options.CodeMirror || CodeMirror;
			this.buttons = {};

			this.markdowneditor = UI.$(tpl);
			this.content = this.markdowneditor.find('.uk-markdowneditor-content');
			this.toolbar = this.markdowneditor.find('.uk-markdowneditor-toolbar');
			this.preview = this.markdowneditor.find('.uk-markdowneditor-preview').children().eq(0);
			this.code = this.markdowneditor.find('.uk-markdowneditor-code');

			this.element.before(this.markdowneditor).appendTo(this.code);
			this.editor = this.CodeMirror.fromTextArea(this.element[0], this.options.codemirror);
			this.editor.markdowneditor = this;
			this.editor.on('change', UI.Utils.debounce(function () { $this.render(); }, 150));
			this.editor.on('change', function () {
				$this.editor.save();
				$this.element.trigger('input');
			});
			this.code.find('.CodeMirror').css('height', this.options.height);

			this.preview.container = this.preview;

			UI.$win.on('resize load', UI.Utils.debounce(function () { $this.fit(); }, 200));

			var previewContainer = $this.preview.parent(),
				codeContent = this.code.find('.CodeMirror-sizer'),
				codeScroll = this.code.find('.CodeMirror-scroll').on('scroll', UI.Utils.debounce(function () {

					if ($this.markdowneditor.attr('data-mode') == 'tab') return;

					// calc position
					var codeHeight = codeContent.height() - codeScroll.height(),
						previewHeight = previewContainer[0].scrollHeight - previewContainer.height(),
						ratio = previewHeight / codeHeight,
						previewPosition = codeScroll.scrollTop() * ratio;

					// apply new scroll
					previewContainer.scrollTop(previewPosition);

				}, 10));

			this.markdowneditor.on('click', '.uk-markdowneditor-button-code, .uk-markdowneditor-button-preview', function (e) {

				e.preventDefault();

				if ($this.markdowneditor.attr('data-mode') == 'tab') {
					$this.markdowneditor.find('.uk-markdowneditor-button-code, .uk-markdowneditor-button-preview').removeClass('uk-active').filter(this).addClass('uk-active');
					$this.activetab = UI.$(this).hasClass('uk-markdowneditor-button-code') ? 'code' : 'preview';
					$this.markdowneditor.attr('data-active-tab', $this.activetab);
					$this.editor.refresh();
				}

			});

			// toolbar actions
			this.markdowneditor.on('click', 'a[data-markdowneditor-button]', function () {

				if (!$this.code.is(':visible')) return;

				$this.trigger('action.' + UI.$(this).data('markdowneditor-button'), [$this.editor]);
			
			});

			this.preview.parent().css('height', this.code.height());

			// autocomplete
			if (this.options.autocomplete && this.CodeMirror.showHint && this.CodeMirror.hint && this.CodeMirror.hint.html) {

				this.editor.on('inputRead', UI.Utils.debounce(function () {
					var doc = $this.editor.getDoc(), POS = doc.getCursor(), mode = $this.CodeMirror.innerMode($this.editor.getMode(), $this.editor.getTokenAt(POS).state).mode.name;
				}, 100));
			}

			this.debouncedRedraw = UI.Utils.debounce(function () { $this.redraw(); }, 5);

			this.on('init.uk.component', function () {
				$this.debouncedRedraw();
			});

			this.element.attr('data-uk-check-display', 1).on('display.uk.check', function (e) {
				if (this.markdowneditor.is(":visible")) this.fit();
			}.bind(this));

			editors.push(this);
		},

		addButton: function (name, button) {
			this.buttons[name] = button;
		},

		addButtons: function (buttons) {
			UI.$.extend(this.buttons, buttons);
		},

		replaceInPreview: function (regexp, callback) {

			var editor = this.editor, results = [], value = editor.getValue(), offset = -1, index = 0;

			this.currentvalue = this.currentvalue.replace(regexp, function () {

				offset = value.indexOf(arguments[0], ++offset);

				var match = {
					matches: arguments,
					from: translateOffset(offset),
					to: translateOffset(offset + arguments[0].length),
					replace: function (value) {
						editor.replaceRange(value, match.from, match.to);
					},
					inRange: function (cursor) {

						if (cursor.line === match.from.line && cursor.line === match.to.line) {
							return cursor.ch >= match.from.ch && cursor.ch < match.to.ch;
						}

						return (cursor.line === match.from.line && cursor.ch >= match.from.ch) ||
							(cursor.line > match.from.line && cursor.line < match.to.line) ||
							(cursor.line === match.to.line && cursor.ch < match.to.ch);
					}
				};

				var result = typeof (callback) === 'string' ? callback : callback(match, index);

				if (!result && result !== '') {
					return arguments[0];
				}

				index++;

				results.push(match);
				return result;

			});

			function translateOffset(offset) {
				var result = editor.getValue().substring(0, offset).split('\n');
				return { line: result.length - 1, ch: result[result.length - 1].length }
			}

			return results;
		},

		_buildtoolbar: function () {

			if (!(this.options.toolbar && this.options.toolbar.length)) return;

			var $this = this, bar = [];

			this.toolbar.empty();

			this.options.toolbar.forEach(function (button) {
				if (!$this.buttons[button]) return;

				var title = $this.buttons[button].title ? $this.buttons[button].title : button;

				bar.push('<li><a data-markdowneditor-button="' + button + '" title="' + title + '" data-uk-tooltip>' + $this.buttons[button].label + '</a></li>');
			});

			this.toolbar.html(bar.join('\n'));
		},

		fit: function () {

			var mode = this.options.mode;

			if (mode == 'split' && this.markdowneditor.width() < this.options.maxsplitsize) {
				mode = 'tab';
			}

			if (mode == 'tab') {
				if (!this.activetab) {
					this.activetab = 'code';
					this.markdowneditor.attr('data-active-tab', this.activetab);
				}

				this.markdowneditor.find('.uk-markdowneditor-button-code, .uk-markdowneditor-button-preview').removeClass('uk-active')
					.filter(this.activetab == 'code' ? '.uk-markdowneditor-button-code' : '.uk-markdowneditor-button-preview')
					.addClass('uk-active');
			}

			this.editor.refresh();
			this.preview.parent().css('height', this.code.height());

			this.markdowneditor.attr('data-mode', mode);

		},

		redraw: function () {
			this._buildtoolbar();
			this.render();
			this.fit();
		},

		getMode: function () {
			return this.editor.getOption('mode');
		},

		render: function () {

			this.currentvalue = this.editor.getValue();

			if (!this.options.enablescripts) {
				this.currentvalue = this.currentvalue.replace(/<(script|style)\b[^<]*(?:(?!<\/(script|style)>)<[^<]*)*<\/(script|style)>/img, '');
			}

			// empty code
			if (!this.currentvalue) {

				this.element.val('');
				this.preview.container.html('');

				return;
			}

			this.trigger('render', [this]);
			this.trigger('renderLate', [this]);

			this.preview.container.html(this.currentvalue);
		},

		addShortcut: function (name, callback) {
			var map = {};
			if (!UI.$.isArray(name)) {
				name = [name];
			}

			name.forEach(function (key) {
				map[key] = callback;
			});

			this.editor.addKeyMap(map);

			return map;
		},

		addShortcutAction: function (action, shortcuts) {
			var editor = this;
			this.addShortcut(shortcuts, function () {
				editor.element.trigger('action.' + action, [editor.editor]);
			});
		},

		replaceSelection: function (replace) {

			var text = this.editor.getSelection();

			if (!text.length) {

				var cur = this.editor.getCursor(),
					curLine = this.editor.getLine(cur.line),
					start = cur.ch,
					end = start;

				while (end < curLine.length && /[\w$]+/.test(curLine.charAt(end)))++end;
				while (start && /[\w$]+/.test(curLine.charAt(start - 1)))--start;

				var curWord = start != end && curLine.slice(start, end);

				if (curWord) {
					this.editor.setSelection({ line: cur.line, ch: start }, { line: cur.line, ch: end });
					text = curWord;
				}
			}

			var html = replace.replace('$1', text);

			this.editor.replaceSelection(html, 'end');
			this.editor.focus();
		},

		replaceLine: function (replace) {
			var pos = this.editor.getDoc().getCursor(),
				text = this.editor.getLine(pos.line),
				html = replace.replace('$1', text);

			this.editor.replaceRange(html, { line: pos.line, ch: 0 }, { line: pos.line, ch: text.length });
			this.editor.setCursor({ line: pos.line, ch: html.length });
			this.editor.focus();
		},

		save: function () {
			this.editor.save();
		}
	});

	UI.components.markdowneditor.template = [
		'<div class="uk-markdowneditor uk-clearfix" data-mode="split">',
			'<div class="uk-markdowneditor-navbar">',
				'<ul class="uk-markdowneditor-navbar-nav uk-markdowneditor-toolbar"></ul>',
				'<div class="uk-markdowneditor-navbar-flip">',
					'<ul class="uk-markdowneditor-navbar-nav">',
						'<li class="uk-markdowneditor-button-code"><a>Markdown</a></li>',
						'<li class="uk-markdowneditor-button-preview"><a>Preview</a></li>',
						'<li><a data-markdowneditor-button="fullscreen"><i class="uk-icon-expand"></i></a></li>',
					'</ul>',
				'</div>',
			'</div>',
			'<div class="uk-markdowneditor-content">',
				'<div class="uk-markdowneditor-code"></div>',
				'<div class="uk-markdowneditor-preview"><div></div></div>',
			'</div>',
		'</div>'
	].join('');

	UI.plugin('markdowneditor', 'base', {

		init: function (editor) {

			editor.addButtons({

				fullscreen: {
					title: 'Fullscreen',
					label: '<i class="uk-icon-expand"></i>'
				},
				bold: {
					title: 'Bold',
					label: '<i class="uk-icon-bold"></i>'
				},
				italic: {
					title: 'Italic',
					label: '<i class="uk-icon-italic"></i>'
				},
				strike: {
					title: 'Strikethrough',
					label: '<i class="uk-icon-strikethrough"></i>'
				},
				blockquote: {
					title: 'Blockquote',
					label: '<i class="uk-icon-quote-right"></i>'
				},
				link: {
					title: 'Link',
					label: '<i class="uk-icon-link"></i>'
				},
				image: {
					title: 'Image',
					label: '<i class="uk-icon-picture-o"></i>'
				},
				listUl: {
					title: 'Unordered List',
					label: '<i class="uk-icon-list-ul"></i>'
				},
				listOl: {
					title: 'Ordered List',
					label: '<i class="uk-icon-list-ol"></i>'
				}

			});

			editor.markdowneditor.on('click', 'a[data-markdowneditor-button="fullscreen"]', function () {

				editor.markdowneditor.toggleClass('uk-markdowneditor-fullscreen');

				var wrap = editor.editor.getWrapperElement();

				if (editor.markdowneditor.hasClass('uk-markdowneditor-fullscreen')) {

					var fixedParent = false, parents = editor.markdowneditor.parents().each(function () {
						if (UI.$(this).css('position') == 'fixed' && !UI.$(this).is('html')) {
							fixedParent = UI.$(this);
						}
					});

					editor.markdowneditor.data('fixedParents', false);

					if (fixedParent) {

						var transformed = [];

						fixedParent = fixedParent.parent().find(parents).each(function () {

							if (UI.$(this).css('transform') != 'none') {
								transformed.push(UI.$(this).data('transform-reset', {
									'transform': this.style.transform,
									'-webkit-transform': this.style.webkitTransform,
									'-webkit-transition': this.style.webkitTransition,
									'transition': this.style.transition
								}).css({
									'transform': 'none',
									'-webkit-transform': 'none',
									'-webkit-transition': 'none',
									'transition': 'none'
								}));
							}
						});

						editor.markdowneditor.data('fixedParents', transformed);
					}

					editor.editor.state.fullScreenRestore = { scrollTop: window.pageYOffset, scrollLeft: window.pageXOffset, width: wrap.style.width, height: wrap.style.height };
					wrap.style.width = '';
					wrap.style.height = editor.content.height() + 'px';
					document.documentElement.style.overflow = 'hidden';

				} else {

					document.documentElement.style.overflow = '';
					var info = editor.editor.state.fullScreenRestore;
					wrap.style.width = info.width; wrap.style.height = info.height;
					window.scrollTo(info.scrollLeft, info.scrollTop);

					if (editor.markdowneditor.data('fixedParents')) {
						editor.markdowneditor.data('fixedParents').forEach(function (parent) {
							parent.css(parent.data('transform-reset'));
						});
					}
				}

				setTimeout(function () {
					editor.fit();
					UI.$win.trigger('resize');
				}, 50);
			});

			editor.addShortcutAction('bold', ['Ctrl-B', 'Cmd-B']);

			var parser = editor.options.mdparser || window.marked || null;

			if (!parser) return;

			addAction('bold', '**$1**');
			addAction('italic', '*$1*');
			addAction('strike', '~~$1~~');
			addAction('blockquote', '> $1', 'replaceLine');
			
			editor.on('action.link', function () {
				editor.editor.AutomadLink();
			});

			editor.on('action.image', function() {
				editor.editor.AutomadSelectImage();
			});

			editor.on('action.listUl', function () {

				var cm = editor.editor,
					pos = cm.getDoc().getCursor(true),
					posend = cm.getDoc().getCursor(false);

				for (var i = pos.line; i < (posend.line + 1); i++) {
					cm.replaceRange('* ' + cm.getLine(i), { line: i, ch: 0 }, { line: i, ch: cm.getLine(i).length });
				}

				cm.setCursor({ line: posend.line, ch: cm.getLine(posend.line).length });
				cm.focus();
			});

			editor.on('action.listOl', function () {

				var cm = editor.editor,
					pos = cm.getDoc().getCursor(true),
					posend = cm.getDoc().getCursor(false),
					prefix = 1;

				if (pos.line > 0) {
					var prevline = cm.getLine(pos.line - 1), matches;

					if (matches = prevline.match(/^(\d+)\./)) {
						prefix = Number(matches[1]) + 1;
					}
				}

				for (var i = pos.line; i < (posend.line + 1); i++) {
					cm.replaceRange(prefix + '. ' + cm.getLine(i), { line: i, ch: 0 }, { line: i, ch: cm.getLine(i).length });
					prefix++;
				}

				cm.setCursor({ line: posend.line, ch: cm.getLine(posend.line).length });
				cm.focus();

			});

			editor.on('renderLate', function () {


				// Fix preview images.
				var pagePath = $('[data-am-path]').data('amPath');

				editor.currentvalue = editor.currentvalue.replace(/!\[([^\]]*)\]\(([^\)]+)\)/g, function (match, alt, file) {

					if (file.includes('://')) {
						return match;
					}

					if (file.startsWith('/')) {
						var path = '.';
					} else {
						var path = 'pages' + pagePath;
					}

					return '![' + alt + '](' + path + file + ')';
					
				})

				editor.currentvalue = parser(editor.currentvalue);

			});

			editor.on('cursorMode', function (e, param) {
				
				var pos = editor.editor.getDoc().getCursor();
				
				if (!editor.editor.getTokenAt(pos).state.base.htmlState) {
					param.mode = 'markdown';
				}

			});

			function addAction(name, replace, mode) {

				editor.on('action.' + name, function () {
					editor[mode == 'replaceLine' ? 'replaceLine' : 'replaceSelection'](replace);
					
				});

			}

		}
	});

	return UI.markdowneditor;

});
