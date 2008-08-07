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
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-maintenance.inc.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader("maintenance-index");
phpAds_MaintenanceSelection("plugins");


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

echo "<br />";
echo $strPluginsPrecis;
echo "<br /><br />";

phpAds_registerGlobal('action', 'returnurl');

if (!empty($action))
{
    switch ($action)
    {
        case 'pref':
            require_once(LIB_PATH.'/Extension/ExtensionCommon.php');
            $oExtensionManager = new OX_Extension_Common();
            $oExtensionManager->runTasksOnDemand();
            break;
        case 'reg':
            require_once(LIB_PATH.'/Extension/ExtensionDelivery.php');
            $oExtensionManager = new OX_Extension_Delivery();
            $oExtensionManager->runTasksOnDemand();
            break;
        case 'dep':
            require_once LIB_PATH . '/Plugin/PluginManager.php';
            $oPluginManager = & new OX_PluginManager();
            $oPluginManager->_cacheDependencies();
            if (empty($oPluginManager->aErrors))
            {
                $oPluginManager->aMessages[] = 'No dependency problems detected';
            }
            break;
        default:
    }
    if ($oPluginManager && $oPluginManager->countErrors())
    {
        foreach ($oPluginManager->aErrrors as $idx => $msg)
        {
            echo $msg.'</br>';
        }
    }
    else
    {
        echo $strPluginsOk.'</br>';
    }
}

phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=pref'>Rebuild Preferences List</a>&nbsp;&nbsp;";
phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=dep'>Check Dependencies</a>&nbsp;&nbsp;";
phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=reg'>Rebuild Delivery Hooks Cache</a>&nbsp;&nbsp;";
phpAds_ShowBreak();


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>
