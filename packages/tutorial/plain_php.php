<?php 
// All classes used in this example are in the Automad\Core namespace.
namespace Automad\Core;
defined('AUTOMAD') or die('Direct access not permitted!'); 
// First the currently active page object and the pagelist instance are stored in variables 
// and a fresh instance of the Toolbox class is created.
$Page = $Automad->Context->get(); 
$Pagelist = $Automad->getPagelist();
$Toolbox = new Toolbox($Automad);
?>
<# 
PHP can be used side-by-side with Automad's template syntax as long as the different code blocks are 
not related functionally. 
While PHP code will always be parsed first, using the Automad syntax for includes of headers or 
footers works perfectly fine since the order of parsing is not relevant here.
#>
<@ snippets/header.php @>
		<@ snippets/navbar.php @>
		<section class="section">
			<div class="content">
				<h1>
					<?php 
					// The 'get()' method of the Page object can used to access any content variable of a Page.
					echo $Page->get('title'); 
					?>
				</h1>
				<div class="is-size-5">
					<?php 
					// The 'Str' class contains multiple useful functions to manipulate the content of variables.
					echo Str::markdown($Page->get('textTeaser')); 
					?>
				</div>
			</div>
			<div class="field">
				<a href="#source" class="button is-light">Jump to Source</a>
			</div>
		</section>
		<section class="section">
			<?php 
			// As a next step, the pagelist instance is configured.
			// Note that query string parameters get used as parameter values to make the pagelist
			// controllable by a menu.
			$Pagelist->config(array(
				'filter' => Request::query('filter'),
				'match' => '{":level": "/(1|2)/"}',
				'search' => Request::query('search'),
				'sort' => Str::def(Request::query('sort'), 'date desc')
			));
			?>
			<# A simple filter menu lets the user filter the paglist dynamically. #>
			<div class="field is-grouped is-grouped-multiline is-marginless">
				<div class="control">
					<div class="field has-addons">
						<p class="control">
							<a 	
							<#
							The first button in the menu resets the filter.
							Note that the 'queryStringMerge' method is used here for the href attribute value to only 
							modify the filter parameter within an existing query string without resetting other options. 
							#>
							href="?<?php echo $Toolbox->queryStringMerge(array('filter' => false)); ?>"
							class="button is-info<?php if (!Request::query('filter')) { ?> is-active<?php } ?>">
								All
							</a>
						</p>
						<?php 
						// The 'getTags()' method returns all available tags of pages in the pagelist.
						// All tags are stored in an iteratable array and can be used to build a filter menu.
						foreach ($Pagelist->getTags() as $filter) { 
						?>
							<p class="control">
								<a 
								href="?<?php echo $Toolbox->queryStringMerge(array('filter' => $filter)); ?>" 
								class="button is-info<?php if (Request::query('filter') == $filter) { ?> is-active<?php } ?>">
									<?php echo $filter; ?>
								</a>
							</p>
						<?php } ?>
					</div>
				</div>
				<# The sorting menu. #>
				<div class="control">
					<div class="field has-addons">
						<p class="control">
							<a 
							href="?<?php echo $Toolbox->queryStringMerge(array('sort' => 'date desc')); ?>"
							class="button is-info<?php if (!Request::query('sort') || Request::query('sort') == 'date desc') { ?> is-active<?php } ?>"
							>
								<span class="icon is-small">
									<i class="fas fa-sort-numeric-down" aria-hidden="true"></i>
								</span>&nbsp;
								Date
							</a>
						</p>
						<p class="control">
							<a 
							href="?<?php echo $Toolbox->queryStringMerge(array('sort' => 'title asc')); ?>"
							class="button is-info<?php if (Request::query('sort') == 'title asc') { ?> is-active<?php } ?>"
							>
								<span class="icon is-small">
									<i class="fas fa-sort-alpha-up" aria-hidden="true"></i>
								</span>&nbsp;
								Title
							</a>
						</p>
					</div>
				</div>
				<# A normal form is used to create the keyword search field. #>
				<div class="control">
					<form action="" method="get">
						<input 
						class="input" 
						type="text" 
						name="search" 
						placeholder="Keyword" 
						value="<?php echo Request::query('search'); ?>"
						/>
					</form>
				</div>
			</div>
			<br />
			<# The pagelist markup. #>
			<div class="columns is-multiline is-8 is-variable">
				<?php 
				// The 'getPages()' method returns an array of all page objects of an pagelist instance.
				foreach ($Pagelist->getPages() as $Page) { 
				?>
					<div class="column is-one-quarter">
						<hr />
						<div class="field">
							<span class="is-size-5 has-text-weight-bold">
								<?php echo $Page->get('title'); ?>
							</span>
							<br />
							<span class="is-size-7">
								<?php echo Str::dateFormat($Page->get('date'), 'F Y'); ?>
							</span>
						</div>
						<div class="field is-size-6">
							<?php 
								// Use the first paragraph block of the page or the textTeaser as fallback.
								echo Str::shorten(
										Str::stripTags(
											Str::def(
												trim(Str::findFirstParagraph(Blocks::render($Page->get('+main'), $Automad))), 
												$Page->get('textTeaser')
											)
										), 
										100
									 ); 
							?>
						</div>
						<a 
						href="<?php echo $Page->get('url'); ?>" 
						class="button is-light is-small"
						>
							More
						</a>
					</div>
				<?php } ?>
			</div>
		</section>
<# As the last step, the footer markup is included. #>
<@ snippets/footer.php @>