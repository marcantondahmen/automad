<?php

$inlineToolbar = file_get_contents(__DIR__ . '/Editor/InlineToolbar.html');
$sectionToolbar = file_get_contents(__DIR__ . '/Editor/SectionToolbar.html');
$toolbox = file_get_contents(__DIR__ . '/Editor/Toolbar/Toolbox.html');
$settings = file_get_contents(__DIR__ . '/Editor/Toolbar/Settings.html');

?>
<section class="am-l-dashboard__section">
	<div class="am-l-dashboard__content">
		<div class="am-u-flex am-u-flex--column am-u-flex--gap-large">
			<am-editor-field style="margin-bottom: 23rem">
				<div>
					<am-editor-js>
						<div
							class="codex-editor codex-editor--narrow codex-editor--toolbox-opened"
						>
							<div
								class="codex-editor__redactor"
								style="padding-bottom: 50px"
							>
								<div class="ce-block ce-block--focused">
									<div class="ce-block__content">
										<div
											class="ce-paragraph cdx-block"
											contenteditable="true"
											data-placeholder=""
										>
											Lorem ipsum dolor sit amet,
											consetetur sadipscing elitr, sed
											diam nonumy eirmod tempor invidunt
											ut labore et dolore magna aliquyam
											erat, sed diam voluptua. At vero eos
											et accusam et justo duo dolores et
											ea rebum. Stet clita kasd gubergren,
											no sea takimata sanctus est Lorem
											ipsum dolor sit amet.
										</div>
									</div>
								</div>
							</div>
							<div class="codex-editor-overlay">
								<div class="codex-editor-overlay__container">
									<div
										class="codex-editor-overlay__rectangle"
										style="display: none"
									></div>
								</div>
							</div>
							<?php echo $toolbox; ?>
						</div>
					</am-editor-js>
				</div>
			</am-editor-field>
			<am-editor-field style="margin-bottom: 17rem;">
				<div>
					<am-editor-js>
						<div class="codex-editor codex-editor--narrow">
							<div
								class="codex-editor__redactor"
								style="padding-bottom: 50px"
							>
								<div class="ce-block ce-block--focused">
									<div class="ce-block__content">
										<div class="am-c-ed-bl-section">
											<span class="am-u-flex"
												><span
													class="am-c-ed-bl-section__label"
												>
													Layout Section
												</span></span
											><am-editor-js
												class="am-c-ed-bl-section__editor am-style--justify-start"
												style="
													--color: inherit;
													--backgroundColor: transparent;
													--backgroundBlendMode: normal;
													--borderColor: transparent;
													--borderWidth: 0;
													--borderRadius: 0;
													--borderStyle: solid;
													--paddingTop: 0;
													--paddingBottom: 0;
													--backgroundImage: none;
												"
												><div class="codex-editor">
													<div
														class="codex-editor__redactor"
														style="
															padding-bottom: 50px;
														"
													>
														<div
															class="ce-block ce-block--selected"
														>
															<div
																class="ce-block__content"
															>
																<div
																	class="ce-paragraph cdx-block"
																	contenteditable="true"
																	data-placeholder=""
																>
																	Lorem ipsum	dolor sit amet, consetetur sadipscing elitr, sed diam nonumy
																	eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
																	voluptua. At vero eos et accusam et justo duo dolores et ea rebum.
																	Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
																</div>
															</div>
														</div>
													</div>
													<div
														class="codex-editor-overlay"
													>
														<div
															class="codex-editor-overlay__container"
														>
															<div
																class="codex-editor-overlay__rectangle"
																style="
																	display: none;
																"
															></div>
														</div>
													</div>
													<?php echo $settings; ?>	
													<div
														class="ce-inline-toolbar"
													>
													</div>
												</div>
											</am-editor-js>
											<?php echo $sectionToolbar; ?>
										</div>
									</div>
								</div>
							</div>
							<div class="codex-editor-overlay">
								<div class="codex-editor-overlay__container">
									<div
										class="codex-editor-overlay__rectangle"
										style="display: none"
									></div>
								</div>
							</div>
							<div
								class="ce-toolbar ce-toolbar--opened"
								style="--x: 209px; --y: 0px"
							>
								<div class="ce-toolbar__content">
									<div
										class="ce-toolbar__actions ce-toolbar__actions--opened"
									>
										<div class="ce-toolbar__plus">
											<svg
												xmlns="http://www.w3.org/2000/svg"
												width="24"
												height="24"
												fill="none"
												viewBox="0 0 24 24"
											>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2"
													d="M12 7V12M12 17V12M17 12H12M12 12H7"
												></path>
											</svg>
										</div>
										<span
											class="ce-toolbar__settings-btn"
											draggable="true"
											><svg
												xmlns="http://www.w3.org/2000/svg"
												width="24"
												height="24"
												fill="none"
												viewBox="0 0 24 24"
											>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2.6"
													d="M9.40999 7.29999H9.4"
												></path>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2.6"
													d="M14.6 7.29999H14.59"
												></path>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2.6"
													d="M9.30999 12H9.3"
												></path>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2.6"
													d="M14.6 12H14.59"
												></path>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2.6"
													d="M9.40999 16.7H9.4"
												></path>
												<path
													stroke="currentColor"
													stroke-linecap="round"
													stroke-width="2.6"
													d="M14.6 16.7H14.59"
												></path></svg
										></span>
										<div class="ce-toolbox">
											<div
												class="ce-popover__overlay ce-popover__overlay--hidden"
											></div>
											<div class="ce-popover">
												<div
													class="cdx-search-field ce-popover__search"
												>
													<div
														class="cdx-search-field__icon"
													>
														<svg
															xmlns="http://www.w3.org/2000/svg"
															width="24"
															height="24"
															fill="none"
															viewBox="0 0 24 24"
														>
															<circle
																cx="10.5"
																cy="10.5"
																r="5.5"
																stroke="currentColor"
																stroke-width="2"
															></circle>
															<line
																x1="15.4142"
																x2="19"
																y1="15"
																y2="18.5858"
																stroke="currentColor"
																stroke-linecap="round"
																stroke-width="2"
															></line>
														</svg>
													</div>
													<input
														class="cdx-search-field__input"
														placeholder="Filter"
													/>
												</div>
												<div
													class="ce-popover__nothing-found-message"
												>
													Nothing found
												</div>
												<div class="ce-popover__items">
													<div
														class="ce-popover-item"
														data-item-name="paragraph"
													>
														<div
															class="ce-popover-item__icon"
														>
															<svg
																xmlns="http://www.w3.org/2000/svg"
																width="24"
																height="24"
																fill="none"
																viewBox="0 0 24 24"
															>
																<path
																	stroke="currentColor"
																	stroke-linecap="round"
																	stroke-width="2"
																	d="M8 9V7.2C8 7.08954 8.08954 7 8.2 7L12 7M16 9V7.2C16 7.08954 15.9105 7 15.8 7L12 7M12 7L12 17M12 17H10M12 17H14"
																></path>
															</svg>
														</div>
														<div
															class="ce-popover-item__title"
														>
															Text
														</div>
													</div>
													<div
														class="ce-popover-item"
														data-item-name="header"
													>
														<div
															class="ce-popover-item__icon"
														>
															<svg
																xmlns="http://www.w3.org/2000/svg"
																width="24"
																height="24"
																fill="none"
																viewBox="0 0 24 24"
															>
																<path
																	stroke="currentColor"
																	stroke-linecap="round"
																	stroke-width="2"
																	d="M9 7L9 12M9 17V12M9 12L15 12M15 7V12M15 17L15 12"
																></path>
															</svg>
														</div>
														<div
															class="ce-popover-item__title"
														>
															Heading
														</div>
													</div>
													<div
														class="ce-popover-item"
														data-item-name="section"
													>
														<div
															class="ce-popover-item__icon"
														>
															<i
																class="bi bi-plus-square-dotted"
															></i>
														</div>
														<div
															class="ce-popover-item__title"
														>
															Layout Section
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="ce-settings"></div>
									</div>
								</div>
							</div>
						</div>
					</am-editor-js>
				</div>
			</am-editor-field>
			<am-editor-field style="margin-bottom: 10rem">
				<div>
					<am-editor-js>
						<div
							class="codex-editor codex-editor--narrow codex-editor--toolbox-opened"
						>
							<div
								class="codex-editor__redactor am-e-contents"
								style="padding-bottom: 50px"
							>
								<div class="ce-block ce-block--focused">
									<div class="ce-block__content">
										<div
											class="ce-paragraph cdx-block"
											contenteditable="true"
											data-placeholder=""
										>
											Lorem ipsum dolor sit amet,
											consetetur sadipscing elitr, sed
											diam nonumy eirmod tempor invidunt
											ut labore et dolore magna aliquyam
											erat, sed diam <a href="">voluptua</a>. At vero eos
											et accusam et justo duo dolores et
											ea rebum. Stet clita kasd gubergren,
											no sea takimata sanctus est Lorem
											ipsum dolor sit amet.
										</div>
									</div>
								</div>
							</div>
							<div class="codex-editor-overlay">
								<div class="codex-editor-overlay__container">
									<div
										class="codex-editor-overlay__rectangle"
										style="display: none"
									></div>
								</div>
							</div>
							<?php echo $inlineToolbar; ?>
						</div>
					</am-editor-js>
				</div>
			</am-editor-field>
		</div>
	</div>
</section>

<div id="editor" class="am-u-display-none"></div>
<script src="https://cdn.jsdelivr.net/npm/automad-editorjs@latest"></script>
<script>
	new EditorJS('editor');
</script>
