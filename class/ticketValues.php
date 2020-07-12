<?php
//$Id: ticketValues.php,v 1.9.2.1 2005/11/16 16:45:04 eric_juden Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
if (!defined('GTD_CONSTANTS_INCLUDED')) {
    exit();
}

require_once(GTD_CLASS_PATH.'/gtdBaseObjectHandler.php');
gtdIncludeLang('admin');

/**
 * gtdTicketValues class
 *
 * Metadata that represents a custom value created for gtd
 *
 * @author Eric Juden <eric@3dev.org>
 * @access public
 * @package gtd
 */
class gtdTicketValues extends XoopsObject
{   
    var $_fields = array();
     
    /**
     * Class Constructor
     *
     * @param mixed $ticketid null for a new object, hash table for an existing object
     * @return none
     * @access public
     */
    function gtdTicketValues($id = null)
    {        
        $this->initVar('ticketid', XOBJ_DTYPE_INT, null, false);
        
        $hFields =& gtdGetHandler('ticketField');
        $fields =& $hFields->getObjects(null, true);
        
        foreach($fields as $field){
            $key = $field->getVar('fieldname');
            $datatype = $this->_getDataType($field->getVar('datatype'), $field->getVar('controltype'));
            $value = $this->_getValueFromXoopsDataType($datatype);
            $required = $field->getVar('required');
            $maxlength = ($field->getVar('fieldlength') < 50 ? $field->getVar('fieldlength') : 50);
            $options = '';
            
            $this->initVar($key, $datatype, null, $required, $maxlength, $options);
            
            $this->_fields[$key] = (($field->getVar('datatype') == _GTD_DATATYPE_TEXT) ? "%s" : "%d");
        }
        $this->_fields['ticketid'] = "%u";


        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            }
        } else {
			$this->setNew();
		}
    }
    
    function _getDataType($datatype, $controltype)
    {      
        switch($controltype)
        {            
            case GTD_CONTROL_TXTBOX:
                return $this->_getXoopsDataType($datatype);
                break;
            
            case GTD_CONTROL_TXTAREA:
                return $this->_getXoopsDataType($datatype);
                break;
            
            case GTD_CONTROL_SELECT:
                    return XOBJ_DTYPE_TXTAREA;
                break;
            
            case GTD_CONTROL_YESNO:
                    return XOBJ_DTYPE_INT;
                break;
            
            case GTD_CONTROL_RADIOBOX:
                    return XOBJ_DTYPE_TXTBOX;
                break;
                
            case GTD_CONTROL_DATETIME:
                return $this->_getXoopsDataType($datatype);
                break;
                
            case GTD_CONTROL_FILE:
                return XOBJ_DTYPE_TXTBOX;
                break;
                
            default:
                return XOBJ_DTYPE_TXTBOX;
                break;
        }
    }
    
    function _getXoopsDataType($datatype)
    {
        switch($datatype)
        {
            case _GTD_DATATYPE_TEXT:
                    return XOBJ_DTYPE_TXTBOX;
                break;
                
            case _GTD_DATATYPE_NUMBER_INT:
                    return XOBJ_DTYPE_INT;
                break;
                
            case _GTD_DATATYPE_NUMBER_DEC:
                    return XOBJ_DTYPE_OTHER;
                break;
            
            default:
                    return XOBJ_DTYPE_TXTBOX;
                break;
        }
    }
    
    function _getValueFromXoopsDataType($datatype)
    {
        switch($datatype)
        {
            case XOBJ_DTYPE_TXTBOX:
            case XOBJ_DTYPE_TXTAREA:
                return '';
                break;
            
            case XOBJ_DTYPE_INT:
                return 0;
                break;
            
            case XOBJ_DTYPE_OTHER:
                return 0.0;
                break;
                
            default:
                return null;
                break;
        }
    }
    
    function getTicketFields()
    {
        return $this->_fields;
    }
}

class gtdTicketValuesHandler extends gtdBaseObjectHandler
{
    /**
     * Name of child class
     * 
     * @var	string
     * @access	private
     */
	 var $classname = 'gtdTicketValues';
	
	/**
	 * DB Table Name
	 *
	 * @var 		string
	 * @access 	private
	 */
	var $_dbtable = 'gtd_ticket_values';
	var $id = 'ticketid';
	var $_idfield = 'ticketid';
	
	/**
	 * Constructor
	 *
	 * @param	object   $db    reference to a xoopsDB object
	 */
	function gtdTicketValuesHandler(&$db) 
	{
		parent::init($db);
    }
	
	function _insertQuery(&$obj)
	{
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {     // Assumes cleanVars has already been called
            ${$k} = $v;
        }
        
        $myFields = $obj->getTicketFields();    // Returns array[$fieldname] = %s or %d for all custom fields
      
        $count = 1;
        $sqlFields = "";
        $sqlVars = "";
        foreach($myFields as $myField=>$datatype){      // Create sql name and value pairs
            if(isset(${$myField}) && ${$myField} != null){                
                if($count > 1){								// If we have been through the loop already
                    $sqlVars .= ", ";
                    $sqlFields .= ", ";
                }
                $sqlFields .= $myField;
                if($datatype == "%s"){              		// If this field is a string
                    $sqlVars .= $this->_db->quoteString(${$myField});     // Add text to sqlVars string
                } else {                                	// If this field is a number
                    $sqlVars .= ${$myField};      // Add text to sqlVars string
                }
                $count++;
            }
        }
        // Create sql statement
        $sql = "INSERT INTO ". $this->_db->prefix($this->_dbtable)." (" . $sqlFields .") VALUES (". $sqlVars .")";
          
        return $sql;
	}
	
	function _updateQuery(&$obj)
	{
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        
        $myFields = $obj->getTicketFields();    // Returns array[$fieldname] = %s or %u for all custom fields
        $count = 1;
        $sqlVars = "";
        foreach($myFields as $myField=>$datatype){      // Used to create sql field and value substrings
            if(isset(${$myField}) && ${$myField} != null){                
                if($count > 1){								// If we have been through the loop already
                    $sqlVars .= ", ";
                }
                if($datatype == "%s"){              		// If this field is a string
                    $sqlVars .= $myField ." = ". $this->_db->quoteString(${$myField});     // Add text to sqlVars string
                } else {                                	// If this field is a number
                    $sqlVars .= $myField ." = ". ${$myField};      // Add text to sqlVars string
                }
                $count++;
            }
        }
        
        // Create update statement
        $sql = "UPDATE ". $this->_db->Prefix($this->_dbtable)." SET ". $sqlVars ." WHERE ticketid = ". $obj->getVar('ticketid');

        return $sql;	    
	}
	
	
	function _deleteQuery(&$obj)
	{
        $sql = sprintf("DELETE FROM %s WHERE ticketid = %u", $this->_db->prefix($this->_dbtable), $obj->getVar($this->id));
	    return $sql;
	}
}
?>