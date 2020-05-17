<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	
<@ if @{ colorText } or @{ colorBg } or @{ colorBorder } or @{ colorMuted } or @{ colorPanelBg } or @{ colorCode } @>
	<style>
		:root {
			<@ if @{ colorText } @>--color-text: @{ colorText };<@ end ~@>
			<@ if @{ colorBg } @>--color-bg: @{ colorBg };<@ end ~@>
			<@ if @{ colorBorder } @>--color-border: @{ colorBorder };<@ end ~@>
			<@ if @{ colorMuted } @>--color-muted: @{ colorMuted };<@ end ~@>
			<@ if @{ colorPanelBg } @>--color-panel-bg: @{ colorPanelBg };<@ end ~@>
			<@ if @{ colorCode } @>--color-code: @{ colorCode };<@ end @>
		}
	</style>
<@ end @>