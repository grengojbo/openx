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

/**
 * @package    Plugin
 * @subpackage openxDeliveryLogCountry
 */

function Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo()
{
    // prevent from running twice
    static $executed;
    if ($executed) return;
    $executed = true;

    if (!empty($GLOBALS['_MAX']['CLIENT_GEO'])) {
        $GLOBALS['_MAX']['deliveryData']['geo'] = $GLOBALS['_MAX']['CLIENT_GEO'];
    } else {
        $GLOBALS['_MAX']['deliveryData']['geo'] = array(
            'country_code'  => null,
            'region'        => null,
            'city'          => null,
            'postal_code'   => null,
            'latitude'      => null,
            'longitude'     => null,
            'dma_code'      => null,
            'area_code'     => null,
            'organisation'  => null,
            'netspeed'      => null,
            'continent'     => null
        );
    }
}

function Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo_Delivery_logRequest()
{
    Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo();
}

function Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo_Delivery_logImpression()
{
    Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo();
}

function Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo_Delivery_logClick()
{
    Plugin_deliveryDataPrepare_oxDeliveryGeo_dataGeo();
}

?>