<# Add a comma if item is not the first one. #>
<@ snippet comma @>
	<@~ if @{ :i } > 1 ~@>
	,
	<@~ end ~@>
<@ end @>

<# Generate JSON for a pagelist #>
<@ snippet pages ~@>
	[
		<@~ foreach in pagelist ~@>
			<@ comma @>{"url":"@{ :origUrl }","title":"@{ title }","date":"@{ date }"}
		<@~ end ~@>
	]
<@~ end @>

<# Generate JSON for tags. #>
<@ snippet tags ~@>
	[
		<@~ foreach in tags ~@>
			<@ comma @>"@{ :tag }"
		<@~ end ~@>
	]
<@~ end @>

<# Generate JSON for the file list. #>
<@ snippet files ~@>
	[
		<@~ foreach in '*.jpg, *.png, *.gif' ~@>
			<@ comma @>{"url":"@{ :file }","caption":"@{ :caption }"}
		<@~ end ~@>
	]
<@~ end @>

<# Generate JSON for the children of the current page. #>
<@ snippet children @>
	<@~ newPagelist {
		excludeHidden: false,
		type: "children"
	} ~@>
	<@ pages ~@>
<@ end @>

<# Generate JSON for the siblings of the current page. #>
<@ snippet siblings @>
	<@~ newPagelist {
		excludeHidden: false,
		excludeCurrent: true,
		type: "siblings"
	} ~@>
	<@ pages ~@>
<@ end @>

<# Generate JSON for related pages. #>
<@ snippet related @>
	<@~ newPagelist {
		excludeHidden: false,
		type: "related"
	} ~@>
	<@ pages ~@>
<@ end @>

<# Generate JSON for the search results list. #>
<@ snippet results @>
	<@~ newPagelist {
		excludeHidden: false,
		search: @{ ?search },
		filter: @{ ?filter }
	} ~@>
	<@ pages ~@>
<@ end @>

<# The actual JSON content. #>
<@ if @{ ?search } or @{ ?filter } @>
[
	<@ results @>
]
<@ else @>
{	
	"title": "@{ title }",
	"date": "@{ date }",
	"text": "@{ text | markdown }",
	"teaser": "@{ textTeaser | markdown }",
	"hidden": "@{ hidden }",
	"parent": "@{ :parent }",
	"path": "@{ :path }",
	"mtime": "@{ :mtime }",
	"tags": <@ tags @>,
	"children": <@ children @>,
	"siblings": <@ siblings @>,
	"related": <@ related @>,
	"files": <@ files @>
}
<@ end @>