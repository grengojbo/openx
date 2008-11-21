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

// Set translation strings

$GLOBALS['strDeliveryEngine']				= "Teslimat Motoru";
$GLOBALS['strMaintenance']					= "Bakım";
$GLOBALS['strAdministrator']				= "Yönetici";


$GLOBALS['strUserlog'][phpAds_actionAdvertiserReportMailed] = "{id} reklamcıya e-mail yolu ile rapor gönder";
$GLOBALS['strUserlog'][phpAds_actionPublisherReportMailed] = "{id} yayıncıya e-mail yolu ile rapor gönder";
$GLOBALS['strUserlog'][phpAds_actionWarningMailed] = "{id} e-mail yolu ile kampanyalar için pasif etme uyarısı gönder";
$GLOBALS['strUserlog'][phpAds_actionDeactivationMailed] = "{id} e-mail yolu ile kampanyalar için pasif etme bildirisi gönder";
$GLOBALS['strUserlog'][phpAds_actionPriorityCalculation] = "Öncelikler tekrar hesaplandı";
$GLOBALS['strUserlog'][phpAds_actionPriorityAutoTargeting] = "Kampanya hedefleri tekrar hesaplandı";
$GLOBALS['strUserlog'][phpAds_actionDeactiveCampaign] = "{id} kampanya pasif edildi";
$GLOBALS['strUserlog'][phpAds_actionActiveCampaign] = "{id} kampanya aktif edildi";
$GLOBALS['strUserlog'][phpAds_actionAutoClean] = "Veritabanını otomatik temizle";




// Note: New translations not found in original lang files but found in CSV
$GLOBALS['strAdvertiser'] = "Reklamveren";
$GLOBALS['strPublisher'] = "Web sitesi";
$GLOBALS['strType'] = "Tip";
$GLOBALS['strDeleted'] = "Sil";
$GLOBALS['strUserlog'][phpAds_actionActivationMailed] = "{id} e-mail yolu ile kampanyalar için pasif etme bildirisi gönder";
?>