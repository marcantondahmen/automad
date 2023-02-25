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

import { Logger } from '../types';

/**
 * The logger factory.
 *
 * @returns the logger instance based on the current environment
 */
export const getLogger = (): Logger => {
	if (DEVELOPMENT) {
		return new DevelopmentLogger();
	}

	return new ProductionLogger();
};

/**
 * Get the calling function from the stack.
 *
 * @returns the calling function
 */
const getCaller = (): string => {
	const funcRegex = /^\s+at\s(?<func>\S+)\s\([^\)]+\)$/gm;
	const stack = [];

	for (const match of new Error('').stack.matchAll(funcRegex)) {
		const func = match.groups.func;

		if (!func.match(/(Generator\.next|__awaiter|Object\.|Array\.)/)) {
			stack.push(func);
		}
	}

	return stack[3] || '';
};

/**
 * Compose an array of log items.
 *
 * @param args
 * @returns an array of items that should be logged
 */
const compose = (...args: any[]): any[] => {
	return [`%c[${getCaller()}]`, 'font-weight: bold;', ...args];
};

/**
 * The logger class being used during development.
 */
class DevelopmentLogger implements Logger {
	error(...args: any[]): void {
		console.error(...compose(...args));
	}

	log(...args: any[]): void {
		console.log(...compose(...args));
	}
}

/**
 * The logger class being used in production.
 */
class ProductionLogger implements Logger {
	error(...args: any[]): void {
		console.error(...args);
	}

	log(...args: any[]): void {}
}
