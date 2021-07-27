<?php

return (new PhpCsFixer\Config())
	->setRules([
		'@PSR2' => true,
		'align_multiline_comment' => ['comment_type' => 'all_multiline'],
		'array_indentation' => true,
		'array_syntax' => ['syntax' => 'long'],
		'blank_line_after_opening_tag' => false,
		'blank_line_before_statement' => true,
		'braces' => [
			'allow_single_line_closure' => true,
			'position_after_functions_and_oop_constructs' => 'same'
		],
		'concat_space' => [
			'spacing' => 'one'
		],
		'ordered_class_elements' => [
			'sort_algorithm' => 'alpha'
		],
		'multiline_comment_opening_closing' => true,
		'no_blank_lines_after_class_opening' => false,
		'no_blank_lines_after_phpdoc' => true,
		'no_closing_tag' => true,
		'no_extra_blank_lines' => true,
		'no_extra_blank_lines' => [
			'tokens' => [
				'curly_brace_block',
				'extra',
				'parenthesis_brace_block',
				'square_brace_block',
				'throw',
				'use',
			]
		],
		'no_spaces_after_function_name' => true,
		'no_trailing_comma_in_list_call' => true,
		'no_trailing_comma_in_singleline_array' => true,
		'no_trailing_whitespace' => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line' => true,
		'phpdoc_add_missing_param_annotation' => true,
		'phpdoc_align' => [
			'align' => 'left'
		],
		'phpdoc_indent' => true,
		'phpdoc_order' => true,
		'phpdoc_trim' => true,
		'return_type_declaration' => true,
		'single_blank_line_before_namespace' => true,
		'single_blank_line_at_eof' => true,
		'single_line_after_imports' => true,
		'single_quote' => true
	])
	->setIndent("\t")
	->setLineEnding("\n")
;