<@ snippet test ~@>
	original
<@~ end @>
<@ test @>
<@ snippet test ~@>
	<@ snippet nested @>
		nested derived
	<@ end @>
	<@~ nested ~@>
<@~ end @>