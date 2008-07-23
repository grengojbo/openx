<?php

/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                             |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                            |
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
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dll.php';
require_once MAX_PATH . '/lib/max/Admin/Redirect.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';
require_once MAX_PATH . '/lib/max/other/common.php';
require_once MAX_PATH . '/lib/max/other/capping/lib-capping.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH .'/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-maintenance-priority.inc.php';
require_once MAX_PATH . '/lib/pear/Date.php';
require_once MAX_PATH . '/lib/OA/Admin/NumberFormat.php';

// Register input variables
phpAds_registerGlobalUnslashed('start', 'startSet', 'anonymous', 'campaignname',
    'clicks', 'companion', 'comments', 'conversions', 'end', 'endSet', 
    'priority', 'high_priority_value', 'revenue', 'revenue_type', 'submit',
    'submit_status', 'target_old', 'target_type_old', 'target_value', 
    'target_type', 'rd_impr_bkd', 'rd_click_bkd', 'rd_conv_bkd', 'impressions',
    'weight_old', 'weight', 'clientid', 'status', 'status_old', 'as_reject_reason',
    'an_status', 'previousimpressions', 'previousconversions','previousclicks'
);

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);
OA_Permission::enforceAccessToObject('clients',   $clientid);
OA_Permission::enforceAccessToObject('campaigns', $campaignid, true);

/*-------------------------------------------------------*/
/* Initialise data                                    */
/*-------------------------------------------------------*/
if ($campaignid != "") {
    // Edit or Convert
    // Fetch exisiting settings
    // Parent setting for converting, campaign settings for editing
    if ($campaignid != "") {
        $ID = $campaignid;
    }

    // Get the campaign data from the campaign table, and store in $campaign
    $doCampaigns = OA_Dal::factoryDO('campaigns');
    $doCampaigns->selectAdd("views AS impressions");
    $doCampaigns->get($ID);
    $data = $doCampaigns->toArray();

    $campaign['campaignname']        = $data['campaignname'];
    $campaign['impressions']         = $data['impressions'];
    $campaign['clicks']              = $data['clicks'];
    $campaign['conversions']         = $data['conversions'];
    $campaign['expire']              = $data['expire'];
    if (OA_Dal::isValidDate($data['expire'])) {
        $oExpireDate                = new Date($data['expire']);
        $campaign['expire_f']            = $oExpireDate->format($date_format);
        $campaign['expire_dayofmonth']   = $oExpireDate->format('%d');
        $campaign['expire_month']        = $oExpireDate->format('%m');
        $campaign['expire_year']         = $oExpireDate->format('%Y');
    }
    $campaign['status']                  = $doCampaigns->status;
    $campaign['an_status']               = $doCampaigns->an_status;
    $campaign['as_reject_reason']        = $doCampaigns->as_reject_reason;

    if (OA_Dal::isValidDate($data['activate'])) {
        $oActivateDate              = new Date($data['activate']);
        $campaign['activate_f']          = $oActivateDate->format($date_format);
        $campaign['activate_dayofmonth'] = $oActivateDate->format('%d');
        $campaign['activate_month']      = $oActivateDate->format('%m');
        $campaign['activate_year']       = $oActivateDate->format('%Y');
    }
    $campaign['priority']            = $data['priority'];
    $campaign['weight']              = $data['weight'];
    $campaign['target_impression']   = $data['target_impression'];
    $campaign['target_click']        = $data['target_click'];
    $campaign['target_conversion']   = $data['target_conversion'];
    $campaign['anonymous']           = $data['anonymous'];
    $campaign['companion']           = $data['companion'];
    $campaign['comments']            = $data['comments'];
    $campaign['revenue']             = OA_Admin_NumberFormat::formatNumber($data['revenue'], 4);
    $campaign['revenue_type']        = $data['revenue_type'];
    $campaign['block']               = $data['block'];
    $campaign['capping']             = $data['capping'];
    $campaign['session_capping']     = $data['session_capping'];
    $campaign['impressionsRemaining'] = '';
    $campaign['clicksRemaining'] = '';
    $campaign['conversionsRemaining'] = '';

    $campaign['impressionsRemaining'] = '';
    $campaign['clicksRemaining']      = '';
    $campaign['conversionsRemaining'] = '';

    // Get the campagin data from the data_intermediate_ad table, and store in $campaign
    if (($campaign['impressions'] >= 0) || ($campaign['clicks'] >= 0) || ($campaign['conversions'] >= 0)) {
        $dalData_intermediate_ad = OA_Dal::factoryDAL('data_intermediate_ad');
        $record = $dalData_intermediate_ad->getDeliveredByCampaign($campaignid);
        $data = $record->toArray();

        $campaign['impressionsRemaining'] = ($campaign['impressions']) ? ($campaign['impressions'] - $data['impressions_delivered']) : '';
        $campaign['clicksRemaining']      = ($campaign['clicks']) ? ($campaign['clicks'] - $data['clicks_delivered']) : '';
        $campaign['conversionsRemaining'] = ($campaign['conversions']) ? ($campaign['conversions'] - $data['conversions_delivered']) : '';
        
        $campaign['impressions_delivered'] = $data['impressions_delivered']; 
        $campaign['clicks_delivered'] = $data['clicks_delivered'];
        $campaign['conversions_delivered'] = $data['conversions_delivered'];
    }

    // Get the value to be used in the target_value field
    if ($campaign['target_impression'] > 0) {
        $campaign['target_value'] = $campaign['target_impression'];
        $campaign['target_type'] = 'target_impression';
    } 
    elseif ($campaign['target_click'] > 0) {
        $campaign['target_value'] = $campaign['target_click'];
        $campaign['target_type'] = 'target_click';
    } 
    elseif ($campaign['target_conversion'] > 0) {
        $campaign['target_value'] = $campaign['target_conversion'];
        $campaign['target_type'] = 'target_conversion';
    } 
    else {
        $campaign['target_value'] = '-';
        $campaign['target_type'] = 'target_impression';
    }

    if ($campaign['target_value'] > 0) {
        $campaign['weight'] = '-';
    } 
    else {
        $campaign['target_value'] = '-';
    }

    // Set default activation settings
    if (!isset($campaign["activate_dayofmonth"])) {
        $campaign["activate_dayofmonth"] = 0;
    }
    if (!isset($campaign["activate_month"])) {
        $campaign["activate_month"] = 0;
    }
    if (!isset($campaign["activate_year"])) {
        $campaign["activate_year"] = 0;
    }
    if (!isset($campaign["activate_f"])) {
        $campaign["activate_f"] = "-";
    }

    // Set default expiration settings
    if (!isset($campaign["expire_dayofmonth"])) {
        $campaign["expire_dayofmonth"] = 0;
    }
    if (!isset($campaign["expire_month"])) {
        $campaign["expire_month"] = 0;
    }
    if (!isset($campaign["expire_year"])) {
        $campaign["expire_year"] = 0;
    }
    if (!isset($campaign["expire_f"])) {
        $campaign["expire_f"] = "-";
    }

    // Set the default financial information
    if (!isset($campaign['revenue'])) {
        $campaign['revenue'] = OA_Admin_NumberFormat::formatNumber(0, 4);
    }

} 
else {
    // New campaign
    $doClients = OA_Dal::factoryDO('clients');
    $doClients->clientid = $clientid;
    $client = $doClients->toArray();

    if ($doClients->find() && $doClients->fetch() && $client = $doClients->toArray()) {
        $campaign['campaignname'] = $client['clientname'].' - ';
    } 
    else {
        $campaign["campaignname"] = '';
    }

    $campaign["campaignname"] .= $strDefault." ".$strCampaign;
    $campaign["impressions"] = '';
    $campaign["clicks"]      = '';
    $campaign["conversions"] = '';
    $campaign["status"]         = (int)$status;
    $campaign["expire"]         = '';
    $campaign["activate"]       = '';
    $campaign["priority"]    = 0;
    $campaign["anonymous"]    = ($pref['gui_campaign_anonymous'] == 't') ? 't' : '';
    $campaign['revenue']     = OA_Admin_NumberFormat::formatNumber(0, 4);;
    $campaign['revenue_type']     = null;
    $campaign['target_value']     = '-';
    $campaign['impressionsRemaining']     = null;
    $campaign['clicksRemaining']     = null;
    $campaign['conversionsRemaining']     = null;
    $campaign['companion']     = null;
    $campaign['block']     = null;
    $campaign['capping']     = null;
    $campaign['session_capping']     = null;
    $campaign['comments']     = null;
    $campaign['target_type'] = null;
}

