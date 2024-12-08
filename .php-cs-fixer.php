<?php

return (new PhpCsFixer\Config())
	->setRules(array(
		'@PSR2' => true,
		'align_multiline_comment' => array('comment_type' => 'all_multiline'),
		'array_indentation' => true,
		'array_syntax' => array('syntax' => 'long'),
		'blank_line_after_opening_tag' => false,
		'blank_line_before_statement' => true,
		'braces_position' => array(
			'classes_opening_brace' => 'same_line',
			'functions_opening_brace' => 'same_line'
		),
		'concat_space' => array(
			'spacing' => 'one'
		),
		'ordered_class_elements' => array(
			'sort_algorithm' => 'alpha'
		),
		'ordered_imports' => true,
		'function_typehint_space' => true,
		'method_argument_space' => array(
			'on_multiline' => 'ensure_fully_multiline',
			'after_heredoc' => true
		),
		'multiline_comment_opening_closing' => true,
		'no_blank_lines_after_class_opening' => false,
		'no_blank_lines_after_phpdoc' => true,
		'no_closing_tag' => true,
		'no_extra_blank_lines' => array(
			'tokens' => array(
				'curly_brace_block',
				'extra',
				'parenthesis_brace_block',
				'square_brace_block',
				'throw',
				'use',
			)
		),
		'no_spaces_after_function_name' => true,
		'no_trailing_comma_in_list_call' => true,
		'no_trailing_comma_in_singleline_array' => true,
		'no_trailing_whitespace' => true,
		'no_unused_imports' => true,
		'no_whitespace_before_comma_in_array' => true,
		'no_whitespace_in_blank_line' => true,
		'nullable_type_declaration_for_default_null_value' => array(
			'use_nullable_type_declaration' => true
		),
		'phpdoc_add_missing_param_annotation' => array(
			'only_untyped' => false
		),
		'phpdoc_align' => array(
			'align' => 'left'
		),
		'phpdoc_indent' => true,
		'phpdoc_order' => true,
		'phpdoc_scalar' => true,
		'phpdoc_trim' => true,
		'phpdoc_trim_consecutive_blank_line_separation' => true,
		'return_type_declaration' => true,
		'phpdoc_types' => true,
		'phpdoc_types_order' => array(
			'null_adjustment' => 'always_last'
		),
		'single_blank_line_before_namespace' => true,
		'single_blank_line_at_eof' => true,
		'single_line_after_imports' => true,
		'single_quote' => true
	))
	->setIndent("\t")
	->setLineEnding("\n")
;
