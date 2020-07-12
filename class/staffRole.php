<?php
//$Id: staffRole.php,v 1.6 2005/02/15 16:58:03 ackbarr Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
if (!defined('GTD_CLASS_PATH')) {
    exit();
}

require_once(GTD_CLASS_PATH.'/gtdBaseObjectHandler.php');

/**
 * gtdStaffRole class
 *
 * Information about an individual staffrole
 *
 * @author Eric Juden <ericj@epcusa.com>
 * @access public
 * @package gtd
 */

class gtdStaffRole extends XoopsObject {
    function gtdStaffRole($id = null)
    {
       $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
       $this->initVar('roleid', XOBJ_DTYPE_INT, null, false);
       $this->initVar('deptid', XOBJ_DTYPE_INT, null, false);
          
       if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			}
		} else {
			$this->setNew();
		} 
    }
}   // end of class

class gtdStaffRoleHandler extends gtdBaseObjectHandler{	
    var $_idfield = 'roleid';
    
    /**
     * Name of child class
     * 
     * @var	string
     * @access	private
     */
	 var $classname = 'gtdstaffrole';
	
	/**
	 * DB Table Name
	 *
	 * @var 		string
	 * @access 	private
	 */
	var $_dbtable = 'gtd_staffroles';
	
	/**
	 * Constructor
	 *
	 * @param object $db reference to a xoopsDB object
	 */
	function gtdStaffRoleHandler(&$db) 
	{
		parent::init($db);
	}
	
    function &get($uid, $roleid, $deptid)
    {
        $crit = new CriteriaCompo('uid', $uid);
        $crit->add(new Criteria('roleid', $roleid));
        $crit->add(new Criteria('deptid', $deptid));
        
        if(!$role =& $this->getObjects($crit)){
            return false;
        }
        return $role;
    }    
    	
	function &getObjectsByStaff($uid, $id_as_key = false) 
	{
	    $uid = intval($uid);
	    $crit = new Criteria('uid', $uid);
        
        $arr = $this->getObjects($crit, $id_as_key);
        
        if(count($arr) == 0){
            $arr = false;
        }
        return $arr;
    }
    
    function staffInRole($uid, $roleid)
    {
        $crit = new CriteriaCompo('uid', $uid);
        $crit->add(new Criteria('roleid', $roleid));
        
        if(!$role =& $this->getObjects($crit)){
            return false;
        }
        return true;
    }

    function _insertQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
		$sql = sprintf("INSERT INTO %s (uid, roleid, deptid) VALUES (%u, %u, %u)", 
                $this->_db->prefix($this->_dbtable), $uid, $roleid, $deptid);
            
        return $sql;
        
    }    
	
    function _deleteQuery(&$obj)
    {
        $sql = sprintf('DELETE FROM %s WHERE uid = %u', $this->_db->prefix($this->_dbtable), $obj->getVar('uid'));
        return $sql;
    }
	

}   // end of handler class
?>