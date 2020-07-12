<?php
//include('header.php');
require('../../mainfile.php');


if (!defined('GTD_CONSTANTS_INCLUDED')) {
    include_once(XOOPS_ROOT_PATH.'/modules/gtd/include/constants.php');
}

include_once(GTD_BASE_PATH.'/functions.php');

if(!$xoopsUser) {
    redirect_header(XOOPS_URL .'/user.php?xoops_redirect='.htmlencode($xoopsRequestUri), 3);
}

if(isset($_GET['id'])){
    $gtd_id = intval($_GET['id']);
}

$viewFile = false;
$hFiles   =& gtdGetHandler('file');
$hTicket  =& gtdGetHandler('ticket');
$hStaff   =& gtdGetHandler('staff');
$file     =& $hFiles->get($gtd_id);
$mimeType = $file->getVar('mimetype');
$ticket   =& $hTicket->get($file->getVar('ticketid'));

$filename_full = $file->getVar('filename');
if($file->getVar('responseid') > 0){
    $removeText = $file->getVar('ticketid')."_".$file->getVar('responseid')."_";
} else {
    $removeText = $file->getVar('ticketid')."_";
}
$filename = str_replace($removeText, '', $filename_full);

//Security:
// Only Staff Members, Admins, or ticket Submitter should be able to see file
// @Todo: Tie this with ticket_email objects
if ($ticket->getVar('uid') == $xoopsUser->getVar('uid')) {
    $viewFile = true;
} elseif ($hStaff->isStaff($xoopsUser->getVar('uid'))) {
    $viewFile = true;
} elseif ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $viewFile = true;
}

if (!$viewFile) {
    redirect_header(GTD_BASE_URL.'/index.php', 3, _NO_PERM);
}

if(isset($mimeType)) {
    header("Content-Type: " . $mimeType);
}

// Add Header to set filename
header("Content-Disposition: attachment; filename=" . $filename);

// Get the absolute path of $file
$fileAbsPath = XOOPS_ROOT_PATH .'/uploads/gtd/' . $filename_full;

// Open the file
if(isset($mimeType) && strstr($mimeType, "text/")) {
    $fp = fopen($fileAbsPath, "r");
} else {
    $fp = fopen($fileAbsPath, "rb");
}

// Write file to browser
fpassthru($fp);

?>