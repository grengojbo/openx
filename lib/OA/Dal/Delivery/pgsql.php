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

/**
 * The pgsql data access layer code the delivery engine.
 *
 * @package    OpenXDal
 * @subpackage Delivery
 * @author     Chris Nutting <chris.nutting@openx.org>
 * @author     Andrew Hill <andrew.hill@openx.org>
 * @author     Matteo Beccati <matteo.beccati@openx.org>
 */

/**
 * The function to open a database connection, or return the resource if already open
 *
 * @param string $database   The name of the database config to use
 *                           (Must match the database section name in the conf file)
 * @return resource|false    The PgSQL database resource
 *                           or false on failure
 */
function OA_Dal_Delivery_connect($database = 'database') {
    // If a connection already exists, then return that
    if ($database == 'database' && isset($GLOBALS['_MAX']['ADMIN_DB_LINK']) && is_resource($GLOBALS['_MAX']['ADMIN_DB_LINK'])) {
        return $GLOBALS['_MAX']['ADMIN_DB_LINK'];
    } elseif ($database == 'rawDatabase' && isset($GLOBALS['_MAX']['RAW_DB_LINK']) && is_resource($GLOBALS['_MAX']['RAW_DB_LINK'])) {
        return $GLOBALS['_MAX']['RAW_DB_LINK'];
    }
    // No connection exists, so create one
    $conf = $GLOBALS['_MAX']['CONF'];
    if (!empty($conf[$database])) {
        $dbConf = $conf[$database];
    } else {
        $dbConf = $conf['database'];
    }
    $dbParams   = array();
    $dbParams[] = 'port='.(isset($dbConf['port']) ? $dbConf['port'] : 5432);
    $dbParams[] = !empty($dbConf['protocol']) && $dbConf['protocol'] == 'unix' ? '' : 'host='.$dbConf['host'];
    $dbParams[] = empty($dbConf['username']) ? '' : 'user='.$dbConf['username'];
    $dbParams[] = empty($dbConf['password']) ? '' : 'password='.$dbConf['password'];
    $dbParams[] = 'dbname='.$dbConf['name'];
    if ($dbConf['persistent']) {
        $dbLink = @pg_pconnect(join(' ', $dbParams));
    } else {
        $dbLink = @pg_connect(join(' ', $dbParams));
    }
    if ($dbLink && !empty($conf['databasePgsql']['schema'])) {
        @pg_query($dbLink, "SET search_path='{$conf['databasePgsql']['schema']}'");
    }
    if ($dbLink && !empty($conf['databaseCharset']['checkComplete']) && !empty($conf['databaseCharset']['clientCharset'])) {
        @pg_client_encoding($dbLink, $conf['databaseCharset']['clientCharset']);
    }
    return $dbLink;
}

/**
 * The function to pass a query to a database link
 *
 * @param string $query    The SQL query to execute
 * @param string $database The database to use for this query
 *                         (Must match the database section name in the conf file)
 * @return resource|false  The PgSQL resource if the query suceeded
 *                          or false on failure
 */
function OA_Dal_Delivery_query($query, $database = 'database') {
    // Connect to the database if necessary
    $dbName = ($database == 'rawDatabase') ? 'RAW_DB_LINK' : 'ADMIN_DB_LINK';

    if (empty($GLOBALS['_MAX'][$dbName])) {
        $GLOBALS['_MAX'][$dbName] = OA_Dal_Delivery_connect($database);
    }
    if (is_resource($GLOBALS['_MAX'][$dbName])) {
        return @pg_query($GLOBALS['_MAX'][$dbName], $query);
    } else {
        return false;
    }
}

/**
 * The function to retrieve the last-insert-id from the database
 *
 * @param string $database The name of the database config to use
 *                         (Must match the database section name in the conf file)
 * @param string $table    The name of the table we need to get the ID from
 * @param string $column   The name of the column we need to get the ID from
 * @return int|false       The last insert ID (zero if last query didn't generate an ID)
 *                         or false on failure
 * @todo Fix this!
 */
function OA_Dal_Delivery_insertId($database = 'database', $table = '', $column = '')
{
    $dbName = ($database == 'rawDatabase') ? 'RAW_DB_LINK' : 'ADMIN_DB_LINK';
    if (!isset($GLOBALS['_MAX'][$dbName]) || !(is_resource($GLOBALS['_MAX'][$dbName]))) {
        return false;
    }
    $seqName = substr($column, 0, 29).'_seq';
    $seqName = substr($table, 0, 62 - strlen($seqName)).'_'.$seqName;
    $query = "SELECT currval('\"".$seqName."\"')";
    return pg_fetch_result(pg_query($query), 0, 0);
}


/**
 * The function to retrieve accounts timezones and the default admin's timezone
 *
 * @return array An array containing the default timezone and the
 *               list of account IDs and their timezones
 */
function OA_Dal_Delivery_getAccountTZs()
{
    $aConf = $GLOBALS['_MAX']['CONF'];

    $query = "
        SELECT
            value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['application_variable']}\"
        WHERE
            name = 'admin_account_id'
    ";

    $res = OA_Dal_Delivery_query($query);

    if (is_resource($res) && pg_num_rows($res)) {
        $adminAccountId = (int)pg_result($res, 0, 0);
    } else {
        $adminAccountId = false;
    }

    $query = "
        SELECT
            a.account_id AS account_id,
            apa.value AS timezone
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['accounts']}\" AS a JOIN
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa ON (apa.account_id = a.account_id) JOIN
            \"{$aConf['table']['prefix']}{$aConf['table']['preferences']}\" AS p ON (p.preference_id = apa.preference_id)
        WHERE
            a.account_type IN ('ADMIN', 'MANAGER') AND
            p.preference_name = 'timezone'
    ";

    $res = OA_Dal_Delivery_query($query);

    $aResult = array(
        'adminAccountId' => $adminAccountId,
        'aAccounts' => array()
    );
    if (is_resource($res)) {
        while ($row = pg_fetch_assoc($res)) {
            $accountId = (int)$row['account_id'];
            if ($accountId === $adminAccountId) {
                $aResult['default'] = $row['timezone'];
            } else {
                $aResult['aAccounts'][$accountId] = $row['timezone'];
            }
        }
    }
    if (empty($aResult['default'])) {
        $aResult['default'] = 'UTC';
    }

    return $aResult;
}

/**
 * This function gets zone properties from the databse
 *
 * @param int $zoneid   The ID of the zone to get information about
 * @return array|false  An array containing the properties for that zone
 *                      or false on failure
 */
