# Mail Extension

The *Mail* extension provides a basic wrapper for the PHP function mail(), including optional human verification using a honeypot. 

---

## Markup

The basic markup requires the method call as well as a contact form.    
Note that the names of the form fields must match the following example:

	<@ standard/mail { to: @{ email } } @>

	<form action="@{ url }" method="post">
	
		<!-- The honeypot - this input will be hidden by the included CSS -->
		<input type="text" name="human" value="">
		
		<!-- The actual form fields and button -->
		<input type="text" name="from" value="" placeholder="Your Email">
		<input type="text" name="subject" value="" placeholder="Your Subject">
		<textarea name="message" placeholder="Your Message"></textarea>
		<button class="uk-button" type="submit">Send</button>
	
	</form>
	
---

## Options

The following options can be specified:

* `to`: The receiving email address
* `error`: The message to be displayed on errors
* `success`: The message to be displayed on success 

A markup with all options specified:

	<@ standard/mail { 
		to: @{ email },
		error: 'Error Message ...',
		success: 'Success Message ...'
	} @>

