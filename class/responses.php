<?php
//$Id: responses.php,v 1.17 2005/11/01 15:05:42 eric_juden Exp $
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
 * gtdResponses class
 *
 * @author Eric Juden <ericj@epcusa.com> 
 * @access public
 * @package gtd
 */
class gtdResponses extends XoopsObject {
    function gtdResponses($id = null) 
	{
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('ticketid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('message', XOBJ_DTYPE_TXTAREA, null, false, 1000000);
        $this->initVar('timeSpent', XOBJ_DTYPE_INT, null, false);
        $this->initVar('updateTime', XOBJ_DTYPE_INT, null, true);
        $this->initVar('userIP', XOBJ_DTYPE_TXTBOX, null, true, 35);
        $this->initVar('private', XOBJ_DTYPE_INT, null, false);
        
        if (isset($id)) {
			if (is_array($id)) {
				$this->assignVars($id);
			}
		} else {
			$this->setNew();
		}
	}
    
	/**
    * Gets a UNIX timestamp
    *
    * @return int Timestamp of last update
    * @access public
    */
	function posted($format="l")
	{
		return formatTimestamp($this->getVar('updateTime'), $format);
	}
	
	function storeUpload($post_field, $response = null, $allowed_mimetypes = null) 
	{
	    //global $xoopsModuleConfig, $xoopsUser, $xoopsDB, $xoopsModule;
        include_once (GTD_CLASS_PATH.'/uploader.php');
        $config =& gtdGetModuleConfig();
        
	    $ticketid = $this->getVar('id');

        if(!isset($allowed_mimetypes)){
            $hMime =& gtdGetHandler('mimetype');
            $allowed_mimetypes = $hMime->checkMimeTypes();
            if(!$allowed_mimetypes){
                return false;
            }
        }
                        
        $maxfilesize = $config['gtd_uploadSize'];
        $maxfilewidth = $config['gtd_uploadWidth'];
        $maxfileheight = $config['gtd_uploadHeight'];
        if(!is_dir(GTD_UPLOAD_PATH)){
            mkdir(GTD_UPLOAD_PATH, 0757);
        }
        
        $uploader = new XoopsMediaUploader(GTD_UPLOAD_PATH.'/', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
        if ($uploader->fetchMedia($post_field)) {
            if (!isset($response)) {
                $uploader->setTargetFileName($ticketid."_". $uploader->getMediaName());
            } else {
                $uploader->setTargetFileName($ticketid."_".$response."_".$uploader->getMediaName());
            }
            if ($uploader->upload()) {
                $hFile =& gtdGetHandler('file');
                $file =& $hFile->create();
                $file->setVar('filename', $uploader->getSavedFileName());
                $file->setVar('ticketid', $ticketid);
                $file->setVar('mimetype', $allowed_mimetypes);
                $file->setVar('responseid', (isset($response) ? intval($response) : 0));
                
                if($hFile->insert($file)){
                    return $file;
                } else {                       
                    return $uploader->getErrors();
                }
             
            } else {
                return $uploader->getErrors();
            }
	    }
	}
	
	function checkUpload($post_field, &$allowed_mimetypes, &$errors)
	{
	    //global $xoopsModuleConfig;
	    include_once (GTD_CLASS_PATH.'/uploader.php');
	    $config =& gtdGetModuleConfig();
	    $maxfilesize = $config['gtd_uploadSize'];
        $maxfilewidth = $config['gtd_uploadWidth'];
        $maxfileheight = $config['gtd_uploadHeight'];
        $errors = array();
        
        if(!isset($allowed_mimetypes)){
            $hMime =& gtdGetHandler('mimetype');
            $allowed_mimetypes = $hMime->checkMimeTypes($post_field);
            if(!$allowed_mimetypes){
                $errors[] = _GTD_MESSAGE_WRONG_MIMETYPE;
                return false;
            }
        }
        $uploader = new XoopsMediaUploader(GTD_UPLOAD_PATH.'/', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
        
        if ($uploader->fetchMedia($post_field)) {
            return true;
        } else {
            $errors = array_merge($errors, $uploader->getErrors(false));
            return false;
        }
	}
}

/**
 * gtdResponsesHandler class
 *
 * Response Handler for gtdResponses class
 *
 * @author Eric Juden <ericj@epcusa.com> &
 * @access public
 * @package gtd
 */
 
class gtdResponsesHandler extends gtdBaseObjectHandler {
	/**
     * Name of child class
     * 
     * @var	string
     * @access	private
     */
	var $classname = 'gtdresponses';
	
    /**
     * DB table name
     * 
     * @var string
     * @access private
     */
     var $_dbtable = 'gtd_responses';
	
	/**
     * Constructor
     *
     * @param	object   $db    reference to a xoopsDB object
     */	
	function gtdResponsesHandler(&$db)
	{
	    parent::init($db);
    }
    
    function _insertQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
        $sql = sprintf("INSERT INTO %s (id, uid, ticketid, message, timeSpent, updateTime, userIP, private) 
            VALUES (%u, %u, %u, %s, %u, %u, %s, %u)", $this->_db->prefix($this->_dbtable), $id, $uid, $ticketid,
            $this->_db->quoteString($message), $timeSpent, time(), $this->_db->quoteString($userIP), $private);
            
        return $sql;
        
    }
    
    function _updateQuery(&$obj)
    {
        // Copy all object vars into local variables
        foreach ($obj->cleanVars as $k => $v) {
            ${$k} = $v;
        }
                
        $sql = sprintf("UPDATE %s SET uid = %u, ticketid = %u, message = %s, timeSpent = %u, 
            updateTime = %u, userIP = %s, private = %u WHERE id = %u", $this->_db->prefix($this->_dbtable), $uid, $ticketid,
            $this->_db->quoteString($message), $timeSpent, time(), 
            $this->_db->quoteString($userIP), $private, $id);
            
        return $sql;
    }
    
    function _deleteQuery(&$obj)
    {
        $sql = sprintf('DELETE FROM %s WHERE id = %u', $this->_db->prefix($this->_dbtable), $obj->getVar('id'));
        return $sql;
    }        
	
	/**
	 * delete a response from the database
	 * 
	 * @param object $obj reference to the {@link gtdResponse} obj to delete
	 * @param bool $force
	 * @return bool FALSE if failed.
	 * @access	public
	 */
	function delete(&$obj, $force = false)
	{

	    // Remove file associated with this response
	    $hFiles =& gtdGetHandler('file');
	    $crit = new CriteriaCompo(new Criteria('ticketid', $obj->getVar('ticketid')));
	    $crit->add(new Criteria('responseid', $obj->getVar('responseid')));
	    if(!$hFiles->deleteAll($crit)){
	        return false;
	    }
	    
        $ret = parent::delete($obj, $force);
        return $ret;
	}
	

	/**
	 * Get number of responses by staff members
	 * 
	 * @param int $ticketid ticket to get count
	 * @return int Number of staff responses
	 * @access	public	 
	 */
	function getStaffResponseCount($ticketid)
	{
	    $sql = sprintf('SELECT COUNT(*) FROM %s r INNER JOIN %s s ON r.uid = s.uid WHERE r.ticketid = %u', 
	        $this->_db->prefix($this->_dbtable), $this->_db->prefix('gtd_staff'), $ticketid);
	    
	    $ret = $this->_db->query($sql);
	    
	    list($count) = $this->_db->fetchRow($ret);
	    return $count;
	}
	
}
?>