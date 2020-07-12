<?php
//$Id: role.php,v 1.6 2005/02/15 16:58:03 ackbarr Exp $
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
 * gtdRole class
 *
 * Information about an individual role
 *
 * @author Eric Juden <ericj@epcusa.com>
 * @access public
 * @package gtd
 */

class gtdRole extends XoopsObject {
    function gtdRole($id = null)
    {
       $this->initVar('id', XOBJ_DTYPE_INT, null, false);
       $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 35);
       $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false, 1024);
       $this->initVar('tasks', XOBJ_DTYPE_INT, 0, false);
          
       if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			}
		} else {
			$this->setNew();
		} 
    }
}   // end of class

class gtdRoleHandler extends gtdBaseObjectHandler{
    /**
     * Name of child class
     * 
     * @var	string
     * @access	private
     */
	 var $classname = 'gtdrole';
	
	/**
	 * DB Table Name
	 *
	 * @var 		string
	 * @access 	private
	 */
	var $_dbtable = 'gtd_roles';
	
	/**
	 * Constructor
	 *
	 * @param	object   $db    reference to a xoopsDB object
	 */
	function gtdRoleHandler(&$db) 
	{
		parent::init($db);
	}
		
    function _insertQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
        $sql = sprintf("INSERT INTO %s (id, name, description, tasks) VALUES (%u, %s, %s, %u)", 
                $this->_db->prefix($this->_dbtable), $id, $this->_db->quoteString($name), 
                $this->_db->quoteString($description), $tasks);
            
        return $sql;
        
    }
    
    function _updateQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
        $sql = sprintf("UPDATE %s SET name = %s, description = %s, tasks = %u WHERE id = %u", 
                $this->_db->prefix($this->_dbtable), $this->_db->quoteString($name), 
                $this->_db->quoteString($description), $tasks, $id);
                
        return $sql;
    }
    
    function _deleteQuery(&$obj)
    {
        $sql = sprintf('DELETE FROM %s WHERE id = %u', $this->_db->prefix($this->_dbtable), $obj->getVar('id'));
        return $sql;
    }
	
	
	/**
	 * delete a role from the database
	 * 
	 * @param object $obj reference to the {@link gtdRole} obj to delete
	 * @param bool $force
	 * @return bool FALSE if failed.
	 * @access	public
	 */
	function delete(&$obj, $force = false)
	{	
		// Remove staff roles from db first
		$hStaffRole =& gtdGetHandler('staffRole');
		if(!$hStaffRole->deleteAll(new Criteria('roleid', $obj->getVar('id')))){
		    return false;
		}
	    
	    $ret = parent::delete($obj, $force);
	    return $ret;
	    
	}
	
}
?>