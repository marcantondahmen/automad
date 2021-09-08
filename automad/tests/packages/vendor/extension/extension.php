<?php

namespace Vendor;

use Automad\Core\Automad;

class Extension {
	public function Extension(array $options, Automad $Automad) {
		return $options['parameter'];
	}
}
