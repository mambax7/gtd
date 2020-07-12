<?php
require('../../../mainfile.php');

if (!defined('GTD_CONSTANTS_INCLUDED')) {
    include_once(XOOPS_ROOT_PATH.'/modules/gtd/include/constants.php');
}
require_once GTD_JPSPAN_PATH.'/JPSpan.php';       // Including this sets up the JPSPAN constants
require_once JPSPAN . 'Server/PostOffice.php';      // Load the PostOffice server
include_once(GTD_BASE_PATH.'/functions.php');

// Create the PostOffice server
$server = & new JPSpan_Server_PostOffice();
$server->addHandler(new gtdWebLib());

if (isset($_SERVER['QUERY_STRING']) &&
        strcasecmp($_SERVER['QUERY_STRING'], 'client')==0) {

    // Compress the output Javascript (e.g. strip whitespace)
    define('JPSPAN_INCLUDE_COMPRESS',TRUE);

    // Display the Javascript client
    $server->displayClient();
} else {
    // This is where the real serving happens...
    // Include error handler
    // PHP errors, warnings and notices serialized to JS
    require_once JPSPAN . 'ErrorHandler.php';

    // Start serving requests...
    $server->serve();
}

class gtdWebLib {
    function customFieldsByDept($deptid)
    {
        $deptid = intval($deptid);
        $hFieldDept =& gtdGetHandler('ticketFieldDepartment');
        $fields =& $hFieldDept->fieldsByDepartment($deptid);
        
        $aFields = array();
        foreach($fields as $field){
            $values = $field->getVar('fieldvalues');
            if ($field->getVar('controltype') == GTD_CONTROL_YESNO) {
                $values = array(1 => _YES, 0 => _NO);
            }
            
            $aValues = array();
            foreach($values as $key=>$value){
                $aValues[] = array($key, $value);
            }
            
            $aFields[] = 
                array('id' => $field->getVar('id'),
                      'name' => $field->getVar('name'),
                      'desc' => $field->getVar('description'),
                      'fieldname' => $field->getVar('fieldname'),
                      'defaultvalue' => $field->getVar('defaultvalue'),
                      'controltype' => $field->getVar('controltype'),
                      'required' => $field->getVar('required'),
                      'fieldlength' => $field->getVar('fieldlength'),
                      'weight' => $field->getVar('weight'),
                      'fieldvalues' => $aValues,
                      'validation' => $field->getVar('validation'));
        }
        return $aFields;
    }
    
    function staffByDept($deptid)
    {
        $mc =& gtdGetModuleConfig();
        $field = $mc['gtd_displayName']== 1 ? 'uname':'name';
        
        
        $deptid = intval($deptid);
        $hMembership =& gtdGetHandler('membership');
        $staff =& $hMembership->xoopsUsersByDept($deptid);
        
        $aStaff = array();
        $aStaff[] = array('uid' => 0,
                          'name' => _GTD_MESSAGE_NOOWNER);
        foreach($staff as $s){
            $aStaff[] = array('uid' => $s->getVar('uid'),
                              'name' => $s->getVar($field));
        }
        return $aStaff;
    }
}
?>