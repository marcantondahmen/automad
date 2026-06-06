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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	confirm,
	create,
	CSS,
	debounce,
	fire,
	html,
	requestAPI,
	Route,
	SetupWizardController,
	transition,
} from '@/admin/core';
import { SetupWizardStep } from '@/admin/types';
import { renderAiSection } from '@/admin/components/Pages/Partials/System/Ai';
import { renderMailSection } from '@/admin/components/Pages/Partials/System/Mail';

/**
 * The content of each possible step.
 */
const steps: {
	[key in SetupWizardStep]: { title: () => string; body: () => string };
} = {
	ai: { title: () => App.text('systemAi'), body: renderAiSection },
	mailConfig: {
		title: () => App.text('systemMail'),
		body: renderMailSection,
	},
} as const;

/**
 * A setup wizard component.
 *
 * @extends BaseComponent
 */
class SetupWizardComponent extends BaseComponent {
	/**
	 * The name for the event that is fired when the current step changes.
	 *
	 * @static
	 */
	private static STEP_CHANGE_EVENT = 'AutomadSetupWizardStepChange';

	/**
	 * The current step index.
	 */
	private _currentIndex = 0;

	/**
	 * The setter for the current index.
	 */
	private set currentIndex(index: number) {
		this._currentIndex = index;

		fire(SetupWizardComponent.STEP_CHANGE_EVENT, this);
	}

	/**
	 * The getter for the current index.
	 */
	private get currentIndex(): number {
		return this._currentIndex;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Initialize the wizard.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		const { data } = await requestAPI(SetupWizardController.getSteps);
		const { steps: stepKeys } = data as { steps: SetupWizardStep[] };

		if (stepKeys.length == 0) {
			await this.finish();

			return;
		}

		this.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGapLarge);

		const body = create(
			'section',
			[CSS.flex, CSS.flexColumn, CSS.flexGapLarge],
			{},
			this
		);

		const header = create('div', [], {}, body);

		const container = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			body
		);

		create('hr', [], {}, this);

		const nav = create(
			'div',
			[CSS.flex, CSS.flexBetween, CSS.flexGap],
			{},
			this
		);

		const navLeft = create('div', [], {}, nav);
		const navRight = create('div', [], {}, nav);

		const buttonPrev = create(
			'button',
			[CSS.button],
			{},
			navLeft,
			App.text('wizardButtonBack')
		);

		const buttonNext = create(
			'button',
			[CSS.button, CSS.buttonPrimary],
			{},
			navRight,
			App.text('wizardButtonNext')
		);

		const buttonFinish = create(
			'button',
			[CSS.button, CSS.buttonPrimary],
			{},
			navRight,
			App.text('wizardButtonFinish')
		);

		const render = this.renderStep.bind(this, stepKeys, container);

		this.listen(
			buttonPrev,
			'click',
			debounce(() => {
				this.currentIndex -= 1;
			})
		);

		this.listen(
			buttonNext,
			'click',
			debounce(() => {
				this.currentIndex += 1;
			})
		);

		this.listen(this, SetupWizardComponent.STEP_CHANGE_EVENT, () => {
			transition(() => {
				buttonPrev.classList.toggle(
					CSS.displayNone,
					this.currentIndex < 1
				);

				buttonNext.classList.toggle(
					CSS.displayNone,
					this.currentIndex >= stepKeys.length - 1
				);

				buttonFinish.classList.toggle(
					CSS.displayNone,
					this.currentIndex + 1 !== stepKeys.length
				);

				header.innerHTML = html`
					<h2>
						${App.text('wizardTitle')}
						${stepKeys.length > 1
							? html` &mdash; ${this.currentIndex + 1} /
								${stepKeys.length}`
							: ''}
					</h2>
					<p>${App.text('wizardSubtitle')}</p>
					<hr />
				`;

				render();
			});
		});

		this.listen(buttonFinish, 'click', this.finish);

		this.currentIndex = 0;
	}

	/**
	 * Render the content of a step.
	 *
	 * @param keys
	 * @param container
	 */
	private renderStep(keys: SetupWizardStep[], container: HTMLElement): void {
		const index =
			this.currentIndex >= keys.length
				? 0
				: this.currentIndex < 0
					? keys.length - 1
					: this.currentIndex;

		const key = keys[index];

		if (!key) {
			return;
		}

		const { title, body } = steps[key];

		container.innerHTML = html`
			<h1>${title()}</h1>
			<div>${body()}</div>
		`;
	}

	/**
	 * Finish the wizard.
	 *
	 * @async
	 */
	private async finish(): Promise<void> {
		if (
			!(await confirm(
				App.text('wizardFinishDialogText'),
				App.text('wizardFinishDialogButton')
			))
		) {
			return;
		}

		await requestAPI(SetupWizardController.finish, { finish: true });

		const base = `${window.location.origin}${App.dashboardURL}/`;

		App.root.setView(new URL(Route.home, base));
	}
}

customElements.define('am-setup-wizard', SetupWizardComponent);
