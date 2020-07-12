<?php
//$Id: department.php,v 1.20 2005/10/11 15:14:58 eric_juden Exp $
include('../../../include/cp_header.php');          
include_once('admin_header.php');           
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');
require_once(GTD_CLASS_PATH . '/gtdForm.php');
require_once(GTD_CLASS_PATH . '/gtdFormRadio.php');
require_once(GTD_CLASS_PATH . '/gtdFormCheckbox.php');

global $xoopsModule;
$module_id = $xoopsModule->getVar('mid');

$start = $limit = 0;   
if (isset($_REQUEST['limit'])) {
    $limit = intval($_REQUEST['limit']);
}
if (isset($_REQUEST['start'])) {
    $start = intval($_REQUEST['start']);
}
if (!$limit) {
    $limit = 15;
}
if(isset($_REQUEST['order'])){
    $order = $_REQUEST['order'];
} else {
    $order = "ASC";
}
if(isset($_REQUEST['sort'])) {
    $sort = $_REQUEST['sort'];
} else {
    $sort = "department";
}

$aSortBy = array('id' => _AM_GTD_TEXT_ID, 'department' => _AM_GTD_TEXT_DEPARTMENT);
$aOrderBy = array('ASC' => _AM_GTD_TEXT_ASCENDING, 'DESC' => _AM_GTD_TEXT_DESCENDING);
$aLimitBy = array('10' => 10, '15' => 15, '20' => 20, '25' => 25, '50' => 50, '100' => 100);

$op = 'default';

if ( isset( $_REQUEST['op'] ) )
{
    $op = $_REQUEST['op'];
}

switch ( $op )
{
    case "activateMailbox":
        activateMailbox();
        break;        
        
    case "AddDepartmentServer":
        addDepartmentServer();
        break;
        
    case "DeleteDepartmentServer":
        DeleteDepartmentServer();
        break;
        
    case "deleteStaffDept":
        deleteStaffDept();
        break;
        
    case "editDepartment":
        editDepartment();
        break;
        
    case "EditDepartmentServer":
        EditDepartmentServer();
        break;
        
    case "manageDepartments":
        manageDepartments();
        break;
    
    case "testMailbox":
        testMailbox();
        break;
    
    case "clearAddSession":
        clearAddSession();
        break;
    
    case "clearEditSession":
        clearEditSession();
        break;
        
    case "Default":
        updateDefault();
        break;
        
    default:
        header("Location: ".GTD_BASE_URL."/admin/index.php");
        break;
}

function activateMailbox()
{
    $id = intval($_GET['id']);
    $setstate = intval($_GET['setstate']);
    
    $hMailbox =& gtdGetHandler('departmentMailBox');
    if ($mailbox =& $hMailbox->get($id)) {
        $url = GTD_BASE_URL.'/admin/department.php?op=editDepartment&id='. $mailbox->getVar('departmentid');
        $mailbox->setVar('active', $setstate);
        if ($hMailbox->insert($mailbox, true)) {
            header("Location: $url");
        } else {
            redirect_header($url, 3, _AM_GTD_DEPARTMENT_SERVER_ERROR);
        }
    } else {
        redirect_header(GTD_BASE_URL.'/admin/department.php?op=manageDepartments', 3, _GTD_NO_MAILBOX_ERROR);
    }   
}

function addDepartmentServer()
{
  if(isset($_GET['id'])){
      $deptID = intval($_GET['id']);
  } else {
    redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, _AM_GTD_DEPARTMENT_NO_ID);
  }


  $hDeptServers =& gtdGetHandler('departmentMailBox');
  $server = $hDeptServers->create();
  $server->setVar('departmentid',$deptID);
  $server->setVar('emailaddress', $_POST['emailaddress']);
  $server->setVar('server',       $_POST['server']);
  $server->setVar('serverport',   $_POST['port']);
  $server->setVar('username',     $_POST['username']);
  $server->setVar('password',     $_POST['password']);
  $server->setVar('genre',     $_POST['genre']);
  //
  if ($hDeptServers->insert($server))   {
    header("Location: ".GTD_ADMIN_URL."/department.php?op=manageDepartments");
  } else {
    redirect_header(GTD_ADMIN_URL.'/department.php?op=manageDepartments', 3, _AM_GTD_DEPARTMENT_SERVER_ERROR);
  }
}

function DeleteDepartmentServer() {
    global $oAdminButton;
    if(isset($_REQUEST['id'])){
        $emailID = intval($_REQUEST['id']);
    } else {
        redirect_header(GTD_ADMIN_URL.'/department.php?op=manageDepartments', 3, _AM_GTD_DEPARTMENT_SERVER_NO_ID);
    }
    
    $hDeptServers =& gtdGetHandler('departmentMailBox');
    $server       =& $hDeptServers->get($emailID);
    
    if (!isset($_POST['ok'])) {
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manDept');
        xoops_confirm(array('op' => 'DeleteDepartmentServer', 'id' => $emailID, 'ok' => 1), GTD_BASE_URL .'/admin/department.php', sprintf(_AM_GTD_MSG_DEPT_MBOX_DEL_CFRM, $server->getVar('emailaddress')));
        xoops_cp_footer();     
    } else {
        //get handler
        if ($hDeptServers->delete($server,true)) {
            header("Location: ".GTD_ADMIN_URL."/department.php?op=manageDepartments");
        } else {
            redirect_header(GTD_ADMIN_URL.'/department.php?op=manageDepartments', 3, _AM_GTD_DEPARTMENT_SERVER_DELETE_ERROR);
        }
    }
}

