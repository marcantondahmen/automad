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
 * Copyright (c) 2014-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Get system status items.
 *
 * To get the status of any config setting (constant),
 * the container has to have a 'data-am-status' attribute.
 * The requested item for each container is then passed via
 * 'data-am-status="item"'.
 */

+(function (Automad, $, UIkit) {
	Automad.Status = {
		dataAttr: 'data-am-status',

		get: function () {
			var s = Automad.Status;

			$('[' + s.dataAttr + ']').each(function () {
				var $container = $(this),
					item = $container.data(
						Automad.Util.dataCamelCase(s.dataAttr)
					);

				$.post(
					'?controller=Status::get',
					{ item: item },
					function (data) {
						$container.html(data.status);
					},
					'json'
				);
			});
		},

		init: function () {
			var $doc = $(document),
				s = Automad.Status;

			$doc.ready(function () {
				s.get();
			});

			$doc.ajaxComplete(
				UIkit.Utils.debounce(function (e, xhr, settings) {
					var triggers = [
						'?controller=UserCollection::edit',
						'?controller=UserCollection::createUser',
						'?controller=UserCollection::inviteUser',
						'?controller=Headless::editTemplate',
						'?controller=Headless::resetTemplate',
						'?controller=Config::update',
						'?controller=PackageManager::getPackages',
						'?controller=System::update',
					];

					if (triggers.includes(settings.url)) {
						s.get();
					}
				}, 1000)
			);
		},
	};

	Automad.Status.init();
})((window.Automad = window.Automad || {}), jQuery, UIkit);