if ($campaign['status'] == OA_ENTITY_STATUS_RUNNING 
    && OA_Dal::isValidDate($campaign['expire']) && $campaign['impressions'] > 0) {
    $campaign['delivery'] = 'auto';
}
elseif ($campaign['target_value'] > 0) {
    $campaign['delivery'] = 'manual';
}
else {
    $campaign['delivery'] = 'none';
}
    
$campaign['clientid'] = $clientid;
$campaign['campaignid'] = $campaignid;
    
/*-------------------------------------------------------*/
/* MAIN REQUEST PROCESSING                               */
/*-------------------------------------------------------*/
//build campaign form
$campaignForm = buildCampaignForm($campaign);

if (!empty($campaign['campaignid']) && defined('OA_AD_DIRECT_ENABLED') 
    && OA_AD_DIRECT_ENABLED === true) {
    //campaign status form
    $statusForm = buildStatusForm($campaign);
}

if ($campaignForm->isSubmitted() && $campaignForm->validate()) {
    //process submitted values
    $errors = processCampaignForm($campaignForm);
    if (!empty($errors)) { //need to redisplay page with general errors
        displayPage($campaign, $campaignForm, $statusForm, $campaignErrors);                
    }
}
else if (!empty($campaign['campaignid']) && defined('OA_AD_DIRECT_ENABLED') && OA_AD_DIRECT_ENABLED === true &&
    $statusForm->isSubmitted() && $statusForm->validate()) {
    processStatusForm($statusForm);
}
else { //either validation failed or no form was not submitted, display the page
    displayPage($campaign, $campaignForm, $statusForm);
}

