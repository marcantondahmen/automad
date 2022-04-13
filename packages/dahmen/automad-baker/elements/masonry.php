<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set {
	:hideDate: @{ checkboxHideDate }
} @>
<@ if @{ :pagelistDisplayCount } @>
	<section <@ if @{ :pagelistDisplayCount } < 2 @>class="am-block"<@ end @>>
		<div class="cards @{ :gridSize } <@ if @{ :pagelistDisplayCount } > 1 @>masonry<@ end @>">
			<@ foreach in pagelist ~@>
				<div class="card">
					<div class="card-content">
						<a href="@{ url }">
							<h2>@{ title }</h2>	
						</a>
						<@ if not @{ :hideDate } @>
							<@ set {
								:date: @{ date | dateFormat (@{ formatDate | def ('l, F jS Y')}, @{ locale }) }
							} @>
						<@ end @>
						<@ if @{ :date } or @{ tags } @>
							<div>
								@{ :date }
								<@ if @{ :date } and @{ tags } @>
									&mdash;
								<@ end @>
								@{ tags }
							</div>
						<@ end @>
						<div class="panel-body">
							<@ if not @{ :hideThumbnails } @>
								<# Reset variable to false in case there is no match. #>
								<@ set { :imageTeaser: false } @>
								<# Try to get image from variable. #>
								<@ with @{ imageTeaser | def ('*.jpg, *.png, *.gif') } { width: 800 } ~@>
									<@ set { :imageTeaser: @{ :fileResized } } @>
								<@~ else ~@>
									<# Else try to get first image from content. #>
									<@ set { :imageTeaser: @{ +main | findFirstImage } } @>
								<@~ end ~@>
								<@~ if @{ :imageTeaser } @>
									<a 
									href="@{ url }" 
									class="@{ :teaserClass } uk-display-block uk-margin-small-bottom"
									>
										<img src="@{ :imageTeaser }">
									</a>
								<@~ end ~@>
							<@ end @>
							<p>
								@{ +main | findFirstParagraph }
							</p>
						</div>
						<a href="@{ url }" class="card-more"></a>
					</div>
				</div>
			<@~ end @>
		</div>
	</section>
<@ else @>
	<h3>@{ notificationNoSearchResults | def('Nothing found.') }</h3>
	<hr>
<@ end @>
