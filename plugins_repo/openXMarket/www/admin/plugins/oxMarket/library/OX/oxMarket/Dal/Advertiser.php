<?php
/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                                                |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
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
require_once OX_MARKET_LIB_PATH . '/OX/oxMarket/Dal/Campaign.php';
require_once OX_MARKET_LIB_PATH . '/OX/oxMarket/Dal/CampaignsOptIn.php';

/**
 * Advertiser (client) DAL Library. 
 * Handles PC API calls and operations on DataObjects for various operation on Advertiser accounts
 *
 * @package    openXMarket
 * @author     Lukasz Wikierski <lukasz.wikierski@openx.org>
 */
class OX_oxMarket_Dal_Advertiser
{
   
    /**
     * Creates market advertiser account with market campaigns and banners
     *
     * @param int $agencyid
     * @return int clientid (advertiser) 
     */
    public function createMarketAdvertiser($agencyid)
    {
        // is market advertiser present? retunr current id
        $doAdvertiser = $this->getMarketAdvertiser($agencyid);
        if (isset($doAdvertiser)) {
            return $doAdvertiser->clientid;
        }
        // create all objects
        $doAdvertiser = OA_Dal::factoryDO('clients');
        $doAdvertiser->agencyid = $agencyid;
        $doAdvertiser->type = DataObjects_Clients::ADVERTISER_TYPE_MARKET;
        $doAdvertiser->clientname = "OpenX Market Advertiser";
        $doAdvertiser->contact = "OpenX Market Advertiser";
        $doAdvertiser->reportdeactivate = 'f';
        $clientid = $doAdvertiser->insert();
        
        // Create market campaigns
        //import DataObjects_Campaigns class
        if (!class_exists(DataObjects_Campaigns)) {
            $campaign = OA_Dal::factoryDO('campaigns');
            unset($campaign);
        }
        // Create campaign optin campaign with market banner
        $oCampaigns = new OX_oxMarket_Dal_Campaign();
        $oCampaigns->addMarketCampaign($clientid,
            DataObjects_Campaigns::CAMPAIGN_TYPE_MARKET_CAMPAIGN_OPTIN,
            'OpenX Market ads served to opted in campaigns');
        //Create zone optin campaign with market banner
        $campaignId = $oCampaigns->addMarketCampaign($clientid,
            DataObjects_Campaigns::CAMPAIGN_TYPE_MARKET_ZONE_OPTIN,
            'OpenX Market ads served to zones by default');
        //optin campaign to the market with floor price =0
        $oCampaignsOptIn = new OX_oxMarket_Dal_CampaignsOptIn();
        $oCampaignsOptIn->insertOrUpdateMarketCampaignPref($campaignId, 0.0);
        return $clientid;
    }
    
    
    /**
     * Find market advertiser for given agencyid
     *
     * @param int $agencyId
     * @return int clientid (advertiser id), null if not found
     */
    public function getMarketAdvertiser($agencyId)
    {
        $doAdvertiser = OA_Dal::factoryDO('clients');
        $doAdvertiser->agencyid = $agencyId;
        $doAdvertiser->type = DataObjects_Clients::ADVERTISER_TYPE_MARKET;
        $doAdvertiser->find();
        if ($doAdvertiser->fetch()) {
            return $doAdvertiser;
        }
        return null;
    }


    /**
     * Create missing market advertisers for newly added managers
     */
    public function createMissingMarketAdvertisers($multipleAccountMode)
    {       
        if (!$multipleAccountMode) {
            // Is market registered
            $doMarketAssocData = OA_Dal::factoryDO('ext_market_assoc_data');
            $doMarketAssocData->account_id = DataObjects_Accounts::getAdminAccountId();
            $doMarketAssocData->find();
            if ($doMarketAssocData->fetch()) {
                $doAgency = OA_Dal::factoryDO('agency');
                $aManagers = $doAgency->getAll('agencyid');
            } else {
                $aManagers = array();
            }
        } else {
            $doAgency = OA_Dal::factoryDO('agency');
            $doAccounts = OA_Dal::factoryDO('accounts');
            $doMarketAssocData = OA_Dal::factoryDO('ext_market_assoc_data');
            $doAccounts->joinAdd($doMarketAssocData);
            $doAgency->joinAdd($doAccounts);
            $aManagers = $doAgency->getAll('agencyid');
        }
        
        foreach ($aManagers as $agencyid) {
            // this method checks if given manager already have market advertiser
            $this->createMarketAdvertiser($agencyid);
        }
    }
    
    /**
     * Creates market advertiser account with market campaigns and banners
     *
     * @param int $account_id Manager account id
     * @return int clientid (advertiser) 
     */
    public function createMarketAdvertiserByManagerAccountId($account_id)
    {
        $doAgency = OA_Dal::factoryDO('agency');
        $doAgency->get('account_id', $account_id);
        return $this->createMarketAdvertiser($doAgency->agencyid);        
    }
    
}