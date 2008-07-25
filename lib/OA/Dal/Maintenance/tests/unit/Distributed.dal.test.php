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

require_once MAX_PATH . '/lib/OA.php';
require_once MAX_PATH . '/lib/OA/Dal/Maintenance/Distributed.php';
require_once 'Date.php';

require_once MAX_PATH . '/lib/OA/Dal/DataGenerator.php';
require_once MAX_PATH . '/lib/OX/Plugin/Component.php';

/**
 * A class for testing the non-DB specific OA_Dal_Maintenance_Common class.
 *
 * @package    OpenXDal
 * @subpackage TestSuite
 * @author     Andrew Hill <andrew.hill@openx.org>
 */
class Test_OA_Dal_Maintenance_Distributed extends UnitTestCase
{

    /**
     * The constructor method.
     */
    function Test_OA_Dal_Maintenance_Distributed()
    {
        $this->UnitTestCase();
    }
    
    function setUp()
    {
        TestEnv::uninstallPluginPackage('openXDeliveryLog', false);
        TestEnv::installPluginPackage('openXDeliveryLog', 'openXDeliveryLog', '/plugins_repo/', false);
    }
    
    function tearDown()
    {
        TestEnv::uninstallPluginPackage('openXDeliveryLog', false);
    }

    function test_setMaintenanceDistributedLastRunInfo()
    {
        $prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
        $oDal = new OA_Dal_Maintenance_Distributed();
        $oDate = & new Date();
        $oDal->setMaintenanceDistributedLastRunInfo($oDate);

        $doLbLocal = OA_Dal::factoryDO('lb_local');
        $doLbLocal->whereAdd("last_run = '".$oDate->getTime()."'",'AND');
        $this->assertTrue($doLbLocal->find(true));
    }

    function test_getMaintenanceDistributedLastRunInfo()
    {
        // Empty raw tables and maintenance never run.
        $prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
        $oTable =& OA_DB_Table_Core::singleton();
        $oTable->truncateTable($prefix . 'lb_local');

        $oDal = new OA_Dal_Maintenance_Distributed();
        $this->assertFalse($oDal->getMaintenanceDistributedLastRunInfo());
        
        // With last_run timestamp
        $oDate = & new Date();
        $oDal->setMaintenanceDistributedLastRunInfo($oDate);
        $oActual = $oDal->getMaintenanceDistributedLastRunInfo();
        $this->assertEqual($oDate, $oActual);
    }

    function test_processBuckets()
    {
    }

   function test__pruneBucket()
    {
        $oDal = new OA_Dal_Maintenance_Distributed();
        $oCurrentIntervalStart = new Date();
        $oCurrentIntervalStart->setDate('2008-05-25 11:00:00');
        
        $oDateThen = new Date();
        $oDateThen->copy($oCurrentIntervalStart);

        $prefix = $oDal->getTablePrefix();
        for ($i=1;$i<=4;$i++)
        {
            $date_time = $oDateThen->getDate(DATE_FORMAT_ISO_EXTENDED);
            $query = "
                  INSERT INTO
                    {$prefix}data_bucket_impression
                  (interval_start, creative_id, zone_id, count)
                  VALUES ('{$date_time}',{$i},{$i},{$i})";
            $oDal->oDbh->exec($query);
            $oDateThen->subtractSeconds(3600);
        }
        
        // Prune up to and including the previous interval_start
        $oPreviousIntervalStart = new Date();
        $oPreviousIntervalStart->copy($oCurrentIntervalStart);
        $oPreviousIntervalStart->subtractSeconds(3600);
        $result = $oDal->_pruneBucket('data_bucket_impression', $oPreviousIntervalStart);
        $this->assertEqual($result,3);

    }

    function test__getFirstRecordTimestamp()
    {
        // Empty table
        $oDal = new OA_Dal_Maintenance_Distributed();
        $this->assertFalse($oDal->_getFirstRecordTimestamp('data_bucket_impression'));
        
        // With some records
        $oNow = new Date();
        $oExpected = new Date();
        $oExpected->copy($oNow);
        
        $prefix = $oDal->getTablePrefix();
        for ($i = 1; $i <= 4; $i++) {
            $date_time = $oNow->getDate();
            $query = "
                      INSERT INTO
                        {$prefix}data_bucket_impression
                      (interval_start, creative_id, zone_id, count)
                      VALUES ('{$date_time}',{$i},{$i},{$i})";
            $oDal->oDbh->exec($query);
            $oNow->addSeconds(3600);
        }
        
        $oActual = $oDal->_getFirstRecordTimestamp('data_bucket_impression');
        $this->assertEqual($oExpected, $oActual);
        
    }

    function test__getBucketTableContent()
    {
        $oDal = new OA_Dal_Maintenance_Distributed();
        $tableName = $oDal->getTablePrefix() . 'data_bucket_impression';
        
        
        // Empty table
        $oCurrentIntervalStart = new Date();
        $oCurrentIntervalStart->setDate('2008-05-25 11:00:00');
        $result = $oDal->_getBucketTableContent($tableName, $oCurrentIntervalStart);
        $this->assertEqual(0, $result->getRowCount(), "Found records that shouldn't be there.");
        
        // With some records
        $oDateThen = new Date();
        $oDateThen->copy($oCurrentIntervalStart);

        $prefix = $oDal->getTablePrefix();
        for ($i=1;$i<=4;$i++)
        {
            $date_time = $oDateThen->getDate(DATE_FORMAT_ISO_EXTENDED);
            $query = "
                  INSERT INTO
                    {$prefix}data_bucket_impression
                  (interval_start, creative_id, zone_id, count)
                  VALUES ('{$date_time}',{$i},{$i},{$i})";
            $oDal->oDbh->exec($query);
            $oDateThen->subtractSeconds(3600);
        }
        
        // Get content up to and including the previous interval_start
        $oPreviousIntervalStart = new Date();
        $oPreviousIntervalStart->copy($oCurrentIntervalStart);
        $oPreviousIntervalStart->subtractSeconds(3600);
        
        $expectedRecords = 3;
        $actualRecords = $oDal->_getBucketTableContent($tableName, $oPreviousIntervalStart)->getRowCount();
        $this->assertEqual($expectedRecords, $actualRecords);
    }

}

?>