/*-------------------------------------------------------*/
/* Build form                                            */
/*-------------------------------------------------------*/
function buildCampaignForm($campaign)
{
    global $pref;
    
    $form = new OA_Admin_UI_Component_Form("campaignform", "POST", $_SERVER['PHP_SELF']);
    $form->forceClientValidation(true);
    $form->addElement('hidden', 'campaignid', $campaign['campaignid']);
    $form->addElement('hidden', 'clientid', $campaign['clientid']);
    $form->addElement('hidden', 'expire', $campaign['expire']);
    $form->addElement('hidden', 'target_old', isset($campaign['target_value']) ? (int)$campaign['target_value'] : 0);
    $form->addElement('hidden', 'target_type_old', isset($campaign['target_type']) ? $campaign['target_type'] : '');
    $form->addElement('hidden', 'weight_old', isset($campaign['weight']) ? (int)$campaign['weight'] : 0);
    $form->addElement('hidden', 'status_old', isset($campaign['status']) ? (int)$campaign['status'] : 1);
    $form->addElement('hidden', 'previousweight', isset($campaign["weight"]) ? $campaign["weight"] : '');
    $form->addElement('hidden', 'previoustarget', isset($campaign["target"]) ? $campaign["target"] : '');
    $form->addElement('hidden', 'previousactive', isset($campaign["active"]) ? $campaign["active"] : '');
    $form->addElement('hidden', 'previousimpressions', isset($campaign["impressions"]) ? $campaign["impressions"] : '');
    $form->addElement('hidden', 'previousclicks', isset($campaign["clicks"]) ? $campaign["clicks"] : '');
    $form->addElement('hidden', 'previousconversions', isset($campaign["conversions"]) ? $campaign["conversions"] : '');
    
    //campaign inactive note (if any)
    if (isset($campaign['status']) && $campaign['status'] != OA_ENTITY_STATUS_RUNNING)
    {
        $aReasons = getCampaignInactiveReasons($campaign);
        $form->addElement('custom', 'campaign-inactive-note', null, 
            array('inactiveReason' => $aReasons), false);
    }
    
    //form sections
    buildBasicInformationFormSection($form, $campaign);
    buildInventoryDetailsFormSection($form, $campaign);
    buildContractDetailsFormSection($form, $campaign);
    buildPriorityFormSection($form, $campaign);
    buildDeliveryCappingFormSection($form, $GLOBALS['strCappingCampaign'], $campaign);
    buildMiscFormSection($form, $campaign);
    
    //form controls
    $form->addElement('controls', 'form-controls');
    $form->addElement('submit', 'submit', $GLOBALS['strSaveChanges']);
    
    //validation rules
    $translation = new OA_Translation();
    $nameRequiredMsg = $translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strName'])); 
    $form->addRule('campaignname', $nameRequiredMsg, 'required');
    
    // Get unique campaignname
    $doCampaigns = OA_Dal::factoryDO('campaigns');
    $doCampaigns->clientid = $campaign['clientid'];
    $aUnique_names = $doCampaigns->getUniqueValuesFromColumn('campaignname', 
        empty($campaign['campaignid'])? '' : $campaign['campaignname']);
    $nameUniqueMsg = $translation->translate($GLOBALS['strXUniqueField'], 
        array($GLOBALS['strCampaign'], strtolower($GLOBALS['strName'])));
    $form->addRule('campaignname', $nameUniqueMsg, 'unique', $aUnique_names);
    
//	$form->addRule('impressions', 'TODO message', 'formattedNumber');
//	$form->addRule('clicks', 'TODO message', 'formattedNumber');
//    if ($conf['logging']['trackerImpressions']) {
//		$form->addRule('conversions', 'TODO message', 'formattedNumber');
//    }    
//	$form->addRule('weight', 'TODO message', 'wholeNumber-');
//	$form->addRule('target_value', 'TODO message', 'wholeNumber-');

	
    //set form values 
    $form->setDefaults($campaign);
    
    $form->setDefaults(
        array(
        'impressions' => 
            !isset($campaign['impressions']) || $campaign['impressions'] == '' || $campaign['impressions'] < 0
            ? '-' : $campaign['impressions'],
        'clicks' => 
            !isset($campaign['clicks']) || $campaign['clicks'] == '' || $campaign['clicks'] < 0
            ? '-' : $campaign['clicks'],
        'conversions' => 
            !isset($campaign['conversions']) || $campaign['conversions'] == '' || $campaign['conversions'] < 0
            ? '-' : $campaign['conversions'],            
    ));
    
    $startDateSet = ($campaign["activate_dayofmonth"] == 0 && $campaign["activate_month"] == 0 
        && $campaign["activate_year"] == 0)? 'f' : 't';
    $endDateSet = ($campaign["expire_dayofmonth"] == 0 && $campaign["expire_month"] == 0 
        && $campaign["expire_year"] == 0)? 'f' : 't';
        
    if ($startDateSet == "t") {
        $oStartDate = new Date($campaign["activate_year"] .'-'
            . $campaign["activate_month"] .'-'. $campaign["activate_dayofmonth"] );
    }
    $startDateStr = is_null($oStartDate) ? '' : $oStartDate->format('%d %B %Y ');
    if ($endDateSet == "t") {
        $oEndDate = new Date($campaign["expire_year"] .'-'
            . $campaign["expire_month"] .'-'. $campaign["expire_dayofmonth"] );
    }
    $endDateStr = is_null($oEndDate) ? '' : $oEndDate->format('%d %B %Y ');    
    
    $form->setDefaults(array(
        'rd_impr_bkd' => ($campaign["impressions"] >= 0 ? 'no' : 'unl'),
        'rd_click_bkd' => ($campaign["clicks"] >= 0 ? 'no' : 'unl'),
        'rd_conv_bkd' => ($campaign["conversions"] >= 0 ? 'no' : 'unl'),
        'startSet' => $startDateSet, 'endSet' => $endDateSet,
        'start' => $startDateStr, 'end' => $endDateStr,
        'priority' => ($campaign['priority'] > '0' && $campaign['campaignid'] != '') 
            ? 2: $campaign['priority'],
        'high_priority_value' => $campaign['priority'] > '0' 
            ? $campaign['priority']: 5,
        'target_value' => !empty($campaign['target_value']) 
            ? $campaign['target_value'] : '-',            
        'weight' => isset($campaign["weight"]) 
            ? $campaign["weight"] : $pref['default_campaign_weight']            
    ));
    
    return $form;    
}


function buildBasicInformationFormSection(&$form, $campaign)
{
    $form->addElement('header', 'h_basic_info', $GLOBALS['strBasicInformation']);
    $form->addElement('text', 'campaignname', $GLOBALS['strName']);
    
    
//EX.	$form->addElement('text', 'test', 'Test field');    
//EX.	$form->addRule('test', 'Weight must be positive number', 'formattednumber');
    
    
//EX.    $form->addDecorator('basic_info', 'tag', 
//        array('attributes' => array('id' => 'test', 'style' => 'display:none')));
//EX.    $form->addDecorator('basic_info', 'tag', array('tag' => 'div', 
//        'attributes' => array('id' => 'innerdiv', 'style' => 'display:none')));
    
    
    $form->addDecorator('test', 'process', array('tag' => 'tr',
        'addAttributes' => array('id' => 'trtest{numCall}', 'style' => 'display: none')));
}

