<?php
//$Id: status.php,v 1.6 2005/09/15 18:41:21 eric_juden Exp $
include('../../../include/cp_header.php');          
include_once('admin_header.php');           
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');
require_once(GTD_CLASS_PATH . '/gtdForm.php');

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
    $sort = "id";
}

$aSortBy = array('id' => _AM_GTD_TEXT_ID, 'description' => _AM_GTD_TEXT_DESCRIPTION, 'state' => _AM_GTD_TEXT_STATE);
$aOrderBy = array('ASC' => _AM_GTD_TEXT_ASCENDING, 'DESC' => _AM_GTD_TEXT_DESCENDING);
$aLimitBy = array('10' => 10, '15' => 15, '20' => 20, '25' => 25, '50' => 50, '100' => 100);

$op = 'default';

if ( isset( $_REQUEST['op'] ) )
{
    $op = $_REQUEST['op'];
}

switch ( $op )
{
    case "deleteStatus":
        deleteStatus();
        break;
        
    case "editStatus":
        editStatus();
        break;
        
    case "manageStatus":
        manageStatus();
        break;
    
    default:
        header("Location: ".GTD_ADMIN_URL."/index.php");
        break;
}

function deleteStatus()
{
    if(isset($_GET['statusid'])){
        $statusid = intval($_GET['statusid']);
    } else {
        header("Location: ".GTD_ADMIN_URL."/status.php?op=manageStatus");
    }
    
    $hTickets =& gtdGetHandler('ticket');
    $hStatus =& gtdGetHandler('status');
    $status =& $hStatus->get($statusid);
    
    // Check for tickets with this status first
    $crit = new Criteria('status', $statusid);
    $ticketCount =& $hTickets->getCount($crit);
    
    if($ticketCount > 0){
        redirect_header(GTD_ADMIN_URL."/status.php?op=manageStatus", 3, _AM_GTD_STATUS_HASTICKETS_ERR);
    }
    
    if($hStatus->delete($status, true)){
        header("Location: ".GTD_ADMIN_URL."/status.php?op=manageStatus");
    } else {
        $message = _AM_GTD_DEL_STATUS_ERR;
        redirect_header(GTD_ADMIN_URL."/status.php?op=manageStatus", 3, $message);
    }
}

function editStatus()
{
    global $oAdminButton;
    if(isset($_REQUEST['statusid'])){
        $statusid = intval($_REQUEST['statusid']);
    } else {
        header("Location: ".GTD_ADMIN_URL."/status.php?op=manageStatus");
    }
    
    $hStatus =& gtdGetHandler('status');
    $status = $hStatus->get($statusid);
    
    if(!isset($_POST['updateStatus'])){
        xoops_cp_header();
        echo $oAdminButton->renderButtons('modTpl');
        echo "<form method='post' action='".GTD_ADMIN_URL."/status.php?op=editStatus&amp;statusid=".$statusid."'>";   
        echo "<table width='100%' cellspacing='1' class='outer'>
              <tr><th colspan='2'><label>"._AM_GTD_TEXT_EDIT_STATUS."</label></th></tr>";
        echo "<tr><td class='head' width='20%'>"._AM_GTD_TEXT_DESCRIPTION."</td>
                  <td class='even'>
                      <input type='text' name='desc' value='".$status->getVar('description', 'e')."' class='formButton' />
                  </td>
              </tr>";
        echo "<tr><td class='head' width='20%'>"._AM_GTD_TEXT_STATE."</td><td class='even'>
                  <select name='state'>";
                  if($status->getVar('state') == 1){
                    echo "<option value='1' selected='selected'>".gtdGetState(1)."</option>
                          <option value='2'>".gtdGetState(2)."</option>";
                  } else {
                    echo "<option value='1'>".gtdGetState(1)."</option>
                          <option value='2' selected='selected'>".gtdGetState(2)."</option>";
                  }
        echo "</select></td></tr>";
        echo "<tr><td class='foot' colspan='2'><input type='submit' name='updateStatus' value='"._AM_GTD_BUTTON_UPDATE."' class='formButton' /></td></tr>";
        echo "</table></form>";
        
        gtd_adminFooter();
        xoops_cp_footer();
    } else {
        if($_POST['desc'] == ''){  // If no description supplied
            $message = _AM_GTD_MESSAGE_NO_DESC;
            redirect_header(GTD_ADMIN_URL."/status.php?op=manageStatus", 3, $message);
        }
        
        $status->setVar('description', $_POST['desc']);
        $status->setVar('state', $_POST['state']);
        if($hStatus->insert($status)){
            header("Location: ".GTD_ADMIN_URL."/status.php?op=manageStatus");
        } else {
            $message = _AM_MESSAGE_EDIT_STATUS_ERR;
            readirect_header(GTD_ADMIN_URL."/status.php?op=manageStatus", 3, $message);
        }
    }
}

