<?php 
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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\System;
use Automad\GUI\Text as Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The users system setting component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Users {


	/**
	 * 	Renders the users component.
	 * 
	 *	@return string The rendered HTML
	 */

	public static function render() {

		$Text = Text::getObject();

		return <<< HTML
				<p>$Text->sys_user_info</p>
				<!-- Registered Users -->
				<a 
				href="#am-users-modal" 
				class="uk-button uk-button-large uk-button-success" 
				data-uk-modal 
				data-am-status="users"
				></a>
				<div id="am-users-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							$Text->sys_user_registered
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form 
						class="uk-form" 
						data-am-handler="users" 
						data-am-init 
						data-am-confirm="$Text->confirm_delete_users"
						></form>
						<div class="uk-modal-footer uk-text-right">
							<button type="button" class="uk-modal-close uk-button">
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</button>
							<button type="button" class="uk-button uk-button-success" data-am-submit="users">
								<i class="uk-icon-user-times"></i>&nbsp;
								$Text->btn_remove_selected
							</button>
						</div>
					</div>
				</div>
				<!-- Add User -->
				<br />
				<a href="#am-add-user-modal" class="uk-button uk-margin-top" data-uk-modal>
					<i class="uk-icon-user-plus"></i>&nbsp;
					$Text->sys_user_add
				</a>
				<div id="am-add-user-modal" class="uk-modal">
					<div class="uk-modal-dialog">
						<div class="uk-modal-header">
							$Text->sys_user_add
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<form 
						class="uk-form" 
						data-am-handler="add_user" 
						data-am-close-on-success="#am-add-user-modal"
						>		
							<input 
							class="uk-form-controls uk-form-large uk-width-1-1" 
							type="text" 
							name="username" 
							placeholder="$Text->sys_user_add_name"
							data-am-enter="#am-add-user-submit" 
							required
							/>	
							<input 
							class="uk-form-controls uk-width-1-1 uk-margin-small-top" 
							type="password" 
							name="password1" 
							placeholder="$Text->sys_user_add_password"  
							autocomplete="new-password"
							data-am-enter="#am-add-user-submit" 
							required
							/>		
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="password2" 
							placeholder="$Text->sys_user_add_repeat"  
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
				<!-- Change Password -->
				<br />
				<a 
				href="#am-change-password-modal" 
				class="uk-button uk-margin-small-top" 
				data-uk-modal
				>
					<i class="uk-icon-key"></i>&nbsp;
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
						data-am-handler="change_password" 
						data-am-close-on-success="#am-change-password-modal"
						>
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="current-password" 
							placeholder="$Text->sys_user_change_password_current"  
							data-am-enter="#am-change-password-submit" 
							required
							/>
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="new-password1" 
							placeholder="$Text->sys_user_change_password_new"  
							data-am-enter="#am-change-password-submit" 
							required
							/>
							<input 
							class="uk-form-controls uk-width-1-1" 
							type="password" 
							name="new-password2" 
							placeholder="$Text->sys_user_change_password_repeat"  
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
HTML;

	}


}