function buildInventoryDetailsFormSection(&$form, $campaign)
{
    global $conf;
    
    $form->addElement('header', 'h_inv_details', $GLOBALS['strInventoryDetails']);

//EX.    $form->addDecorator('inv_details', 'tag', array('tag' => 'span', 
//        'mode' => 'wrap', 'attributes' => array('id' => 'test', 'style' => 'display:none')));
//EX.    $form->addDecorator('basic_info', 'tag', array('tag' => 'div', 
//        'attributes' => array('id' => 'innerdiv', 'style' => 'display:none')));
    
    
    
    //impr booked
    $imprCount['radio'] = $form->createElement('radio', 'rd_impr_bkd', null, null, 
        'no', array('id' => 'limitedimpressions'));
    $imprCount['impressions'] = $form->createElement('text', 'impressions', null, array('class'=>'small'));
    $imprCount['note'] = $form->createElement('custom', 'campaign-remaining-impr', null, 
        array('impressionsRemaining' => $campaign['impressionsRemaining']), false);
        
    $imprBookedGroup['count'] = $form->createElement('group', 'impr_booked', null, $imprCount, null, false);        
    $imprBookedGroup['unlimitedradio'] = $form->createElement('radio', 'rd_impr_bkd', null, 
        $GLOBALS['strUnlimited'], 'unl', array('id' => 'unlimitedimpressions'));
    
    $form->addGroup($imprBookedGroup, 'impr_booked', $GLOBALS['strImpressionsBooked'], 
        "<br/>");
    
    //clicks booked
    $clickCount['radio'] = $form->createElement('radio', 'rd_click_bkd', null, null, 
        'no', array('id' => 'limitedclicks'));
    $clickCount['clicks'] = $form->createElement('text', 'clicks', null, array('class'=>'small'));
    $clickCount['note'] = $form->createElement('custom', 'campaign-remaining-click', null, 
        array('clicksRemaining' => $campaign['clicksRemaining']), false);
    $clickBookedGroup['count'] = $form->createElement('group', 'click_booked', null, $clickCount, null, false);        
    $clickBookedGroup['unlimitedradio'] = $form->createElement('radio', 'rd_click_bkd', null, 
        $GLOBALS['strUnlimited'], 'unl', array('id' => 'unlimitedclicks'));
    
    $form->addGroup($clickBookedGroup, 'click_booked', $GLOBALS['strClicksBooked'], 
        "<br/>");    
    
    // Conditionally display conversion tracking
    if ($conf['logging']['trackerImpressions']) {
        //conversions booked
        $convCount['radio'] = $form->createElement('radio', 'rd_conv_bkd', null, null, 
            'no', array('id' => 'limitedconv'));
        $convCount['conversions'] = $form->createElement('text', 'conversions', null, array('class'=>'small'));
        $convCount['note'] = $form->createElement('html', null,  
            '<span  id="remainingConversions" >'.$GLOBALS['strConversionsRemaining']
            .':<span id="remainingConversionsCount">'
            .$campaign['conversionsRemaining'].'</span></span>');        
        $convBookedGroup['count'] = $form->createElement('group', 'conv_booked', null, $convCount, null, false);        
        $convBookedGroup['unlimitedradio'] = $form->createElement('radio', 'rd_conv_bkd', null, 
            $GLOBALS['strUnlimited'], 'unl', array('id' => 'unlimitedconversions'));
        
       $form->addGroup($convBookedGroup, 'conv_booked', $GLOBALS['strConversionsBooked'], 
            "<br/>");         
    }
}


function buildContractDetailsFormSection(&$form, $campaign)
{
    global $conf;    
    
    $form->addElement('header', 'h_contract', $GLOBALS['strContractDetails']);
    //activation date
    $actDateGroup['now'] = $form->createElement('radio', 'startSet', null, 
        $GLOBALS['strActivateNow'], 'f', array('id' => 'startSet_immediate', 
        'onClick' => 'phpAds_formDateClick(\'start\', false)'));

    $setActDate['radio'] = $form->createElement('radio', 'startSet', null, null, 
        't', array('id' => 'startSet_specific', 'onClick' => 'phpAds_formDateClick(\'start\', false)'));
    $setActDate['date'] = $form->createElement('text', 'start', null, 
        array('id' => 'start', 'onChange' => 'phpAds_formDateCheck(\'start\');'));
    $setActDate['cal_img'] = $form->createElement('image', 'start_button',
        MAX::assetPath() . "/images/icon-calendar.gif", 
        array('id' => 'start_button', 'align' => 'absmiddle'));
    $setActDate['note'] = $form->createElement('html', null, $GLOBALS['strActivationDateComment']); 

    $actDateGroup['setDate'] = $form->createElement('group', 'setDate', null, $setActDate, null, false);        
    $form->addGroup($actDateGroup, 'act_date', $GLOBALS['strActivationDate'], "<br/>");    
    
    //expiriation date
    $expDateGroup['now'] = $form->createElement('radio', 'endSet', null, 
        $GLOBALS['strDontExpire'], 'f', array('id' => 'endSet_immediate', 
        'onClick' => 'phpAds_formDateClick(\'end\', false)'));

    $expActDate['radio'] = $form->createElement('radio', 'endSet', null, null, 
        't', array('id' => 'endSet_specific', 'onClick' => 'phpAds_formDateClick(\'end\', false)'));
    $expActDate['date'] = $form->createElement('text', 'end', null, 
        array('id' => 'end', 'onChange' => 'phpAds_formDateCheck(\'end\');'));
    $expActDate['cal_img'] = $form->createElement('image', 'end_button',
        MAX::assetPath() . "/images/icon-calendar.gif", 
        array('id' => 'end_button', 'align' => 'absmiddle'));
    $expActDate['note'] = $form->createElement('html', null, $GLOBALS['strExpirationDateComment']); 

    $expDateGroup['setDate'] = $form->createElement('group', 'setDate', null, $expActDate, null, false);        
    $form->addGroup($expDateGroup, 'act_date', $GLOBALS['strExpirationDate'], "<br/>");    

    //revenue info
    $revInfGroup['text'] = $form->createElement('text', 'revenue', null, 
        array('size' => 7));
    $aRevenueTypes = array(MAX_FINANCE_CPM => $GLOBALS['strFinanceCPM'], 
                            MAX_FINANCE_CPC => $GLOBALS['strFinanceCPC']);
    // Conditionally display conversion tracking
    if ($conf['logging']['trackerImpressions']) {
      $aRevenueTypes[MAX_FINANCE_CPA] = $GLOBALS['strFinanceCPA'];
    }
    $aRevenueTypes[MAX_FINANCE_MT] = $GLOBALS['strFinanceMT'];
    $revInfGroup['select'] = $form->createElement('select', 'revenue_type', 
        null, $aRevenueTypes);
    $form->addGroup($revInfGroup, 'revenue_g', $GLOBALS['strRevenueInfo']);
    
    
//EX.    $form->addDecorator('revenue_type', 'tag', array('tag' => 'span', 
//        'attributes' => array('id' => 'revTypeSel', 'style' => 'display:none')));
    
    
    /* 
      if (defined('OA_AD_DIRECT_ENABLED') && OA_AD_DIRECT_ENABLED === true) {    
        $form->addElement('static', 'total_revenue', $GLOBALS['strTotalRevenue'], 
        'REVENUE VALUE GOES HERE');
    }*/
}