function manageStatus()
{
    global $oAdminButton, $aSortBy, $aOrderBy, $aLimitBy, $order, $limit, $start, $sort;
    $hStatus =& gtdGetHandler('status');
    
    if(isset($_POST['changeDefaultStatus'])){
        gtdSetMeta("default_status", $_POST['default']);
    }
    
    if(isset($_POST['newStatus'])){
        if($_POST['desc'] == ''){  // If no description supplied
            $message = _AM_GTD_MESSAGE_NO_DESC;
            redirect_header(GTD_ADMIN_URL."/status.php?op=manageStatus", 3, $message);
        }
        $newStatus =& $hStatus->create();
        
        $newStatus->setVar('state', intval($_POST['state']));
        $newStatus->setVar('description', $_POST['desc']);
        if($hStatus->insert($newStatus)){
            header("Location: ".GTD_ADMIN_URL."/status.php?op=manageStatus");
        } else {
            $message = _AM_MESSAGE_ADD_STATUS_ERR;
            redirect_header(GTD_ADMIN_URL."/status.php?op=manageStatus", 3, $message);
        }
    }
    xoops_cp_header();
    echo $oAdminButton->renderButtons('manStatus');        
    echo "<form method='post' action='".GTD_ADMIN_URL."/status.php?op=manageStatus'>";   
    echo "<table width='100%' cellspacing='1' class='outer'>
          <tr><th colspan='2'><label>"._AM_GTD_TEXT_ADD_STATUS."</label></th></tr>";
    echo "<tr><td class='head' width='20%'>"._AM_GTD_TEXT_DESCRIPTION."</td>
              <td class='even'>
                  <input type='text' name='desc' value='' class='formButton' />
              </td>
          </tr>";
    echo "<tr><td class='head' width='20%'>"._AM_GTD_TEXT_STATE."</td><td class='even'>
              <select name='state'>
              <option value='1'>".gtdGetState(1)."</option>
              <option value='2'>".gtdGetState(2)."</option>
          </select></td></tr>";
    echo "<tr><td class='foot' colspan='2'><input type='submit' name='newStatus' value='"._AM_GTD_TEXT_ADD_STATUS."' class='formButton' /></td></tr>";
    echo "</table></form>";
    
    // Get list of existing statuses
    $crit = new Criteria('', '');
    $crit->setOrder($order);
    $crit->setSort($sort);
    $crit->setLimit($limit);
    $crit->setStart($start);
    $statuses =& $hStatus->getObjects($crit);
    $total = $hStatus->getCount($crit);
    
    $aStatuses = array();
    foreach($statuses as $status){
        $aStatuses[$status->getVar('id')] = $status->getVar('description');
    }
    
    if(!$default_status = gtdGetMeta("default_status")){
        gtdSetMeta("default_status", "1");
        $default_status = 1;
    }
    $form = new gtdForm(_AM_GTD_TEXT_DEFAULT_STATUS, 'default_status', gtdMakeURI(GTD_ADMIN_URL.'/status.php', array('op'=>'manageStatus')));
    $status_select =& new XoopsFormSelect(_AM_GTD_TEXT_STATUS, 'default', $default_status);
    $status_select->addOptionArray($aStatuses);
    $btn_tray = new XoopsFormElementTray('');
    $btn_tray->addElement(new XoopsFormButton('', 'changeDefaultStatus', _AM_GTD_BUTTON_SUBMIT, 'submit')); 
    $form->addElement($status_select);
    $form->addElement($btn_tray);
    $form->setLabelWidth('20%');
    echo $form->render();
    
    $nav = new XoopsPageNav($total, $limit, $start, 'start', "op=manageStatus&amp;limit=$limit");
    
    echo "<form action='". GTD_ADMIN_URL."/status.php?op=manageStatus' style='margin:0; padding:0;' method='post'>";
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
                  <input type='submit' name='status_sort' id='status_sort' value='"._AM_GTD_BUTTON_SUBMIT."' />
              </td>
          </tr>";
    echo "</table></form>";
    
    echo "<table width='100%' cellspacing='1' class='outer'>
          <tr><th colspan='4'><label>"._AM_GTD_TEXT_MANAGE_STATUSES."</label></th></tr>";
    echo "<tr class='head'>
              <td>"._AM_GTD_TEXT_ID."</td>
              <td>"._AM_GTD_TEXT_DESCRIPTION."</td>
              <td>"._AM_GTD_TEXT_STATE."</td>
              <td>"._AM_GTD_TEXT_ACTIONS."</td>
          </tr>";
    foreach($statuses as $status){
        echo "<tr class='even'><td>".$status->getVar('id')."</td><td>".$status->getVar('description')."</td>
              <td>".gtdGetState($status->getVar('state'))."</td>
              <td>
                  <a href='status.php?op=editStatus&amp;statusid=".$status->getVar('id')."'><img src='".GTD_IMAGE_URL."/button_edit.png' title='"._AM_GTD_TEXT_EDIT."' name='editStatus' /></a>&nbsp;
                  <a href='status.php?op=deleteStatus&amp;statusid=".$status->getVar('id')."'><img src='".GTD_IMAGE_URL."/button_delete.png' title='"._AM_GTD_TEXT_DELETE."' name='deleteStatus' /></a></td></tr>
              </td></tr>";
    }
    echo "</table>";
    echo "<div id='status_nav'>".$nav->renderNav()."</div>";
    gtd_adminFooter();
    xoops_cp_footer();
          
}

?>