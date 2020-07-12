<?php
//$Id: file.php,v 1.9 2005/09/06 14:00:52 eric_juden Exp $
include('../../../include/cp_header.php');          
include_once('admin_header.php');           
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');

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

$aSortBy = array('id' => _AM_GTD_TEXT_ID, 'ticketid' => _AM_GTD_TEXT_TICKETID, 'filename' => _AM_GTD_TEXT_FILENAME,
                 'mimetype' => _AM_GTD_TEXT_MIMETYPE);
$aOrderBy = array('ASC' => _AM_GTD_TEXT_ASCENDING, 'DESC' => _AM_GTD_TEXT_DESCENDING);
$aLimitBy = array('10' => 10, '15' => 15, '20' => 20, '25' => 25, '50' => 50, '100' => 100);

$op = 'default';

if ( isset( $_REQUEST['op'] ) )
{
    $op = $_REQUEST['op'];
}

switch ( $op )
{
    case "deleteFile":
        deleteFile();
        break;
        
    case "deleteResolved":
        deleteResolved();
        break;
        
    case "manageFiles":       
        manageFiles();
        break;
        
    default:
        header("Location: ".GTD_ADMIN_URL."/index.php");
        break;
}

function deleteFile()
{
    $hFile =& gtdGetHandler('file');
    
    if(!isset($_GET['fileid'])){
        redirect_header(GTD_ADMIN_URL."/file.php?op=manageFiles", 3, _GTD_MESSAGE_DELETE_FILE_ERR);
    }
    $fileid = intval($_GET['fileid']);
    if(!isset($_POST['ok'])){
        xoops_cp_header();
        xoops_confirm(array('op' => 'deleteFile', 'ok' => 1), GTD_ADMIN_URL.'/file.php?fileid='. $fileid, _AM_GTD_MSG_DELETE_FILE);
        xoops_cp_footer();
    } else {
    
        $file =& $hFile->get($fileid);
        if($hFile->delete($file, true)){
            header("Location: ".GTD_ADMIN_URL."/file.php?op=manageFiles");
        }
        redirect_header(GTD_ADMIN_URL."/file.php?op=manageFiles", 3, _GTD_MESSAGE_DELETE_FILE_ERR);
    }
}

function deleteResolved()
{
    global $oAdminButton;
    
    if(!isset($_POST['ok'])){
        xoops_cp_header();
        echo $oAdminButton->renderButtons('manFiles');
        xoops_confirm(array('op' => 'deleteResolved', 'ok' => 1), GTD_BASE_URL .'/admin/file.php', _AM_GTD_MSG_DELETE_RESOLVED);
        xoops_cp_footer();
    } else {
        $hTicket =& gtdGetHandler('ticket');
        $hFile =& gtdGetHandler('file');
        
        $tickets =& $hTicket->getObjectsByState(1);     // Memory saver - unresolved should be less tickets
            
        $aTickets = array();
        foreach($tickets as $ticket){
            $aTickets[$ticket->getVar('id')] = $ticket->getVar('id');
        }
        
        // Retrieve all unresolved ticket attachments
        $crit = new CriteriaCompo();
        foreach($aTickets as $ticket){
            $crit->add(new Criteria('ticketid', $ticket, "!="));
        }
        if($hFile->deleteAll($crit)){
            header("Location: ".GTD_ADMIN_URL."/file.php?op=manageFiles");
        } else {           
            redirect_header(GTD_ADMIN_URL."/file.php?op=manageFiles", 3, _GTD_MESSAGE_DELETE_FILE_ERR);
        }
    }
}