function buildPriorityFormSection(&$form, $campaign)
{
    global $conf;
    
    //priority section
    $form->addElement('header', 'h_priority', $GLOBALS['strPriorityInformation']);
    
    //priority level
    $prioritiesG['excl'] = $form->createElement('radio', 'priority', null, 
        $GLOBALS['strExclusive']." ".$GLOBALS['strPriorityExclusive'], -1, 
        array('id' => 'priority-e', 'onClick' => 'phpAds_formPriorityRadioClick(this);' ));
    $highP['radio'] = $form->createElement('radio', 'priority', null, null, 2, 
        array('id' => 'priority-h', 'onClick' => 'phpAds_formPriorityRadioClick(this);' ));
    for ($i = 10; $i >= 1; $i--) {
        $aHighPriorities[$i] = $GLOBALS['strHigh']." ($i)";
    }
    $highP['select'] = $form->createElement('select', 'high_priority_value', 
        null, $aHighPriorities);
    $highP['note'] = $form->createElement('html', null, $GLOBALS['strPriorityHigh']);
    $prioritiesG['high'] = $form->createElement('group', 'high_p', null, $highP, null, false);        
    $prioritiesG['low'] = $form->createElement('radio', 'priority', null, 
        $GLOBALS['strLow']." ".$GLOBALS['strPriorityLow'], 0, 
        array('id' => 'priority-l', 'onClick' => 'phpAds_formPriorityRadioClick(this);' ));
    $form->addGroup($prioritiesG, 'priotity_level_g', $GLOBALS['strPriorityLevel'], "<BR>");
    
    //distribution
    $distributionG['aut'] = $form->createElement('radio', 'delivery', null, 
        $GLOBALS['strPriorityAutoTargeting'], 'auto', 
        array('id' => 'delivery-a', 'onClick' => 'phpAds_formDeliveryRadioClick(this);'));
    
    $aManualDel['radio'] = $form->createElement('radio', 'delivery', null, null, 
        'manual', array('id' => 'delivery-n', 'onClick' => 'phpAds_formDeliveryRadioClick(this);' ));
    
    $aTargetTypes['target_impression'] = $GLOBALS['strImpressions'];
    $aTargetTypes['target_click'] = $GLOBALS['strClicks'];
    // Conditionally display conversion tracking
    if ($conf['logging']['trackerImpressions']) {
      $aTargetTypes['target_conversion'] = $GLOBALS['strConversions'];
    }
    $aManualDel['select'] = $form->createElement('select', 'target_type', null, 
        $aTargetTypes);
    $aManualDel['text'] = $form->createElement('text', 'target_value', $GLOBALS['strTo'], 
        array('size' => 7, 'onBlur' => 'phpAds_formPriorityUpdate(this.form);'));
    $aManualDel['perDayNote'] = $form->createElement('html', null, $GLOBALS['strTargetPerDay']);        
    $distributionG['man'] = $form->createElement('group', 'd_man', null, $aManualDel, null, false);
    
    $aNoneDel['radio'] = $form->createElement('radio', 'delivery', null, 
        $GLOBALS['strCampaignWeight'].":", 'none', array('id' => 'delivery-n', 
        'onClick' => 'phpAds_formDeliveryRadioClick(this);' ));
    $aNoneDel['text'] = $form->createElement('text', 'weight', null, 
        array('size' => 7, 'onBlur' => 'phpAds_formPriorityUpdate(this.form);'));
    $distributionG['none'] = $form->createElement('group', 'd_none', null, $aNoneDel, null, false);
    
    $form->addGroup($distributionG, 'distribution_g', $GLOBALS['strPriorityTargeting'], "<BR>");
    
    //priority misc
    $miscG['anonymous'] = $form->createElement('advcheckbox', 'anonymous', null, 
        $GLOBALS['strAnonymous'], null, array("f", "t"));
    $miscG['companion'] = $form->createElement('checkbox', 'companion', null, 
        $GLOBALS['strCompanionPositioning']);
    $form->addGroup($miscG, 'misc_g', $GLOBALS['strPriorityOptimisation'], "<BR>");
}


function buildMiscFormSection(&$form, $campaign)
{
    $form->addElement('header', 'h_misc', $GLOBALS['strMiscellaneous']);
    $form->addElement('textarea', 'comments', $GLOBALS['strComments']);
}


