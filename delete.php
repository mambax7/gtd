<?php
//$Id: delete.php,v 1.18 2005/03/03 16:26:04 eric_juden Exp $
require_once('header.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/cacheService.php');
include_once(GTD_BASE_PATH.'/functions.php');
$_eventsrv->advise('delete_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('delete_ticket', gtd_cacheService::singleton());

/**
 * @todo move these into ticket.php and profile.php respectivly
 */
if($xoopsUser){
    $uid = $xoopsUser->getVar('uid');
    
    if(isset($_POST['delete_ticket'])){
        $hTicket =& gtdGetHandler('ticket');
        if(isset($_POST['ticketid'])){
            $gtd_id = $_POST['ticketid'];
        }
        $ticketInfo =& $hTicket->get($gtd_id);      // Retrieve ticket information
        if($hTicket->delete(& $ticketInfo)){
            $message = _GTD_MESSAGE_DELETE_TICKET;
            $_eventsrv->trigger('delete_ticket', array(&$ticketInfo));
        } else {
            $message = _GTD_MESSAGE_DELETE_TICKET_ERROR;
        }
        redirect_header(GTD_BASE_URL.'/index.php', 3, $message);
    } elseif(isset($_POST['delete_responseTpl'])){ 
        //Should only the owner of a template be able to delete it?
        $hResponseTpl = gtdGetHandler('responseTemplates');
        $displayTpl =& $hResponseTpl->get($_POST['tplID']);
        if ($xoopsUser->getVar('uid') != $displayTpl->getVar('uid')) {
            $message = _NOPERM;
        } else {
        
            if($hResponseTpl->delete($displayTpl)){
                $message = _GTD_MESSAGE_DELETE_RESPONSE_TPL;
                $_eventsrv->trigger('delete_responseTpl', array($displayTpl));
            } else {
                $message = _GTD_MESSAGE_DELETE_RESPONSE_TPL_ERROR;
            }
        }
        redirect_header(GTD_BASE_URL."/profile.php", 3, $message);
    }
} else {    // If not a user
    redirect_header(XOOPS_URL .'/user.php', 3);
}

?>