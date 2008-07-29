<?php
/**
 * Table Definition for data_bkt_c_country
 */
require_once MAX_PATH.'/lib/max/Dal/DataObjects/DB_DataObjectCommon.php';

class DataObjects_Data_bkt_c_country extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'data_bkt_c_country';    // table name
    public $interval_start;                  // DATETIME() => openads_datetime => 142
    public $creative_id;                     // BIGINT(20) => openads_mediumint => 129
    public $zone_id;                         // BIGINT(20) => openads_mediumint => 129
    public $country;                         // CHAR(3) => openads_varchar => 130
    public $count;                           // INTEGER(11) => openads_int => 1

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Data_bkt_c_country',$k,$v); }

    var $defaultValues = array(
                'interval_start' => '',
                'country' => '',
                'count' => '',
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
?>