function buildStatusForm($campaign)
{
    $form = new OA_Admin_UI_Component_Form("statusChangeForm", "POST", $_SERVER['PHP_SELF']);
    $form->forceClientValidation(true);
    $form->addElement('hidden', 'campaignid', $campaign['campaignid']);
    $form->addElement('hidden', 'campaignid', $campaign['clientid']);
    $form->addElement('header', 'h_misc', $GLOBALS['strCampaignStatus']);

    if ($campaign['status'] == OA_ENTITY_STATUS_APPROVAL) {
        $form->addElement('radio', 'status', $GLOBALS['strStatus'], 
            $GLOBALS['strCampaignApprove']." - ".$GLOBALS['strCampaignApproveDescription'], 
            OA_ENTITY_STATUS_RUNNING , array('id' => 'sts_approve'));

        $form->addElement('radio', 'status', $GLOBALS['strStatus'], 
            $GLOBALS['strCampaignReject']." - ".$GLOBALS['strCampaignRejectDescription'], 
            OA_ENTITY_STATUS_REJECTED , array('id' => 'sts_reject'));    
    } 
    elseif ($campaign['status'] == OA_ENTITY_STATUS_RUNNING) {
        $form->addElement('radio', 'status', $GLOBALS['strStatus'], 
            $GLOBALS['strCampaignPause']." - ".$GLOBALS['strCampaignPauseDescription'], 
            OA_ENTITY_STATUS_PAUSED , array('id' => 'sts_pause'));
    } 
    elseif ($campaign['status'] == OA_ENTITY_STATUS_PAUSED) {
        $form->addElement('radio', 'status', $GLOBALS['strStatus'], 
            $GLOBALS['strCampaignRestart']." - ".$GLOBALS['strCampaignRestartDescription'], 
            OA_ENTITY_STATUS_RUNNING , array('id' => 'sts_restart'));
    } 
    elseif ($campaign['status'] == OA_ENTITY_STATUS_REJECTED) {
        $rejectionReasonText = phpAds_showStatusRejected($campaign['as_reject_reason']);
        $form->addElement('static', 'status', $GLOBALS['strStatus'],
            $rejectionReasonText, 
            OA_ENTITY_STATUS_PAUSED , array('id' => 'sts_pause'));
    }
    
    $form->addElement('select', 'as_reject_reason', $GLOBALS['strReasonForRejection'], 
        array(OA_ENTITY_ADVSIGNUP_REJECT_NOTLIVE => $GLOBALS['strReasonSiteNotLive'],
            OA_ENTITY_ADVSIGNUP_REJECT_BADCREATIVE =>  $GLOBALS['strReasonBadCreative'],
            OA_ENTITY_ADVSIGNUP_REJECT_BADURL => $GLOBALS['strReasonBadUrl'],
            OA_ENTITY_ADVSIGNUP_REJECT_BREAKTERMS => $GLOBALS['strReasonBreakTerms']));
            
    $form->addDecorator('as_reject_reason', 'process', array('tag' => 'tr',
        'addAttributes' => array('id' => 'rsn_row{numCall}', 'style' => 'display: none')));
            
    $form->addElement('controls', 'form-controls');
    $submitLabel = (!empty($zone['zoneid']))  ? $GLOBALS['strSaveChanges'] : $GLOBALS['strNext'].' >';    
    $form->addElement('submit', 'submit_status', $GLOBALS['strChangeStatus']);
    
    return $form;    
}



/*-------------------------------------------------------*/
/* Process submitted form                                */
/*-------------------------------------------------------*/
/**
 * Processes submit values of campaign form
 *
 * @param OA_Admin_UI_Component_Form $form form to process
 * @return An array of Pear::Error objects if any
 */