function deleteStaffDept()
{
    if(isset($_GET['deptid'])){
        $deptID = intval($_GET['deptid']);
    } else {
        redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, _AM_GTD_MSG_NO_DEPTID);
    }
    if(isset($_GET['uid'])){
        $staffID = intval($_GET['uid']);
    } elseif(isset($_POST['staff'])){
        $staffID = $_POST['staff'];
    } else {
        redirect_header(GTD_ADMIN_URL."/department.php?op=editDepartment&deptid=$deptID", 3, _AM_GTD_MSG_NO_UID);
    }
    
    $hMembership =& gtdGetHandler('membership');
    if(is_array($staffID)){
        foreach($staffID as $sid){
            $ret = $hMembership->removeDeptFromStaff($deptID, $sid);
        }
    } else {
        $ret = $hMembership->removeDeptFromStaff($deptID, $staffID);
    }
    
    if($ret){
        header("Location: ".GTD_ADMIN_URL."/department.php?op=editDepartment&deptid=$deptID");
    } else {
        redirect_header(GTD_ADMIN_URL."/department.php??op=editDepartment&deptid=$deptID", 3, _AM_GTD_MSG_REMOVE_STAFF_DEPT_ERR);
    }
}

function editDepartment()
{
    $_gtdSession = Session::singleton();
    global $imagearray, $xoopsModule, $oAdminButton, $limit, $start, $xoopsModuleConfig;
    $module_id = $xoopsModule->getVar('mid');
    $displayName =& $xoopsModuleConfig['gtd_displayName'];    // Determines if username or real name is displayed
  
    $_gtdSession->set("gtd_return_page", substr(strstr($_SERVER['REQUEST_URI'], 'admin/'), 6));

    if(isset($_REQUEST["deptid"])){
        $deptID = $_REQUEST['deptid'];
    } else {
        redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, _AM_GTD_MSG_NO_DEPTID);
    }
    
    $hDepartments  =& gtdGetHandler('department');    
    $hGroups =& xoops_gethandler('group');
    $hGroupPerm =& xoops_gethandler('groupperm');
    
    if(isset($_POST['updateDept'])){
        $groups = (isset($_POST['groups']) ? $_POST['groups'] : array());
        
        $hasErrors = false;
        //Department Name supplied?
        if (trim($_POST['newDept']) == '') {
            $hasErrors = true;
            $errors['newDept'][] = _AM_GTD_MESSAGE_NO_DEPT;
        } else {
        
            //Department Name unique?
            $crit = new CriteriaCompo(new Criteria('department', $_POST['newDept']));
            $crit->add(new Criteria('id', $deptID, '!='));
            if($existingDepts = $hDepartments->getCount($crit)){
                $hasErrors = true;
                $errors['newDept'][] = _GTD_MESSAGE_DEPT_EXISTS;
                
            }
        }
	
        
        if ($hasErrors) {
            $session =& Session::singleton();
            //Store existing dept info in session, reload addition page
            $aDept = array();
            $aDept['newDept'] = $_POST['newDept'];
            $aDept['groups'] = $groups;
            $session->set("gtd_editDepartment_$deptID", $aDept);
            $session->set("gtd_editDepartmentErrors_$deptID", $errors);
            header('Location: '. gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'editDepartment', 'deptid'=>$deptID), false));
            exit();
        }        
	
        
        $dept =& $hDepartments->get($deptID);
        
        $oldDept = $dept;
        $groups = $_POST['groups'];
        
        // Need to remove old group permissions first
        $crit = new CriteriaCompo(new Criteria('gperm_modid', $module_id));
        $crit->add(new Criteria('gperm_itemid', $deptID));
        $crit->add(new Criteria('gperm_name', _GTD_GROUP_PERM_DEPT));
        $hGroupPerm->deleteAll($crit);
        
        foreach($groups as $group){     // Add new group permissions
            $hGroupPerm->addRight(_GTD_GROUP_PERM_DEPT, $deptID, $group, $module_id);
        }
        
        $dept->setVar('department', $_POST['newDept']);
	
	$dept->setVar('tarif_1', $_POST['tarif_1']);
	$dept->setVar('tarif_2', $_POST['tarif_2']);
	$dept->setVar('tarif_3', $_POST['tarif_3']);
            
        if($hDepartments->insert($dept)){
            $message = _GTD_MESSAGE_UPDATE_DEPT;
            
            // Update default dept
            if(isset($_POST['defaultDept']) && ($_POST['defaultDept'] == 1)){
                gtdSetMeta("default_department", $dept->getVar('id'));
            } else {
                $depts =& $hDepartments->getObjects();
                $aDepts = array();
                foreach($depts as $dpt){
                    $aDepts[] = $dpt->getVar('id');
                }
                gtdSetMeta("default_department", $aDepts[0]);
            }
            
            // Edit configoption for department
            $hConfigOption =& xoops_gethandler('configoption');
            $crit = new CriteriaCompo(new Criteria('confop_name', $oldDept->getVar('department')));
	    /*$crit = new CriteriaCompo(new Criteria('confop_name', $oldDept->getVar('tarif_1')));*/
            $crit->add(new Criteria('confop_value', $oldDept->getVar('id')));
            $confOption =& $hConfigOption->getObjects($crit);
                                    
            if(count($confOption) > 0){
                $confOption[0]->setVar('confop_name', $dept->getVar('department'));
		/*$confOption[0]->setVar('confop_name', $dept->getVar('tarif_1'));*/
                
                if(!$hConfigOption->insert($confOption[0])){
                    redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, _AM_GTD_MSG_UPDATE_CONFIG_ERR);
                }
            }
            _clearEditSessionVars($deptID);
            header("Location: ".GTD_ADMIN_URL."/department.php?op=manageDepartments");
        } else {
            $message = _GTD_MESSAGE_UPDATE_DEPT_ERROR . $dept->getHtmlErrors();
            redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, $message);
        }
        
    } else {
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manDept');
        
        $dept =& $hDepartments->get($deptID);
        
        $session =& Session::singleton();
        $sess_dept = $session->get("gtd_editDepartment_$deptID");
        $sess_errors = $session->get("gtd_editDepartmentErrors_$deptID");
        
        //Display any form errors
        if (! $sess_errors === false) {
            gtdRenderErrors($sess_errors, gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'clearEditSession', 'deptid'=>$deptID)));
        }
        
        // Get list of groups with permission
        $crit = new CriteriaCompo(new Criteria('gperm_modid', $module_id));
        $crit->add(new Criteria('gperm_itemid', $deptID));
        $crit->add(new Criteria('gperm_name', _GTD_GROUP_PERM_DEPT));
        $group_perms =& $hGroupPerm->getObjects($crit);
        
        $aPerms = array();      // Put group_perms in usable format
        foreach($group_perms as $perm){
            $aPerms[$perm->getVar('gperm_groupid')] = $perm->getVar('gperm_groupid');
        }        
        
        if (! $sess_dept === false) {
            $fld_newDept = $sess_dept['newDept'];
            $fld_groups  = $sess_dept['groups'];
        } else {
            $fld_newDept = $dept->getVar('department');  //julien quand on l'enleve la case se vide.
            $fld_groups = $aPerms;
        }

                
        // Get list of all groups
        $crit = new Criteria('', '');
        $crit->setSort('name');
        $crit->setOrder('ASC');
        $groups =& $hGroups->getObjects($crit, true);
        
        $aGroups = array();
        foreach($groups as $group_id=>$group){
            $aGroups[$group_id] = $group->getVar('name');
        }
        asort($aGroups);    // Set groups in alphabetical order

        echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';        
        $form = new gtdForm(_AM_GTD_EDIT_DEPARTMENT, 'edit_dept', gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'editDepartment', 'deptid' => $deptID)));
        $dept_name =& new XoopsFormText(_AM_GTD_TEXT_EDIT_DEPT, 'newDept', 20, 35, $fld_newDept);
        $group_select =& new XoopsFormSelect(_AM_GTD_TEXT_EDIT_DEPT_PERMS, 'groups', $fld_groups, 6, true);
        $group_select->addOptionArray($aGroups);
		$dept_tarif_1 =& new XoopsFormText(_AM_GTD_TEXT_TARIF_EDIT_1, 'tarif_1', 6,6, $fld_tarif_1);
		$dept_tarif_2 =& new XoopsFormText(_AM_GTD_TEXT_TARIF_EDIT_2, 'tarif_2', 6,6, $fld_tarif_2);
		$dept_tarif_3 =& new XoopsFormText(_AM_GTD_TEXT_TARIF_EDIT_3, 'tarif_3', 6,6, $fld_tarif_3);
        $defaultDeptID = gtdGetMeta("default_department");
        $defaultDept =& new gtdFormCheckbox(_AM_GTD_TEXT_DEFAULT_DEPT, 'defaultDept', (($defaultDeptID == $deptID) ? 1 : 0), 'defaultDept');
        $defaultDept->addOption(1, "");
        $btn_tray = new XoopsFormElementTray('');
        $btn_tray->addElement(new XoopsFormButton('', 'updateDept', _AM_GTD_BUTTON_SUBMIT, 'submit')); 
        $form->addElement($dept_name);
		$form->addElement($dept_tarif_1);
		$form->addElement($dept_tarif_2);
		$form->addElement($dept_tarif_3);
        $form->addElement($group_select);
        $form->addElement($defaultDept);
        $form->addElement($btn_tray);
        $form->setLabelWidth('20%');
        echo $form->render();
        
        // Get dept staff members
        $hMembership =& gtdGetHandler('membership');
        $hMember =& xoops_gethandler('member');
        $hStaffRole =& gtdGetHandler('staffRole');
        $hRole =& gtdGetHandler('role');
        
        $staff = $hMembership->membershipByDept($deptID, $limit, $start);
        $crit = new Criteria('j.department', $deptID);
        $staffCount =& $hMembership->getCount($crit);
        $roles =& $hRole->getObjects(null, true);
              
        echo "<form action='".GTD_ADMIN_URL."/department.php?op=deleteStaffDept&amp;deptid=".$deptID."' method='post'>";
        echo "<table width='100%' cellspacing='1' class='outer'>
              <tr><th colspan='".(3+count($roles))."'><label>". _AM_GTD_MANAGE_STAFF ."</label></th></tr>";
              
        if($staffCount > 0){
            $aStaff = array();
            foreach($staff as $stf){
                $aStaff[$stf->getVar('uid')] = $stf->getVar('uid');     // Get array of staff uid
            }
            
            // Get user list
            $crit = new Criteria('uid', "(". implode($aStaff, ',') .")", "IN");
            //$members =& $hMember->getUserList($crit);
            $members =& gtdGetUsers($crit, $displayName);
            
            // Get staff roles
            $crit = new CriteriaCompo(new Criteria('uid', "(". implode($aStaff, ',') .")", "IN"));
            $crit->add(new Criteria('deptid', $deptID));
            $staffRoles =& $hStaffRole->getObjects($crit);
            unset($aStaff);
            
            $staffInfo = array();
            foreach($staff as $stf){
                $staff_uid = $stf->getVar('uid');
                $staffInfo[$staff_uid]['uname'] = $members[$staff_uid];
                $aRoles = array();
                foreach($staffRoles as $role){
                    $role_id = $role->getVar('roleid');
                    if($role->getVar('uid') == $staff_uid){
                        $aRoles[$role_id] = $roles[$role_id]->getVar('name');
                    }    
                    $staffInfo[$staff_uid]['roles'] = implode($aRoles, ', ');
                }
            }
            $nav = new XoopsPageNav($staffCount, $limit, $start, 'start', "op=editDepartment&amp;deptid=$deptID&amp;limit=$limit");
              
            echo "<tr class='head'><td rowspan='2'>"._AM_GTD_TEXT_ID."</td><td rowspan='2'>"._AM_GTD_TEXT_USER."</td><td colspan='".count($roles)."'>"._AM_GTD_TEXT_ROLES."</td><td rowspan='2'>"._AM_GTD_TEXT_ACTIONS."</td></tr>";
            echo "<tr class='head'>";
            foreach ($roles as $thisrole) echo "<td>".$thisrole->getVar('name')."</td>";
            echo "</tr>";
            foreach($staffInfo as $uid=>$staff){
                echo "<tr class='even'>
                          <td><input type='checkbox' name='staff[]' value='".$uid."' />".$uid."</td>
                          <td>".$staff['uname']."</td>";
 			foreach ($roles as $thisrole) {
 				echo "<td><img src='".GTD_BASE_URL."/images/";
 				echo (in_array($thisrole->getVar('name'),explode(', ',$staff['roles']))) ? "on.png" : "off.png";
				echo "' /></td>";
			}
            echo "    <td>
                          <a href='".GTD_ADMIN_URL."/staff.php?op=editStaff&amp;uid=".$uid."'><img src='".XOOPS_URL."/modules/gtd/images/button_edit.png' title='"._AM_GTD_TEXT_EDIT."' name='editStaff' /></a>&nbsp;
                          <a href='".GTD_ADMIN_URL."/department.php?op=deleteStaffDept&amp;uid=".$uid."&amp;deptid=".$deptID."'><img src='".XOOPS_URL."/modules/gtd/images/button_delete.png' title='"._AM_GTD_TEXT_DELETE_STAFF_DEPT."' name='deleteStaffDept' /></a>
                      </td>
                  </tr>";
            }
            echo "<tr>
                      <td class='foot' colspan='".(3+count($roles))."'>
                          <input type='checkbox' name='checkallRoles' value='0' onclick='selectAll(this.form,\"staff[]\",this.checked);' />
                          <input type='submit' name='deleteStaff' id='deleteStaff' value='"._AM_GTD_BUTTON_DELETE."' />
                      </td>
                  </tr>";
            echo "</table></form>";
            echo "<div id='staff_nav'>".$nav->renderNav()."</div>";
        } else {
            echo "</table></form>";
        }
        
        //now do the list of servers
        $hDeptServers =& gtdGetHandler('departmentMailBox');
        $deptServers  =& $hDeptServers->getByDepartment($deptID);
        //iterate
        if (count($deptServers) > 0) {
          echo "<br /><table width='100%' cellspacing='1' class='outer'>
               <tr>
                 <th colspan='5'><label>". _AM_GTD_DEPARTMENT_SERVERS ."</label></th>
               </tr>
               <tr>
                 <td class='head' width='20%'><label>". _AM_GTD_DEPARTMENT_SERVERS_EMAIL ."</label></td>
                 <td class='head'><label>". _AM_GTD_DEPARTMENT_SERVERS_TYPE ."</label></td>
                 <td class='head'><label>". _AM_GTD_DEPARTMENT_SERVERS_SERVERNAME ."</label></td>
                 <td class='head'><label>". _AM_GTD_DEPARTMENT_SERVERS_PORT ."</label></td>
                 <td class='head'><label>". _AM_GTD_DEPARTMENT_SERVERS_ACTION ."</label></td>
               </tr>";
          $i = 0;
          foreach($deptServers as $server){
            if ($server->getVar('active')) {
                $activ_link = '".GTD_ADMIN_URL."/department.php?op=activateMailbox&amp;setstate=0&amp;id='. $server->getVar('id');
                $activ_img = $imagearray['online'];
                $activ_title = _AM_GTD_MESSAGE_DEACTIVATE;
            } else {
                $activ_link = '".GTD_ADMIN_URL."/department.php?op=activateMailbox&amp;setstate=1&amp;id='. $server->getVar('id');
                $activ_img = $imagearray['offline'];
                $activ_title = _AM_GTD_MESSAGE_ACTIVATE;
            }

            echo '<tr class="even">
                   <td>'.$server->getVar('emailaddress').'</td>
                   <td>'.gtdGetMBoxType($server->getVar('mboxtype')).'</td>
                   <td>'.$server->getVar('server').'</td>
                   <td>'.$server->getVar('serverport').'</td>
                   <td> <a href="'. $activ_link.'" title="'. $activ_title.'">'. $activ_img.'</a>
                        <a href="'.GTD_ADMIN_URL.'/department.php?op=EditDepartmentServer&amp;id='.$server->GetVar('id').'">'.$imagearray['editimg'].'</a>
                        <a href="'.GTD_ADMIN_URL.'/department.php?op=DeleteDepartmentServer&amp;id='.$server->GetVar('id').'">'.$imagearray['deleteimg'].'</a>
                        
                   </td>
                 </tr>';
          }
          echo '</table>';
        }
        //finally add Mailbox form
        echo "<br /><br />";
        
        $formElements = array('type_select', 'server_text', 'port_text', 'username_text', 'pass_text', 'genre_radio',
                              'email_text', 'btn_tray');
        $form = new gtdForm(_AM_GTD_DEPARTMENT_ADD_SERVER, 'add_server', gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'AddDepartmentServer', 'id' => $deptID)));
        
        $type_select =& new XoopsFormSelect(_AM_GTD_DEPARTMENT_SERVERS_TYPE, 'mboxtype');
        $type_select->setExtra("id='mboxtype'");
        $type_select->addOption(_GTD_MAILBOXTYPE_POP3, _AM_GTD_MBOX_POP3);
        
        $server_text =& new XoopsFormText(_AM_GTD_DEPARTMENT_SERVERS_SERVERNAME, 'server', 40, 50);
        $server_text->setExtra("id='txtServer'");
        
        $port_text =& new XoopsFormText(_AM_GTD_DEPARTMENT_SERVERS_PORT, 'port', 5, 5, "110");
        $port_text->setExtra("id='txtPort'");
        
        $username_text =& new XoopsFormText(_AM_GTD_DEPARTMENT_SERVER_USERNAME, 'username', 25, 50);
        $username_text->setExtra("id='txtUsername'");
        
        $pass_text =& new XoopsFormText(_AM_GTD_DEPARTMENT_SERVER_PASSWORD, 'password', 25, 50);
        $pass_text->setExtra("id='txtPassword'");
        
        $genre_radio =& new gtdFormRadio(_AM_GTD_DEPARTMENT_SERVERS_GENRE, 'genre', GTD_DEFAULT_GENRE);
        $genre_array = array('1' => "<label for='genre1'><img src='".GTD_IMAGE_URL."/genre1.png' title='". gtdGetPriority(1)."' alt='genre1' /></label>", 
                                '2' => "<label for='genre2'><img src='".GTD_IMAGE_URL."/genre2.png' title='". gtdGetPriority(2)."' alt='genre2' /></label>", 
                                '3' => "<label for='genre3'><img src='".GTD_IMAGE_URL."/genre3.png' title='". gtdGetPriority(3)."' alt='genre3' /></label>",
                                '4' => "<label for='genre4'><img src='".GTD_IMAGE_URL."/genre4.png' title='". gtdGetPriority(4)."' alt='genre4' /></label>", 
                                '5' => "<label for='genre5'><img src='".GTD_IMAGE_URL."/genre5.png' title='". gtdGetPriority(5)."' alt='genre5' /></label>");
        $genre_radio->addOptionArray($genre_array);
        
        $email_text =& new XoopsFormText(_AM_GTD_DEPARTMENT_SERVER_EMAILADDRESS, 'emailaddress', 50, 255);
        $email_text->setExtra("id='txtEmailaddress'");
        
        $btn_tray = new XoopsFormElementTray('');
        $test_button =& new XoopsFormButton('', 'email_test', _AM_GTD_BUTTON_TEST, 'button');
        $test_button->setExtra("id='test'");
        $submit_button =& new XoopsFormButton('', 'updateDept2', _AM_GTD_BUTTON_SUBMIT, 'submit');
        $cancel2_button =& new XoopsFormButton('', 'cancel2', _AM_GTD_BUTTON_CANCEL, 'button');
        $cancel2_button->setExtra("onclick='history.go(-1)'");
        $btn_tray->addElement($test_button);
        $btn_tray->addElement($submit_button);
        $btn_tray->addElement($cancel2_button);
        
        $form->setLabelWidth('20%');
        foreach($formElements as $element){
            $form->addElement($$element);
        }
        echo $form->render();

        echo "<script type=\"text/javascript\" language=\"javascript\">
          <!--
          function gtdEmailTest()
          {
            pop = openWithSelfMain(\"\", \"email_test\", 250, 150);
            frm = xoopsGetElementById(\"add_server\");
            newaction = \"department.php?op=testMailbox\";
            oldaction = frm.action;
            frm.action = newaction;
            frm.target = \"email_test\";
            frm.submit();
            frm.action = oldaction;
            frm.target = \"main\";
            
          }
          
          gtdDOMAddEvent(xoopsGetElementById(\"email_test\"), \"click\", gtdEmailTest, false);
          
          //-->
          </script>";
        gtd_adminFooter();
        xoops_cp_footer();
    }
}

function EditDepartmentServer()
{
    if(isset($_GET['id'])){
        $id = intval($_GET['id']);
    } else {
        redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3);       // TODO: Make message for no mbox_id
    }
    
    $hDeptServers =& gtdGetHandler('departmentMailBox');
    $deptServer =& $hDeptServers->get($id);
    
    if(isset($_POST['updateMailbox'])){
        $deptServer->setVar('emailaddress', $_POST['emailaddress']);
        $deptServer->setVar('server',       $_POST['server']);
        $deptServer->setVar('serverport',   $_POST['port']);
        $deptServer->setVar('username',     $_POST['username']);
        $deptServer->setVar('password',     $_POST['password']);
        $deptServer->setVar('genre',     $_POST['genre']);
        $deptServer->setVar('active',       $_POST['activity']);
        
        if($hDeptServers->insert($deptServer)){
            header("Location: ".GTD_ADMIN_URL."/department.php?op=editDepartment&deptid=".$deptServer->getVar('departmentid'));
        } else {
            redirect_header(GTD_ADMIN_URL."/department.php?op=editDepartment&deptid=".$deptServer->getVar('departmentid'),3);
        }
    } else {
        global $oAdminButton;
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manDept');
        echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
        echo "<form method='post' id='edit_server' action='department.php?op=EditDepartmentServer&amp;id=".$id."'>
               <table width='100%' cellspacing='1' class='outer'>   
                 <tr>
                   <th colspan='2'><label>". _AM_GTD_DEPARTMENT_EDIT_SERVER ."</label></th>
                 </tr>
                 <tr>
                   <td class='head' width='20%'><label for='mboxtype'>"._AM_GTD_DEPARTMENT_SERVERS_TYPE."</label></td>
                   <td class='even'>
                     <select name='mboxtype' id='mboxtype' onchange='gtdPortOnChange(this.options[this.selectedIndex].text, \"txtPort\")'>
                       <option value='"._GTD_MAILBOXTYPE_POP3."'>"._AM_GTD_MBOX_POP3."</option>
                       <!--<option value='"._GTD_MAILBOXTYPE_IMAP."'>"._AM_GTD_MBOX_IMAP."</option>-->
                     </select>
                   </td>
                 </tr>
                 <tr>
                   <td class='head'><label for='txtServer'>"._AM_GTD_DEPARTMENT_SERVERS_SERVERNAME."</label></td>
                   <td class='even'><input type='text' id='txtServer' name='server' value='".$deptServer->getVar('server')."' size='40' maxlength='50' />
                 </tr>                 
                 <tr>
                   <td class='head'><label for='txtPort'>"._AM_GTD_DEPARTMENT_SERVERS_PORT."</label></td>
                   <td class='even'><input type='text' id='txtPort' name='port' maxlength='5' size='5' value='".$deptServer->getVar('serverport')."' />
                 </tr>
                 <tr>
                   <td class='head'><label for='txtUsername'>"._AM_GTD_DEPARTMENT_SERVER_USERNAME."</label></td>
                   <td class='even'><input type='text' id='txtUsername' name='username' value='".$deptServer->getVar('username')."' size='25' maxlength='50' />
                 </tr>
                 <tr>
                   <td class='head'><label for='txtPassword'>"._AM_GTD_DEPARTMENT_SERVER_PASSWORD."</label></td>
                   <td class='even'><input type='text' id='txtPassword' name='password' value='".$deptServer->getVar('password')."' size='25' maxlength='50' />
                 </tr>                 
                 <tr>
                   <td width='38%' class='head'><label for='txtPriority'>"._AM_GTD_DEPARTMENT_SERVERS_GENRE."</label></td>
                   <td width='62%' class='even'>";
        for($i = 1; $i < 6; $i++) {
                   $checked = '';
                   if($deptServer->getVar('genre') == $i){
                       $checked = 'checked="checked"';
                   }
                   echo("<input type=\"radio\" value=\"$i\" id=\"genre$i\" name=\"genre\" $checked />");
                   echo("<label for=\"genre$i\"><img src=\"../images/genre$i.png\" title=\"". gtdGetPriority($i)."\" alt=\"genre$i\" /></label>");
        }
                   echo "</td>
                 </tr>
                 <tr>
                   <td class='head'><label for='txtEmailaddress'>"._AM_GTD_DEPARTMENT_SERVER_EMAILADDRESS."</label></td>
                   <td class='even'><input type='text' id='txtEmailaddress' name='emailaddress' value='".$deptServer->getVar('emailaddress')."' size='50' maxlength='255' />
                 </tr>
                 <tr>
                   <td class='head'><label for='txtActive'>"._AM_GTD_TEXT_ACTIVITY."</label></td>
                   <td class='even'>";
                            if($deptServer->getVar('active') == 1){
                                echo "<input type='radio' value='1' name='activity' checked='checked' />"._AM_GTD_TEXT_ACTIVE."
                                      <input type='radio' value='0' name='activity' />"._AM_GTD_TEXT_INACTIVE;
                            } else {
                                echo "<input type='radio' value='1' name='activity' />"._AM_GTD_TEXT_ACTIVE."
                                      <input type='radio' value='0' name='activity' checked='checked' />"._AM_GTD_TEXT_INACTIVE;
                            }
                                    
                 echo "</td>
                 </tr>
    
                 <tr class='foot'>
                   <td colspan='2'><div align='right'><span >
                       <input type='button' id='email_test' name='test' value='"._AM_GTD_BUTTON_TEST."' class='formButton' />
                       <input type='submit' name='updateMailbox' value='"._AM_GTD_BUTTON_SUBMIT."' class='formButton' />
                       <input type='button' name='cancel' value='"._AM_GTD_BUTTON_CANCEL."' onclick='history.go(-1)' class='formButton' />
                   </span></div></td>
                 </tr>
               </table>
             </form>";
        echo "<script type=\"text/javascript\" language=\"javascript\">
          <!--
          function gtdEmailTest()
          {
            pop = openWithSelfMain(\"\", \"email_test\", 250, 150);
            frm = xoopsGetElementById(\"edit_server\");
            newaction = \"department.php?op=testMailbox\";
            oldaction = frm.action;
            frm.action = newaction;
            frm.target = \"email_test\";
            frm.submit();
            frm.action = oldaction;
            frm.target = \"main\";
            
          }
          
          gtdDOMAddEvent(xoopsGetElementById(\"email_test\"), \"click\", gtdEmailTest, false);
          
          //-->
          </script>";
          gtd_adminFooter();
          xoops_cp_footer();
    }
}

function manageDepartments()
{
    global $xoopsModule, $oAdminButton, $aSortBy, $aOrderBy, $aLimitBy, $order, $limit, $start, $sort;
    $module_id = $xoopsModule->getVar('mid');
    
    $hGroups =& xoops_gethandler('group');
    $hGroupPerm =& xoops_gethandler('groupperm');
    
    if(isset($_POST['addDept'])){
        $hasErrors = false;
        $errors = array();
        $groups = (isset($_POST['groups']) ? $_POST['groups'] : array());
        $hDepartments  =& gtdGetHandler('department');
        
        //Department Name supplied?
        if (trim($_POST['newDept']) == '') {
            $hasErrors = true;
            $errors['newDept'][] = _AM_GTD_MESSAGE_NO_DEPT;
        } else {
        
            //Department Name unique?
            $crit = new Criteria('department', $_POST['newDept']);
            if($existingDepts = $hDepartments->getCount($crit)){
                $hasErrors = true;
                $errors['newDept'][] = _GTD_MESSAGE_DEPT_EXISTS;
                
            }
        }
        
        if ($hasErrors) {
            $session =& Session::singleton();
            //Store existing dept info in session, reload addition page
            $aDept = array();
            $aDept['newDept'] = $_POST['newDept'];
            $aDept['groups'] = $groups;
            $session->set('gtd_addDepartment', $aDept);
            $session->set('gtd_addDepartmentErrors', $errors);
            header('Location: '. gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'manageDepartments'), false));
            exit();
        }
        
        $department =& $hDepartments->create();
        $department->setVar('department', $_POST['newDept']);
		$department->setVar('tarif_1', $_POST['tarif_1']);
		$department->setVar('tarif_2', $_POST['tarif_2']);
		$department->setVar('tarif_3', $_POST['tarif_3']);
/*
echo "tarif1 = " . $_POST['tarif_1'] . "<br/>";
print_r($department);
global $xoopsLogger;
$xoopsLogger->addExtra('CREATE', 'Avant insertion du dept');
*/
        if($hDepartments->insert($department)){
            $deptID = $department->getVar('id');
            foreach($groups as $group){     // Add new group permissions
                $hGroupPerm->addRight(_GTD_GROUP_PERM_DEPT, $deptID, $group, $module_id);
            }
            
            // Set as default department?
            if(isset($_POST['defaultDept']) && ($_POST['defaultDept'] == 1)){
                gtdSetMeta("default_department", $deptID);
            }
            
            $hStaff =& gtdGetHandler('staff');
            $allDeptStaff =& $hStaff->getByAllDepts();
            if (count($allDeptStaff) > 0) {
                $hMembership =& gtdGetHandler('membership');
                if($hMembership->addStaffToDept($allDeptStaff, $department->getVar('id'))){
                    $message = _GTD_MESSAGE_ADD_DEPT;
                } else {
                    $message = _AM_GTD_MESSAGE_STAFF_UPDATE_ERROR;
                }
            } else {
                $message = _GTD_MESSAGE_ADD_DEPT;
            }
            
            // Add configoption for new department
            $hConfig =& xoops_gethandler('config');
            $hConfigOption =& xoops_gethandler('configoption');
            
            $crit = new Criteria('conf_name', 'gtd_defaultDept');
            $config =& $hConfig->getConfigs($crit);
            
            if(count($config) > 0){
                $newOption =& $hConfigOption->create();
                $newOption->setVar('confop_name', $department->getVar('department'));
                $newOption->setVar('confop_value', $department->getVar('id'));
                $newOption->setVar('conf_id', $config[0]->getVar('conf_id'));
                
                if(!$hConfigOption->insert($newOption)){
                    redirect_header(GTD_ADMIN_URL."/department.php?op=manageDepartments", 3, _AM_GTD_MSG_ADD_CONFIG_ERR);
                }
            }
            _clearAddSessionVars();
            header("Location: ".GTD_ADMIN_URL."/department.php?op=manageDepartments");
        } else {
            $message = _GTD_MESSAGE_ADD_DEPT_ERROR . $department->getHtmlErrors();
        }
        
        $deptID = $department->getVar('id');
        
        /* Not sure if this is needed. Already exists in if block above (ej)
        foreach($groups as $group){
            $hGroupPerm->addRight(_GTD_GROUP_PERM_DEPT, $deptID, $group, $module_id);
        }
        */
        
        redirect_header(GTD_ADMIN_URL.'/department.php?op=manageDepartments', 3, $message);
     } else {     
        $hDepartments  =& gtdGetHandler('department');
        $crit = new Criteria('','');
        $crit->setOrder($order);
        $crit->setSort($sort);
        $crit->setLimit($limit);
        $crit->setStart($start);
        $total = $hDepartments->getCount($crit);
        $departmentInfo =& $hDepartments->getObjects($crit);
        
        $nav = new XoopsPageNav($total, $limit, $start, 'start', "op=manageDepartments&amp;limit=$limit");
        
        // Get list of all groups
        $crit = new Criteria('', '');
        $crit->setSort('name');
        $crit->setOrder('ASC');
        $groups =& $hGroups->getObjects($crit, true);
        
        $aGroups = array();
        foreach($groups as $group_id=>$group){
            $aGroups[$group_id] = $group->getVar('name');
        }
        asort($aGroups);    // Set groups in alphabetical order
        
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manDept');
        
        $session =& Session::singleton();
        $sess_dept = $session->get('gtd_addDepartment');
        $sess_errors = $session->get('gtd_addDepartmentErrors');
        
        //Display any form errors
        if (! $sess_errors === false) {
            gtdRenderErrors($sess_errors, gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'clearAddSession'), false));
        }
        
        if (! $sess_dept === false) {
            $fld_newDept = $sess_dept['newDept'];
            $fld_groups  = $sess_dept['groups'];
        } else {
            $fld_newDept = '';
            $fld_groups = array();
        }
        
        echo "<form method='post' action='".GTD_ADMIN_URL."/department.php?op=manageDepartments'>";
        echo "<table width='100%' cellspacing='1' class='outer'>
              <tr><th colspan='2'><label for='newDept'>". _AM_GTD_LINK_ADD_DEPT ." </label></th></tr>";
        echo "<tr><td class='head' width='20%' valign='top'>". _AM_GTD_TEXT_NAME ."</td><td class='even'>";
        echo "<input type='text' id='newDept' name='newDept' class='formButton' value='$fld_newDept' /></td></tr>";
        echo "<tr><td class='head' width='20%' valign='top'>"._AM_GTD_TEXT_EDIT_DEPT_PERMS."</td><td class='even'>";
        echo "<select name='groups[]' multiple='multiple'>";
                  foreach($aGroups as $group_id=>$group){
                      if (in_array($group_id, $fld_groups, true)) {
                        echo "<option value='$group_id' selected='selected'>$group</option>";
                      } else {
                        echo "<option value='$group_id'>$group</option>";
                      }
                  }
        echo "</select></td></tr>";
        echo "<tr><td class='head' width='20%' valign='top'>"._AM_GTD_TEXT_TARIF_EDIT_1."</td>
					<td class=\"even\"> <input type='text' name='tarif_1' id='tarif_1' value='0.00' /> </td></tr>";
		echo "<tr><td class='head' width='20%' valign='top'>"._AM_GTD_TEXT_TARIF_EDIT_2."</td>
					<td class=\"even\"> <input type='text' name='tarif_2' id='tarif_2' value='0.00' /> </td></tr>";
		echo "<tr><td class='head' width='20%' valign='top'>"._AM_GTD_TEXT_TARIF_EDIT_3."</td>
					<td class=\"even\"> <input type='text' name='tarif_3' id='tarif_3' value='0.00' /> </td></tr>";
        echo "<tr><td class='head' width='20%' valign='top'>"._AM_GTD_TEXT_DEFAULT_DEPT."?</td>
                  <td class='even'><input type='checkbox' name='defaultDept' id='defaultDept' value='1' /></td></tr>";
        echo "<tr><td class='foot' colspan='2'><input type='submit' name='addDept' value='"._AM_GTD_BUTTON_SUBMIT."' class='formButton' /></td></tr>";
        echo "</table><br />";
        echo "</form>";
        if($total > 0){     // Make sure there are departments
            echo "<form action='". GTD_ADMIN_URL."/department.php?op=manageDepartments' style='margin:0; padding:0;' method='post'>";
            echo "<table width='100%' cellspacing='1' class='outer'>";
            echo "<tr><td align='right'>"._AM_GTD_TEXT_SORT_BY." 
                          <select name='sort'>";
                        foreach($aSortBy as $value=>$text){
                            ($sort == $value) ? $selected = "selected='selected'" : $selected = '';
                            echo "<option value='$value' $selected>$text</option>";
                        }
                        echo "</select>
                        &nbsp;&nbsp;&nbsp;
                          "._AM_GTD_TEXT_ORDER_BY."
                          <select name='order'>";
                        foreach($aOrderBy as $value=>$text){
                            ($order == $value) ? $selected = "selected='selected'" : $selected = '';
                            echo "<option value='$value' $selected>$text</option>";
                        }
                        echo "</select>
                          &nbsp;&nbsp;&nbsp;
                          "._AM_GTD_TEXT_NUMBER_PER_PAGE."
                          <select name='limit'>";
                        foreach($aLimitBy as $value=>$text){
                            ($limit == $value) ? $selected = "selected='selected'" : $selected = '';
                            echo "<option value='$value' $selected>$text</option>";
                        }
                        echo "</select>
                          <input type='submit' name='dept_sort' id='dept_sort' value='"._AM_GTD_BUTTON_SUBMIT."' />
                      </td>
                  </tr>";
            echo "</table></form>";
            echo "<table width='100%' cellspacing='1' class='outer'>
                  <tr><th colspan='4'>"._AM_GTD_EXISTING_DEPARTMENTS."</th></tr>
                  <tr><td class='head'>"._AM_GTD_TEXT_ID."</td><td class='head'>"._AM_GTD_TEXT_DEPARTMENT."</td><td class='head'>"._AM_GTD_TEXT_DEFAULT."</td><td class='head'>"._AM_GTD_TEXT_ACTIONS."</td></tr>";
                  
            if(isset($departmentInfo)){
                $defaultDept = gtdGetMeta("default_department");
                foreach($departmentInfo as $dept){
                    echo "<tr><td class='even'>". $dept->getVar('id')."</td><td class='even'>". $dept->getVar('department') ."</td>";
                    if($dept->getVar('id') != $defaultDept){
                        echo "<td class='even' width='10%'><a href='".GTD_ADMIN_URL."/department.php?op=updateDefault&amp;id=".$dept->getVar('id')."'><img src='".GTD_IMAGE_URL."/off.png' alt='"._AM_GTD_TEXT_MAKE_DEFAULT_DEPT."' title='"._AM_GTD_TEXT_MAKE_DEFAULT_DEPT."' /></a></td>";
                    } else {
                        echo "<td class='even' width='10%'><img src='".GTD_IMAGE_URL."/on.png'</td>";
                    }
                    //echo "<td class='even' width='10%'><img src='".GTD_IMAGE_URL."/". (($dept->getVar('id') == $defaultDept) ? "on.png" : "off.png")."'</td>";
                    echo "<td class='even' width='70'><a href='".GTD_ADMIN_URL."/department.php?op=editDepartment&amp;deptid=".$dept->getVar('id')."'><img src='".XOOPS_URL."/modules/gtd/images/button_edit.png' title='"._AM_GTD_TEXT_EDIT."' name='editDepartment' /></a>&nbsp;&nbsp;";
                    echo "<a href='".GTD_ADMIN_URL."/delete.php?deleteDept=1&amp;deptid=".$dept->getVar('id')."'><img src='".XOOPS_URL."/modules/gtd/images/button_delete.png' title='"._AM_GTD_TEXT_DELETE."' name='deleteDepartment' /></a></td></tr>";
                }
                                    
            }
        }
        echo "</td></tr></table>";
        echo "<div id='dept_nav'>".$nav->renderNav()."</div>";
        gtd_adminFooter();
        xoops_cp_footer();
    }
}

function testMailbox()
{
    $hDeptServers =& gtdGetHandler('departmentMailBox');
    $server = $hDeptServers->create();
    $server->setVar('emailaddress', $_POST['emailaddress']);
    $server->setVar('server',       $_POST['server']);
    $server->setVar('serverport',   $_POST['port']);
    $server->setVar('username',     $_POST['username']);
    $server->setVar('password',     $_POST['password']);
    $server->setVar('genre',     $_POST['genre']);    
    echo "<html>";
    echo "<head>";
    echo "<link rel='stylesheet' type='text/css' media'screen' href='".XOOPS_URL."/xoops.css' />
          <link rel='stylesheet' type='text/css' media='screen' href='". xoops_getcss() ."' />
          <link rel='stylesheet' type='text/css' media='screen' href='".XOOPS_URL."/modules/system/style.css' />";
    echo "</head>";
    echo "<body>";      
    echo "<table style='margin:0; padding:0;' class='outer'>";
    if (@$server->connect()) {
        //Connection Succeeded
        echo "<tr><td class='head'>Connection Successful!</td></tr>";
    } else {
        //Connection Failed
        echo "<tr class='head'><td>Connection Failed!</td></tr>";
        echo "<tr class='even'><td>". $server->getHtmlErrors()."</td></tr>";
    }
    echo "</table>";
    echo "</body>";
    echo "</html>";
}

function clearAddSession()
{
    _clearAddSessionVars();
    header('Location: ' . gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'manageDepartments'), false));
}

function _clearAddSessionVars()
{
    $session = Session::singleton();
    $session->del('gtd_addDepartment');
    $session->del('gtd_addDepartmentErrors');
}

function clearEditSession()
{
    $deptid = $_REQUEST['deptid'];
    _clearEditSessionVars($deptid);
    header('Location: ' . gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'editDepartment', 'deptid'=>$deptid), false));
}

function _clearEditSessionVars($id)
{
    $id = intval($id);
    $session = Session::singleton();
    $session->del("gtd_editDepartment_$id");
    $session->del("gtd_editDepartmentErrors_$id");
}

function updateDefault()
{
    $id = intval($_REQUEST['id']);
    gtdSetMeta("default_department", $id);
    header('Location: '. gtdMakeURI(GTD_ADMIN_URL.'/department.php', array('op'=>'manageDepartments'), false));
}
?>