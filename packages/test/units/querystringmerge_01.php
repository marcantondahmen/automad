<@ queryStringMerge {
	source: false,
	key1: @{ test | sanitize },
	"key2": "another-@{ var | def ("Test Value") | sanitize }",
	key3: @{ x | +5 }
} @>