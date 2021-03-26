<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
@{ iconNav | replace('/^(.+)$/s', '<span class="nav-icon">$1</span>') }@{ title | stripTags }