<section class="am-l-dashboard__section am-l-dashboard__section--breadcrumbs">
	<div class="am-l-dashboard__content">
		<div class="am-c-breadcrumbs">
			<span class="am-c-breadcrumbs__item">My Site</span>
			<span class="am-c-breadcrumbs__item">Dashboard</span>
			<span class="am-c-breadcrumbs__item"><?php echo ucwords($activeSlug); ?></span>
		</div>
	</div>
</section>
<section class="am-l-dashboard__section am-l-dashboard__section--sticky">
	<div class="am-l-dashboard__content am-l-dashboard__content--row">
		<div class="am-c-menu">
			<span class="am-c-menu__item am-c-menu__item--active">Settings</span>
			<span class="am-c-menu__item">Content</span>
			<span class="am-c-menu__item">
				<span>Files</span>
				<span class="am-e-badge">5</span>
			</span>
		</div>
		<div class="am-c-filter">
			<input class="am-c-filter__input am-f-input" type="text" placeholder="Filter">
			<span class="am-c-filter__key-combo">
				<span class="am-e-key-combo">⌘ + K</span>
			</span>
		</div>
		<div class="am-c-privacy-indicator"><i class="bi bi-eye-slash"></i></div>
		<div class="am-c-dropdown am-c-dropdown--hover am-c-dropdown--right">
			<span class="am-c-menu__item">
				<span>More</span>
				<span class="am-e-dropdown-arrow"></span>
			</span>
			<div class="am-c-dropdown__items">
				<a class="am-c-dropdown__link">
					<i class="bi bi-pencil"></i>
					<span>Edit Page</span>
				</a>
				<a class="am-c-dropdown__link">
					<i class="bi bi-trash3"></i>
					<span>Delete Page</span>
				</a>
			</div>
		</div>
	</div>
</section>