function OA_Dal_Delivery_getZoneInfo($zoneid) {
    $aConf = $GLOBALS['_MAX']['CONF'];

    // Get the zone information
    $query = "
        SELECT
            z.zoneid AS zone_id,
            z.zonename AS name,
            z.delivery AS type,
            z.description AS description,
            z.width AS width,
            z.height AS height,
            z.chain AS chain,
            z.prepend AS prepend,
            z.append AS append,
            z.appendtype AS appendtype,
            z.forceappend AS forceappend,
            z.inventory_forecast_type AS inventory_forecast_type,
            z.block AS block_zone,
            z.capping AS cap_zone,
            z.session_capping AS session_cap_zone,
            a.account_id AS trafficker_account_id,
            m.account_id AS manager_account_id
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['zones']}\" AS z,
            \"{$aConf['table']['prefix']}{$aConf['table']['affiliates']}\" AS a,
            \"{$aConf['table']['prefix']}{$aConf['table']['agency']}\" AS m
        WHERE
            z.zoneid = {$zoneid}
          AND
            z.affiliateid = a.affiliateid
          AND
            a.agencyid = m.agencyid";
    $rZoneInfo = OA_Dal_Delivery_query($query);

    if (!is_resource($rZoneInfo)) {
        return false;
    }
    $aZoneInfo = pg_fetch_assoc($rZoneInfo);

    // Set the default banner preference information for the zone
    $query = "
        SELECT
            p.preference_id AS preference_id,
            p.preference_name AS preference_name
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['preferences']}\" AS p
        WHERE
            p.preference_name = 'default_banner_image_url'
            OR
            p.preference_name = 'default_banner_destination_url'";
    $rPreferenceInfo = OA_Dal_Delivery_query($query);

    if (!is_resource($rPreferenceInfo)) {
        return false;
    }
    if (pg_num_rows($rPreferenceInfo) != 2) {
        // Something went wrong, there should be two preferences, if not,
        // cannot get the default banner image and destination URLs
        return $aZoneInfo;
    }
    // Set the IDs of the two preferences for default banner image and
    // destination URLs
    $aPreferenceInfo = pg_fetch_assoc($rPreferenceInfo);
    $variableName = $aPreferenceInfo['preference_name'] . '_id';
    $$variableName = $aPreferenceInfo['preference_id'];
    $aPreferenceInfo = pg_fetch_assoc($rPreferenceInfo);
    $variableName = $aPreferenceInfo['preference_name'] . '_id';
    $$variableName = $aPreferenceInfo['preference_id'];

    // Search for possible default banner preference information for the zone
    $query = "
        SELECT
            'default_banner_image_url_trafficker' AS item,
            apa.value AS value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa
        WHERE
            apa.account_id = {$aZoneInfo['trafficker_account_id']}
            AND
            apa.preference_id = $default_banner_image_url_id
        UNION
        SELECT
            'default_banner_destination_url_trafficker' AS item,
            apa.value AS value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa
        WHERE
            apa.account_id = {$aZoneInfo['trafficker_account_id']}
            AND
            apa.preference_id = $default_banner_destination_url_id
        UNION
        SELECT
            'default_banner_image_url_manager' AS item,
            apa.value AS value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa
        WHERE
            apa.account_id = {$aZoneInfo['manager_account_id']}
            AND
            apa.preference_id = $default_banner_image_url_id
        UNION
        SELECT
            'default_banner_destination_url_manager' AS item,
            apa.value AS value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa
        WHERE
            apa.account_id = {$aZoneInfo['manager_account_id']}
            AND
            apa.preference_id = $default_banner_destination_url_id
        UNION
        SELECT
            'default_banner_image_url_admin' AS item,
            apa.value AS value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa,
            \"{$aConf['table']['prefix']}{$aConf['table']['accounts']}\" AS a
        WHERE
            apa.account_id = a.account_id
            AND
            a.account_type = 'ADMIN'
            AND
            apa.preference_id = $default_banner_image_url_id
        UNION
        SELECT
            'default_banner_destination_url_admin' AS item,
            apa.value AS value
        FROM
            \"{$aConf['table']['prefix']}{$aConf['table']['account_preference_assoc']}\" AS apa,
            \"{$aConf['table']['prefix']}{$aConf['table']['accounts']}\" AS a
        WHERE
            apa.account_id = a.account_id
            AND
            a.account_type = 'ADMIN'
            AND
            apa.preference_id = $default_banner_destination_url_id";
    $rDefaultBannerInfo = OA_Dal_Delivery_query($query);

    if (!is_resource($rDefaultBannerInfo)) {
        return false;
    }

    if (pg_num_rows($rDefaultBannerInfo) == 0) {
        // No default banner image or destination URLs to deal with
        return $aZoneInfo;
    }

    // Deal with the default banner image or destination URLs found
    $aDefaultImageURLs = array();
    $aDefaultDestinationURLs = array();
    while ($aRow = pg_fetch_assoc($rDefaultBannerInfo)) {
        if (stristr($aRow['item'], 'default_banner_image_url')) {
            $aDefaultImageURLs[$aRow['item']] = $aRow['value'];
        } else if (stristr($aRow['item'], 'default_banner_destination_url')) {
            $aDefaultDestinationURLs[$aRow['item']] = $aRow['value'];
        }
    }

    // The three possible preference types, in reverse order of preference (i.e.
    // use admin only if no manger, only if no trafficer
    $aTypes = array(
        0 => 'admin',
        1 => 'manager',
        2 => 'trafficker'
    );

    // Iterate over the found default values, setting the admin value(s) (if found)
    // first, then overriding with the manager value(s), then the trafficer value(s),
    // again, if found
    foreach ($aTypes as $type) {
        if (isset($aDefaultImageURLs['default_banner_image_url_' . $type])) {
            $aZoneInfo['default_banner_image_url']  = $aDefaultImageURLs['default_banner_image_url_' . $type];
        }
        if (isset($aDefaultDestinationURLs['default_banner_destination_url_' . $type])) {
            $aZoneInfo['default_banner_destination_url']  = $aDefaultDestinationURLs['default_banner_destination_url_' . $type];
        }
    }

    // Done, at last!
    return $aZoneInfo;
}

/**
 * This function gets a list of zones for a publisher (indexed on zone_id)
 *
 * @param int $publisherid   The ID of the publisher
 * @return array|false  An array containing the zones for that publisher
 *                      or false on failure
 */
