<?php 

defined('AUTOMAD') or die('Direct access not permitted!'); 

$View = new Automad\Core\View($Automad);
$Page = $Automad->Context->get();

$data = array(
	'test' => 'Test String',
	'x' => '10'
);

$Page->data = array_merge($Page->data, $data);

$units = array(
	'pipe_string_01' => 'Test String',
	'pipe_string_02' => 'This is a "Test String"',
	'pipe_string_03' => 'This is a "Test String"',
	'pipe_replace_01' => 'Some {test} string',
	'pipe_math_01' => '15',
	'pipe_math_02' => '50',
	'for_01' => '1, 2, 3, 4, 5',
	'if_01' => 'True',
	'querystringmerge_01' => 'source=0&key1=test-string&key2=another-test-value&key3=15'
);

$tests = array();
$passed = 0;

foreach ($units as $file => $expected) {
	
	$unit = trim(file_get_contents(__DIR__ . '/units/' . $file . '.php'));				
	$result = trim($View->interpret($unit, __DIR__ . '/units'));
	
	if ($result == $expected) {
		$tag = '<span class="tag is-success">Succeeded</span>';
		$color = '';
		$passed++;
	} else {
		$tag = '<span class="tag is-danger">Failed</span>';
		$color = 'has-text-danger';
	}
	
	$tests[$file] = array(
		'expected' => $expected,
		'result' => $result,
		'tag' => $tag,
		'color' => $color
	);
	
}

?> 
<@ snippets/header.php @>
		<section class="section">
			<div class="content">
				<?php if ($passed == count($tests)) { ?>
					<span class="tag is-medium is-success">
						<?php echo $passed . '/' . count($tests); ?> Tests Succeeded
					</span>
				<?php } else { ?>
					<span class="tag is-medium is-danger">
						<?php echo $passed . '/' . count($tests); ?> Tests Succeeded
					</span>
				<?php } ?>
			</div>
			<div class="content">
				<@ if @{ ?source } @>
					<a 
					href="@{ url }?source=0" 
					class="button is-light is-small"
					>
						<span class="icon is-small">
							<i class="fas fa-check-circle"></i>
						</span>
						<span>Source</span>
					</a>
				<@ else @>
					<a 
					href="@{ url }?source=1" 
					class="button is-light is-small"
					>
						<span class="icon is-small">
							<i class="far fa-circle"></i>
						</span>
						<span>Source</span>
					</a>
				<@ end @>
			</div>
			<br />
			<?php 
				
				foreach ($tests as $file => $test) {
					
					?>
						<div class="content">
							<div class="tags has-addons">
								<?php echo $test['tag']; ?>
								<span class="tag"><?php echo $file; ?></span>
							</div>
							<@ if @{ ?source } @>
								<pre><#
									#><@ test/source {
										file: '/packages/@{ theme }/units/<?php echo $file; ?>.php' 
									} @><br /><#
									#><span class="has-text-grey-light"><#
										#><br /><#
										#>Expected:  <?php echo $test['expected']; ?><#
										#><br /><span class="<?php echo $test['color']; ?>"><#
										#>Result:    <?php echo $test['result']; ?></span> <#
									#></span><#
								#></pre>
								<br />
							<@ end @>
						</div>	
					<?php	
		
				}

			?>
		</section>
<@ snippets/footer.php @>