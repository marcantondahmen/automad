<?php

func('test', function ($options) {
	return json_encode($options);
});

?>

<@~ test { text: "Hello" } ~@>
