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
 * Copyright (c) 2018-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * 	Copy image resized.
 */

+(function (Automad, $, UIkit) {
	Automad.CopyResized = {
		dataAttr: {
			fileInfo: 'data-am-file-info',
			url: 'data-am-url',
		},

		selectors: {
			modal: '#am-copy-resized-modal',
			button: '#am-copy-resized-submit',
			filename: '#am-copy-resized-filename',
			width: '#am-copy-resized-width',
			height: '#am-copy-resized-height',
			crop: '#am-copy-resized-crop',
		},

		destroy: function () {
			var s = Automad.CopyResized.selectors;

			$(s.filename).val('');
			$(s.width).val('');
			$(s.height).val('');
		},

		init: function () {
			var cr = Automad.CopyResized,
				da = cr.dataAttr,
				s = cr.selectors,
				$panel = $(this).closest('[' + da.fileInfo + ']'),
				info = $panel.data(Automad.Util.dataCamelCase(da.fileInfo)),
				$modal = $(s.modal);

			$(s.filename).val(info.filename);
			$(s.width).val(info.img.originalWidth);
			$(s.height).val(info.img.originalHeight);

			// Events.
			// Remove previously attached events.
			$modal.off('hide.uk.modal.automad.copyResized');

			// Define event to destroy info on hide (Close button or esc key - without submission).
			$modal.on('hide.uk.modal.automad.copyResized', cr.destroy);
		},

		submit: function (e) {
			var cr = Automad.CopyResized,
				s = cr.selectors,
				$button = $(e.target),
				param = {
					url: $(s.modal).data(
						Automad.Util.dataCamelCase(cr.dataAttr.url)
					), // If URL is empty, the "/shared" files will be managed.
					filename: $(s.filename).val(),
					width: $(s.width).val(),
					height: $(s.height).val(),
					crop: Number($(s.crop).is(':checked')),
				};

			// Temporary disable button to avoid submitting the form twice.
			$button.prop('disabled', true);

			// Post form data to the controller.
			$.post(
				'?controller=Image::copyResized',
				param,
				function (data) {
					// Re-enable button again after AJAX call.
					$button.prop('disabled', false);

					if (data.error) {
						Automad.Notify.error(data.error);
					}

					if (data.success) {
						Automad.Notify.success(data.success);
					}

					// Wait for the modal to be hidden before refreshing the filelist,
					// otherwise the modal class wouldn't be removed from the <html> element
					// and the UI will stay blocked.
					$(s.modal).on(
						'hide.uk.modal.automad.copyResized',
						function () {
							// Remove info before refreshing the file list.
							cr.destroy();
							// Refresh file list (empty & submit).
							$(s.modal).closest('form').empty().submit();
						}
					);

					// Close modal.
					UIkit.modal(s.modal).hide();
				},
				'json'
			);
		},
	};

	// Modal setup.
	$(document).on(
		'click',
		'a[href="' + Automad.CopyResized.selectors.modal + '"]',
		Automad.CopyResized.init
	);

	// Submit AJAX call.
	$(document).on(
		'click',
		Automad.CopyResized.selectors.button,
		Automad.CopyResized.submit
	);
})((window.Automad = window.Automad || {}), jQuery, UIkit);
