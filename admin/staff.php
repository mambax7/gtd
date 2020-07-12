<?php
//$Id: staff.php,v 1.18 2005/10/17 18:13:30 eric_juden Exp $
include('../../../include/cp_header.php');          
include_once('admin_header.php');           
include_once(GTD_CLASS_PATH.'/gtdPageNav.php');

global $xoopsModule, $xoopsModuleConfig;
$module_id = $xoopsModule->getVar('mid');
$displayName =& $xoopsModuleConfig['gtd_displayName'];    // Determines if username or real name is displayed

$op = 'default';

if ( isset( $_REQUEST['op'] ) )
{
    $op = $_REQUEST['op'];
}

switch ( $op )
{
    case "addRole":
        addRole();
        break;
        
    case "clearOrphanedStaff":
        clearOrphanedStaff();
        break;
        
    case "clearRoles":
        clearRoles();
        break;
        
    case "customDept":
        customDept();
        break;
        
    case "editRole":
        editRole();
        break;
    
    case "editStaff":
        editStaff();
        break;
    
    case "manageStaff":
        manageStaff();
        break;
    
    default:
        header("Location: ".GTD_BASE_URL."/admin/index.php");
        break;
}

function addRole()
{
    require_once(GTD_CLASS_PATH.'/session.php');
    $_gtdSession = new Session();
    global $oAdminButton;
    if(!isset($_POST['add'])){
        // Set array of security items  
        $tasks = array(_AM_GTD_SEC_TICKET_ADD         => _AM_GTD_SEC_TEXT_TICKET_ADD, 
                       _AM_GTD_SEC_TICKET_EDIT        => _AM_GTD_SEC_TEXT_TICKET_EDIT,
                       _AM_GTD_SEC_TICKET_DELETE      => _AM_GTD_SEC_TEXT_TICKET_DELETE,
                       _AM_GTD_SEC_TICKET_MERGE       => _AM_GTD_SEC_TEXT_TICKET_MERGE,
                       _AM_GTD_SEC_TICKET_OWNERSHIP   => _AM_GTD_SEC_TEXT_TICKET_OWNERSHIP,
                       _AM_GTD_SEC_TICKET_STATUS      => _AM_GTD_SEC_TEXT_TICKET_STATUS,
                       _AM_GTD_SEC_TICKET_GENRE    => _AM_GTD_SEC_TEXT_TICKET_GENRE,
                       _AM_GTD_SEC_TICKET_LOGUSER     => _AM_GTD_SEC_TEXT_TICKET_LOGUSER,
                       _AM_GTD_SEC_RESPONSE_ADD       => _AM_GTD_SEC_TEXT_RESPONSE_ADD,
                       _AM_GTD_SEC_RESPONSE_EDIT      => _AM_GTD_SEC_TEXT_RESPONSE_EDIT,
                       _AM_GTD_SEC_FILE_DELETE        => _AM_GTD_SEC_TEXT_FILE_DELETE);
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manStaff');
        echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
        echo "<form action='staff.php?op=addRole' method='post'>";
        echo "<table width='100%' cellspacing='1' class='outer'>";
        echo "<tr><th colspan='2'>"._AM_GTD_TEXT_CREATE_ROLE."</th></tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_NAME."</td>
                  <td class='even'><input type='text' name='roleName' maxlength='35' value='' class='formButton'></td>
              </tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_DESCRIPTION."</td>
                  <td class='even'><textarea name='roleDescription' class='formButton'></textarea></td>
              </tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_PERMISSIONS."</td>
                  <td class='even'>
                     <table border='0'>
                     <tr><td>";
                     foreach($tasks as $bit_value => $task){
                         echo "<tr><td><input type='checkbox' name='tasks[]' value='". pow(2,$bit_value) ."' />".$task."</td></tr>";
                     }
                     echo "<tr><td><input type='checkbox' name='allTasks' value='0' onclick='selectAll(this.form,\"tasks[]\",this.checked);' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
                     echo "</table>
                  </td>
              </tr>";
        echo "<tr>
                  <td colspan='2' class='foot'>
                      <input type='submit' name='add' value='". _AM_GTD_BUTTON_CREATE_ROLE ."' class='formButton'>
                      <input type='button' name='cancel' value='"._AM_GTD_BUTTON_CANCEL."' onclick='history.go(-1)' class='formButton' />                  
                  </td>
              </tr>";
        echo "</table></form>";
        gtd_adminFooter();
        xoops_cp_footer();
    } else {
        $hRole =& gtdGetHandler('role');
        
        $role =& $hRole->create();
        $role->setVar('name', $_POST['roleName']);
        $role->setVar('description', $_POST['roleDescription']);
        if(isset($_POST['tasks'])){
            $tasksValue = array_sum($_POST['tasks']);
        } else {
            $tasksValue = 0;
        }
        $role->setVar('tasks', $tasksValue);
        
        $lastPage = $_gtdSession->get("gtd_return_op");
        
        if($hRole->insert($role)){
            $message = _AM_GTD_MESSAGE_ROLE_INSERT;
            header("Location: ".GTD_ADMIN_URL."/staff.php?op=$lastPage");
        } else {
            $message = _AM_GTD_MESSAGE_ROLE_INSERT_ERROR;
            redirect_header(GTD_ADMIN_URL."/staff.php?op=$lastPage", 3, $message);
        }
    }
}

function clearOrphanedStaff()
{
    $hMember =& xoops_gethandler('member');
    $hStaff =& gtdGetHandler('staff');
    $users =& $hMember->getUserList();
    $staff =& $hStaff->getObjects();
    
    $aUsers = array();
    foreach($staff as $stf){
        $staff_uid = $stf->getVar('uid');
        if(!array_key_exists($staff_uid, $users)){
            $aUsers[$staff_uid] = $staff_uid;
        }
    }
    
    $crit = new Criteria('uid', "(". implode($aUsers, ',') .")", "IN");
    $ret = $hStaff->deleteAll($crit);
    
    if($ret){
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=manageStaff");
    } else {
        redirect_header(GTD_ADMIN_URL."/staff.php?op=manageStaff", 3, _AM_GTD_MSG_CLEAR_ORPHANED_ERR);
    }
}

