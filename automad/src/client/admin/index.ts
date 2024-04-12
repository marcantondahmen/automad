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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
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

import './components/Breadcrumbs/BreadcrumbsPage';
import './components/Breadcrumbs/BreadcrumbsRoute';

import './components/Fields/Color';
import './components/Fields/Date';
import './components/Fields/Editor';
import './components/Fields/Email';
import './components/Fields/FeedFieldSelect';
import './components/Fields/ImageSelect';
import './components/Fields/Input';
import './components/Fields/MainTheme';
import './components/Fields/Markdown';
import './components/Fields/Number';
import './components/Fields/NumberUnit';
import './components/Fields/PageTags';
import './components/Fields/PageTemplate';
import './components/Fields/Password';
import './components/Fields/SyntaxThemeSelect';
import './components/Fields/Textarea';
import './components/Fields/Title';
import './components/Fields/Toggle';
import './components/Fields/ToggleLarge';
import './components/Fields/ToggleSelect';
import './components/Fields/Url';

import './components/File/FileCard';
import './components/File/FileCount';
import './components/File/FileInfo';
import './components/File/FileRobot';
import './components/File/Upload';

import './components/Forms/FileCollection/ListForm';
import './components/Forms/FileCollection/Submit';
import './components/Forms/ConfigFileForm';
import './components/Forms/DeleteUsersForm';
import './components/Forms/Form';
import './components/Forms/FormError';
import './components/Forms/HistoryModalForm';
import './components/Forms/InPageForm';
import './components/Forms/MailConfigForm';
import './components/Forms/PageDataForm';
import './components/Forms/PublishForm';
import './components/Forms/ResetPasswordForm';
import './components/Forms/SearchForm';
import './components/Forms/SetupForm';
import './components/Forms/SharedDataForm';
import './components/Forms/Submit';
import './components/Forms/SystemUpdateForm';
import './components/Forms/TrashForm';

import './components/Home/RecentlyEditedPages';
import './components/Home/ServerInfo';

import './components/Indicators/Menu/PrivateIndicator';
import './components/Indicators/Navbar/DebugIndicator';
import './components/Indicators/Navbar/OutdatedPackagesIndicator';
import './components/Indicators/Navbar/SystemUpdateIndicator';
import './components/Indicators/Sidebar/OutdatedPackagesIndicator';
import './components/Indicators/System/CacheIndicator';
import './components/Indicators/System/DebugIndicator';
import './components/Indicators/System/FeedIndicator';
import './components/Indicators/System/I18nIndicator';
import './components/Indicators/System/MailIndicator';
import './components/Indicators/System/SystemUpdateIndicator';
import './components/Indicators/System/UserCountIndicator';

import './components/Modal/Modal';
import './components/Modal/ModalBody';
import './components/Modal/ModalClose';
import './components/Modal/ModalDialog';
import './components/Modal/ModalFooter';
import './components/Modal/ModalHeader';
import './components/Modal/ModalField';
import './components/Modal/ModalJumpbar';
import './components/Modal/ModalJumpbarDialog';
import './components/Modal/ModalToggle';

import './components/PackageManager/PackageCard';
import './components/PackageManager/PackageList';
import './components/PackageManager/UpdateAllPackages';

import './components/Pages/Home';
import './components/Pages/InPage';
import './components/Pages/Login';
import './components/Pages/Packages';
import './components/Pages/Page';
import './components/Pages/ResetPassword';
import './components/Pages/Search';
import './components/Pages/Setup';
import './components/Pages/Shared';
import './components/Pages/System';
import './components/Pages/Trash';

import './components/Sidebar/Sidebar';
import './components/Sidebar/SidebarToggle';

import './components/Switcher/Switcher';
import './components/Switcher/SwitcherDropdown';
import './components/Switcher/SwitcherLabel';
import './components/Switcher/SwitcherLink';
import './components/Switcher/SwitcherSection';

import './components/System/CacheEnable';
import './components/System/CacheLifetime';
import './components/System/CacheMonitor';
import './components/System/DebugEnable';
import './components/System/FeedEnable';
import './components/System/FeedFields';
import './components/System/I18nEnable';
import './components/System/LanguageSelect';
import './components/System/UserEmail';
import './components/System/UserName';

import './components/Alert';
import './components/Autocomplete';
import './components/AutocompleteUrl';
import './components/Checkbox';
import './components/CustomIconCheckbox';
import './components/Copy';
import './components/DashboardThemeToggle';
import './components/Dropdown';
import './components/EditorJS';
import './components/Filter';
import './components/IconText';
import './components/ImageCollection';
import './components/ImagePicker';
import './components/Img';
import './components/KeyComboBadge';
import './components/Link';
import './components/Logo';
import './components/NavItem';
import './components/NavTree';
import './components/NumberUnitInput';
import './components/PageSelectTree';
import './components/Root';
import './components/Select';
import './components/Spinner';
import './components/UndoButtons';
