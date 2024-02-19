<@ newPagelist { search: 'find me', sort: ':index asc' } @>
<@~ foreach in pagelist @>[@{ url }]: @{ :searchContext | stripTags } <@ end @>
<@~ newPagelist { search: 'nofind', sort: ':index asc' } @>
<@~ foreach in pagelist @>[@{ url }]: @{ :searchContext | stripTags } <@ end @>