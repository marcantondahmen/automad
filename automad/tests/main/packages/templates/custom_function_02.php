<?php

func('inherit', function ($options, $Automad) {
	return inc(__DIR__ . '/../snippets/definition.php', $Automad);
});

?>

<@~ inherit ~@>
<@~ includeTest ~@>
