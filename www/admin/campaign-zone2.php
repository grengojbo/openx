<?php

/*
+---------------------------------------------------------------------------+
| OpenX  v${RELEASE_MAJOR_MINOR}                                                              |
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
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/max/other/proto.php';


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

$advertiserId   = MAX_getValue('clientid');
$campaignId     = MAX_getValue('campaignid');
$agencyId = OA_Permission::getAgencyId();
$aOtherAdvertisers = Admin_DA::getAdvertisers(array('agency_id' => $agencyId));
$aOtherCampaigns = Admin_DA::getPlacements(array('advertiser_id' => $advertiserId));
$pageName = basename($_SERVER['PHP_SELF']);
$aEntities = array('clientid' => $advertiserId, 'campaignid' => $campaignId);
MAX_displayNavigationCampaign($pageName, $aOtherAdvertisers, $aOtherCampaigns, $aEntities);

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

require_once MAX_PATH . '/lib/OA/Admin/Template.php';

$oTpl = new OA_Admin_Template('campaign-zone.html');

$runOfNetwork = false;

$websites = getWebsites($advertiserId, $campaignId, '');

$oTpl->assign('websites', $websites);
$oTpl->assign('runOfNetwork', $runOfNetwork);

$oTpl->assign('advertiserId', $advertiserId);
$oTpl->assign('campaignId', $campaignId);

$oTpl->display();

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>
