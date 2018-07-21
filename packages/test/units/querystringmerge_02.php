<@ queryStringMerge {
	source: false,
	key1: @{ var | def ('Some, "key": "value", pair.') | sanitize }
} @>