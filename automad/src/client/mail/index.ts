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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import './styles.less';

/**
 * Create an ascii map for a given string.
 *
 * @param str
 * @return the array of ascii codes
 */
const convertToAsciiMap = (str: string): number[] => {
	const charCodes: number[] = [];

	for (let i = 0; i < str.length; i++) {
		charCodes.push(str.charCodeAt(i));
	}

	return charCodes;
};

/**
 * Decrypt a string that was encrypted with an xor algorithm using a given key.
 *
 * @param encrypted
 * @param key
 * @return the decrypted string
 */
const decrypt = (encrypted: string, key: string): string => {
	const keyCodes = convertToAsciiMap(key);
	const keySize = keyCodes.length;
	const encryptedCodes = convertToAsciiMap(atob(encrypted));
	const encryptedSize = encryptedCodes.length;

	let decrypted = '';

	for (let i = 0; i < encryptedSize; i++) {
		decrypted += String.fromCharCode(
			encryptedCodes[i] ^ keyCodes[i % keySize]
		);
	}

	return decrypted.replace(/\0/g, '');
};

/**
 * Initialize the mail address handler.
 */
const initMailHandler = (): void => {
	const links = Array.from(
		document.querySelectorAll<HTMLAnchorElement>('[data-eml]')
	);

	links.forEach((link) => {
		const encrypted = link.dataset.eml;
		const key = link.dataset.key;

		link.removeAttribute('data-eml');
		link.removeAttribute('data-key');

		link.addEventListener('click', () => {
			link.href = `mailto:${decrypt(encrypted, key)}`;
		});
	});
};

initMailHandler();
