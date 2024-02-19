# Translations

This directory contains all UI text modules and their translations. The keys that reference a text module can be found in the source of the backend being used by the `Automad\Core\Text` class or in the frontend being used by the `App.text()` method or by several components as values for their attributes.

Note that the text modules that are used by [FileRobot](https://github.com/scaleflex/filerobot-image-editor) are also part of the language files but can't be found in the Automad soure since FileRobot is an external dependency. The text modules object just gets passed to the FileRobot configuration as a whole.

## FileRobot Keys

Below is a list of all [text modules](https://github.com/scaleflex/filerobot-image-editor/blob/master/packages/react-filerobot-image-editor/src/context/defaultTranslations.js) that are used by [FileRobot](https://github.com/scaleflex/filerobot-image-editor). They are included in this README in order to make them appear in searches during refactoring.

	name
	save
	saveAs
	back
	loading
	resetOperations
	changesLoseConfirmation
	changesLoseConfirmationHint
	cancel
	continue
	undoTitle
	redoTitle
	showImageTitle
	zoomInTitle
	zoomOutTitle
	resetZoomTitle
	adjustTab
	finetuneTab
	filtersTab
	watermarkTab
	annotateTab
	resize
	resizeTab
	invalidImageError
	uploadImageError
	areNotImages
	isNotImage
	toBeUploaded
	cropTool
	original
	custom
	square
	landscape
	portrait
	ellipse
	classicTv
	cinemascope
	arrowTool
	blurTool
	brightnessTool
	contrastTool
	ellipseTool
	unFlipX
	flipX
	unFlipY
	flipY
	hsvTool
	hue
	saturation
	value
	imageTool
	importing
	addImage
	lineTool
	penTool
	polygonTool
	sides
	rectangleTool
	cornerRadius
	resizeWidthTitle
	resizeHeightTitle
	toggleRatioLockTitle
	reset
	resetSize
	rotateTool
	textTool
	textSpacings
	textAlignment
	fontFamily
	size
	letterSpacing
	lineHeight
	warmthTool
	addWatermark
	addWatermarkTitle
	uploadWatermark
	addWatermarkAsText
	padding
	shadow
	horizontal
	vertical
	blur
	opacity
	position
	stroke
	saveAsModalLabel
	extension
	nameIsRequired
	quality
	imageDimensionsHoverTitle
	cropSizeLowerThanResizedWarning