<?php
//$Id: department.php,v 1.11 2005/02/15 16:58:02 ackbarr Exp $
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
 * gtdDepartment class
 *
 * @author Eric Juden <ericj@epcusa.com> 
 * @access public
 * @package gtd
 */ 
class gtdDepartment extends XoopsObject {
    function gtdDepartment($id = null) 
	{
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('department', XOBJ_DTYPE_TXTBOX, null, false, 35);
		$this->initVar('tarif_1', XOBJ_DTYPE_TXTBOX, null, false, 8);
		$this->initVar('tarif_2', XOBJ_DTYPE_TXTBOX, null, false, 8);
		$this->initVar('tarif_3', XOBJ_DTYPE_TXTBOX, null, false, 8);        
        if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			}
		} else {
			$this->setNew();
		}
	}
}   //end of class

/**
 * gtdDepartmentHandler class
 *
 * Department Handler for gtdDepartment class
 *
 * @author Eric Juden <ericj@epcusa.com> &
 * @access public
 * @package gtd
 */
 
class gtdDepartmentHandler extends gtdBaseObjectHandler {
	/**
     * Name of child class
     * 
     * @var	string
     * @access	private
     */
	var $classname = 'gtddepartment';
	
    /**
     * DB table name
     * 
     * @var string
     * @access private
     */
     var $_dbtable = 'gtd_departments';
	
	/**
     * Constructor
     *
     * @param	object   $db    reference to a xoopsDB object
     */	
	function gtdDepartmentHandler(&$db)
	{
	    parent::init($db);
    }
            
    function getNameById($id)
    {
        $tmp =& $this->get($id);
        if ($tmp) {
            return $tmp->getVar('department');
        } else {
            return _GTD_TEXT_NO_DEPT;
        }   
    }
    
    function _insertQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
        $sql = sprintf("INSERT INTO %s (id, department, tarif_1, tarif_2, tarif_3) VALUES (%u, %s, %.2f, %.2f, %.2f)",
            $this->_db->prefix($this->_dbtable), $id, $this->_db->quoteString($department), $tarif_1, $tarif_2, $tarif_3);
            
        return $sql;
        
    }
    
    function _updateQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
        $sql = sprintf("UPDATE %s SET department = %s, tarif_1 = %.2f, tarif_2 = %.2f, tarif_3 = %.2f WHERE id = %u",
                $this->_db->prefix($this->_dbtable), $this->_db->quoteString($department), $tarif_1, $tarif_2, $tarif_3, $id);
		/*echo($sql);
			die();*/
		            global $xoopsLogger;
	    $xoopsLogger->addExtra('ticket.php update', "requete SQL = '$sql'");
        return $sql;
    }
    
    function _deleteQuery(&$obj)
    {
        $sql = sprintf('DELETE FROM %s WHERE id = %u', $this->_db->prefix($this->_dbtable), $obj->getVar('id'));
        return $sql;
    }
}
?>