<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<# 
Like in all other tutorial templates, the header, 
navbar and content snippets are include first. 
#>
<@ snippets/header.php @>	
	<@ snippets/navbar.php @>
	<section class="section">		
		<@ snippets/content.php @>
	</section>
	<section class="section">
		<div class="tile is-ancestor">
			<#
			The filelist can be defined by one or more glob patterns.
			It is recommended to use a variable with a default pattern 
			as value for the 'glob' parameter. 
			#>
			<@ filelist { 
				glob: @{ files | def('/shared/image-*.png') },
				sort: 'asc'
			} @>
			<# 
			The 'foreach' statement initiates an iteration over all files in the filelist.
			Note that it is possible to define resize options within the statement to be applied 
			to the files.
			To access a single image it is also possible to use the 'with' statement.
			#>
			<@ foreach in filelist { width: 400, height: 300, crop: true } @>
				<div class="tile is-vertical is-4 is-parent">
					<div class="tile is-child card">
						<div class="card-image">
							<figure class="image is-4by3">
								<img 
								<# 
								The ':fileResized' runtime variable represents the path
								of the resized image in the cache. 
								#>
								src="@{ :fileResized }" 
								<# The basename of the current file in the list. #>
								alt="@{ :basename }"
								<# 
								The ':file' runtime variable contains the path to the
								original unmodified image.
								#>
								title="@{ :file }"
								<# The size of the resized image. #>
								width="@{ :widthResized }"
								height="@{ :heightResized }"
								>
							</figure>
						</div>
						<div class="card-content">
							<div class="content">
								<h3>@{ :basename | stripEnd ('.png') | ucwords }</h3>
								<#
								A caption can be saved along with an image. To get the caption for 
								the current image, the ':caption' variable can used.
								#>
								<p>@{ :caption | def ('This file has **no** caption.') | markdown }</p>
								<p>
									<span class="tag is-info">
										<# The size of the original image. #>
										@{ :width } x @{ :height }
									</span>
								</p>
							</div>
						</div>
					</div>
				</div>
			<@ end @>
		</div>
	</section>
<# As the last step, the footer markup is included. #>
<@ snippets/footer.php @>