<?php
require('servicemain.php');

//Include gtd Related Includes
require_once(GTD_CLASS_PATH.'/eventService.php');

require(GTD_CLASS_PATH.'/notificationService.php');
require(GTD_CLASS_PATH.'/logService.php');
require(GTD_CLASS_PATH.'/cacheService.php');
require(GTD_CLASS_PATH.'/mailboxPOP3.php');
require(GTD_CLASS_PATH.'/msgParser.php');
require(GTD_CLASS_PATH.'/msgStore.php');
require(GTD_CLASS_PATH.'/validator.php');

//Initialize gtd objects
$msgParser  =& new gtdEmailParser();
$msgStore   =& new gtdEmailStore();
$hDeptBoxes =& gtdGetHandler('departmentMailBox');
$hMailEvent =& gtdGetHandler('mailEvent');
$hTicket    =& gtdGetHandler('ticket');

$_eventsrv = gtd_eventService::singleton();
$_eventsrv->advise('new_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('new_ticket', gtd_logService::singleton());
$_eventsrv->advise('new_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('new_response', gtd_logService::singleton());
$_eventsrv->advise('new_response', gtd_notificationService::singleton());
$_eventsrv->advise('new_user_by_email', gtd_notificationService::singleton());
$_eventsrv->advise('user_email_error', gtd_notificationService::singleton());

//Get All Department Mailboxes
$deptmboxes =& $hDeptBoxes->getActiveMailboxes();

//Loop Through All Department Mailboxes
foreach($deptmboxes as $mbox) {
    $deptid = $mbox->getVar('departmentid');
    //Connect to the mailbox
    if ($mbox->connect()) {
        //Check for new messages
        if ($mbox->hasMessages()) {
            //Retrieve / Store each message
            while ($msg =& $mbox->getMessage()) {
                $msg_logs = array();
                $skip_msg = false;
                
                
                //Check if there are any errors parsing msg
                if ($parsed =& $msgParser->parseMessage($msg)) {
                    
                    //Sanity Check: Disallow emails from other department mailboxes
                    if (_isDepartmentEmail($parsed->getEmail())) {
                        $msg_logs[_GTD_MAIL_CLASS3][] = sprintf(_GTD_MESSAGE_EMAIL_DEPT_MBOX, $parsed->getEmail());    
                    } else {
                        
                        //Create new user account if necessary
                        
                        if (!$xoopsUser =& gtdEmailIsXoopsUser($parsed->getEmail())) {
                            
                            if ($xoopsModuleConfig['gtd_allowAnonymous']) {                   
                                switch($xoopsConfigUser['activation_type']){
                                    case 1:
                                        $level = 1;
                                    break;
                                    
                                    case 0:
                                    case 2:
                                    default:
                                        $level = 0;
                                }
                                $xoopsUser =& gtdXoopsAccountFromEmail($parsed->getEmail(), $parsed->getName(), $password, $level);
                                $_eventsrv->trigger('new_user_by_email', array($password, $xoopsUser));
                            } else {
                                $msg_logs[_GTD_MAIL_CLASS3][] = sprintf(_GTD_MESSAGE_NO_ANON, $parsed->getEmail());
                                $skip_msg = true;
                            }
                        }
                        
                      
                        if ($skip_msg == false) {
                            //Store Message In Server
                            if($obj =& $msgStore->storeMsg($parsed, $xoopsUser, $mbox, $errors)) {
                                switch ($parsed->getMsgType()) {
                                case _GTD_MSGTYPE_TICKET:
                                    //Trigger New Ticket Events
                                    $_eventsrv->trigger('new_ticket', $obj);
                                    break;
                                case _GTD_MSGTYPE_RESPONSE:
                                    //Trigger New Response Events
                                    $_eventsrv->trigger('new_response', array(0, $obj));
                                    break;
                                }        
                            //} else {        // If message not stored properly, log event                    
                            //    $storeEvent =& $hMailEvent->newEvent($mbox->getVar('id'), _GTD_MAILEVENT_DESC2, _GTD_MAILEVENT_CLASS2);
                            } else {
                                $msg_logs[_GTD_MAILEVENT_CLASS2] =& $errors;
                            }
                        }
                    }
                } else {
                    $msg_logs[_GTD_MAILEVENT_CLASS1][] = _GTD_MAILEVENT_DESC1;
                }
                //Remove Message From Server
                $mbox->deleteMessage($msg);

                //Log Any Messages
                _logMessages($mbox->getVar('id'),$msg_logs);
               
            }
        }
        //Disconnect from Server
        $mbox->disconnect();
    } else {                        // If mailbox not connected properly, log event
        $connEvent =& $hMailEvent->newEvent($mbox->getVar('id'), _GTD_MAILEVENT_DESC0, _GTD_MAILEVENT_CLASS0);
    }
}


function _logMessages($mbox, $arr) 
{
    global $hMailEvent;
    foreach ($arr as $class=>$msg) {
        if (is_array($msg)) {
            $msg = implode("\r\n", $msg);
        }
        $event =& $hMailEvent->newEvent($mbox, $msg, $class);
    }
}

function _isDepartmentEmail($email)
{
    static $email_arr;
    
    if (!isset($email_arr)) {
        global $hDeptBoxes;
        $deptmboxes =& $hDeptBoxes->getObjects();
        $email_arr = array();
        foreach($deptmboxes as $obj) {
            $email_arr[] = $obj->getVar('emailaddress');
        }
        unset($deptmboxes);
    }
    
    $ret = in_array($email, $email_arr);
    return $ret;
}
    
?>