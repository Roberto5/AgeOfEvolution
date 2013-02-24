<?php
/**
 * track
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_track extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = 'site_track';
    
    function getTrack() {
    	return $this->getDefaultAdapter()->fetchAll("
    	SELECT `site_track`.`id`,`site_track`.`uid`,`site_track`.`status`,`site_track`.`type`,
    		`site_track_cat`.`name` as `cat`,`site_track`.`description`,`site_track`.`screen`
    	FROM `site_track`,`site_track_cat` WHERE `site_track_cat`.`id`=`site_track`.`category`");
    }
    function see($id) {
    	return $this->getDefaultAdapter()->fetchRow("
    	SELECT `site_track`.`id`,`site_track`.`uid`,`site_track`.`status`,`site_track`.`type`,
    		`site_track_cat`.`name` as `cat`,`site_track`.`description`,`site_track`.`screen`
    	FROM `site_track`,`site_track_cat` WHERE `site_track_cat`.`id`=`site_track`.`category` AND `site_track`.`id`='$id'");
    }
}