function OA_Dal_Delivery_getPublisherZones($publisherid) {
    $conf = $GLOBALS['_MAX']['CONF'];

    $publisherid = (int)$publisherid;

    $rZones = OA_Dal_Delivery_query("
    SELECT
        z.zoneid AS zone_id,
        z.affiliateid AS publisher_id,
        z.zonename AS name,
        z.delivery AS type
    FROM
        {$conf['table']['prefix']}{$conf['table']['zones']} AS z
    WHERE
        z.affiliateid={$publisherid}
    ");

    if (!is_resource($rZones)) {
        return false;
    }
    while ($aZone = pg_fetch_assoc($rZones)) {
        $aZones[$aZone['zone_id']] = $aZone;
    }

    return ($aZones);
}

/**
 * The function to get and return the ads linked to a zone
 *
 * @param  int   $zoneid The id of the zone to get linked ads for
 * @return array|false
 *               The array containg zone information with nested arrays of linked ads
 *               or false on failure. Note that:
 *                  - Exclusive ads are in "xAds"
 *                  - Normal (paid) ads are in "ads"
 *                  - Low-priority ads are in "lAds"
 *                  - Companion ads, in addition to being in one of the above, are
 *                    also in "cAds" and "clAds"
 *                  - Exclusive and low-priority ads have had their priorities
 *                    calculated on the basis of the placement and advertisement
 *                    weight
 */
function OA_Dal_Delivery_getZoneLinkedAds($zoneid) {

    $conf = $GLOBALS['_MAX']['CONF'];
    $aRows = OA_Dal_Delivery_getZoneInfo($zoneid);

    $aRows['xAds']  = array();
    $aRows['cAds']  = array();
    $aRows['clAds'] = array();
    $aRows['ads']   = array();
    $aRows['lAds']  = array();
    $aRows['count_active'] = 0;
    $aRows['zone_companion'] = false;
    $aRows['count_active'] = 0;

    $totals = array(
        'xAds'  => 0,
        'cAds'  => 0,
        'clAds' => 0,
        'ads'   => 0,
        'lAds'  => 0
    );

    $query = "
        SELECT
            d.bannerid AS ad_id,
            d.campaignid AS placement_id,
            d.status AS status,
            d.description AS name,
            d.storagetype AS type,
            d.contenttype AS contenttype,
            d.pluginversion AS pluginversion,
            d.filename AS filename,
            d.imageurl AS imageurl,
            d.htmltemplate AS htmltemplate,
            d.htmlcache AS htmlcache,
            d.width AS width,
            d.height AS height,
            d.weight AS weight,
            d.seq AS seq,
            d.target AS target,
            d.url AS url,
            d.alt AS alt,
            d.statustext AS statustext,
            d.bannertext AS bannertext,
            d.autohtml AS autohtml,
            d.adserver AS adserver,
            d.block AS block_ad,
            d.capping AS cap_ad,
            d.session_capping AS session_cap_ad,
            d.compiledlimitation AS compiledlimitation,
            d.acl_plugins AS acl_plugins,
            d.append AS append,
            d.appendtype AS appendtype,
            d.bannertype AS bannertype,
            d.alt_filename AS alt_filename,
            d.alt_imageurl AS alt_imageurl,
            d.alt_contenttype AS alt_contenttype,
            d.parameters AS parameters,
            d.transparent AS transparent,
            d.ext_bannertype AS ext_bannertype,
            az.priority AS priority,
            az.priority_factor AS priority_factor,
            az.to_be_delivered AS to_be_delivered,
            c.campaignid AS campaign_id,
            c.priority AS campaign_priority,
            c.weight AS campaign_weight,
            c.companion AS campaign_companion,
            c.block AS block_campaign,
            c.capping AS cap_campaign,
            c.session_capping AS session_cap_campaign,
            c.clientid AS client_id,
            m.advertiser_limitation AS advertiser_limitation,
            a.account_id AS account_id
        FROM
            \"{$conf['table']['prefix']}{$conf['table']['banners']}\" AS d JOIN
            \"{$conf['table']['prefix']}{$conf['table']['ad_zone_assoc']}\" AS az ON (d.bannerid = az.ad_id) JOIN
            \"{$conf['table']['prefix']}{$conf['table']['campaigns']}\" AS c ON (c.campaignid = d.campaignid) LEFT JOIN
            \"{$conf['table']['prefix']}{$conf['table']['clients']}\" AS m ON (m.clientid = c.clientid) LEFT JOIN
            \"{$conf['table']['prefix']}{$conf['table']['agency']}\" AS a ON (a.agencyid = m.agencyid)
        WHERE
            az.zone_id = {$zoneid}
          AND
            d.status <= 0
          AND
            c.status <= 0
    ";

    $rAds = OA_Dal_Delivery_query($query);

    if (!is_resource($rAds)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    }

    // Get timezone data
    $aTimezones = MAX_cacheGetAccountTZs();

    while ($aAd = pg_fetch_assoc($rAds)) {
        // Add timezone
        if (isset($aAd['account_id']) && isset($aTimezones['aAccounts'][$aAd['account_id']])) {
            $aAd['timezone'] = $aTimezones['aAccounts'][$aAd['account_id']];
        } else {
            $aAd['timezone'] = $aTimezones['default'];
        }
        // Is the ad Exclusive, Low, or Normal Priority?
        if ($aAd['campaign_priority'] == -1) {
            // Ad is in an exclusive placement
            $aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
            $aRows['xAds'][$aAd['ad_id']] = $aAd;
            $aRows['count_active']++;
            $totals['xAds'] += $aAd['priority'];
        } elseif ($aAd['campaign_priority'] == 0) {
            // Ad is in a low priority placement
            $aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
            $aRows['lAds'][$aAd['ad_id']] = $aAd;
            $aRows['count_active']++;
            $totals['lAds'] += $aAd['priority'];
        } else {
            // Ad is in a paid placement
            $aRows['ads'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
            $aRows['count_active']++;
        }
        // Also store Companion ads in additional array
        if ($aAd['campaign_companion'] == 1) {
            if ($aAd['campaign_priority'] == 0) {
                // Store a low priority companion ad
                $aRows['zone_companion'][] = $aAd['placement_id'];
                $aRows['clAds'][$aAd['ad_id']] = $aAd;
                $totals['clAds'] += $aAd['priority'];
            } else {
                // Store a paid priority companion ad
                $aRows['zone_companion'][] = $aAd['placement_id'];
                $aRows['cAds'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
            }

        }
    }
    // If there are paid ads, prepare array of priority totals
    // to allow delivery to do the scaling work later
    if (is_array($aRows['ads'])) {
        $totals['ads'] = _pgsqlGetTotalPrioritiesByCP($aRows['ads']);
    }
    // If there are low priority ads, sort by priority
    if (is_array($aRows['lAds'])) {
        uasort($aRows['lAds'], '_pgsqlSortArrayPriority');
    }
    // If there are paid companion ads, prepare array of priority totals
    // to allow delivery to do the scaling work later
    if (is_array($aRows['cAds'])) {
        $totals['cAds'] = _pgsqlGetTotalPrioritiesByCP($aRows['cAds']);
    }
    // If there are low priority companion ads, sort by priority
    if (is_array($aRows['clAds'])) {
        uasort($aRows['clAds'], '_pgsqlSortArrayPriority');
    }
    $aRows['priority'] = $totals;
    return $aRows;
}

/**
 * The function to get and return the ads for direct selection
 *
 * @param string  $search       The search string for this banner selection
 *                              Usually 'bannerid:123' or 'campaignid:123'
 * @param string  $campaignid   The campaign ID to fecth banners from, added in 2.3.32 to allow BC with 2.0
 * @param boolean $lastpart     Are there any other search strings left
 *
 * @return array|false          The array of ads matching the search criteria
 *                              or false on failure
 */
function OA_Dal_Delivery_getLinkedAds($search, $campaignid = '', $lastpart = true) {

    if ($campaignid > 0) {
        $precondition = " AND d.campaignid = '".$campaignid."' ";
    } else {
        $precondition = '';
    }

    $aRows['xAds']  = array();
    $aRows['cAds']  = array();
    $aRows['clAds'] = array();
    $aRows['ads']   = array();
    $aRows['lAds']  = array();
    $aRows['count_active'] = 0;
    $aRows['zone_companion'] = false;
    $aRows['count_active'] = 0;

    $totals = array(
        'xAds'  => 0,
        'cAds'  => 0,
        'clAds' => 0,
        'ads'   => 0,
        'lAds'  => 0
    );

    $query = OA_Dal_Delivery_buildQuery($search, $lastpart, $precondition);

    $rAds = OA_Dal_Delivery_query($query);

    if (!is_resource($rAds)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    }

    // Get timezone data
    $aTimezones = MAX_cacheGetAccountTZs();

    while ($aAd = pg_fetch_assoc($rAds)) {
        // Add timezone
        if (isset($aAd['account_id']) && isset($aTimezones['aAccounts'][$aAd['account_id']])) {
            $aAd['timezone'] = $aTimezones['aAccounts'][$aAd['account_id']];
        } else {
            $aAd['timezone'] = $aTimezones['default'];
        }
        // Is the ad Exclusive, Low, or Normal Priority?
        if ($aAd['campaign_priority'] == -1) {
            // Ad is in an exclusive placement
            $aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
            $aRows['xAds'][$aAd['ad_id']] = $aAd;
            $aRows['count_active']++;
            $totals['xAds'] += $aAd['priority'];
        } elseif ($aAd['campaign_priority'] == 0) {
            // Ad is in a low priority placement
            $aAd['priority'] = $aAd['campaign_weight'] * $aAd['weight'];
            $aRows['lAds'][$aAd['ad_id']] = $aAd;
            $aRows['count_active']++;
            $totals['lAds'] += $aAd['priority'];
        } else {
            // Ad is in a paid placement
            $aRows['ads'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
            $aRows['count_active']++;
        }
        // Also store Companion ads in additional array
        if ($aAd['campaign_companion'] == 1) {
            if ($aAd['campaign_priority'] == 0) {
                // Store a low priority companion ad
                $aRows['zone_companion'][] = $aAd['placement_id'];
                $aRows['clAds'][$aAd['ad_id']] = $aAd;
                $totals['clAds'] += $aAd['priority'];
            } else {
                // Store a paid priority companion ad
                $aRows['zone_companion'][] = $aAd['placement_id'];
                $aRows['cAds'][$aAd['campaign_priority']][$aAd['ad_id']] = $aAd;
            }

        }
    }
    // If there are paid ads, prepare array of priority totals
    // to allow delivery to do the scaling work later
    if (isset($aRows['ads']) && is_array($aRows['ads'])) {
        $totals['ads'] = _pgsqlGetTotalPrioritiesByCP($aRows['ads']);
    }
    // If there are low priority ads, sort by priority
    if (isset($aRows['lAds']) && is_array($aRows['lAds'])) {
        uasort($aRows['lAds'], '_pgsqlSortArrayPriority');
    }
    // If there are paid companion ads, prepare array of priority totals
    // to allow delivery to do the scaling work later
    if (isset($aRows['cAds']) && is_array($aRows['cAds'])) {
        $totals['cAds'] = _pgsqlGetTotalPrioritiesByCP($aRows['cAds']);
    }
    // If there are low priority companion ads, sort by priority
    if (isset($aRows['clAds']) && is_array($aRows['clAds'])) {
        uasort($aRows['clAds'], '_pgsqlSortArrayPriority');
    }
    $aRows['priority'] = $totals;
    return $aRows;
}

/**
 * The function to get and return a single ad
 *
 * @param  string       $ad_id     The ad id for the specified ad
 *
 * @return array|null   $ad        An array containing the ad data or null if nothing found
 */
function OA_Dal_Delivery_getAd($ad_id) {
    $conf = $GLOBALS['_MAX']['CONF'];

    $query = "
        SELECT
        d.bannerid AS ad_id,
        d.campaignid AS placement_id,
        d.status AS status,
        d.description AS name,
        d.storagetype AS type,
        d.contenttype AS contenttype,
        d.pluginversion AS pluginversion,
        d.filename AS filename,
        d.imageurl AS imageurl,
        d.htmltemplate AS htmltemplate,
        d.htmlcache AS htmlcache,
        d.width AS width,
        d.height AS height,
        d.weight AS weight,
        d.seq AS seq,
        d.target AS target,
        d.url AS url,
        d.alt AS alt,
        d.statustext AS statustext,
        d.bannertext AS bannertext,
        d.autohtml AS autohtml,
        d.adserver AS adserver,
        d.block AS block_ad,
        d.capping AS cap_ad,
        d.session_capping AS session_cap_ad,
        d.compiledlimitation AS compiledlimitation,
        d.append AS append,
        d.appendtype AS appendtype,
        d.bannertype AS bannertype,
        d.alt_filename AS alt_filename,
        d.alt_imageurl AS alt_imageurl,
        d.alt_contenttype AS alt_contenttype,
        d.parameters AS parameters,
        d.transparent AS transparent,
        d.ext_bannertype AS ext_bannertype,
        c.campaignid AS campaign_id,
        c.block AS block_campaign,
        c.capping AS cap_campaign,
        c.session_capping AS session_cap_campaign,
        m.clientid AS client_id,
        m.advertiser_limitation AS advertiser_limitation
    FROM
        \"{$conf['table']['prefix']}{$conf['table']['banners']}\" AS d,
        \"{$conf['table']['prefix']}{$conf['table']['campaigns']}\" AS c,
        \"{$conf['table']['prefix']}{$conf['table']['clients']}\" AS m
    WHERE
        d.bannerid={$ad_id}
        AND
        d.campaignid = c.campaignid
        AND
        m.clientid = c.clientid
    ";
    $rAd = OA_Dal_Delivery_query($query);
    if (!is_resource($rAd)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    } else {
        return (pg_fetch_assoc($rAd));
    }
}

/**
 * The function to get delivery limitations for a channel
 *
 * @param  int       $channelid    The channelid for the specified channel
 *
 * @return array     $limitations  An array with the acls_plugins, and compiledlimitation
 */
function OA_Dal_Delivery_getChannelLimitations($channelid) {
    $conf = $GLOBALS['_MAX']['CONF'];

    $rLimitation = OA_Dal_Delivery_query("
    SELECT
            acl_plugins,compiledlimitation
    FROM
            \"{$conf['table']['prefix']}{$conf['table']['channel']}\"
    WHERE
            channelid={$channelid}");
    if (!is_resource($rLimitation)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    }
    $limitations = pg_fetch_assoc($rLimitation);
    return $limitations;
}

/**
 * This function gets a creative stored as a BLOB from the database
 *
 * @param string $filename  The filename of the creative as stored in the database
 * @return array            An array with the last-modified timestamp, and the binary contents
 */
function OA_Dal_Delivery_getCreative($filename)
{
    $conf = $GLOBALS['_MAX']['CONF'];
    $rCreative = OA_Dal_Delivery_query("
        SELECT
            contents,
            t_stamp
        FROM
            \"{$conf['table']['prefix']}{$conf['table']['images']}\"
        WHERE
            filename = '{$filename}'
    ");
    if (!is_resource($rCreative)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    } else {
        $aResult = pg_fetch_assoc($rCreative);
        $aResult['contents'] = pg_unescape_bytea($aResult['contents']);
        $aResult['t_stamp'] = strtotime($aResult['t_stamp'] . ' GMT');
        return $aResult;
    }
}

/**
 * This function gets a tracker and it's properties from the database
 *
 * @param int $trackerid    The ID of the tracker to get
 * @return array            The array of tracker properties
 */
function OA_Dal_Delivery_getTracker($trackerid)
{
    $conf = $GLOBALS['_MAX']['CONF'];
    $rTracker = OA_Dal_Delivery_query("
        SELECT
            t.clientid AS advertiser_id,
            t.trackerid AS tracker_id,
            t.trackername AS name,
            t.variablemethod AS variablemethod,
            t.description AS description,
            t.viewwindow AS viewwindow,
            t.clickwindow AS clickwindow,
            t.blockwindow AS blockwindow,
            t.appendcode AS appendcode
        FROM
            \"{$conf['table']['prefix']}{$conf['table']['trackers']}\" AS t
        WHERE
            t.trackerid={$trackerid}
    ");
    if (!is_resource($rTracker)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    } else {
        return (pg_fetch_assoc($rTracker));
    }
}

/**
 * This function gets all variables linked to a tracker
 *
 * @param int $trackerid    The ID of the tracker
 * @return array            An array indexed by variable_id of the variables linked to this tracker
 */
function OA_Dal_Delivery_getTrackerVariables($trackerid)
{
    $conf = $GLOBALS['_MAX']['CONF'];
    $rVariables = OA_Dal_Delivery_query("
        SELECT
            v.variableid AS variable_id,
            v.trackerid AS tracker_id,
            v.name AS name,
            v.datatype AS type,
            v.variablecode AS variablecode
        FROM
            \"{$conf['table']['prefix']}{$conf['table']['variables']}\" AS v
        WHERE
            v.trackerid={$trackerid}
    ");
    if (!is_resource($rVariables)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    } else {
        $output = array();
        while ($aRow = pg_fetch_assoc($rVariables)) {
            $output[$aRow['variable_id']] = $aRow;
        }
        return $output;
    }
}

/**
 * This function retrieves the last run timestamp from auto maintenance
 *
 * @return string            The timestamp for the last time auto maintenance ran
 */
function OA_Dal_Delivery_getMaintenanceInfo()
{
    $conf = $GLOBALS['_MAX']['CONF'];
    $result = OA_Dal_Delivery_query("
        SELECT
            value AS maintenance_timestamp
        FROM
            \"{$conf['table']['prefix']}{$conf['table']['application_variable']}\"
        WHERE name = 'maintenance_timestamp'
    ");
    if (!is_resource($result)) {
        if (defined('OA_DELIVERY_CACHE_FUNCTION_ERROR')) {
            return OA_DELIVERY_CACHE_FUNCTION_ERROR;
        } else {
            return null;
        }
    } else {
        $result = pg_fetch_assoc($result);

        return $result['maintenance_timestamp'];
    }
}

/**
 * A function to insert ad requests, ad impressions, ad clicks
 * and tracker clicks into the raw tables. Does NOT work with
 * tracker impressions.
 *
 * @param string  $table         The raw table name to insert into.
 * @param string  $viewerId      The viewer ID.
 * @param integer $adId          The advertisement ID.
 * @param integer $creativeId    The creative ID (currently unused).
 * @param integer $zoneId        The zone ID.
 * @param array   $aGeotargeting An array holding the viewer's geotargeting info.
 * @param array   $zoneInfo      An array to store information about the URL
 *                               the viewer used to access the page containing the zone.
 * @param array   $userAgentInfo An array to store information about the
 *                               viewer's web browser and operating system.
 * @param integer $maxHttps      An integer to store if the call to OpenX was
 *                               performed using HTTPS or not.
 */
function OA_Dal_Delivery_logAction($table, $viewerId, $adId, $creativeId, $zoneId,
                                   $aGeotargeting, $zoneInfo, $userAgentInfo, $maxHttps)
{
    // Whenever we assign a *new* viewer ID (or no viewerId was found),
    // we should log the cookieless ID
    if ((empty($viewerId) || !empty($GLOBALS['_MAX']['COOKIE']['newViewerId']))) {
        $log_viewerId = MAX_cookieGetCookielessViewerID();
    } else {
        $log_viewerId = substr($viewerId, 0, 32);
    }
    // Ensure that all geotargeting data is correctly escaped
    $aGeotargeting = array_map('pg_escape_string', $aGeotargeting);
    // Log the raw data
    $query = "
        INSERT INTO
            \"{$table}\"
            (
                viewer_id,
                viewer_session_id,
                date_time,
                ad_id,
                creative_id,
                zone_id,";
    if (isset($_GET['source'])) {
        $query .= "
                channel,";
    }
    if (isset($zoneInfo['channel_ids'])) {
        $query .= "
                channel_ids,";
    }
    $query .= "
                language,
                ip_address,
                host_name,";
    if (isset($aGeotargeting['country_code'])) {
        $query .= "
                country,";
    }
    if (isset($zoneInfo['scheme'])) {
        $query .= "
                https,";
    }
    if (isset($zoneInfo['host'])) {
        $query .= "
                domain,";
    }
    if (isset($zoneInfo['path'])) {
        $query .= "
                page,";
    }
    if (isset($zoneInfo['query'])) {
        $query .= "
                query,";
    }
    if ($GLOBALS['_MAX']['CONF']['logging']['referer'] && isset($_GET['referer'])) {
        $query .= "
                referer,";
    }
    $query .= "
                search_term,";
    if ($GLOBALS['_MAX']['CONF']['logging']['useragent']) {
        $query .= "
                user_agent,";
    }
    if (isset($userAgentInfo['os'])) {
        $query .= "
                os,";
    }
    if (isset($userAgentInfo['browser'])) {
        $query .= "
                browser,";
    }
    $query .= "
                max_https,";
    if (isset($aGeotargeting['region'])) {
        $query .= "
                geo_region,";
    }
    if (isset($aGeotargeting['city'])) {
        $query .= "
                geo_city,";
    }
    if (isset($aGeotargeting['postal_code'])) {
        $query .= "
                geo_postal_code,";
    }
    if (isset($aGeotargeting['latitude'])) {
        $query .= "
                geo_latitude,";
    }
    if (isset($aGeotargeting['longitude'])) {
        $query .= "
                geo_longitude,";
    }
    if (isset($aGeotargeting['dma_code'])) {
        $query .= "
                geo_dma_code,";
    }
    if (isset($aGeotargeting['area_code'])) {
        $query .= "
                geo_area_code,";
    }
    if (isset($aGeotargeting['organisation'])) {
        $query .= "
                geo_organisation,";
    }
    if (isset($aGeotargeting['netspeed'])) {
        $query .= "
                geo_netspeed,";
    }
    if (isset($aGeotargeting['continent'])) {
        $query .= "
                geo_continent,";
    }
    // Strip end comma!
    $query = substr_replace($query, '', strlen($query) - 1);
    $query .= "
            )
        VALUES
            (
                '$log_viewerId',
                '',
                '".gmdate('Y-m-d H:i:s')."',
                '".(int)$adId."',
                '".(int)$creativeId."',
                '".(int)$zoneId."',";
    if (isset($_GET['source'])) {
        $query .= "
                '".pg_escape_string(MAX_commonDecrypt($_GET['source']))."',";
    }
    if (isset($zoneInfo['channel_ids'])) {
        $query .= "
                '{$zoneInfo['channel_ids']}',";
    }
    $httpLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
    $query .= "
                '".pg_escape_string(substr($httpLanguage, 0, 32))."',
                '".pg_escape_string($_SERVER['REMOTE_ADDR'])."',
                '".pg_escape_string($_SERVER['REMOTE_HOST'])."',";
    if (isset($aGeotargeting['country_code'])) {
        $query .= "
                '{$aGeotargeting['country_code']}',";
    }
    if (isset($zoneInfo['scheme'])) {
        $query .= "
                '{$zoneInfo['scheme']}',";
    }
    if (isset($zoneInfo['host'])) {
        $query .= "
                '{$zoneInfo['host']}',";
    }
    if (isset($zoneInfo['path'])) {
        $query .= "
                '{$zoneInfo['path']}',";
    }
    if (isset($zoneInfo['query'])) {
        $query .= "
                '{$zoneInfo['query']}',";
    }
    if ($GLOBALS['_MAX']['CONF']['logging']['referer'] && isset($_GET['referer'])) {
        $query .= "
                '".pg_escape_string($_GET['referer'])."',";
    }
    $query .= "
                '',";
    if ($GLOBALS['_MAX']['CONF']['logging']['useragent']) {
        $query .= "
                '".pg_escape_string(substr($_SERVER['HTTP_USER_AGENT'], 0, 255))."',";
    }
    if (isset($userAgentInfo['os'])) {
        $query .= "
                '{$userAgentInfo['os']}',";
    }
    if (isset($userAgentInfo['browser'])) {
        $query .= "
                '{$userAgentInfo['browser']}',";
    }
    $query .= intval($maxHttps).",";
    if (isset($aGeotargeting['region'])) {
        $query .= "
                '{$aGeotargeting['region']}',";
    }
    if (isset($aGeotargeting['city'])) {
        $query .= "
                '{$aGeotargeting['city']}',";
    }
    if (isset($aGeotargeting['postal_code'])) {
        $query .= "
                '{$aGeotargeting['postal_code']}',";
    }
    if (isset($aGeotargeting['latitude'])) {
        $query .= "
                " . floatval($aGeotargeting['latitude']) . ",";
    }
    if (isset($aGeotargeting['longitude'])) {
        $query .= "
                " . floatval($aGeotargeting['longitude']) . ",";
    }
    if (isset($aGeotargeting['dma_code'])) {
        $query .= "
                '{$aGeotargeting['dma_code']}',";
    }
    if (isset($aGeotargeting['area_code'])) {
        $query .= "
                '{$aGeotargeting['area_code']}',";
    }
    if (isset($aGeotargeting['organisation'])) {
        $query .= "
                '{$aGeotargeting['organisation']}',";
    }
    if (isset($aGeotargeting['netspeed'])) {
        $query .= "
                '{$aGeotargeting['netspeed']}',";
    }
    if (isset($aGeotargeting['continent'])) {
        $query .= "
                '{$aGeotargeting['continent']}',";
    }
    // Strip end comma!
    $query = substr_replace($query, '', strlen($query) - 1);
    $query .= "
            )";
    $result = OA_Dal_Delivery_query(
        $query,
        'rawDatabase'
    );
    return $result;
}

/**
 * A function to insert tracker impressions into the raw table.
 *
 * @param string  $table         The raw table name to insert into.
 * @param string  $viewerId      The viewer ID.
 * @param integer $adId          The advertisement ID.
 * @param integer $creativeId    The creative ID (currently unused).
 * @param integer $zoneId        The zone ID.
 * @param array   $aGeotargeting An array holding the viewer's geotargeting info.
 * @param array   $zoneInfo      An array to store information about the URL
 *                               the viewer used to access the page containing the zone.
 * @param array   $userAgentInfo An array to store information about the
 *                               viewer's web browser and operating system.
 * @param integer $maxHttps      An integer to store if the call to OpenX was
 *                               performed using HTTPS or not.

 * @return int|false             Returns the insert ID for this record or false on failure
 */
function OA_Dal_Delivery_logTracker($table, $viewerId, $trackerId, $serverRawIp,
                                    $aGeotargeting, $zoneInfo, $userAgentInfo, $maxHttps)
{
    // Whenever we assign a *new* viewer ID (or no viewerId was found),
    // we should log the cookieless ID
    if ((empty($viewerId) || !empty($GLOBALS['_MAX']['COOKIE']['newViewerId']))) {
        $log_viewerId = MAX_cookieGetCookielessViewerID();
    } else {
        $log_viewerId = substr($viewerId, 0, 32);
    }
    $source = isset($_GET['source']) ? $_GET['source'] : '';
    $referer = isset($_GET['referer']) ? $_GET['referer'] : '';
    $httpUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $httpLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
    // Ensure that all geotargeting data is correctly escaped
    $aGeotargeting = array_map('pg_escape_string', $aGeotargeting);
    // Log the raw data
    $res = OA_Dal_Delivery_query("
        INSERT INTO
            \"{$table}\"
        (
            server_raw_ip,
            viewer_id,
            viewer_session_id,
            date_time,
            tracker_id,
            channel,
            channel_ids,
            language,
            ip_address,
            host_name,
            country,
            https,
            domain,
            page,
            query,
            referer,
            search_term,
            user_agent,
            os,
            browser,
            max_https,
            geo_region,
            geo_city,
            geo_postal_code,
            geo_latitude,
            geo_longitude,
            geo_dma_code,
            geo_area_code,
            geo_organisation,
            geo_netspeed,
            geo_continent
        )
    VALUES
        (
            '$serverRawIp',
            '$log_viewerId',
            '',
            '".gmdate('Y-m-d H:i:s')."',
            '".(int)$trackerId."',
            '".pg_escape_string(MAX_commonDecrypt($source))."',
            '{$zoneInfo['channel_ids']}',
            '".pg_escape_string(substr($httpLanguage, 0, 32))."',
            '".pg_escape_string($_SERVER['REMOTE_ADDR'])."',
            '".pg_escape_string($_SERVER['REMOTE_HOST'])."',
            '{$aGeotargeting['country_code']}',
            '".intval($zoneInfo['scheme'])."',
            '{$zoneInfo['host']}',
            '{$zoneInfo['path']}',
            '{$zoneInfo['query']}',
            '{$referer}',
            '',
            '".pg_escape_string(substr($httpUserAgent, 0, 255))."',
            '{$userAgentInfo['os']}',
            '{$userAgentInfo['browser']}',
            '".intval($maxHttps)."',
            '{$aGeotargeting['region']}',
            '{$aGeotargeting['city']}',
            '{$aGeotargeting['postal_code']}',
            '".floatval($aGeotargeting['latitude'])."',
            '".floatval($aGeotargeting['longitude'])."',
            '{$aGeotargeting['dma_code']}',
            '{$aGeotargeting['area_code']}',
            '{$aGeotargeting['organisation']}',
            '{$aGeotargeting['netspeed']}',
            '{$aGeotargeting['continent']}'
    )", 'rawDatabase');

    $aConf = $GLOBALS['_MAX']['CONF'];
    $pkey = 'server'.substr($table, strlen($aConf['table']['prefix']) + 4);

    return $res ? OA_Dal_Delivery_insertId('rawDatabase', $table, $pkey) : false;
}

/**
 * This function logs the variable data passed in to a tracker impression
 *
 * @param array  $variables                     An array of the variable name=value data to be logged
 * @param int    $serverRawTrackerImpressionId  The associated tracker-impression ID for these values
 * @param string $serverRawIp                   The IP address of the raw database that logged the
 *                                              initial tracker-impression
 * @return bool True on success
 */
function OA_Dal_Delivery_logVariableValues($variables, $serverRawTrackerImpressionId, $serverRawIp)
{
    $conf = $GLOBALS['_MAX']['CONF'];
    $aRows = array();
    foreach ($variables as $variable) {
        $aRows[] = "(
                        '{$variable['variable_id']}',
                        '{$serverRawTrackerImpressionId}',
                        '{$serverRawIp}',
                        '".gmdate('Y-m-d H:i:s')."',
                        '".pg_escape_string($variable['value'])."'
                    )";
    }
    if (empty($aRows)) {
        return;
    }
    OA_Dal_Delivery_query("BEGIN", 'rawDatabase');
    foreach ($aRows as $sValues) {
        $query = "
            INSERT INTO
                \"{$conf['table']['prefix']}{$conf['table']['data_raw_tracker_variable_value']}\"
                (
                    tracker_variable_id,
                    server_raw_tracker_impression_id,
                    server_raw_ip,
                    date_time,
                    value
                )
            VALUES " . $sValues;

        $res = OA_Dal_Delivery_query($query, 'rawDatabase');

        if (!$res) {
            OA_Dal_Delivery_query("ROLLBACK", 'rawDatabase');
            return false;
        }
    }
    OA_Dal_Delivery_query("COMMIT", 'rawDatabase');

    return true;
}

/**
 * A function to generate a direct selection query preserving 2.0 backwards compatibility
 *
 * @param string  $part         The what parameter part to build the query
 * @param boolean $lastpart     True if there are no other parts to work on
 * @param string  $precondition Any SQL preconditions to apply
 * @return string The generated query
 */
function OA_Dal_Delivery_buildQuery($part, $lastpart, $precondition)
{
    $conf = $GLOBALS['_MAX']['CONF'];

    $aColumns = array(
            'd.bannerid AS ad_id',
            'd.campaignid AS placement_id',
            'd.status AS status',
            'd.description AS name',
            'd.storagetype AS type',
            'd.contenttype AS contenttype',
            'd.pluginversion AS pluginversion',
            'd.filename AS filename',
            'd.imageurl AS imageurl',
            'd.htmltemplate AS htmltemplate',
            'd.htmlcache AS htmlcache',
            'd.width AS width',
            'd.height AS height',
            'd.weight AS weight',
            'd.seq AS seq',
            'd.target AS target',
            'd.url AS url',
            'd.alt AS alt',
            'd.statustext AS statustext',
            'd.bannertext AS bannertext',
            'd.autohtml AS autohtml',
            'd.adserver AS adserver',
            'd.block AS block_ad',
            'd.capping AS cap_ad',
            'd.session_capping AS session_cap_ad',
            'd.compiledlimitation AS compiledlimitation',
            'd.acl_plugins AS acl_plugins',
            'd.append AS append',
            'd.appendtype AS appendtype',
            'd.bannertype AS bannertype',
            'd.alt_filename AS alt_filename',
            'd.alt_imageurl AS alt_imageurl',
            'd.alt_contenttype AS alt_contenttype',
            'd.parameters AS parameters',
            'd.transparent AS transparent',
            'd.ext_bannertype AS ext_bannertype',
            'az.priority AS priority',
            'az.priority_factor AS priority_factor',
            'az.to_be_delivered AS to_be_delivered',
            'm.campaignid AS campaign_id',
            'm.priority AS campaign_priority',
            'm.weight AS campaign_weight',
            'm.companion AS campaign_companion',
            'm.block AS block_campaign',
            'm.capping AS cap_campaign',
            'm.session_capping AS session_cap_campaign',
            'cl.clientid AS client_id',
            'cl.advertiser_limitation AS advertiser_limitation',
            'a.account_id AS account_id'
    );

    $aTables = array(
        "\"{$conf['table']['prefix']}{$conf['table']['banners']}\" AS d",
        "JOIN \"{$conf['table']['prefix']}{$conf['table']['campaigns']}\" AS m ON (d.campaignid = m.campaignid) ",
        "JOIN \"{$conf['table']['prefix']}{$conf['table']['clients']}\" AS cl ON (m.clientid = cl.clientid) ",
        "JOIN \"{$conf['table']['prefix']}{$conf['table']['ad_zone_assoc']}\" AS az ON (d.bannerid = az.ad_id)"
    );
    $select = "
      az.zone_id = 0
      AND m.status <= 0
      AND d.status <= 0";

    // Add preconditions to query
    if ($precondition != '')
        $select .= " $precondition ";


    // Other
    if ($part != '')
    {
        $conditions = '';
        $onlykeywords = true;

        $part_array = explode(',', $part);
        for ($k=0; $k < count($part_array); $k++)
        {
            // Process switches
            if (substr($part_array[$k], 0, 1) == '+' || substr($part_array[$k], 0, 1) == '_')
            {
                $operator = 'AND';
                $part_array[$k] = substr($part_array[$k], 1);
            }
            elseif (substr($part_array[$k], 0, 1) == '-')
            {
                $operator = 'NOT';
                $part_array[$k] = substr($part_array[$k], 1);
            }
            else
                $operator = 'OR';


            //  Test statements
            if($part_array[$k] != '' && $part_array[$k] != ' ')
            {
                // Banner dimensions, updated to support 2.3-only size keyword
                if(preg_match('#^(?:size:)?([0-9]+x[0-9]+)$#', $part_array[$k], $m))
                {
                    list($width, $height) = explode('x', $m[1]);

                    if ($operator == 'OR')
                        $conditions .= "OR (d.width = $width AND d.height = $height) ";
                    elseif ($operator == 'AND')
                        $conditions .= "AND (d.width = $width AND d.height = $height) ";
                    else
                        $conditions .= "AND (d.width != $width OR d.height != $height) ";

                    $onlykeywords = false;
                }

                // Banner Width
                elseif (substr($part_array[$k],0,6) == 'width:')
                {
                    $part_array[$k] = substr($part_array[$k], 6);

                    if ($part_array[$k] != '' && $part_array[$k] != ' ')
                    {
                        if (is_int(strpos($part_array[$k], '-')))
                        {
                            // Width range
                            list($min, $max) = explode('-', $part_array[$k]);

                            // Only upper limit, set lower limit to make sure not text ads are delivered
                            if ($min == '')
                                $min = 1;

                            // Only lower limit
                            if ($max == '')
                            {
                                if ($operator == 'OR')
                                    $conditions .= "OR d.width >= '".trim($min)."' ";
                                elseif ($operator == 'AND')
                                    $conditions .= "AND d.width >= '".trim($min)."' ";
                                else
                                    $conditions .= "AND d.width < '".trim($min)."' ";
                            }

                            // Both lower and upper limit
                            if ($max != '')
                            {
                                if ($operator == 'OR')
                                    $conditions .= "OR (d.width >= '".trim($min)."' AND d.width <= '".trim($max)."') ";
                                elseif ($operator == 'AND')
                                    $conditions .= "AND (d.width >= '".trim($min)."' AND d.width <= '".trim($max)."') ";
                                else
                                    $conditions .= "AND (d.width < '".trim($min)."' OR d.width > '".trim($max)."') ";
                            }
                        }
                        else
                        {
                            // Single value

                            if ($operator == 'OR')
                                $conditions .= "OR d.width = '".trim($part_array[$k])."' ";
                            elseif ($operator == 'AND')
                                $conditions .= "AND d.width = '".trim($part_array[$k])."' ";
                            else
                                $conditions .= "AND d.width != '".trim($part_array[$k])."' ";
                        }
                    }

                    $onlykeywords = false;
                }

                // Banner Height
                elseif (substr($part_array[$k],0,7) == 'height:')
                {
                    $part_array[$k] = substr($part_array[$k], 7);
                    if ($part_array[$k] != '' && $part_array[$k] != ' ')
                    {
                        if (is_int(strpos($part_array[$k], '-')))
                        {
                            // Height range
                            list($min, $max) = explode('-', $part_array[$k]);

                            // Only upper limit, set lower limit to make sure not text ads are delivered
                            if ($min == '')
                                $min = 1;

                            // Only lower limit
                            if ($max == '')
                            {
                                if ($operator == 'OR')
                                    $conditions .= "OR d.height >= '".trim($min)."' ";
                                elseif ($operator == 'AND')
                                    $conditions .= "AND d.height >= '".trim($min)."' ";
                                else
                                    $conditions .= "AND d.height < '".trim($min)."' ";
                            }

                            // Both lower and upper limit
                            if ($max != '')
                            {
                                if ($operator == 'OR')
                                    $conditions .= "OR (d.height >= '".trim($min)."' AND d.height <= '".trim($max)."') ";
                                elseif ($operator == 'AND')
                                    $conditions .= "AND (d.height >= '".trim($min)."' AND d.height <= '".trim($max)."') ";
                                else
                                    $conditions .= "AND (d.height < '".trim($min)."' OR d.height > '".trim($max)."') ";
                            }
                        }
                        else
                        {
                            // Single value

                            if ($operator == 'OR')
                                $conditions .= "OR d.height = '".trim($part_array[$k])."' ";
                            elseif ($operator == 'AND')
                                $conditions .= "AND d.height = '".trim($part_array[$k])."' ";
                            else
                                $conditions .= "AND d.height != '".trim($part_array[$k])."' ";
                        }
                    }

                    $onlykeywords = false;
                }

                // Banner ID, updated to support 2.3-only adid or ad_id
                elseif (preg_match('#^(?:(?:bannerid|adid|ad_id):)?([0-9]+)$#', $part_array[$k], $m))
                {
                    $part_array[$k] = $m[1];

                    if ($part_array[$k])
                    {
                        if ($operator == 'OR')
                            $conditions .= "OR d.bannerid='".$part_array[$k]."' ";
                        elseif ($operator == 'AND')
                            $conditions .= "AND d.bannerid='".$part_array[$k]."' ";
                        else
                            $conditions .= "AND d.bannerid!='".$part_array[$k]."' ";
                    }

                    $onlykeywords = false;
                }

                // Campaign ID
                elseif (preg_match('#^(?:(?:clientid|campaignid|placementid|placement_id):)?([0-9]+)$#', $part_array[$k], $m))
                {
                    $part_array[$k] = $m[1];

                    if ($part_array[$k])
                    {
                        if ($operator == 'OR')
                            $conditions .= "OR d.campaignid='".trim($part_array[$k])."' ";
                        elseif ($operator == 'AND')
                            $conditions .= "AND d.campaignid='".trim($part_array[$k])."' ";
                        else
                            $conditions .= "AND d.campaignid!='".trim($part_array[$k])."' ";
                    }

                    $onlykeywords = false;
                }

                // Format
                elseif (substr($part_array[$k], 0, 7) == 'format:')
                {
                    $part_array[$k] = substr($part_array[$k], 7);
                    if($part_array[$k] != '' && $part_array[$k] != ' ')
                    {
                        if ($operator == 'OR')
                            $conditions .= "OR d.contenttype='".trim($part_array[$k])."' ";
                        elseif ($operator == 'AND')
                            $conditions .= "AND d.contenttype='".trim($part_array[$k])."' ";
                        else
                            $conditions .= "AND d.contenttype!='".trim($part_array[$k])."' ";
                    }

                    $onlykeywords = false;
                }

                // HTML
                elseif($part_array[$k] == 'html')
                {
                    if ($operator == 'OR')
                        $conditions .= "OR d.storagetype='html' ";
                    elseif ($operator == 'AND')
                        $conditions .= "AND d.storagetype='html' ";
                    else
                        $conditions .= "AND d.storagetype!='html' ";

                    $onlykeywords = false;
                }

                // TextAd
                elseif($part_array[$k] == 'textad')
                {
                    if ($operator == 'OR')
                        $conditions .= "OR d.storagetype='txt' ";
                    elseif ($operator == 'AND')
                        $conditions .= "AND d.storagetype='txt' ";
                    else
                        $conditions .= "AND d.storagetype!='txt' ";

                    $onlykeywords = false;
                }
/*
                // Categories
                elseif (substr($part_array[$k], 0, 4) == 'cat:')
                {
                    $part_array[$k] = substr($part_array[$k], 4);
                    if($part_array[$k] != '' && $part_array[$k] != ' ')
                    {
                        $aTables[] = '"'.$conf['table']['prefix'].$conf['table']['ad_category_assoc'] . '" AS ac';
                        $aTables[] = '"'.$conf['table']['prefix'].$conf['table']['category'] . '" AS cat';

                        if ($operator == 'OR')
                            $conditions .= "OR d.bannerid=ac.ad_id AND ac.category_id=cat.category_id ";
                        elseif ($operator == 'AND')
                            $conditions .= "AND d.bannerid=ac.ad_id AND ac.category_id=cat.category_id ";
                        else
                            $conditions .= "AND d.bannerid=ac.ad_id AND ac.category_id=cat.category_id ";
                    }
                }
*/
                // Keywords
                else
                {
                    if ($operator == 'OR')
                        $conditions .= "OR LOWER(' ' || d.keyword || ' ') LIKE LOWER('% $part_array[$k] %') ";
                    elseif ($operator == 'AND')
                        $conditions .= "AND LOWER(' ' || d.keyword || ' ') LIKE LOWER('% $part_array[$k] %') ";
                    else
                        $conditions .= "AND LOWER(' ' || d.keyword || ' ') LIKE LOWER('% $part_array[$k] %') ";
                }
            }
        }

        // Strip first AND or OR from $conditions
        $conditions = strstr($conditions, ' ');

        // Add global keyword
        if ($lastpart == true && $onlykeywords == true)
            $conditions .= "OR LOWER(' ' || d.keyword || ' ') LIKE '% global %' ";

        // Add conditions to select
        if ($conditions != '') $select .= ' AND ('.$conditions.') ';
    }

    $columns = implode(",\n    ", $aColumns);
    $tables = implode("\n    ", $aTables);

    $leftJoin = "
            LEFT JOIN \"{$conf['table']['prefix']}{$conf['table']['clients']}\" AS c ON (c.clientid = m.clientid)
            LEFT JOIN \"{$conf['table']['prefix']}{$conf['table']['agency']}\" AS a ON (a.agencyid = c.agencyid)
    ";

    $query = "SELECT\n    " . $columns . "\nFROM\n    " . $tables . $leftJoin . "\nWHERE " . $select;

    return $query;
}


/**
 * A private callback function for the uasort function
 *
 * @param  string   $a      First parameter
 * @param  string   $b      Second parameter
 *
 * @return boolean  Compare result
 */
function _pgsqlSortArrayPriority($a, $b)
{
    $compare = ($a['priority'] > $b['priority']) ? -1 : 1;
    return $compare;
}


/**
 * A private method to calculate total expected priority values
 * for each campaign priority. The values are used later during
 * delivery to scale priorities to 1
 *
 * @param  array    $aAdsByCP   Ads array grouped by CP
 *
 * @return array    Array of total priorities by campaign priority
 */

function _pgsqlGetTotalPrioritiesByCP($aAdsByCP)
{
    $totals = array();

    $blank_priority = 1;
    $total_priority_cp = array();

    foreach ($aAdsByCP as $campaign_priority => $aAds) {
        $total_priority_cp[$campaign_priority] = 0;
        foreach ($aAds as $key => $aAd) {
            $blank_priority -= (double)$aAd['priority'];
            if ($aAd['to_be_delivered']) {
                $priority = $aAd['priority'] * $aAd['priority_factor'];
            } else {
                $priority = 0.00001;
            }
            $total_priority_cp[$campaign_priority] += $priority;
            $aAdsByCP[$campaign_priority][$key]['priority'] = $priority;
        }
    }

    // Sort by ascending CP
    ksort($total_priority_cp);

    // Store blank priority, ensuring that small rounding errors are
    // not taken into account
    $total_priority = $blank_priority <= 1e-15 ? 0 : $blank_priority;

    // Calculate totals for each campaign priority
    foreach($total_priority_cp as $campaign_priority => $priority) {
        $total_priority += $priority;
        $totals[$campaign_priority] = $priority / $total_priority;
    }

    return $totals;
}

function OX_bucket_updateTable($tableName, $aQuery, $counter = 'count')
{
    $prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
    $query = OX_bucket_prepareUpdateQuery($prefix . $tableName, $aQuery, $counter);
    $result = OA_Dal_Delivery_query(
        $query,
        'rawDatabase'
    );
    return $result;
}

function OX_bucket_prepareUpdateQuery($tableName, $aQuery, $counter = 'count')
{
    $args = implode(',', OX_bucket_quoteArgs($aQuery));
    $query = "SELECT bucket_update_{$tableName}({$args})";
    return $query;
}

function OX_bucket_quoteArgs($aArgs)
{
    $array = $aArgs;
    foreach ($array as &$value) {
        if (!is_integer($value)) {
            $value = "'" . $value . "'";
        }
    }
    return $array;
}

function OX_escapeString($string)
{
    return pg_escape_string($string);
}

?>
