<?php
/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                                              |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| Copyright (c) 2000-2003 the phpAdsNew developers                          |
| For contact details, see: http://www.phpadsnew.com/                       |
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
$Id: market-account-edit.php 24004 2008-08-11 15:34:24Z radek.maciaszek@openx.org $
*/

require_once 'market-common.php';
require_once MAX_PATH . '/lib/max/Admin/UI/Field/DaySpanField.php';

/*-------------------------------------------------------*/
/* MAIN REQUEST PROCESSING                               */
/*-------------------------------------------------------*/
$oComponent = OX_Component::factory('admin', 'oxMarket');

OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADMIN);
OA_Permission::enforceAccessToObject('agency', OA_Permission::getAgencyId());

displayPage($oComponent);

/*-------------------------------------------------------*/
/* Display page                                          */
/*-------------------------------------------------------*/
function displayPage($oComponent)
{
    global $session;

    //get template and display form
    $pageName = basename($_SERVER['PHP_SELF']);

    $affiliateId    = MAX_getStoredValue('affiliateid', null);
    if (!is_null($affiliateId)) {
        OA_Permission::enforceAccessToObject('affiliates', $affiliateId);
    }

    $orderdirection = MAX_getStoredValue('orderdirection', '');
    $listorder      = MAX_getStoredValue('listorder', '');
    $startDate      = MAX_getStoredValue('period_start', null);
    $startDate      = (!empty($startDate)) ? date('Y-m-d', strtotime($startDate)) : '';
    $endDate        = MAX_getStoredValue('period_end', null);
    $endDate        = (!empty($endDate)) ? date('Y-m-d', strtotime($endDate)) : null;
    $periodPreset   = MAX_getStoredValue('period_preset', null);

    $aOption = array(
        'affiliateid' => $affiliateId,
        'orderdirection' => $orderdirection,
        'listorder' => $listorder,
        'period_preset'     => $periodPreset,
        'period_start'      => $startDate,
        'period_end'        => $endDate
    );

    $oDaySpan = new Admin_UI_DaySpanField('period');
    $oDaySpan->setValueFromArray($aOption);
    $oDaySpan->enableAutoSubmit();

    OA_Admin_UI::queueMessage ( 'OpenX Market Reports are not real time statistics', 'local', 'info', 0 );

    //header
    phpAds_PageHeader("openx-market-stats",'','../../');

    $tmpl = (is_null($affiliateId)) ? 'market-stats-website.html' : 'market-stats-zone.html';
    $oTpl = new OA_Plugin_Template($tmpl, 'openXMarket');
    $oReport = OA_Dal::factoryDO('ext_market_publisher_reporting');
    if (!is_null($affiliateId)) {
        $aReportData = $oReport->getZoneStatsByAffiliateId($aOption);
        $oTpl->assign('url', "market-stats.php?affiliateid=$affiliateId");
        $oTpl->assign('affiliateid', $affiliateId);
    } else {
        $aReportData = $oReport->getWebsiteStatsByAgencyId($aOption);
    }

    $oTpl->assign('aReportData',    $aReportData);
    $oTpl->assign('daySpan',        $oDaySpan);
    $oTpl->assign('assetPath',      OX::assetPath());
    $oTpl->assign('listorder',      $listorder);
    $oTpl->assign('orderdirection', $orderdirection);
    $oTpl->display();

    //footer
    phpAds_PageFooter();

    $session['prefs'][$pageName]['listorder'] = $listorder;
    $session['prefs'][$pageName]['orderdirection'] = $orderdirection;
    $session['prefs']['GLOBALS']['period_start'] = $startDate;
    $session['prefs']['GLOBALS']['period_end'] = $endDate;
    $session['prefs']['GLOBALS']['period_preset'] = $periodPreset;

    phpAds_SessionDataStore();
}

?>
