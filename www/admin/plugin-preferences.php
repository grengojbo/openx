<?php

/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                                                |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                                                                |
|                                                                           |
| Copyright (c) 2003-2008 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';

require_once MAX_PATH . '/lib/max/Admin/Redirect.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Admin/TemplatePlugin.php';
require_once LIB_PATH . '/Plugin/ComponentGroupManager.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('preferences');

// Prepare an array for storing error messages
$aErrormessage = array();

$name   = $_REQUEST['group'];
$parent = $_REQUEST['parent'];

if ($parent) {
    $backURL =  "plugin-index.php?action=info&package=$parent";
}
else {
    $backURL = "plugin-index.php?selection=plugins";
}

// get the settings for this plugin
$oManager   = & new OX_Plugin_ComponentGroupManager();
$aConfig    = $oManager->_getComponentGroupConfiguration($name);

// If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true')
{
    // Prepare an array of the HTML elements to process, and the
    // location to save the values in the settings configuration
    // file
    $aElements = array();
    foreach ($aConfig['preferences'] as $k => $v)
    {
        $aElements[] = $name.'_'.$v['name'];
    }
    $aCheckboxes = array();

    // Create a new settings object, and save the settings!
    $result = OA_Preferences::processPreferencesFromForm($aElements, $aCheckboxes);
    if ($result)
    {
        // The settings configuration file was written correctly,
        // go back to the plugins main page from here
        require_once MAX_PATH . '/lib/max/Admin/Redirect.php';
        MAX_Admin_Redirect::redirect($backURL);
    }
    // Could not write the settings configuration file, store this
    // error message and continue
    $aErrormessage[0][] = $strUnableToWritePrefs;
}
// Set the correct section of the settings pages and display the drop-down menu
//$oOptions->selection('email');

// Prepare an array of HTML elements to display for the form, and
// output using the $oOption object
$aPreferences[0]['text'] = $name.' '.$strPreferences;
$count = count($aConfig['preferences']);
$i = 0;
foreach ($aConfig['preferences'] as $k => $v)
{
    $aPreferences[0]['items'][] = array(
                                         'type'    => $v['type'],
                                         'name'    => $name.'_'.$v['name'],
                                         'text'    => $v['label'],
                                         'req'     => $v['required'],
                                         'size'    => $v['size'],
                                         'value'   => $v['value'],
                                         'visible' => $v['visible'],
                                         );
    //add break after a field excluding last
    $i++;
    if ($i < $count) {
        $aPreferences[0]['items'][] = array (
                    'type'    => 'break'
                );
    }
}


$aPreferences[0]['items'][] = array(
                                     'type'    => 'hiddenfield',
                                     'name'    => 'plugin',
                                     'value'   => $name,
                                     );
$aPreferences[0]['items'][] = array(
                                 'type'    => 'hiddenfield',
                                 'name'    => 'parent',
                                 'value'   => $parent,
                                 );


$GLOBALS['_MAX']['PREF_EXTRA'] = OA_Preferences::loadPreferences(true, true);
/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader("plugin-index");

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/
//display back link
$oTpl = new OA_Admin_Template('plugin-group-preferences.html');
$oTpl->assign('backURL', MAX::constructURL(MAX_URL_ADMIN, $backURL));
$oTpl->assign('parent', $parent);
$oTpl->assign('name', $name);
$oTpl->display();

//display options form
$oOptions->show($aPreferences, $aErrormessage);

phpAds_PageFooter();


?>