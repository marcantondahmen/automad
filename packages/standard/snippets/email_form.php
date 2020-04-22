<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	
	<@~ standard/mail {
		to: @{ email },
		success: '<div class="uk-margin-bottom">
					@{ notificationMailSuccess |
						def ("**Successfully sent email!**") |
						markdown
					}
				  </div>',
		error: '<div class="uk-margin-bottom">
					@{ notificationMailError | 
						def ("**Please fill out all fields!**") |
						markdown
					}
				</div>'
	} ~@>
	<form class="uk-form" action="@{ url }" method="post">
		<input type="text" name="human" value="" />		
		<div class="uk-form-row">
			<input 
			class="uk-form-controls uk-width-1-1" 
			type="text" 
			name="from" 
			value="" 
			placeholder="@{ placeholderEmail | def ('Your Email Address')}" 
			required
			/>
		</div>
		<div class="uk-form-row">
			<input 
			class="uk-form-controls uk-width-1-1" 
			type="text" 
			name="subject" 
			value="" 
			placeholder="@{ placeholderSubject | def ('Subject') }" 
			required
			/>
		</div>
		<div class="uk-form-row">
			<textarea 
			class="uk-form-controls uk-width-1-1" 
			name="message" 
			rows="5" 
			placeholder="@{ placeholderMessage | def ('Message') }"
			required
			></textarea>
		</div>
		<button class="uk-button uk-margin-small-top" type="submit">
			<svg class="bi bi-envelope" width="1.45em" height="1.45em" viewBox="0 1 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  				<path fill-rule="evenodd" d="M14 3H2a1 1 0 00-1 1v8a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1zM2 2a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H2z" clip-rule="evenodd"/>
  				<path fill-rule="evenodd" d="M.071 4.243a.5.5 0 01.686-.172L8 8.417l7.243-4.346a.5.5 0 01.514.858L8 9.583.243 4.93a.5.5 0 01-.172-.686z" clip-rule="evenodd"/>
  				<path d="M6.752 8.932l.432-.252-.504-.864-.432.252.504.864zm-6 3.5l6-3.5-.504-.864-6 3.5.504.864zm8.496-3.5l-.432-.252.504-.864.432.252-.504.864zm6 3.5l-6-3.5.504-.864 6 3.5-.504.864z"/>
			</svg>&nbsp;
			@{ labelSendMail | def ('Send') }
		</button>
	</form>