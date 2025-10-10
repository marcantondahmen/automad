/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import 'modern-normalize/modern-normalize.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'sortable-tree/dist/sortable-tree.css';

import 'dist-font-inter/variable';
import '@fontsource-variable/jetbrains-mono';

import './styles/index.less';

import('./components/Breadcrumbs/BreadcrumbsPage.js');
import('./components/Breadcrumbs/BreadcrumbsRoute.js');

import('./components/Fields/CodeField.js');
import('./components/Fields/ColorField.js');
import('./components/Fields/DateField.js');
import('./components/Fields/EditorField.js');
import('./components/Fields/EmailField.js');
import('./components/Fields/FeedFieldSelectField.js');
import('./components/Fields/ImageField.js');
import('./components/Fields/InputField.js');
import('./components/Fields/MainThemeField.js');
import('./components/Fields/MarkdownField.js');
import('./components/Fields/NumberField.js');
import('./components/Fields/NumberUnitField.js');
import('./components/Fields/PageTagsField.js');
import('./components/Fields/PageTemplateField.js');
import('./components/Fields/PasswordField.js');
import('./components/Fields/PlatformSelectField.js');
import('./components/Fields/SelectField.js');
import('./components/Fields/SyntaxThemeSelectField.js');
import('./components/Fields/TextareaField.js');
import('./components/Fields/TitleField.js');
import('./components/Fields/ToggleField.js');
import('./components/Fields/ToggleLargeField.js');
import('./components/Fields/ToggleSelectField.js');
import('./components/Fields/UrlField.js');

import('./components/File/FileCard.js');
import('./components/File/FileCount.js');
import('./components/File/FileInfo.js');
import('./components/File/FileRobot.js');
import('./components/File/Upload.js');

import('./components/Forms/FileCollection/Delete.js');
import('./components/Forms/FileCollection/Move.js');
import('./components/Forms/FileCollection/ListForm.js');
import('./components/Forms/Publish/ComponentPublishForm.js');
import('./components/Forms/Publish/PagePublishForm.js');
import('./components/Forms/Publish/SharedPublishForm.js');
import('./components/Forms/ConfigFileForm.js');
import('./components/Forms/DeleteUsersForm.js');
import('./components/Forms/Form.js');
import('./components/Forms/FormError.js');
import('./components/Forms/HistoryModalForm.js');
import('./components/Forms/InPageForm.js');
import('./components/Forms/MailConfigForm.js');
import('./components/Forms/PageDataForm.js');
import('./components/Forms/ResetPasswordForm.js');
import('./components/Forms/SearchForm.js');
import('./components/Forms/SetupForm.js');
import('./components/Forms/ComponentCollectionForm.js');
import('./components/Forms/SharedDataForm.js');
import('./components/Forms/Submit.js');
import('./components/Forms/SystemUpdateForm.js');
import('./components/Forms/TrashForm.js');

import('./components/Home/RecentlyEditedPages.js');
import('./components/Home/ServerInfo.js');

import('./components/Indicators/Menu/PrivateIndicator.js');
import('./components/Indicators/Navbar/DebugIndicator.js');
import('./components/Indicators/Navbar/OutdatedPackagesIndicator.js');
import('./components/Indicators/Navbar/SystemUpdateIndicator.js');
import('./components/Indicators/Sidebar/OutdatedPackagesIndicator.js');
import('./components/Indicators/System/CacheIndicator.js');
import('./components/Indicators/System/DebugIndicator.js');
import('./components/Indicators/System/FeedIndicator.js');
import('./components/Indicators/System/I18nIndicator.js');
import('./components/Indicators/System/MailIndicator.js');
import('./components/Indicators/System/SystemUpdateIndicator.js');
import('./components/Indicators/System/UserCountIndicator.js');

import('./components/Modal/Modal.js');
import('./components/Modal/ModalBody.js');
import('./components/Modal/ModalClose.js');
import('./components/Modal/ModalDialog.js');
import('./components/Modal/ModalFooter.js');
import('./components/Modal/ModalHeader.js');
import('./components/Modal/ModalField.js');
import('./components/Modal/ModalJumpbar.js');
import('./components/Modal/ModalJumpbarDialog.js');
import('./components/Modal/ModalToggle.js');

import('./components/PackageManager/AddRepository.js');
import('./components/PackageManager/ComposerAuth.js');
import('./components/PackageManager/PackageCard.js');
import('./components/PackageManager/PackageList.js');
import('./components/PackageManager/RepositoryCard.js');
import('./components/PackageManager/RepositoryList.js');
import('./components/PackageManager/UpdateAllPackages.js');

import('./components/Pages/Components.js');
import('./components/Pages/Home.js');
import('./components/Pages/InPage.js');
import('./components/Pages/Login.js');
import('./components/Pages/Packages.js');
import('./components/Pages/Page.js');
import('./components/Pages/ResetPassword.js');
import('./components/Pages/Search.js');
import('./components/Pages/Setup.js');
import('./components/Pages/Shared.js');
import('./components/Pages/System.js');
import('./components/Pages/Trash.js');

import('./components/Sidebar/Sidebar.js');
import('./components/Sidebar/SidebarToggle.js');

import('./components/Switcher/Switcher.js');
import('./components/Switcher/SwitcherDropdown.js');
import('./components/Switcher/SwitcherLabel.js');
import('./components/Switcher/SwitcherLink.js');
import('./components/Switcher/SwitcherSection.js');

import('./components/System/CacheEnable.js');
import('./components/System/CacheLifetime.js');
import('./components/System/CacheMonitor.js');
import('./components/System/DebugEnable.js');
import('./components/System/FeedEnable.js');
import('./components/System/FeedFields.js');
import('./components/System/I18nEnable.js');
import('./components/System/LanguageSelect.js');
import('./components/System/UserEmail.js');
import('./components/System/UserName.js');

import('./components/Alert.js');
import('./components/Autocomplete.js');
import('./components/AutocompleteUrl.js');
import('./components/Checkbox.js');
import('./components/ComponentEditor.js');
import('./components/Copy.js');
import('./components/CustomIconCheckbox.js');
import('./components/DashboardThemeToggle.js');
import('./components/Dropdown.js');
import('./components/EditorJS.js');
import('./components/EmbedService.js');
import('./components/Filter.js');
import('./components/IconText.js');
import('./components/ImageCollection.js');
import('./components/ImagePicker.js');
import('./components/Img.js');
import('./components/KeyComboBadge.js');
import('./components/Link.js');
import('./components/Logo.js');
import('./components/Maintenance.js');
import('./components/MissingEmailAlert.js');
import('./components/NavItem.js');
import('./components/NavTree.js');
import('./components/NumberUnitInput.js');
import('./components/PageSelectTree.js');
import('./components/Root.js');
import('./components/Select.js');
import('./components/Spinner.js');
import('./components/UndoButtons.js');

export {};
