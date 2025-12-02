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
import '@/vendor/katex.scss';

import 'dist-font-inter/variable';
import '@fontsource-variable/jetbrains-mono';

import './styles/index.less';

import('./components/Breadcrumbs/BreadcrumbsPage');
import('./components/Breadcrumbs/BreadcrumbsRoute');

import('./components/Fields/CodeField');
import('./components/Fields/ColorField');
import('./components/Fields/DateField');
import('./components/Fields/EditorField');
import('./components/Fields/EmailField');
import('./components/Fields/FeedFieldSelectField');
import('./components/Fields/ImageField');
import('./components/Fields/InputField');
import('./components/Fields/MainThemeField');
import('./components/Fields/MarkdownField');
import('./components/Fields/NumberField');
import('./components/Fields/NumberUnitField');
import('./components/Fields/PageTagsField');
import('./components/Fields/PageTemplateField');
import('./components/Fields/PasswordField');
import('./components/Fields/PlatformSelectField');
import('./components/Fields/SelectField');
import('./components/Fields/SyntaxThemeSelectField');
import('./components/Fields/TextareaField');
import('./components/Fields/TitleField');
import('./components/Fields/ToggleField');
import('./components/Fields/ToggleLargeField');
import('./components/Fields/ToggleSelectField');
import('./components/Fields/UrlField');

import('./components/File/FileCard');
import('./components/File/FileCount');
import('./components/File/FileInfo');
import('./components/File/FileRobot');
import('./components/File/Upload');

import('./components/Forms/FileCollection/Delete');
import('./components/Forms/FileCollection/Move');
import('./components/Forms/FileCollection/ListForm');
import('./components/Forms/Publish/ComponentPublishForm');
import('./components/Forms/Publish/PagePublishForm');
import('./components/Forms/Publish/SharedPublishForm');
import('./components/Forms/DeleteUsersForm');
import('./components/Forms/Form');
import('./components/Forms/FormError');
import('./components/Forms/HistoryModalForm');
import('./components/Forms/InPageForm');
import('./components/Forms/MailConfigForm');
import('./components/Forms/PageDataForm');
import('./components/Forms/ResetPasswordForm');
import('./components/Forms/SearchForm');
import('./components/Forms/SetupForm');
import('./components/Forms/ComponentCollectionForm');
import('./components/Forms/SharedDataForm');
import('./components/Forms/Submit');
import('./components/Forms/SystemUpdateForm');
import('./components/Forms/TrashForm');

import('./components/Home/RecentlyEditedPages');
import('./components/Home/ServerInfo');

import('./components/Indicators/Menu/PrivateIndicator');
import('./components/Indicators/Navbar/DebugIndicator');
import('./components/Indicators/Navbar/OutdatedPackagesIndicator');
import('./components/Indicators/Navbar/SystemUpdateIndicator');
import('./components/Indicators/Sidebar/OutdatedPackagesIndicator');
import('./components/Indicators/System/CacheIndicator');
import('./components/Indicators/System/DebugIndicator');
import('./components/Indicators/System/FeedIndicator');
import('./components/Indicators/System/I18nIndicator');
import('./components/Indicators/System/MailIndicator');
import('./components/Indicators/System/SystemUpdateIndicator');
import('./components/Indicators/System/UserCountIndicator');

import('./components/Modal/Modal');
import('./components/Modal/ModalBody');
import('./components/Modal/ModalClose');
import('./components/Modal/ModalDialog');
import('./components/Modal/ModalFooter');
import('./components/Modal/ModalHeader');
import('./components/Modal/ModalField');
import('./components/Modal/ModalJumpbar');
import('./components/Modal/ModalJumpbarDialog');
import('./components/Modal/ModalToggle');

import('./components/PackageManager/AddRepository');
import('./components/PackageManager/ComposerAuth');
import('./components/PackageManager/PackageCard');
import('./components/PackageManager/PackageList');
import('./components/PackageManager/RepositoryCard');
import('./components/PackageManager/RepositoryList');
import('./components/PackageManager/UpdateAllPackages');

import('./components/Pages/Components');
import('./components/Pages/Home');
import('./components/Pages/InPage');
import('./components/Pages/Login');
import('./components/Pages/Packages');
import('./components/Pages/Page');
import('./components/Pages/ResetPassword');
import('./components/Pages/Search');
import('./components/Pages/Setup');
import('./components/Pages/Shared');
import('./components/Pages/System');
import('./components/Pages/Trash');

import('./components/Sidebar/Sidebar');
import('./components/Sidebar/SidebarToggle');

import('./components/Switcher/Switcher');
import('./components/Switcher/SwitcherDropdown');
import('./components/Switcher/SwitcherLabel');
import('./components/Switcher/SwitcherLink');
import('./components/Switcher/SwitcherSection');

import('./components/System/CacheEnable');
import('./components/System/CacheLifetime');
import('./components/System/CacheMonitor');
import('./components/System/DebugEnable');
import('./components/System/FeedEnable');
import('./components/System/FeedFields');
import('./components/System/I18nEnable');
import('./components/System/LanguageSelect');
import('./components/System/UserEmail');
import('./components/System/UserName');

import('./components/Alert');
import('./components/Autocomplete');
import('./components/AutocompleteUrl');
import('./components/Checkbox');
import('./components/ComponentEditor');
import('./components/Copy');
import('./components/CustomIconCheckbox');
import('./components/DashboardThemeToggle');
import('./components/Dropdown');
import('./components/EditorJS');
import('./components/EmbedService');
import('./components/Filter');
import('./components/IconText');
import('./components/ImageCollection');
import('./components/ImagePicker');
import('./components/Img');
import('./components/KeyComboBadge');
import('./components/Link');
import('./components/Logo');
import('./components/Maintenance');
import('./components/MissingEmailAlert');
import('./components/NavItem');
import('./components/NavTree');
import('./components/NumberUnitInput');
import('./components/PageSelectTree');
import('./components/Root');
import('./components/Select');
import('./components/Spinner');
import('./components/UndoButtons');
