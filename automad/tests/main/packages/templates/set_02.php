@{ +setInSnippet }
<@ snippet test @>
	<@ set { custom: 'not this value' } @>
<@ end @>
<@ test @>
@{ custom }
