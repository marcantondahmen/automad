<?php

namespace Automad;

function test($options) {
	$json = json_encode($options);

	return $json;
}

?>

<@~ Automad/test { text: "Hello" } ~@>
