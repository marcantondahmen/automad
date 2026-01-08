<@ newPagelist { search: 'find me', sort: ':index asc' } @>
<@~ foreach in pagelist @>[@{ url }]: @{ :searchResultsContext | stripTags } <@ end @>
<@~ newPagelist { search: 'nofind', sort: ':index asc' } @>
<@~ foreach in pagelist @>[@{ url }]: @{ :searchResultsContext | stripTags } <@ end @>