function clearRoles()
{
    require_once(GTD_CLASS_PATH.'/session.php');
    $_gtdSession = new Session();
    
    $hDept =& gtdGetHandler('department');
    $depts =& $hDept->getObjects();
    
    foreach($depts as $dept){
        $deptid = $dept->getVar('id');
        if($deptRoles = $_gtdSession->get("gtd_dept_$deptid")){
            $_gtdSession->del("gtd_dept_$deptid");
        }
    }
    
    if(!$returnPage =& $_gtdSession->get("gtd_return_page")){
        $returnPage = false;
    }
    
    $_gtdSession->del("gtd_return_page");
    $_gtdSession->del("gtd_mainRoles");
    $_gtdSession->del("gtd_mainDepts");
    $_gtdSession->del("gtd_return_op");
    
    if(!$returnPage){
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=manageStaff");
    } else {
        header("Location: ".GTD_ADMIN_URL."/$returnPage");
    }
    exit();
}

function customDept()
{
    require_once(GTD_CLASS_PATH.'/session.php');
    $_gtdSession = new Session();
    global $xoopsUser, $displayName;
    
    $lastPage = $_gtdSession->get("gtd_return_op");
    
    if(isset($_REQUEST['uid'])){
        $uid = intval($_REQUEST['uid']);
    } else {
        $uid = 0;
    }
    if($uid == 0){
        redirect_header(GTD_ADMIN_URL."/staff.php?op=$lastPage", 3, _AM_GTD_MSG_NEED_UID);
    }
    if(isset($_REQUEST['deptid'])){
        $deptid = intval($_REQUEST['deptid']);
    }
    
    if(!isset($_POST['submit'])){
        if(isset($_POST['addRole'])){
            
            $_gtdSession->set("gtd_return_op2", $lastPage);
            $_gtdSession->set("gtd_return_op", substr(strstr($_SERVER['REQUEST_URI'], 'op='), 3));
            header("Location: ".GTD_ADMIN_URL."/staff.php?op=addRole");
        }
        
        if(isset($_GET['gtd_role'])){
            $aRoles = explode(",", $_GET['gtd_role']);
            foreach($aRoles as $role){
                $role = intval($role);
            }
            $_gtdSession->set("gtd_mainRoles", $aRoles);    // Store roles from the manage staff page
        }
        
        if(isset($_GET['gtd_depts'])){
            $aDepts = explode(",", $_GET['gtd_depts']);
            foreach($aDepts as $dept){
                $dept = intval($dept);
            }
            $_gtdSession->set("gtd_mainDepts", $aDepts);    // Store depts from the manage staff page
        }
        
        $hDept =& gtdGetHandler('department');
        $hRole =& gtdGetHandler('role');
        
        $dept =& $hDept->get($deptid);
        
        $crit = new Criteria('', '');
        $crit->setOrder('ASC');
        $crit->setSort('name');
        $roles =& $hRole->getObjects($crit);
        
        $lastPage = $_gtdSession->get("gtd_return_op");
        xoops_cp_header();
        echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
        echo "<form action='staff.php?op=customDept&amp;deptid=".$deptid."&amp;uid=".$uid."' method='post'>";
        echo "<table width='100%' cellspacing='1' class='outer'>";
        echo "<tr><th colspan='2'>"._AM_GTD_TEXT_DEPT_PERMS."</th></tr>";
        echo "<tr><td class='head' width='20%'>"._AM_GTD_TEXT_USER."</td>
                  <td class='even'>".gtdGetUsername($uid, $displayName)."</td></tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_DEPARTMENT."</td>
                  <td class='even'>".$dept->getVar('department')."</td></tr>";
        echo "<tr><td class='head'>". _AM_GTD_TEXT_ROLES ."</td>
                  <td class='even'><table width='75%'>";
                  
                  $bFound = false;
                  if($storedRoles =& $_gtdSession->get("gtd_dept_$deptid")){    // If editing previously customized dept
                      foreach ($roles as $role){
                          if($storedRoles['roles'] != -1){
                              foreach($storedRoles['roles'] as $storedRole){
                                  if($role->getVar('id') == $storedRole){
                                      $bFound = true;
                                      break;
                                  } else {
                                      $bFound = false;
                                  }
                              }
                          }
                          if($bFound){
                              echo "<tr><td><input type='checkbox' name='roles[]' checked='checked' value='". $role->getVar('id')."' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                          } else {
                              echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                          }
                      }
                  } elseif($mainRoles = $_gtdSession->get("gtd_mainRoles")){    // If roles set on manage staff page
                      foreach($roles as $role){
                          if(!in_array($role->getVar('id'), $mainRoles)){
                              echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                          } else {
                              echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' checked='checked' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                          }
                      }
                  } elseif($lastPage == "editStaff" && (!$storedRoles =& $_gtdSession->get("gtd_dept_$deptid"))){
                      $hStaff =& gtdGetHandler('staff');
                      $myRoles =& $hStaff->getRolesByDept($uid, $deptid);
                      
                      $bFound = false;
                      foreach($roles as $role){
                          if(!empty($myRoles)){
                              foreach($myRoles as $myRole){
                                  if($role->getVar('id') == $myRole->getVar('roleid')){
                                      $bFound = true;
                                      break;
                                  } else {
                                      $bFound = false;
                                  }
                              }
                          }
                          if($bFound){
                              echo "<tr><td><input type='checkbox' name='roles[]' checked='checked' value='". $role->getVar('id')."' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";   
                          } else {
                              echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                          }
                      }
                  } else {
                      foreach($roles as $role){     // If no roles set
                          echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                      }
                  }
        echo "<tr><td><input type='checkbox' name='checkallRoles' value='0' onclick='selectAll(this.form,\"roles[]\",this.checked);' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
        echo "</table></td></tr>";
        echo "<tr><td colspan='2' class='foot'>
                      <input type='submit' name='submit' value='". _AM_GTD_BUTTON_UPDATE ."' class='formButton' />
                      <input type='submit' name='addRole' value='". _AM_GTD_BUTTON_CREATE_ROLE ."' class='formButton' />
                      <input type='button' name='cancel' value='"._AM_GTD_BUTTON_CANCEL."' onclick='history.go(-1)' class='formButton' />
                  </td>
              </tr>";
        echo "</table>";
        gtd_adminFooter();
        xoops_cp_footer();
    } else {
        $hRole =& gtdGetHandler('role');
        
        if(!empty($_POST['roles'])){
            foreach($_POST['roles'] as $role){
                $thisRole =& $hRole->get($role);
                $aRoleNames[] = $thisRole->getVar('name');
            }
        }
        
        $_gtdSession->set("gtd_dept_$deptid",       // Store roles for customized dept
                            array('id' => $deptid,
                                  'roles' => ((!empty($_POST['roles'])) ? $_POST['roles']: -1),
                                  'roleNames' => ((!empty($aRoleNames)) ? $aRoleNames : -1)
                                 ));
        
        $gtd_has_deptRoles = false;                                  
        if($hasRoles = $_gtdSession->get("gtd_dept_$deptid")){
            $gtd_has_deptRoles = true;  
            if($hasRoles['roles'] == -1){                   // No perms for this dept
                //$_gtdSession->del("gtd_dept_$deptid");  // Delete custom roles for dept
                $gtd_has_deptRoles = false;
            }
        }
          
        if($mainDepts = $_gtdSession->get("gtd_mainDepts")){
            if($gtd_has_deptRoles){           // If dept has roles
                if(!in_array($deptid, $mainDepts)){     // Does dept already exist in array?
                    array_push($mainDepts, $deptid);    // Add dept to array
                    $_gtdSession->set("gtd_mainDepts", $mainDepts); // Set session with new dept value
                } 
            } else {
                // Unset element in array with current dept value
                foreach($mainDepts as $dept){
                    if($dept == $deptid){
                        unset($dept);
                    }
                }
                $_gtdSession->set("gtd_mainDepts",$mainDepts);
            }
        } else {                        // If mainDepts is not set
            if($gtd_has_deptRoles){   // If dept has any roles
                $_gtdSession->set("gtd_mainDepts", array($deptid)); 
            }
        }

        if(!$lastPage = $_gtdSession->get("gtd_return_op2")){
            $lastPage = $_gtdSession->get("gtd_return_op");
        }
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=$lastPage&uid=$uid");
    }
}

function deleteRole($gtd_id, $return_op)
{
    
    $gtd_id = intval($gtd_id);
    
    $hRole =& gtdGetHandler('role');
    $role =& $hRole->get($gtd_id);
    
    if($hRole->delete($role, true)){
        $message = _AM_GTD_MESSAGE_ROLE_DELETE;
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=$return_op");
    } else {
        $message = _AM_GTD_MESSAGE_ROLE_DELETE_ERROR;
        redirect_header(GTD_ADMIN_URL."/staff.php?op=$return_op", 3, $message);
    }
}

function editRole()
{
    global $oAdminButton;
    require_once(GTD_CLASS_PATH.'/session.php');
    $_gtdSession = new Session();
    
    $lastPage = $_gtdSession->get("gtd_return_op");
    
    if(isset($_REQUEST['id'])){
        $gtd_id = intval($_REQUEST['id']);
    }
    
    if(isset($_REQUEST['uid'])){
        $uid = intval($_REQUEST['uid']);
    } else {
        $uid = 0;
    }
    
    $hRole =& gtdGetHandler('role');
    $role =& $hRole->get($gtd_id);
    
    if(isset($_POST['deleteRole'])){
        deleteRole($gtd_id, "manageStaff");
        exit();
    }
    
    if(!isset($_POST['edit'])){
        $_gtdSession->set("gtd_return_op2", $lastPage);
        $_gtdSession->set("gtd_return_op", substr(strstr($_SERVER['REQUEST_URI'], 'op='), 3));
        
        // Set array of security items  
        $tasks = array(_AM_GTD_SEC_TICKET_ADD         => _AM_GTD_SEC_TEXT_TICKET_ADD, 
                       _AM_GTD_SEC_TICKET_EDIT        => _AM_GTD_SEC_TEXT_TICKET_EDIT,
                       _AM_GTD_SEC_TICKET_DELETE      => _AM_GTD_SEC_TEXT_TICKET_DELETE,
                       _AM_GTD_SEC_TICKET_OWNERSHIP   => _AM_GTD_SEC_TEXT_TICKET_OWNERSHIP,
                       _AM_GTD_SEC_TICKET_STATUS      => _AM_GTD_SEC_TEXT_TICKET_STATUS,
                       _AM_GTD_SEC_TICKET_GENRE    => _AM_GTD_SEC_TEXT_TICKET_GENRE,
                       _AM_GTD_SEC_TICKET_LOGUSER     => _AM_GTD_SEC_TEXT_TICKET_LOGUSER,
                       _AM_GTD_SEC_RESPONSE_ADD       => _AM_GTD_SEC_TEXT_RESPONSE_ADD,
                       _AM_GTD_SEC_RESPONSE_EDIT      => _AM_GTD_SEC_TEXT_RESPONSE_EDIT,
                       _AM_GTD_SEC_TICKET_MERGE       => _AM_GTD_SEC_TEXT_TICKET_MERGE,
                       _AM_GTD_SEC_FILE_DELETE        => _AM_GTD_SEC_TEXT_FILE_DELETE);
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manStaff');
        echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
        echo "<form action='staff.php?op=editRole&amp;id=".$gtd_id."&amp;uid=".$uid."' method='post'>";
        echo "<table width='100%' cellspacing='1' class='outer'>";
        echo "<tr><th colspan='2'>"._AM_GTD_TEXT_EDIT_ROLE."</th></tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_NAME."</td>
                  <td class='even'><input type='text' name='roleName' maxlength='35' value='".$role->getVar('name')."' class='formButton'></td>
              </tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_DESCRIPTION."</td>
                  <td class='even'><textarea name='roleDescription' class='formButton'>".$role->getVar('description')."</textarea></td>
              </tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_PERMISSIONS."</td>
                  <td class='even'>
                     <table border='0'>
                     <tr><td>";
                     foreach($tasks as $bit_value => $task){
                         if(($role->getVar('tasks') & pow(2, $bit_value)) > 0){
                            echo "<tr><td><input type='checkbox' name='tasks[]' value='". pow(2,$bit_value) ."' checked='checked' />".$task."</td></tr>";
                         } else {
                            echo "<tr><td><input type='checkbox' name='tasks[]' value='". pow(2,$bit_value) ."' />".$task."</td></tr>";
                         }
                     }
                     echo "<tr><td><input type='checkbox' name='allTasks' value='0' onclick='selectAll(this.form,\"tasks[]\",this.checked);' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
                     echo "</table>
                  </td>
              </tr>";
        echo "<tr>
                  <td colspan='2' class='foot'>
                      <input type='submit' name='edit' value='". _AM_GTD_BUTTON_UPDATE ."' class='formButton' />
                      <input type='button' name='cancel' value='"._AM_GTD_BUTTON_CANCEL."' onclick='history.go(-1)' class='formButton' />                  
                      <input type='submit' name='deleteRole' value='"._AM_GTD_BUTTON_DELETE."' class='formButton' />
                      
                  </td>
              </tr>";
        echo "</table></form>";
        gtd_adminFooter();
        xoops_cp_footer();
    } else {     
        $role->setVar('name', $_POST['roleName']);
        $role->setVar('description', $_POST['roleDescription']);
        if(isset($_POST['tasks'])){
            $tasksValue = array_sum($_POST['tasks']);
        } else {
            $tasksValue = 0;
        }
        $role->setVar('tasks', $tasksValue);
        
        if(!$lastPage = $_gtdSession->get("gtd_return_op2")){
            $lastPage = $_gtdSession->get("gtd_return_op");
        }
        
        if($hRole->insert($role)){
            gtdResetDbTimestamp();
            
            $message = _AM_GTD_MESSAGE_ROLE_UPDATE;
            header("Location: ".GTD_ADMIN_URL."/staff.php?op=$lastPage&uid=$uid");
        } else {
            $message = _AM_GTD_MESSAGE_ROLE_UPDATE_ERROR;
            redirect_header(GTD_ADMIN_URL."/staff.php?op=$lastPage&uid=$uid", 3, $message);
        }
    }
}

function editStaff()
{
    global $_POST, $_GET, $xoopsModule, $xoopsUser, $oAdminButton, $displayName;
    require_once(GTD_CLASS_PATH.'/session.php');
    $_gtdSession = new Session();
    
    if (isset($_REQUEST['uid']))
    {
        $uid = $_REQUEST['uid'];
    }
/*
    if(isset($_REQUEST['user'])){       // Remove me
        $uid = $_REQUEST['user'];
    }
*/    
    if(isset($_POST['clearRoles'])){
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=clearRoles");
        exit();
    }
    
    $_gtdSession->set("gtd_return_op", "editStaff");
    
    if(!isset($_POST['updateStaff'])){
        //xoops_cp_header();
        $member_handler =& xoops_gethandler('member');          // Get member handler
        $member =& $member_handler->getUser($uid);
        
        $hRoles =& gtdGetHandler('role');
        $crit = new Criteria('', '');
        $crit->setOrder('ASC');
        $crit->setSort('name');
        $roles =& $hRoles->getObjects($crit, true);
        
        $hDepartments  =& gtdGetHandler('department');    // Get department handler
        $crit = new Criteria('','');
        $crit->setSort('department');
        $crit->setOrder('ASC');
        $total = $hDepartments->getCount($crit);
        $departmentInfo =& $hDepartments->getObjects($crit);
            
        $hStaff =& gtdGetHandler('staff');       // Get staff handler
        $staff =& $hStaff->getByUid($uid);
        $hMembership =& gtdGetHandler('membership');
        $staffDepts = $hMembership->membershipByStaff($uid);
        $staffroles = $staff->getAllRoleRights();
        $global_roles = (isset($staffroles[0]['roles']) ? array_keys($staffroles[0]['roles']) : array());  //Get all Global Roles               

        $gtd_depts = array();
        foreach($staffDepts as $myDept){
            $deptid = $myDept->getVar('id');
            if($deptid != 0){
                $gtd_depts[] = $deptid;
            }
        }
        $gtd_depts = implode(',', $gtd_depts);
        
        //$myRoles =& $hStaff->getRoles($staff->getVar('uid'));
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manStaff');
        echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
        echo "<form name='frmEditStaff' method='post' action='staff.php?op=editStaff&amp;uid=".$uid."'>";
        echo "<table width='100%' border='0' cellspacing='1' class='outer'>
              <tr><th colspan='2'><label>"._AM_GTD_EDIT_STAFF ."</label></th></tr>";
        echo "<tr><td class='head' width='20%'>". _AM_GTD_TEXT_USER ."</td>
                  <td class='even'>". gtdGetUsername($member, $displayName);
        echo "</td></tr>";
        echo "<tr><td class='head'>". _AM_GTD_TEXT_ROLES ."</td>
                  <td class='even'><table width='75%'>";
        
        foreach($roles as $role){
            $roleid = $role->getVar('id');
            if (in_array($roleid, $global_roles)) {
                echo "<tr><td><input type='checkbox' name='roles[]' checked='checked' value='". $role->getVar('id')."' onclick=\"gtdRoleCustOnClick('frmEditStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
            } else {
                if($mainRoles = $_gtdSession->get("gtd_mainRoles")){
                    if(in_array($roleid, $mainRoles)){
                        echo "<tr><td><input type='checkbox' name='roles[]' checked='checked' value='". $role->getVar('id')."' onclick=\"gtdRoleCustOnClick('frmEditStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                    } else {
                        echo "<tr><td><input type='checkbox' name='roles[]'  value='". $role->getVar('id')."' onclick=\"gtdRoleCustOnClick('frmEditStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                    }
                } else {
                    echo "<tr><td><input type='checkbox' name='roles[]'  value='". $role->getVar('id')."' onclick=\"gtdRoleCustOnClick('frmEditStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" /><a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $uid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                }
            }
        }
        echo "<tr><td><input type='checkbox' name='checkallRoles' value='0' onclick='selectAll(this.form,\"roles[]\",this.checked); gtdRoleCustOnClick(\"frmEditStaff\", \"roles[]\", \"gtd_role\", \"&amp;\", \"gtd_dept_cust\");' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
        echo "</table></td></tr>";
        echo "<tr><td class='head'>". _AM_GTD_TEXT_DEPARTMENTS ."</td>
                  <td class='even'><table width='75%'>";
                 
        // This block is used to append custom role names to each department
        foreach($departmentInfo as $dept) {
            $deptid   = $dept->getVar('id');
            $deptname = $dept->getVar('department');
            $inDept   = false;  //Is the user a member of the dept
            
            $deptroleids   = '';
            $deptrolenames = '';
            
            if ($sess_roles = $_gtdSession->get("gtd_dept_$deptid")) {  //Customized roles stored in session?
                if ($sess_roles['roles'] != -1) {                           //Is the user assigned to any roles in the dept?
                    $inDept = true;
                    foreach($sess_roles['roles'] as $roleid){   // Check if customized roles match global roles
                        if(in_array($roleid, $global_roles)){   // If found role in global roles
                            $deptroleids[] = $roleid;           // Add role to array of checked roles
                        }
                    }
                    $deptroleids = implode(',', $sess_roles['roles']);  // Put all roles into 1 string separated by a ','
                    
                    //An empty string means dept roles match global roles
                    if (strlen($deptroleids) > 0) { //Customized Roles
                        $deptrolenames = implode(', ', $sess_roles['roleNames']);
                    }
                } else {                                //Not a member of the dept
                    $inDept = false;
                }
            } elseif (isset($staffroles[$deptid])) {    //User has assigned dept roles
                $inDept = true;
                
                if ($staffroles[$deptid]['roles'] == $staffroles[0]['roles']) { // If global roles same as dept roles
                    $deptrolenames = '';
                    $deptroleids   = array();
                    foreach($staffroles[$deptid]['roles'] as $roleid=>$tasks){
                        if(isset($roles[$roleid])){
                            $deptroleids[] = $roleid;
                        }
                    }
                    $deptroleids = implode(',', $deptroleids);
                } else {
                    $deptrolenames = array();
                    $deptroleids   = array();
                    foreach($staffroles[$deptid]['roles'] as $roleid=>$tasks) {
                        if (isset($roles[$roleid])) {
                            $deptroleids[]   = $roleid;
                            $deptrolenames[] = $roles[$roleid]->getVar('name');
                        }
                    }
                    $deptrolenames = implode(', ', $deptrolenames);
                    $deptroleids   = implode(',', $deptroleids);
                }
            } else {        //Not a member of the dept
                $deptroleids = array(); 
                foreach($staffroles[0]['roles'] as $roleid=>$tasks){
                    if(isset($roles[$roleid])){
                        $deptroleids[] = $roleid;
                    }
                }
                $deptroleids = implode(',', $deptroleids);
                $deptrolenames = '';
                
                $inDept = false;
            }
            
            //Should element be checked?            
            $checked = ($inDept ? "checked='checked'" : '');
            
            printf("<tr><td><input type='checkbox' name='departments[]' value='%u' %s onclick=\"gtdRoleCustOnClick('frmEditStaff', 'departments[]', 'gtd_depts', '&amp;', 'gtd_dept_cust');\" />%s [<a href='staff.php?op=customDept&amp;deptid=%u&amp;uid=%u&amp;gtd_role=%s&amp;gtd_depts=%s' class='gtd_dept_cust'>Customize</a>] <i>%s</i><input type='hidden' name='custrole[%u]' value='%s' /></td></tr>", $deptid, $checked, $deptname, $deptid, $uid, $deptroleids, $gtd_depts, $deptrolenames, $deptid, $deptroleids);
        }
        echo "<tr><td>
                  <input type='checkbox' name='checkAll' value='0' onclick='selectAll(this.form,\"departments[]\", this.checked);gtdRoleCustOnClick(\"frmEditStaff\", \"departments[]\", \"gtd_depts\", \"&amp;\", \"gtd_dept_cust\");' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
        echo "<tr><td>";
        echo "</td></tr>";
        echo "</table>";
        echo "</td></tr>";
        echo "<tr><td colspan='2' class='foot'>
                  <input type='hidden' name='uid' value='".$uid."' />
                  <input type='submit' name='updateStaff' value='". _AM_GTD_BUTTON_UPDATESTAFF ."' />
                  <input type='button' name='cancel' value='"._AM_GTD_BUTTON_CANCEL."' onclick='history.go(-1)' class='formButton' />
              </td></tr>";
        echo "</table></form>";
        
        gtd_adminFooter();
        xoops_cp_footer();
    } else {
        $uid       = intval($_POST['uid']);
        $depts     = $_POST['departments'];
        $roles     = $_POST['roles'];
        $custroles = $_POST['custrole'];
               
        $hStaff      =& gtdGetHandler('staff');  
        $hMembership =& gtdGetHandler('membership');      
        
        //Remove existing dept membership
        if(!$hMembership->clearStaffMembership($uid)){              
            $message = _GTD_MESSAGE_EDITSTAFF_NOCLEAR_ERROR;
            redirect_header(GTD_ADMIN_URL.'/staff.php?op=manageStaff', 3, $message);
        }
        
        //Add staff member to selected depts
        if($hMembership->addDeptToStaff($depts, $uid)){      
            $message = _GTD_MESSAGE_EDITSTAFF;
        } else {
            $message = _GTD_MESSAGE_EDITSTAFF_ERROR; 
        }
        
        //Clear Existing Staff Role Permissions
        $removedRoles = $hStaff->removeStaffRoles($uid);
        
        //Add Global Role Permissions
        foreach($roles as $role){
            $hStaff->addStaffRole($uid, $role, 0);
        }
        
        //Add Department Specific Roles
        foreach($depts as $dept){
            if (strlen($custroles[$dept]) > 0) {
                $dept_roles = explode(',', $custroles[$dept]);
            } else {
                $dept_roles = $roles;
            }
            
            
            foreach ($dept_roles as $role) {
                $hStaff->addStaffRole($uid, $role, $dept);
            }
        }
        
        $staff =& $hStaff->getByUid($uid);
        $staff->setVar('permTimestamp', time());
        if(!$hStaff->insert($staff)){
            $message = _GTD_MESSAGE_EDITSTAFF;
        }
               
        redirect_header(GTD_ADMIN_URL.'/staff.php?op=clearRoles', 3, $message);
    }//end if
}//end function

function manageStaff()
{   
    global $xoopsModule, $xoopsUser, $oAdminButton, $displayName;
    require_once(GTD_CLASS_PATH.'/session.php');
    $_gtdSession = new Session();
    $_gtdSession->del("gtd_return_page");
    
    $start = $limit = 0;
    $dstart = $dlimit = 0;

    if(isset($_POST['addRole'])){
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=addRole");
        exit();
    }
    if(isset($_POST['clearRoles'])){
        header("Location: ".GTD_ADMIN_URL."/staff.php?op=clearRoles");
        exit();
    }
    
    if (isset($_GET['limit'])) {
        $limit = intval($_GET['limit']);
    }
    
    if (isset($_GET['start'])) {
        $start = intval($_GET['start']);
    }
    
    if (!$limit) {
        $limit = 20;
    }
    
    if (isset($_GET['dlimit'])) {
        $dlimit = intval($_GET['dlimit']);
    }
    
    if (isset($_GET['dstart'])) {
        $dstart = intval($_GET['dstart']);
    }
    
    if (!$dlimit) {
        $dlimit = 10;
    }

    $_gtdSession->set("gtd_return_op", "manageStaff");
    
    if(!isset($_POST['addStaff'])){
                
        $member_handler =& xoops_gethandler('member');          // Get member handler
        $hStaff         =& gtdGetHandler('staff');       // Get staff handler
        $hDepartments  =& gtdGetHandler('department');    // Get department handler
        $hRoles =& gtdGetHandler('role');
        
        //Get List of depts in system
        $crit = new Criteria('','');
        $crit->setSort('department');
        $crit->setOrder('ASC');
        
        $dept_count = $hDepartments->getCount($crit);
        $dept_obj   =& $hDepartments->getObjects($crit);
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manStaff');
        
        if(isset($_GET['uid'])){
            $userid = intval($_GET['uid']);
            $uname = $xoopsUser->getUnameFromId($userid);
        } else {
            $userid = 0;
            $uname = '';
        }
        
        if ($dept_count > 0) {
            $userid = (isset($_GET['uid']) ? intval($_GET['uid']) : 0);
        
            //Get List of staff members
            $crit = new Criteria('', '');
            $crit->setStart($start);
            $crit->setLimit($limit);
            
            $staff_obj   =& $hStaff->getObjects($crit);
            $staff_count = $hStaff->getCount($crit);
            $user_count = $member_handler->getUserCount();
            
            $nav = new gtdPageNav($staff_count, $limit, $start, 'start', "op=manageStaff&amp;limit=$limit");
            
            //Get List of Staff Roles
            $crit = new Criteria('', '');
            $crit->setOrder('ASC');
            $crit->setSort('name');
            $roles =& $hRoles->getObjects($crit);
            
            echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
            echo "<form method='post' id='manageStaff' name='manageStaff' action='staff.php?op=manageStaff'>";
            echo "<table width='100%' cellspacing='1' class='outer'>
                  <tr><th colspan='2'>"._AM_GTD_ADD_STAFF."</th></tr>"; 
                  
            echo "<tr><td class='head' width='20%'>". _AM_GTD_TEXT_USER ."</td>
                      <td class='even'>
                          <input type='text' id='fullname' name='fullname' class='formButton' value='".$uname."' disabled='disabled' style='background-color:#E1E1E1;' onchange=\"window.location='staff.php?op=manageStaff&amp;uid='+user_id.value;\" />
                          <input type='hidden' id='user_id' name='user_id' class='formButton' value='".$userid."' />";
                    echo "&nbsp;<a href=\"javascript:openWithSelfMain('".GTD_BASE_URL."/lookup.php?admin=1', 'lookup',400, 300);\" title='"._AM_GTD_TEXT_FIND_USERS."'>"._AM_GTD_TEXT_FIND_USERS."</a>
                      </td>
                  </tr>";
            
            echo "</td></tr>";
            echo "<tr><td class='head' width='20%'>". _AM_GTD_TEXT_ROLES ."</td>
                      <td class='even'><table width='75%'>";
            if($mainRoles = $_gtdSession->get("gtd_mainRoles")){
                foreach($roles as $role){
                    if(!in_array($role->getVar('id'), $mainRoles)){
                        echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' onclick=\"gtdRoleCustOnClick('manageStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" />
                              <a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $userid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                    } else {
                        echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' checked='checked' onclick=\"gtdRoleCustOnClick('manageStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" />
                              <a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $userid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                    }
                }
            } else {
                foreach($roles as $role){
                    echo "<tr><td><input type='checkbox' name='roles[]' value='". $role->getVar('id')."' onclick=\"gtdRoleCustOnClick('manageStaff', 'roles[]', 'gtd_role', '&amp;', 'gtd_dept_cust');\" />
                          <a href='staff.php?op=editRole&amp;id=".$role->getVar('id')."&amp;uid=". $userid ."'>". $role->getVar('name') ."</a> - ". $role->getVar('description')."</td></tr>";
                }
            }
            echo "<tr><td><input type='checkbox' name='checkallRoles' value='0' onclick='selectAll(this.form,\"roles[]\",this.checked); gtdRoleCustOnClick(\"manageStaff\", \"roles[]\", \"gtd_role\", \"&amp;\", \"gtd_dept_cust\");' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
            echo "</table></td></tr>";
            echo "<tr><td class='head' width='20%'>". _AM_GTD_TEXT_DEPARTMENTS ."</td>
                  <td class='even' width='50%'><table width='75%'>";
            if($mainDepts =& $_gtdSession->get("gtd_mainDepts")){
                foreach($dept_obj as $dept){
                    $deptid = $dept->getVar('id');
                    $aDept = $_gtdSession->get("gtd_dept_$deptid");
                    $aDeptRoles = $aDept['roleNames'];
                    if(!empty($aDeptRoles) && is_array($aDeptRoles)){
                        $deptRoles = implode(", ", $aDeptRoles);
                    } else {
                        $deptRoles = '';
                    }
                    if(!in_array($dept->getVar('id'), $mainDepts)){
                        echo "<tr><td>
                              <input type='checkbox' name='departments[]' value='".$dept->getVar('id')."' onclick=\"gtdRoleCustOnClick('manageStaff', 'departments[]', 'gtd_depts', '&amp;', 'gtd_dept_cust');\" />
                              ".$dept->getVar('department')." [<a href='staff.php?op=customDept&amp;deptid=".$dept->getVar('id')."&amp;uid=".$userid."' class='gtd_dept_cust'>". _AM_GTD_TEXT_CUSTOMIZE ."</a>] <i>". $deptRoles ."</i>
                              </td></tr>";
                    } else {
                        echo "<tr><td>
                              <input type='checkbox' name='departments[]' checked='checked' value='".$dept->getVar('id')."' onclick=\"gtdRoleCustOnClick('manageStaff', 'departments[]', 'gtd_depts', '&amp;', 'gtd_dept_cust');\" />
                              ".$dept->getVar('department')." [<a href='staff.php?op=customDept&amp;deptid=".$dept->getVar('id')."&amp;uid=".$userid."' class='gtd_dept_cust'>". _AM_GTD_TEXT_CUSTOMIZE ."</a>] <i>". $deptRoles ."</i>
                              </td></tr>";
                    }
                }
            } else {
                foreach($dept_obj as $dept){
                    $deptid = $dept->getVar('id');
                    $aDept = $_gtdSession->get("gtd_dept_$deptid");
                    $aDeptRoles = $aDept['roleNames'];
                    if(!empty($aDeptRoles)){
                        $deptRoles = implode(", ", $aDeptRoles);
                    } else {
                        $deptRoles = '';
                    }
                    echo "<tr><td>
                          <input type='checkbox' name='departments[]' value='".$dept->getVar('id')."' onclick=\"gtdRoleCustOnClick('manageStaff', 'departments[]', 'gtd_depts', '&amp;', 'gtd_dept_cust');\" />
                          ".$dept->getVar('department')." [<a href='staff.php?op=customDept&amp;deptid=".$dept->getVar('id')."&amp;uid=".$userid."' class='gtd_dept_cust'>". _AM_GTD_TEXT_CUSTOMIZE ."</a>] <i>". $deptRoles ."</i>
                          </td></tr>";
                }
            }
            echo "<tr><td><input type='checkbox' name='checkallDepts' value='0' onclick='selectAll(this.form,\"departments[]\",this.checked);gtdRoleCustOnClick(\"manageStaff\", \"departments[]\", \"gtd_depts\", \"&amp;\", \"gtd_dept_cust\");' /><b>"._AM_GTD_TEXT_SELECT_ALL."</b></td></tr>";
            echo "</table></td></tr>";
            echo "<tr><td colspan='2' class='foot'>
                  <input type='submit' name='addStaff' value='". _AM_GTD_BUTTON_ADDSTAFF ."' />
                  <input type='submit' name='addRole' value='". _AM_GTD_BUTTON_CREATE_ROLE ."' class='formButton' />
                  <input type='submit' name='clearRoles' value='"._AM_GTD_BUTTON_CLEAR_PERMS."' class='formButton' />
                  </td></tr>";
            echo "</table></form>"; 
            
            echo "<form method='post' id='cleanStaff' name='cleanStaff' action='staff.php?op=clearOrphanedStaff'>";
            echo "<table width='100%' cellspacing='1' class='outer'>
                  <tr><th colspan='2'>"._AM_GTD_TEXT_MAINTENANCE."</th></tr>";
            echo "<tr><td class='head' width='40%'>"._AM_GTD_TEXT_ORPHANED."</td>
                      <td class='even'><input type='submit' name='cleanStaff' value='"._AM_GTD_BUTTON_SUBMIT."' /></td>
                  </tr>";
            echo "</table></form>";
                        
            if($staff_count > 0){
                //Get User Information for each staff member
                $staff_uids = array();
                foreach($staff_obj as $obj) {
                    $staff_uids[] = $obj->getVar('uid');
                }
                $crit = new Criteria('uid', '('.implode(',', $staff_uids).')', 'IN');
                $staff_users = $member_handler->getUsers($crit);
		   $crit = new Criteria('', '');
		   $crit->setStart($dstart);
		   $crit->setLimit($dlimit);

                $allDepts = $hDepartments->getObjects($crit, true);
                $dnav = new gtdPageNav($hDepartments->getCount($crit), $dlimit, $dstart, 'dstart', "op=manageStaff&amp;start=$start&amp;limit=$limit&amp;dlimit=$dlimit", "tblManageStaff");
                
                echo "<table width='100%' cellspacing='1' class='outer' id='tblManageStaff'>
                      <tr><th colspan='".(3+count($allDepts))."'><label>". _AM_GTD_MANAGE_STAFF ."</label></th></tr>";
                echo "<tr class='head'><td rowspan='2'>"._AM_GTD_TEXT_ID."</td><td rowspan='2'>"._AM_GTD_TEXT_USER."</td><td colspan='".count($allDepts)."'>"._AM_GTD_TEXT_DEPARTMENTS." ".$dnav->renderNav()."</td><td rowspan='2'>"._AM_GTD_TEXT_ACTIONS."</td></tr>";
                echo "<tr class='head'>";
                foreach ($allDepts as $thisdept) echo "<td>".$thisdept->getVar('department')."</td>";
                echo "</tr>";
                $hMembership =& gtdGetHandler('membership');
                $hStaffRole =& gtdGetHandler('staffRole');
                foreach($staff_users as $staff){
                    $departments = $hMembership->membershipByStaff($staff->getVar('uid'), true);
                    echo "<tr class='even'><td>".$staff->getVar('uid')."</td><td>".$staff->getVar('uname')."</td>";
                    foreach ($allDepts as $thisdept) {
                    	echo "<td><img src='".XOOPS_URL."/modules/gtd/images/";
                    	echo (array_key_exists($thisdept->getVar('id'), $departments)) ? "on" : "off";
                    	echo ".png' /></td>";
                    }
                    echo "<td><a href='staff.php?op=editStaff&amp;uid=".$staff->getVar('uid')."'><img src='".XOOPS_URL."/modules/gtd/images/button_edit.png' title='"._AM_GTD_TEXT_EDIT."' name='editStaff' /></a>&nbsp;
                              <a href='delete.php?deleteStaff=1&amp;uid=".$staff->getVar('uid')."'><img src='".XOOPS_URL."/modules/gtd/images/button_delete.png' title='"._AM_GTD_TEXT_DELETE."' name='deleteStaff' /></a>
                          </td></tr>";
                }
                echo "</table><br />";
                echo "<div id='staff_nav'>".$nav->renderNav()."</div>";
            }
        } else {
            echo "<div id='readOnly' class='errorMsg'>";
            echo _AM_GTD_TEXT_MAKE_DEPTS;
            echo "</div>";
            echo "<br /><a href='department.php?op=manageDepartments'>". _AM_GTD_LINK_ADD_DEPT ."</a>";   
        }
        
        gtd_adminFooter();
        xoops_cp_footer();
    } else {
        $uid = $_POST['user_id'];
        $depts = $_POST['departments'];
        $roles = $_POST['roles'];
        //$selectAll = $_POST['selectall'];
        
        $hStaff =& gtdGetHandler('staff');
        
        if(!isset($uid) || $uid == ''){
            redirect_header(GTD_ADMIN_URL.'/staff.php?op=manageStaff', 3, _AM_GTD_STAFF_ERROR_USERS);
        }
        if (!isset($depts)) {
            redirect_header(GTD_ADMIN_URL.'/staff.php?op=manageStaff', 3, _AM_GTD_STAFF_ERROR_DEPTARTMENTS);
        }
        if (!isset($roles)) {
            redirect_header(GTD_ADMIN_URL.'/staff.php?op=manageStaff', 3, _AM_GTD_STAFF_ERROR_ROLES);
        }
        if($hStaff->isStaff($uid)){
            redirect_header(GTD_ADMIN_URL.'/staff.php?op=manageStaff', 3, _AM_GTD_STAFF_EXISTS);
        }
        
        $member_handler =& xoops_gethandler('member');          // Get member handler
        $newUser =& $member_handler->getUser($uid);
        
        $email = $newUser->getVar('email');
        if($hStaff->addStaff($uid, $email)){    // $selectAll
      		$message = _GTD_MESSAGE_ADDSTAFF;
            $hMembership =& gtdGetHandler('membership');

            //Set Department Membership
            if($hMembership->addDeptToStaff($depts, $uid)){
                $message = _GTD_MESSAGE_ADDSTAFF;
            } else {
                $message = _GTD_MESSAGE_ADDSTAFF_ERROR;
            }
		    
		    //Set Global Roles
		    foreach($roles as $role){
                $hStaff->addStaffRole($uid, $role, 0);
            }
		    
		    //Set Department Roles
            foreach($depts as $dept){
                if($custRoles = $_gtdSession->get("gtd_dept_$dept")){
                    if($custRoles['roles'] != -1){
                        foreach($custRoles['roles'] as $role){
                            $hStaff->addStaffRole($uid, $role, $dept);
                        }
                    } else {
                        // If dept still checked, but no custom depts, give global roles to dept
                        foreach($roles as $role){
                            $hStaff->addStaffRole($uid, $role, $dept);
                        }
                    }
                } else {
                    foreach($roles as $role){
                        $hStaff->addStaffRole($uid, $role, $dept);
                    }
                }
            }
            $hTicketList =& gtdGetHandler('ticketList');
            $hasTicketLists = $hTicketList->createStaffGlobalLists($uid);
            
            header("Location: ".GTD_ADMIN_URL."/staff.php?op=clearRoles");
        } else {
            $message = _GTD_MESSAGE_ADDSTAFF_ERROR;
            redirect_header(GTD_ADMIN_URL.'/staff.php?op=clearRoles', 3, $message);
        }
    }//end if
}
?>