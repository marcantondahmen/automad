<?php

namespace Test;

defined('AUTOMAD') or die('Direct access not permitted!');

class Source {
	
	
	public function Source($options, $Automad) {
		
		$defaults = array('file' => false);
		$options = array_merge($defaults, $options);
		
		$source = file_get_contents(AM_BASE_DIR . $options['file']);
		$source = htmlentities(str_replace("\t", '  ', $source));
	
		return $source;
		
	}
	
	
}