function processCampaignForm($form) 
{
    $aFields = $form->exportValues();
    
    $expire = !empty($aFields['end']) ? date('Y-m-d', strtotime($aFields['end'])) 
       : OA_Dal::noDateValue();
    $activate = !empty($aFields['start']) ? date('Y-m-d', strtotime($aFields['start'])) 
       : OA_Dal::noDateValue();

    // If ID is not set, it should be a null-value for the auto_increment
    if (empty($aFields['campaignid'])) {
        $aFields['campaignid'] = "null";
    } 
    else {
        require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
        $oldCampaignAdZoneAssocs = Admin_DA::getAdZones(
            array('placement_id' => $aFields['campaignid']));
        $errors = array();
        foreach ($oldCampaignAdZoneAssocs as $adZoneAssocId => $adZoneAssoc) {
            $aZone = Admin_DA::getZone($adZoneAssoc['zone_id']);
            if ($aZone['type'] == MAX_ZoneEmail) {
                $thisLink = Admin_DA::_checkEmailZoneAdAssoc($aZone, 
                    $aFields['campaignid'], $activate, $expire);
                if (PEAR::isError($thisLink)) {
                    $errors[] = $thisLink;
                    break;
                }
            }
        }
    }

    //correct and check revenue
    //correction revenue from other formats (23234,34 or 23 234,34 or 23.234,34)
    //to format acceptable by is_numeric (23234.34)
    $corrected_revenue = OA_Admin_NumberFormat::unformatNumber($aFields['revenue']);
    if ( $corrected_revenue !== false ) {
        $aFields['revenue'] = $corrected_revenue;
        unset($corrected_revenue);
    }
    if (!empty($aFields['revenue']) && !(is_numeric($aFields['revenue']))) {
        // Suppress PEAR error handling to show this error only on top of HTML form
        PEAR::pushErrorHandling(null);
        $errors[] = PEAR::raiseError($GLOBALS['strErrorEditingCampaignRevenue']);
        PEAR::popErrorHandling();
    }

    if (empty($errors)) {
        // Set expired
        if ($aFields['impressions'] == '-') {
            $aFields['impressions'] = 0;
        }
        if ($aFields['clicks'] == '-') {
            $aFields['clicks'] = 0;
        }
        if ($aFields['conversions'] == '-') {
            $aFields['conversions'] = 0;
        }
        // Set unlimited
        if (!isset($aFields['rd_impr_bkd']) || $aFields['rd_impr_bkd'] != 'no') {
            $aFields['impressions'] = -1;
        }
        if (!isset($aFields['rd_click_bkd']) || $aFields['rd_click_bkd'] != 'no') {
            $aFields['clicks'] = -1;
        }
        if (!isset($aFields['rd_conv_bkd']) || $aFields['rd_conv_bkd'] != 'no') {
            $aFields['conversions'] = -1;
        }
        if ($aFields['priority'] > 0) {
            // Set target
            $target_impression = 0;
            $target_click      = 0;
            $target_conversion = 0;
            if ((isset($aFields['target_value'])) && ($aFields['target_value'] != '-')) {
                switch ($aFields['target_type']) {
                case 'target_impression':
                    $target_impression = $aFields['target_value'];
                    break;

                case 'target_click':
                    $target_click = $aFields['target_value'];
                    break;

                case 'target_conversion':
                    $target_conversion = $aFields['target_value'];
                    break;
                }
            }
            if (isset($aFields['high_priority_value'])) {
                $aFields['priority'] = $aFields['high_priority_value'];
            }
            $aFields['weight'] = 0;
        } 
        else {
            // Set weight
            if (!isset($aFields['weight']) || $aFields['weight'] == '-' 
                || $aFields['weight'] == '') {
                    $aFields['weight'] = 0;
            } 
            $target_impression = 0;
            $target_click      = 0;
            $target_conversion = 0;
        }

        if ($aFields['anonymous'] != 't') {
            $aFields['anonymous'] = 'f';
        }
        if ($aFields['companion'] != 1) {
            $aFields['companion'] = 0;
        }
        $new_campaign = $aFields['campaignid'] == 'null';

        if (empty($aFields['revenue']) || ($aFields['revenue'] <= 0)) {
            // No revenue information, set to null
            $aFields['revenue'] = 'NULL';
            $aFields['revenue_type'] = 'NULL';
        }

        // Get the capping variables
        $block = _initCappingVariables($aFields['time'], $aFields['capping'], $aFields['session_capping']);

        $noDateValue = OA_Dal::noDateValue();
        if (!isset($noDateValue)) {
            $noDateValue = 0;
        }

        $doCampaigns = OA_Dal::factoryDO('campaigns');
        $doCampaigns->campaignname = $aFields['campaignname'];
        $doCampaigns->clientid = $aFields['clientid'];
        $doCampaigns->views = $aFields['impressions'];
        $doCampaigns->clicks = $aFields['clicks'];
        $doCampaigns->conversions = $aFields['conversions'];
        $doCampaigns->expire = OA_Dal::isValidDate($expire) 
            ? $expire : $noDateValue;
        $doCampaigns->activate = OA_Dal::isValidDate($activate) 
            ? $activate : $noDateValue;
        $doCampaigns->priority = $aFields['priority'];
        $doCampaigns->weight = $aFields['weight'];
        $doCampaigns->target_impression = $target_impression;
        $doCampaigns->target_click = $target_click;
        $doCampaigns->target_conversion = $target_conversion;
        $doCampaigns->anonymous = $aFields['anonymous'];
        $doCampaigns->companion = $aFields['companion'];
        $doCampaigns->comments = $aFields['comments'];
        $doCampaigns->revenue = $aFields['revenue'];
        $doCampaigns->revenue_type = $aFields['revenue_type'];
        $doCampaigns->block = $block;
        $doCampaigns->capping = $aFields['capping'];
        $doCampaigns->session_capping = $aFields['session_capping'];
        
        
        $doCampaigns->updated = OA::getNow();

        if (!empty($aFields['campaignid']) && $aFields['campaignid'] != "null") {
            $doCampaigns->campaignid = $aFields['campaignid'];
            $doCampaigns->update();
        } 
        else {
            $aFields['campaignid'] = $doCampaigns->insert();
        }


        // Recalculate priority only when editing a campaign
        // or moving banners into a newly created, and when:
        //
        // - campaign changes status (activated or deactivated) or
        // - the campaign is active and target/weight are changed
        //
        if (!$new_campaign) {
            $doCampaigns = OA_Dal::staticGetDO('campaigns', $aFields['campaignid']);
            $status = $doCampaigns->status;
            switch(true) {
                case ((bool)$status != (bool)$aFields['status_old']):
                // Run the Maintenance Priority Engine process
                OA_Maintenance_Priority::scheduleRun();
                break;
                
                case ($status == OA_ENTITY_STATUS_RUNNING):
                    if ((!empty($aFields['target_type']) && ${$aFields['target_type']} != $aFields['target_old'])
                        || (!empty($aFields['target_type']) && $aFields['target_type_old'] != $aFields['target_type'])
                        || $aFields['weight'] != $aFields['weight_old']
                        || $aFields['clicks'] != $aFields['previousclicks']
                        || $aFields['conversions'] != $aFields['previousconversions']
                        || $aFields['impressions'] != $aFields['previousimpressions']) {
                        // Run the Maintenance Priority Engine process
                        OA_Maintenance_Priority::scheduleRun();
                    }
                    break;
            }
        }

        // Rebuild cache
        // include_once MAX_PATH . '/lib/max/deliverycache/cache-'.$conf['delivery']['cache'].'.inc.php';
        // phpAds_cacheDelete();

        // Delete channel forecasting cache
        include_once 'Cache/Lite.php';
        $options = array(
            'cacheDir' => MAX_CACHE,
        );
        $cache = new Cache_Lite($options);
        $group = 'campaign_'.$aFields['campaignid'];
        $cache->clean($group);

        $oUI = new OA_Admin_UI();
        MAX_Admin_Redirect::redirect("campaign-zone.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']);
    }
    
    //return processing errors
    return $errors;
}


function processStatusForm($form)
{ 
    $aFields = $form->exportValues();
    
    if (empty($aFields['campaignid'])) {
        return;
    }

    //update status for existing campaign
    $doCampaigns = OA_Dal::factoryDO('campaigns');
    $doCampaigns->campaignid       = $aFields['campaignid'];
    $doCampaigns->as_reject_reason = $aFields['as_reject_reason'];
    $doCampaigns->status           = $aFields['status'];
    $doCampaigns->update();

    // Run the Maintenance Priority Engine process
    OA_Maintenance_Priority::scheduleRun();

    MAX_Admin_Redirect::redirect("campaign-edit.php?clientid=".$aFields['clientid']."&campaignid=".$aFields['campaignid']);
}
    


/*-------------------------------------------------------*/
/* Display page                                          */
/*-------------------------------------------------------*/
function displayPage($campaign, $campaignForm, $statusForm, $campaignErrors = null)
{
    global $conf;
    
    //header and breadcrumbs
    if ($campaign['campaignid'] != "") { //edit campaign
        // Initialise some parameters
        $pageName = basename($_SERVER['PHP_SELF']);
        $tabindex = 1;
        $agencyId = OA_Permission::getAgencyId();
        $aEntities = array('clientid' => $campaign['clientid'], 
            'campaignid' => $campaign['campaignid']);
    
        // Display navigation
        $aOtherAdvertisers = Admin_DA::getAdvertisers(array('agency_id' => $agencyId));
        $aOtherCampaigns = Admin_DA::getPlacements(
            array('advertiser_id' => $campaign['clientid']));
        MAX_displayNavigationCampaign($pageName, $aOtherAdvertisers, 
            $aOtherCampaigns, $aEntities);
    } 
    else { //new campaign
        $advertiser = phpAds_getClientDetails($campaign['clientid']);
        $advertiserName = $advertiser['clientname'];
        $advertiserEditUrl = "advertiser-edit.php?clientid=".$campaign['clientid'];
    
       // New campaign
        MAX_displayInventoryBreadcrumbs(array(
                                            array("name" => $advertiserName, "url" => $advertiserEditUrl),
                                            array("name" => "")),
                                        "campaign", true);
        phpAds_PageHeader("campaign-edit_new");
    }    
    
    //get template and display form
    $oTpl = new OA_Admin_Template('campaign-edit.html');
    $oTpl->assign('clientid', $campaign['clientid']);
    $oTpl->assign('campaignid', $campaign['campaignid']);
    $oTpl->assign('showAddBannerLink', !empty($campaign['campaignid']) 
        && !OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER));
    $oTpl->assign('calendarBeginOfWeek', $GLOBALS['pref']['begin_of_week'] ? 1 : 0);
    $oTpl->assign('language', $GLOBALS['_MAX']['PREF']['language']);
    $oTpl->assign('conversionsEnabled', $conf['logging']['trackerImpressions']);
    $oTpl->assign('adDirectEnabled', defined('OA_AD_DIRECT_ENABLED') && OA_AD_DIRECT_ENABLED === true);

    $oTpl->assign('impressionsDelivered', isset($campaign['impressions_delivered']) ? $campaign['impressions_delivered'] : 0); 
    $oTpl->assign('clicksDelivered', isset($campaign['clicks_delivered']) ? $campaign['clicks_delivered'] : 0);
    $oTpl->assign('conversionsDelivered', isset($campaign['conversions_delivered']) ? $campaign['conversions_delivered'] : 0);
        
    $oTpl->assign('strCampaignWarningNoTargetMessage', 
        str_replace("\n", '\n', addslashes($GLOBALS['strCampaignWarningNoTarget'])));
    $oTpl->assign('strCampaignWarningNoWeightMessage', 
        str_replace("\n", '\n', addslashes($GLOBALS['strCampaignWarningNoWeight'])));
    
    $oTpl->assign('campaignErrors', $campaignErrors);        
    $oTpl->assign('campaignFormId', $campaignForm->getId());
    $oTpl->assign('campaignForm', $campaignForm->serialize());
    if (!empty($campaign['campaignid']) && defined('OA_AD_DIRECT_ENABLED') && OA_AD_DIRECT_ENABLED === true) {    
        $oTpl->assign('statusForm', $statusForm->serialize());
    }    
    $oTpl->display();
    
    _echoDeliveryCappingJs();
    
    //footer
    phpAds_PageFooter();
}


