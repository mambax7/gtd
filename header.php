<?php
require('../../mainfile.php');

if (!defined('GTD_CONSTANTS_INCLUDED')) {
    include_once(XOOPS_ROOT_PATH.'/modules/gtd/include/constants.php');
}

include_once(GTD_BASE_PATH.'/functions.php');
require_once(GTD_CLASS_PATH.'/session.php');
require_once(GTD_CLASS_PATH.'/eventService.php');

$_gtdSession = new Session();
$_eventsrv = gtd_eventService::singleton();

$roleReset  = false;
$gtd_isStaff    = false;

// Is the current user a staff member?
if($xoopsUser){
    $hStaff =& gtdGetHandler('staff');
    if($gtd_staff =& $hStaff->getByUid($xoopsUser->getVar('uid'))){
        $gtd_isStaff = true;
        
        // Check if the staff member permissions have changed since the last page request
        if(!$myTime = $_gtdSession->get("gtd_permTime")){
            $roleReset = true;
        } else {
            $dbTime = $gtd_staff->getVar('permTimestamp');
            if($dbTime > $myTime){
                $roleReset = true;
            }
        }
        
        // Update staff member permissions (if necessary)   
        if($roleReset){
            $updateRoles = $gtd_staff->resetRoleRights();
            $_gtdSession->set("gtd_permTime", time());
        }
        
        //Retrieve the staff member's saved searches
        if(!$aSavedSearches =& $_gtdSession->get("gtd_savedSearches")){
            $aSavedSearches =& gtdGetSavedSearches();
        }
    }
}

$gtd_module_css = GTD_BASE_URL . '/styles/gtd.css';
$gtd_module_header = '<link rel="stylesheet" type="text/css" media="all" href="'.$gtd_module_css.'" /><!--[if gte IE 5.5000]><script src="iepngfix.js" language="JavaScript" type="text/javascript"></script><![endif]-->';

// @todo - this line is for compatiblity, remove once all references to $isStaff have been modified
$isStaff = $gtd_isStaff;


?>