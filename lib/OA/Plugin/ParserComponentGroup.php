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
// $Id$
*/

require_once MAX_PATH.'/lib/OA/Plugin/ParserBase.php';

/**
 * Parses an XML plugin install file
 *
 * @package OpenX Plugins
 * @author  Monique Szpak <monique.szpak@openx.org>
 */
class OX_ParserComponentGroup extends OX_ParserBase
{

    var $aNav    = array(OA_ACCOUNT_ADMIN => array(),
                         OA_ACCOUNT_MANAGER => array(),
                         OA_ACCOUNT_ADVERTISER => array(),
                         OA_ACCOUNT_TRAFFICKER => array(),
                         );
    var $aSchema = array(
                          'mdb2schema'  => '',
                          'dboschema'   => '',
                          'dbolinks'    => '',
                          'dataobjects' => '',
                           );
    var $aData;

    function startHandler($xp, $element, $attribs)
    {

        parent::startHandler($xp, $element, $attribs);

        switch ($this->element)
        {
            case 'plugin':

                $this->aNav = array(
                                     OA_ACCOUNT_ADMIN => array(),
                                     OA_ACCOUNT_MANAGER => array(),
                                     OA_ACCOUNT_ADVERTISER => array(),
                                     OA_ACCOUNT_TRAFFICKER => array(),
                                    );
                $this->aSchema  = array(
                                      'mdb2schema'  => '',
                                      'dboschema'   => '',
                                      'dbolinks'    => '',
                                      'dataobjects' => array(),
                                       );

                break;
            case 'plugin-install-navigation-admin-menu':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-navigation-manager-menu':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-navigation-advertiser-menu':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-navigation-trafficker-menu':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-configuration':
                $this->aSettings = array();
                $this->aPrefs = array();
                break;
            case 'plugin-install-configuration-setting':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-configuration-preference':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
        }
    }

    function endHandler($xp, $element)
    {

        switch ($this->element)
        {
            case 'plugin-install-navigation-admin-menu':
                $this->aNav[OA_ACCOUNT_ADMIN][] = $this->aData;
                break;
            case 'plugin-install-navigation-manager-menu':
                $this->aNav[OA_ACCOUNT_MANAGER][] = $this->aData;
                break;
            case 'plugin-install-navigation-advertiser-menu':
                $this->aNav[OA_ACCOUNT_ADVERTISER][] = $this->aData;
                break;
            case 'plugin-install-navigation-trafficker-menu':
                $this->aNav[OA_ACCOUNT_TRAFFICKER][] = $this->aData;
                break;
            case 'plugin':
                $this->aInstall['navigation'] = $this->aNav;
                $this->aInstall['schema']     = $this->aSchema;
                break;
            case 'plugin-install-configuration-setting':
                $this->aSettings[] = $this->aData;
                break;
            case 'plugin-install-configuration-preference':
                $this->aPrefs[] = $this->aData;
                break;
        }

        parent::endHandler($xp, $element);
    }

    function cdataHandler($xp, $data)
    {
        parent::cdataHandler($xp, $data);

        switch ($this->element)
        {
            case 'plugin-install-navigation-admin-menu':
            case 'plugin-install-navigation-manager-menu':
            case 'plugin-install-navigation-advertiser-menu':
            case 'plugin-install-navigation-trafficker-menu':
                $this->aData['value'] = $data;
                break;
            case 'plugin-install-schema-mdb2schema':
                $this->aSchema['mdb2schema'] = $data;
                break;
            case 'plugin-install-schema-dboschema':
                $this->aSchema['dboschema'] = $data;;
                break;
            case 'plugin-install-schema-dbolinks':
                $this->aSchema['dbolinks'] = $data;;
                break;
            case 'plugin-install-schema-dataobject':
                $this->aSchema['dataobjects'][] = $data;;
                break;
            case 'plugin-install-configuration-setting':
                $this->aData['value'] = $data;
                break;
            case 'plugin-install-configuration-preference':
                $this->aData['value'] = $data;
                break;
        }

    }

}

?>
