<?php
//$Id: delete.php,v 1.23 2005/09/26 20:57:21 eric_juden Exp $
//require_once('header.php');
require('../../../include/cp_header.php');
require_once('admin_header.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/staffService.php');

$_eventsrv->advise('delete_staff', gtd_staffService::singleton());

global $xoopsUser;
$uid = $xoopsUser->getVar('uid');

if(isset($_REQUEST['deleteDept'])){
    if (isset( $_REQUEST['deptid'])){
        $deptID = $_REQUEST['deptid'];
    } else {
        redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, _AM_GTD_MESSAGE_NO_DEPT);
    }
    
    if (!isset($_POST['ok'])) {
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manDept');
        xoops_confirm(array('deleteDept' => 1, 'deptid' => $deptID, 'ok' => 1), GTD_BASE_URL .'/admin/delete.php', sprintf(_AM_GTD_MSG_DEPT_DEL_CFRM, $deptID));
        xoops_cp_footer();
    } else {
        $hDepartments =& gtdGetHandler('department');
        $hGroupPerm =& xoops_gethandler('groupperm');
        $dept =& $hDepartments->get($deptID);
    
        $crit = new CriteriaCompo(new Criteria('gperm_name', _GTD_GROUP_PERM_DEPT));
        $crit->add(new Criteria('gperm_itemid', $deptID));
        $hGroupPerm->deleteAll($crit);
        
        $deptCopy = $dept;
    
        if($hDepartments->delete($dept)){
            $_eventsrv->trigger('delete_department', array(&$dept));
            $message = _GTD_MESSAGE_DEPT_DELETE;
            
            // Remove configoption for department
            $hConfigOption =& xoops_gethandler('configoption');
            $crit = new CriteriaCompo(new Criteria('confop_name', $deptCopy->getVar('department')));
            $crit->add(new Criteria('confop_value', $deptCopy->getVar('id')));
            $configOption =& $hConfigOption->getObjects($crit);
            
            if(count($configOption) > 0){
                if(!$hConfigOption->delete($configOption[0])){
                    $message = '';
                }
                unset($deptCopy);
            }
            
            // Change default department
            $depts =& $hDepartments->getObjects();
            $aDepts = array();
            foreach($depts as $dpt){
                $aDepts[] = $dpt->getVar('id');
            }
            gtdSetMeta("default_department", $aDepts[0]);
        } else {
            $message = _GTD_MESSAGE_DEPT_DELETE_ERROR . $dept->getHtmlErrors();
        }
        redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, $message);
     }
} elseif(isset($_REQUEST['deleteStaff'])){
    if (isset( $_REQUEST['uid'])){
        $staffid = $_REQUEST['uid'];
      
        if (!isset($_POST['ok'])) {
            xoops_cp_header();
            echo $oAdminButton->renderButtons('manDept');
            xoops_confirm(array('deleteStaff' => 1, 'uid' => $staffid, 'ok' => 1), GTD_BASE_URL .'/admin/delete.php', sprintf(_AM_GTD_MSG_STAFF_DEL_CFRM, $staffid));
            xoops_cp_footer();
        } else {
            $hStaff =& gtdGetHandler('staff');
            $staff =& $hStaff->getByUid($staffid);
        
            if($hStaff->delete($staff)){
                $_eventsrv->trigger('delete_staff', array(&$staff));
                $message = _GTD_MESSAGE_STAFF_DELETE;
            } else {
                $message = _GTD_MESSAGE_STAFF_DELETE_ERROR . $staff->getHtmlErrors();
            }
            redirect_header(GTD_ADMIN_URL."/staff.php?op=manageStaff", 3, $message);
        }
    }
    
       
}
?>