//UTILS
function phpAds_showStatusRejected($reject_reason) {
    global $strReasonSiteNotLive, $strReasonBadCreative, $strReasonBadUrl,
            $strReasonBreakTerms, $strCampaignStatusRejected;

    switch ($reject_reason) {
        case OA_ENTITY_ADVSIGNUP_REJECT_NOTLIVE:
            $text = $strReasonSiteNotLive;
            break;
        case OA_ENTITY_ADVSIGNUP_REJECT_BADCREATIVE:
            $text = $strReasonBadCreative;
            break;
        case OA_ENTITY_ADVSIGNUP_REJECT_BADURL:
            $text = $strReasonBadUrl;
            break;
        case OA_ENTITY_ADVSIGNUP_REJECT_BREAKTERMS:
            $text = $strReasonBreakTerms;
            break;
    }

    return $strCampaignStatusRejected . ": " . $text;
}


function getCampaignInactiveReasons($aCampaign)
{
    $activate_ts = mktime(23, 59, 59, $aCampaign["activate_month"], $aCampaign["activate_dayofmonth"], $aCampaign["activate_year"]);
    $expire_ts = $aCampaign['expire_year'] 
        ? mktime(23, 59, 59, $aCampaign["expire_month"], $aCampaign["expire_dayofmonth"], $aCampaign["expire_year"]) 
        : 0;
    $aReasons = array();

    if ($aCampaign['impressions'] == 0) {
        $aReasons[] =  $GLOBALS['strNoMoreImpressions'];
    }
    if ($aCampaign['clicks'] == 0) {
        $aReasons[] =  $GLOBALS['strNoMoreClicks'];
    }
    if ($aCampaign['conversions'] == 0) {
        $aReasons[] =  $GLOBALS['strNoMoreConversions'];
    }
    if ($activate_ts > 0 && $activate_ts > time()) {
        $aReasons[] =  $GLOBALS['strBeforeActivate'];
    }
    if ($expire_ts > 0 && time() > $expire_ts) {
        $aReasons[] =  $GLOBALS['strAfterExpire'];
    }

    if ($aCampaign['priority'] == 0  && $aCampaign['weight'] == 0) {
        $aReasons[] =  $GLOBALS['strWeightIsNull'];
    }
    if ($aCampaign['priority'] > 0  && $aCampaign['target_value'] == 0) {
        $aReasons[] =  $GLOBALS['strTargetIsNull'];
    }
    
    return $aReasons;
}


?>
