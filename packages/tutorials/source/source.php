<?php

namespace Tutorials;

defined('AUTOMAD') or die('Direct access not permitted!');

class Source {
	
	
	public function Source($options, $Automad) {
		
		$defaults = array('file' => false);
		$options = array_merge($defaults, $options);
		
		$source = file_get_contents(AM_BASE_DIR . $options['file']);
		$source = htmlentities(str_replace("\t", '  ', $source));
		$source = preg_replace(
			'/(&lt;#.*?#&gt;)/is', 
			'<span class="has-text-grey-light">$1</span>',
			$source
		);
		$source = preg_replace(
			'/(&lt;@.*?@&gt;)/is', 
			'<span class="has-text-info has-text-weight-semibold">$1</span>',
			$source
		);
		$source = preg_replace(
			'/(@\{.*?\})/is', 
			'<span class="has-text-info has-text-weight-bold">$1</span>',
			$source
		);
		$source = preg_replace(
			'/(&lt;\?php.*?\?&gt;)/is', 
			'<span class="has-text-info has-text-weight-semibold">$1</span>',
			$source
		);
		$source = preg_replace(
			'/(\/\/.*?\\n)/is', 
			'<span class="has-text-grey-light has-text-weight-normal">$1</span>',
			$source
		);
		
		return '<pre><code class="has-text-grey">' . $source . '</code></pre>';
		
	}
	
	
}