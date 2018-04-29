<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ ../snippets/teaser.php @>
	<@ am/mail {
		to: @{ email },
		success: '<h2>Successfully sent email!</h2>',
		error: '<h2>Please fill out all fields!</h2>'
	} @>
	<form class="uk-form uk-margin-large-top" action="@{ url }" method="post">
		<input type="text" name="human" value="" />		
		<div class="uk-form-row">
			<input 
			class="uk-form-controls uk-width-1-1" 
			type="text" 
			name="from" 
			value="" 
			placeholder="Your Email" 
			/>
		</div>
		<div class="uk-form-row">
			<input 
			class="uk-form-controls uk-width-1-1" 
			type="text" 
			name="subject" 
			value="" 
			placeholder="Your Subject" 
			/>
		</div>
		<div class="uk-form-row">
			<textarea 
			class="uk-form-controls uk-width-1-1" 
			name="message" 
			rows="5" 
			placeholder="Your Message"
			></textarea>
		</div>
		<button class="uk-button uk-margin-large-top" type="submit">
			<i class="uk-icon-paper-plane"></i>&nbsp;
			Send Mail
		</button>
	</form>
	
<@ snippets/footer.php @>