function manageFiles()
{
    global $oAdminButton, $aSortBy, $aOrderBy, $aLimitBy, $order, $limit, $start, $sort;
    $gtdUploadDir = GTD_UPLOAD_PATH;
    $dir_status =& gtd_admin_getPathStatus($gtdUploadDir, true);
    
    if($dir_status == -1){
        $can_upload = gtd_admin_mkdir($gtdUploadDir);
    }
    
    $hFile =& gtdGetHandler('file');
    
    if(isset($_POST['deleteFiles'])){   // Delete all selected files
        $aFiles = $_POST['files'];
        $crit = new Criteria('id', "(". implode($aFiles, ',') .")", "IN");
        
        if($hFile->deleteAll($crit)){
            header("Location: ".GTD_ADMIN_URL."/file.php?op=manageFiles");
        }
        redirect_header(GTD_ADMIN_URL."/file.php?op=manageFiles", 3, _GTD_MESSAGE_DELETE_FILE_ERR);
    }
    xoops_cp_header();
    echo $oAdminButton->renderButtons('manFiles');
    echo '<script type="text/javascript" src="'.XOOPS_URL.'/modules/gtd/include/functions.js"></script>';
    echo "<form method='post' action='".GTD_ADMIN_URL."/file.php?op=manageFiles'>";
    
    echo "<table width='100%' cellspacing='1' class='outer'>
          <tr><th colspan='2'><label>"._AM_GTD_TEXT_TOTAL_USED_SPACE."</label></th></tr>";
    
    echo "<tr><td class='head' width='20%'>"._AM_GTD_TEXT_ALL_ATTACH."</td>
              <td class='even'>".gtdDirsize($gtdUploadDir)."
              </td>
          </tr>";
    
    $resolvedSize = gtdDirsize($gtdUploadDir, true);
    echo "<tr><td class='head'>"._AM_GTD_TEXT_RESOLVED_ATTACH."</td>
              <td class='even'>";
              if($resolvedSize > 0){
                  echo $resolvedSize." <b>("._AM_GTD_TEXT_DELETE_RESOLVED."
                  <a href='".GTD_ADMIN_URL."/file.php?op=deleteResolved'><img src='".GTD_IMAGE_URL."/button_delete.png' title='"._AM_GTD_TEXT_DELETE."' name='deleteFile' /></a>)</b>";
              } else {
                echo $resolvedSize;
              }
              echo "</td>
          </tr>";
    echo "</table></form>";
    
    $crit = new Criteria('', '');
    $crit->setOrder($order);
    $crit->setSort($sort);
    $crit->setLimit($limit);
    $crit->setStart($start);
    $files =& $hFile->getObjects($crit);
    $total = $hFile->getCount($crit);
    
    $nav = new XoopsPageNav($total, $limit, $start, 'start', "op=manageFiles&amp;limit=$limit");
    
    echo "<form action='". GTD_ADMIN_URL."/file.php?op=manageFiles' style='margin:0; padding:0;' method='post'>";
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
                  <input type='submit' name='file_sort' id='file_sort' value='"._AM_GTD_BUTTON_SUBMIT."' />
              </td>
          </tr>";
    echo "</table></form>";
    
    echo "<form method='post' action='".GTD_ADMIN_URL."/file.php?op=manageFiles'>";
    echo "<table width='100%' cellspacing='1' class='outer'>
          <tr><th colspan='6'><label>"._AM_GTD_TEXT_MANAGE_FILES."</label></th></tr>";
    if($total != 0){
        echo "<tr class='head'>
                  <td>"._AM_GTD_TEXT_ID."</td>
                  <td>"._AM_GTD_TEXT_TICKETID."</td>
                  <td>"._AM_GTD_TEXT_FILENAME."</td>
                  <td>"._AM_GTD_TEXT_SIZE."</td>
                  <td>"._AM_GTD_TEXT_MIMETYPE."</td>
                  <td>"._AM_GTD_TEXT_ACTIONS."</td>
              </tr>";
              
        foreach($files as $file){
            $filepath = GTD_BASE_URL.'/viewFile.php?id='. $file->getVar('id');
            $ticketpath = GTD_BASE_URL.'/ticket.php?id='.$file->getVar('ticketid');
            $filesize = filesize($gtdUploadDir.'/'.$file->getVar('filename'));
            
            echo "<tr class='even'>
                      <td><input type='checkbox' name='files[]' value='".$file->getVar('id')."' /> ".$file->getVar('id')."</td>
                      <td><a href='".$ticketpath."' target='_BLANK'>".$file->getVar('ticketid')."</a></td>
                      <td><a href='".$filepath."'>".$file->getVar('filename')."</a></td>
                      <td>".gtdPrettyBytes($filesize)."</td>
                      <td>".$file->getVar('mimetype')."</td>
                      <td>
                          <a href='".GTD_ADMIN_URL."/file.php?op=deleteFile&amp;fileid=".$file->getVar('id')."'><img src='".XOOPS_URL."/modules/gtd/images/button_delete.png' title='"._AM_GTD_TEXT_DELETE."' name='deleteFile' /></a>
                      </td>
                 </tr>";
        }
        echo "<tr class='foot'><td colspan='6'>
                                   <input type='checkbox' name='checkAllFiles' value='0' onclick='selectAll(this.form,\"files[]\",this.checked);' />
                                   <input type='submit' name='deleteFiles' id='deleteFiles' value='"._AM_GTD_BUTTON_DELETE."' /></td></tr>";
        echo "</table></form>";
        echo "<div id='status_nav'>".$nav->renderNav()."</div>";
    } else {
        echo "<tr class='even'<td colspan='6'>"._AM_GTD_TEXT_NO_FILES."</td></tr>";
        echo "</table></form>";
    }
            
          
    gtd_adminFooter();
    xoops_cp_footer();
    
}

?>