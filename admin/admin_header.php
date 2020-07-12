<?php
if (!defined('GTD_CONSTANTS_INCLUDED')) {
    include_once(XOOPS_ROOT_PATH.'/modules/gtd/include/constants.php');
}

require(GTD_BASE_PATH.'/admin/admin_buttons.php');
require_once(GTD_BASE_PATH.'/functions.php');
require_once(GTD_INCLUDE_PATH.'/functions_admin.php');
require_once(GTD_CLASS_PATH.'/session.php');
require_once(GTD_CLASS_PATH.'/eventService.php');

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

gtdIncludeLang('main');
gtdIncludeLang('modinfo');


global $xoopsModule;
$module_id = $xoopsModule->getVar('mid');
$oAdminButton = new AdminButtons();
$oAdminButton->AddTitle(sprintf(_AM_GTD_ADMIN_TITLE, $xoopsModule->getVar('name')));
$oAdminButton->AddButton(_AM_GTD_INDEX, GTD_ADMIN_URL."/index.php", 'index');
$oAdminButton->AddButton(_AM_GTD_MENU_MANAGE_DEPARTMENTS, GTD_ADMIN_URL."/department.php?op=manageDepartments", 'manDept');
$oAdminButton->AddButton(_AM_GTD_MENU_MANAGE_STAFF, GTD_ADMIN_URL."/staff.php?op=manageStaff", 'manStaff');
$oAdminButton->addButton(_AM_GTD_TEXT_MANAGE_FIELDS, GTD_ADMIN_URL.'/fields.php', 'manfields');
$oAdminButton->AddButton(_AM_GTD_TEXT_MANAGE_FILES, GTD_ADMIN_URL."/file.php?op=manageFiles", 'manFiles');
$oAdminButton->AddButton(_AM_GTD_TEXT_MANAGE_NOTIFICATIONS, GTD_ADMIN_URL."/notifications.php", 'manNotify');
echo"\n";
$oAdminButton->AddButton(_AM_GTD_TEXT_MANAGE_STATUSES, GTD_ADMIN_URL."/status.php?op=manageStatus", 'manStatus');
//$oAdminButton->AddButton(_AM_GTD_MENU_MODIFY_TICKET_FIELDS, "index.php?op=modifyTicketFields", 'modTickFields');
$oAdminButton->addButton(_AM_GTD_MENU_MIMETYPES, GTD_ADMIN_URL."/mimetypes.php", 'mimetypes');
$oAdminButton->addButton(_AM_GTD_TEXT_MAIL_EVENTS, GTD_ADMIN_URL."/index.php?op=mailEvents", 'mailEvents');
$oAdminButton->AddTopLink(_AM_GTD_MENU_PREFERENCES, XOOPS_URL ."/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=". $module_id);
//$oAdminButton->AddTopLink(_AM_GTD_BLOCK_TEXT, GTD_ADMIN_URL."/index.php?op=blocks");
$oAdminButton->addTopLink(_AM_GTD_UPDATE_MODULE, XOOPS_URL ."/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=gtd");
$oAdminButton->addTopLink(_MI_GTD_MENU_CHECK_TABLES, GTD_ADMIN_URL."/upgrade.php?op=checkTables");
$oAdminButton->AddTopLink(_AM_GTD_ADMIN_GOTOMODULE, GTD_BASE_URL."/index.php");
$oAdminButton->AddTopLink(_AM_GTD_ADMIN_ABOUT, GTD_ADMIN_URL."/index.php?op=about");

$gtds = &MyTextSanitizer::getInstance();
$_eventsrv = gtd_eventService::singleton();

$imagearray = array(
	'editimg' => "<img src='". GTD_IMAGE_URL ."/button_edit.png' alt='" . _AM_GTD_ICO_EDIT . "' align='middle' />",
    'deleteimg' => "<img src='". GTD_IMAGE_URL ."/button_delete.png' alt='" . _AM_GTD_ICO_DELETE . "' align='middle' />",
    'online' => "<img src='". GTD_IMAGE_URL ."/on.png' alt='" . _AM_GTD_ICO_ONLINE . "' align='middle' />",
    'offline' => "<img src='". GTD_IMAGE_URL ."/off.png' alt='" . _AM_GTD_ICO_OFFLINE . "' align='middle' />",
	);

// Overdue time
require_once(GTD_CLASS_PATH.'/session.php');
$_gtdSession = new Session();	

if(!$overdueTime = $_gtdSession->get("gtd_overdueTime")){
    $_gtdSession->set("gtd_overdueTime", $xoopsModuleConfig['gtd_overdueTime']);
    $overdueTime = $_gtdSession->get("gtd_overdueTime");
}

if($overdueTime != $xoopsModuleConfig['gtd_overdueTime']){
    $_gtdSession->set("gtd_overdueTime", $xoopsModuleConfig['gtd_overdueTime']);   // Set new value for overdueTime
    
    // Change overdueTime in all of tickets (OPEN & HOLD)
    $hTickets =& gtdGetHandler('ticket');
    $crit = new Criteria('status', 2, '<>');
    $tickets =& $hTickets->getObjects($crit);
    $updatedTickets = array();
    foreach($tickets as $ticket){
        $ticket->setVar('overdueTime', $ticket->getVar('posted') + ($xoopsModuleConfig['gtd_overdueTime'] *60*60));
        if(!$hTickets->insert($ticket, true)){
            $updatedTickets[$ticket->getVar('id')] = false; // Not used anywhere
        } else {
            $updatedTickets[$ticket->getVar('id')] = true;  // Not used anywhere
        }
    }
}
?>