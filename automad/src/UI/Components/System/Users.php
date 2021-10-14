<?php
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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\System;

use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The users system setting component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Users {
	/**
	 * Renders the users component.
	 *
	 * @return string The rendered HTML
	 */
	public static function render() {
		$Text = Text::getObject();
		$UserCollectionModel = new UserCollectionModel();
		$username = Session::getUsername();
		$User = $UserCollectionModel->getUser($username);
		$email = $User->email;

		return <<< HTML
			<p>$Text->sys_user_info</p>
			<form 
			class="uk-form uk-form-stacked uk-margin-top uk-margin-bottom"
			data-am-controller="User::edit"
			>
				<div class="uk-grid uk-grid-width-large-1-2">
					<div class="uk-form-row">
						<label for="am-user-name" class="uk-form-label uk-margin-top-remove">$Text->sys_user_name</label>
						<input id="am-user-name" type="text" class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" name="username" value="$username" required>
					</div>
					<div class="uk-form-row">
						<label for="am-user-email" class="uk-form-label uk-margin-top-remove">$Text->sys_user_email</label>
						<input id="am-user-email" type="email" class="uk-form-controls uk-width-1-1 uk-margin-small-bottom" name="email" value="$email" required>
					</div>
				</div>
				<button 
				type="button" 
				class="uk-button uk-button-success"
				data-am-submit="User::edit"
				>
					<i class="uk-icon-check"></i>&nbsp;
					$Text->btn_save
				</button>
			</form>
			<!-- Change password -->
			<a 
			href="#am-change-password-modal" 
			class="uk-button uk-button-success" 
			data-uk-modal
			>
				<i class="uk-icon-unlock-alt"></i>&nbsp;
				$Text->sys_user_change_password
			</a>
			<div id="am-change-password-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						$Text->sys_user_change_password
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<form 
					class="uk-form" 
					data-am-controller="User::changePassword" 
					data-am-close-on-success="#am-change-password-modal"
					>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="password" 
						name="current-password" 
						placeholder="$Text->sys_user_current_password"  
						data-am-enter="#am-change-password-submit" 
						required
						/>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="password" 
						name="new-password1" 
						placeholder="$Text->sys_user_new_password"  
						data-am-enter="#am-change-password-submit" 
						required
						/>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="password" 
						name="new-password2" 
						placeholder="$Text->sys_user_repeat_password"  
						data-am-enter="#am-change-password-submit" 
						required
						/>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</button>
							<button id="am-change-password-submit" type="submit" class="uk-button uk-button-success">
								<i class="uk-icon-check"></i>&nbsp;
								$Text->btn_save
							</button>
						</div>
					</form>
				</div>
			</div>
			
			<p class="uk-margin-large-top">$Text->sys_user_registered_info</p>
			<!-- Registered Users -->
			<a 
			href="#am-users-modal" 
			class="uk-button uk-button-large" 
			data-uk-modal 
			data-am-status="users"
			>
				$Text->sys_user_registered
				&nbsp;<span class="uk-badge">&nbsp;</span>
			</a>&nbsp;
			<div id="am-users-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						$Text->sys_user_registered
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<form 
					class="uk-form" 
					data-am-controller="UserCollection::edit" 
					data-am-init 
					data-am-confirm="$Text->confirm_delete_users"
					></form>
					<div class="uk-modal-footer uk-text-right">
						<button type="button" class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;
							$Text->btn_close
						</button>
						<button type="button" class="uk-button uk-button-success" data-am-submit="UserCollection::edit">
							<i class="uk-icon-user-times"></i>&nbsp;
							$Text->btn_remove_selected
						</button>
					</div>
				</div>
			</div>
			<br>
			<!-- Invite & Add User Buttons -->
			<div class="uk-button-group uk-margin-small-top">
				<a href="#am-invite-user-modal" class="uk-button" data-uk-modal>
					<i class="uk-icon-send"></i>&nbsp;
					$Text->sys_user_invite
				</a>
				<a href="#am-add-user-modal" class="uk-button" data-uk-modal>
					<i class="uk-icon-user-plus"></i>&nbsp;
					$Text->sys_user_add
				</a>
			</div>
			<!-- Invite User Modal -->
			<div id="am-invite-user-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						$Text->sys_user_invite
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<form 
					class="uk-form" 
					data-am-controller="UserCollection::inviteUser" 
					data-am-close-on-success="#am-invite-user-modal"
					>
						<input 
						class="uk-form-controls uk-form-large uk-width-1-1" 
						type="text" 
						name="username" 
						placeholder="$Text->sys_user_name"
						data-am-enter="#am-invite-user-submit" 
						required
						/>
						<input
						class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
						type="email" 
						name="email" 
						placeholder="$Text->sys_user_email"  
						data-am-enter="#am-invite-user-submit" 
						required
						/>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</button>
							<button id="am-invite-user-submit" type="submit" class="uk-button uk-button-success">
								<i class="uk-icon-send"></i>&nbsp;
								$Text->btn_invite
							</button>
						</div>
					</form>
				</div>
			</div>
			<!-- Add User Modal -->
			<div id="am-add-user-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						$Text->sys_user_add
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<form 
					class="uk-form" 
					data-am-controller="UserCollection::createUser" 
					data-am-close-on-success="#am-add-user-modal"
					>
						<input 
						class="uk-form-controls uk-form-large uk-width-1-1" 
						type="text" 
						name="username" 
						placeholder="$Text->sys_user_name"
						data-am-enter="#am-add-user-submit" 
						required
						/>
						<input
						class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
						type="email" 
						name="email" 
						placeholder="$Text->sys_user_email"  
						data-am-enter="#am-add-user-submit" 
						required
						/>
						<input 
						class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
						type="password" 
						name="password1" 
						placeholder="$Text->sys_user_password"  
						autocomplete="new-password"
						data-am-enter="#am-add-user-submit" 
						required
						/>
						<input
						class="uk-form-controls uk-width-1-1" 
						type="password" 
						name="password2" 
						placeholder="$Text->sys_user_repeat_password"  
						autocomplete="new-password"
						data-am-enter="#am-add-user-submit" 
						required
						/>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</button>
							<button id="am-add-user-submit" type="submit" class="uk-button uk-button-success">
								<i class="uk-icon-user-plus"></i>&nbsp;
								$Text->btn_add
							</button>
						</div>
					</form>
				</div>
			</div>
		HTML;
	}
}
