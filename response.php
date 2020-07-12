<?php
//$Id: response.php,v 1.62 2005/10/18 19:27:30 eric_juden Exp $
require_once('header.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/logService.php');
require_once(GTD_CLASS_PATH.'/cacheService.php');
require_once(GTD_CLASS_PATH.'/staffService.php');

//Handlers for each event triggered by this script
$_eventsrv->advise('update_status', gtd_notificationService::singleton());
$_eventsrv->advise('update_owner', gtd_notificationService::singleton());
$_eventsrv->advise('update_owner', gtd_logService::singleton());
$_eventsrv->advise('new_response', gtd_logService::singleton());
$_eventsrv->advise('new_response', gtd_notificationService::singleton());
$_eventsrv->advise('new_response', gtd_staffService::singleton());
$_eventsrv->advise('close_ticket', gtd_logService::singleton());
$_eventsrv->advise('close_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('close_ticket', gtd_staffService::singleton());
$_eventsrv->advise('reopen_ticket', gtd_logService::singleton());
$_eventsrv->advise('reopen_ticket', gtd_staffService::singleton());
$_eventsrv->advise('edit_response', gtd_notificationService::singleton());
$_eventsrv->advise('edit_response', gtd_logService::singleton());

if(!$xoopsUser){
   redirect_header(XOOPS_URL .'/user.php', 3);
}

$refresh = 0;
if(isset($_GET['refresh'])){
    $refresh = intval($_GET['refresh']);
}

$uid = $xoopsUser->getVar('uid');

// Get the id of the ticket
if(isset($_GET['id'])){
    $ticketid = intval($_GET['id']);
}

if (isset($_GET['responseid'])) {
    $responseid = intval($_GET['responseid']);
}

$hTicket      =& gtdGetHandler('ticket');
$hResponseTpl =& gtdGetHandler('responseTemplates');
$hMembership  =& gtdGetHandler('membership');
$hResponse    =& gtdGetHandler('responses');
$hStaff       =& gtdGetHandler('staff');

if (!$ticketInfo =& $hTicket->get($ticketid)) {
    //Invalid ticketID specified
    redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_INV_TICKET);
}

$has_owner = $ticketInfo->getVar('ownership');

$op = 'staffFrm'; //Default Action for page

if(isset($_GET['op'])){
    $op = $_GET['op'];
}

if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op) {
    
case 'staffAdd':
    //0. Check that the user can perform this action
    $message = '';
    $url = GTD_BASE_URL.'/index.php';
    $hasErrors = false;
    $errors = array();
    $uploadFile = $ticketReopen = $changeOwner = $ticketClosed = $newStatus = false;
    
    if ($isStaff) {
        // Check if staff has permission to respond to the ticket
        $hTicketEmails =& gtdGetHandler('ticketEmails');
        $crit = new CriteriaCompo(new Criteria('ticketid', $ticketid));
        $crit->add(new Criteria('uid', $xoopsUser->getVar('uid')));
        $ticketEmails =& $hTicketEmails->getObjects($crit);
        if(count($ticketEmails > 0) || $gtd_staff->checkRoleRights(_GTD_SEC_RESPONSE_ADD, $ticketInfo->getVar('department'))){
            //1. Verify Response fields are filled properly
            require_once(GTD_CLASS_PATH.'/validator.php');
            $v = array();
            $v['response'][] = new ValidateLength($_POST['response'], 2, 50000);
            $v['timespent'][] = new ValidateNumber($_POST['timespent']);
        
            if($xoopsModuleConfig['gtd_allowUpload'] && is_uploaded_file($_FILES['userfile']['tmp_name'])){
                $hMime =& gtdGetHandler('mimetype');
                //Add File Upload Validation Rules
                $v['userfile'][] = new ValidateMimeType($_FILES['userfile']['name'], $_FILES['userfile']['type'], $hMime->getArray());
                $v['userfile'][] = new ValidateFileSize($_FILES['userfile']['tmp_name'], $xoopsModuleConfig['gtd_uploadSize']);
                $v['userfile'][] = new ValidateImageSize($_FILES['userfile']['tmp_name'], $xoopsModuleConfig['gtd_uploadWidth'], $xoopsModuleConfig['gtd_uploadHeight']);
                $uploadFile = true;
            }
            
            
            // Perform each validation
            $fields = array();
            $errors = array();
            foreach($v as $fieldname=>$validator) {
                if (!gtdCheckRules($validator, $errors)) {
                    $hasErrors = true;
                    //Mark field with error
                    $fields[$fieldname]['haserrors'] = true;
                    $fields[$fieldname]['errors'] = $errors;
                } else {
                    $fields[$fieldname]['haserrors'] = false;
                }
            }  
        
            if ($hasErrors) {
                //Store field values in session
                //Store error messages in session
                _setResponseToSession($ticketInfo, $fields);
                //redirect to response.php?op=staffFrm
                header("Location: ". GTD_BASE_URL."/response.php?op=staffFrm&id=$ticketid");
                exit();
            }
            
            //Check if status changed
            if ($_POST['status'] <> $ticketInfo->getVar('status')) {
                $hStatus =& gtdGetHandler('status');
                $oldStatus = $hStatus->get($ticketInfo->getVar('status'));
                $newStatus = $hStatus->get(intval($_POST['status']));
                
                if ($oldStatus->getVar('state') == 1 && $newStatus->getVar('state') == 2) {
                    $ticketClosed = true;
                } elseif ($oldStatus->getVar('state') ==2  && $newStatus->getVar('state') == 1) {
                    $ticketReopen = true;
                }
                $ticketInfo->setVar('status', intval($_POST['status']));
            }
            
            //Check if user claimed ownership
            if (isset($_POST['claimOwner'])) {
                $ownerid = intval($_POST['claimOwner']);
                if ($ownerid > 0) {
                    $oldOwner = $ticketInfo->getVar('ownership');
                    $ticketInfo->setVar('ownership', $ownerid);
                    $changeOwner = true;
                }
            }
                
            //2. Fill Response Object
            $response =& $hResponse->create();
            $response->setVar('uid', $xoopsUser->getVar('uid'));
            $response->setVar('ticketid', $ticketid);
            $response->setVar('message', $_POST['response']);
            $response->setVar('timeSpent', $_POST['timespent']);
            $response->setVar('updateTime', $ticketInfo->getVar('lastUpdated'));
            $response->setVar('userIP', getenv("REMOTE_ADDR"));
            if(isset($_POST['private'])){
                $response->setVar('private', $_POST['private']);
            }
          
            //3. Store Response Object in DB
            if ($hResponse->insert($response)) {
                $_eventsrv->trigger('new_response', array(&$ticketInfo, &$response));
            } else {
                //Store response fields in session
                _setResponseToSession($ticketInfo,$fields);
                //Notify user of error (using redirect_header())'
                redirect_header(GTD_BASE_URL."/ticket.php?id=$ticketid", 3, _GTD_MESSAGE_ADDRESPONSE_ERROR);
            }
            
            //4. Update Ticket object
            if (isset($_POST['timespent'])) {
                $oldspent = $ticketInfo->getVar('totalTimeSpent');
                $ticketInfo->setVar('totalTimeSpent', $oldspent + intval($_POST['timespent']));
            }
            $ticketInfo->setVar('lastUpdated', time());
            
            //5. Store Ticket Object
            if ($hTicket->insert($ticketInfo)) {
                if ($newStatus) {
                    $_eventsrv->trigger('update_status', array(&$ticketInfo, &$oldStatus, &$newStatus));
                }
                if ($ticketClosed) {
                    $_eventsrv->trigger('close_ticket', array(&$ticketInfo));
                }
                if ($ticketReopen) {
                    $_eventsrv->trigger('reopen_ticket', array(&$ticketInfo));
                }
                if ($changeOwner) {
                    $_eventsrv->trigger('update_owner', array(&$ticketInfo, $oldOwner));
                }
            } else {
                //Ticket Update Error
                redirect_header(GTD_BASE_URL."/response.php?op=staffFrm&id=$ticketid", 3, _GTD_MESSAGE_EDITTICKET_ERROR);
                exit();
            }
                
            //6. Save Attachments
            if ($uploadFile) {
                $allowed_mimetypes = $hMime->checkMimeTypes('userfile');
                if (!$file = $ticketInfo->storeUpload('userfile', $response->getVar('id'), $allowed_mimetypes)) {           
                    redirect_header(GTD_BASE_URL."/ticket.php?id=$ticketid", 3, _GTD_MESSAGE_ADDFILE_ERROR);            
                    exit();
                }
            }    
            
            //7. Success, clear session, redirect to ticket    
            _clearResponseFromSession();
            redirect_header(GTD_BASE_URL."/ticket.php?id=$ticketid", 3, _GTD_MESSAGE_ADDRESPONSE);
        } else {
            redirect_header($url, 3, _GTD_ERROR_NODEPTPERM);
            exit();
        }
    }
    break;

    

case 'staffFrm':
    $isSubmitter = false;
    $isStaff = $hMembership->isStaffMember($xoopsUser->getVar('uid'), $ticketInfo->getVar('department'));
    
    // Check if staff has permission to respond to the ticket
    $hTicketEmails =& gtdGetHandler('ticketEmails');
    $crit = new CriteriaCompo(new Criteria('ticketid', $ticketid));
    $crit->add(new Criteria('uid', $xoopsUser->getVar('uid')));
    $ticketEmails =& $hTicketEmails->getObjects($crit);
    if(count($ticketEmails) > 0){
        $isSubmitter = true;
    }
    if($isSubmitter || $gtd_staff->checkRoleRights(_GTD_SEC_RESPONSE_ADD, $ticketInfo->getVar('department'))){
        $hStatus =& gtdGetHandler('status');
        $crit = new Criteria('', '');
        $crit->setSort('description');
        $crit->setOrder('ASC');
        $statuses =& $hStatus->getObjects($crit);
        $aStatuses = array();
        foreach($statuses as $status){
            $aStatuses[$status->getVar('id')] = array('id' => $status->getVar('id'),
                                                      'desc' => $status->getVar('description'),
                                                      'state' => $status->getVar('state'));
        }
            
        $xoopsOption['template_main'] = 'gtd_response.html';   // Set template
        require(XOOPS_ROOT_PATH.'/header.php');
        
        $xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
        $xoopsTpl->assign('gtd_has_owner', $has_owner);
        $xoopsTpl->assign('gtd_currentUser', $xoopsUser->getVar('uid'));
        $xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
        $xoopsTpl->assign('gtd_ticketID', $ticketid);
        $xoopsTpl->assign('gtd_ticket_status', $ticketInfo->getVar('status'));
        $xoopsTpl->assign('gtd_ticket_description', $ticketInfo->getVar('description')); 
        $xoopsTpl->assign('gtd_ticket_subject', $ticketInfo->getVar('subject'));
        $xoopsTpl->assign('gtd_statuses', $aStatuses);
        $xoopsTpl->assign('gtd_isSubmitter', $isSubmitter);
        $xoopsTpl->assign('gtd_ticket_details', sprintf(_GTD_TEXT_TICKETDETAILS, $ticketInfo->getVar('id')));
        $xoopsTpl->assign('gtd_savedSearches', $aSavedSearches);
        
        $aElements = array();
        if($validateErrors =& $_gtdSession->get('gtd_validateError')){
            $errors = array();
            foreach($validateErrors as $fieldname=>$error){
                if(!empty($error['errors'])){
                    $aElements[] = $fieldname;
                    foreach($error['errors'] as $err){
                        $errors[$fieldname] = $err;
                    }
                }
            }
            $xoopsTpl->assign('gtd_errors', $errors);
        } else {
            $xoopsTpl->assign('gtd_errors', null);
        }
        
        $elements = array('response', 'timespent');
        foreach($elements as $element){         // Foreach element in the predefined list
            $xoopsTpl->assign("gtd_element_$element", "formButton");
            foreach($aElements as $aElement){   // Foreach that has an error
                if($aElement == $element){      // If the names are equal
                    $xoopsTpl->assign("gtd_element_$element", "validateError");
                    break;
                }   
            }
        }     
    
        //Get all staff defined templates
        $crit = new Criteria('uid', $uid);
        $crit->setSort('name');
        $responseTpl =& $hResponseTpl->getObjects($crit, true);
        
        $xoopsTpl->append('gtd_responseTpl_values', '------------------');
        $xoopsTpl->append('gtd_responseTpl_ids', 0);
        
        foreach($responseTpl as $obj) {
            $xoopsTpl->append('gtd_responseTpl_values', $obj->getVar('name'));
            $xoopsTpl->append('gtd_responseTpl_ids', $obj->getVar('id'));
        }
        $xoopsTpl->assign('gtd_hasResponseTpl', (isset($responseTpl) ? count($responseTpl) > 0 : 0));
        $xoopsTpl->append('gtd_responseTpl_selected', $refresh);
        $xoopsTpl->assign('gtd_templateID', $refresh);
        
        //Format Response Message Var
        $message = '';
        if($refresh) {
            if($displayTpl = $responseTpl[$refresh]) {
                $message = $displayTpl->getVar('response', 'e');
            }    
        }
        if ($temp = $_gtdSession->get('gtd_response_message')) {
            $message = $temp;
        }
        
        //Fill Response Fields (if set in session)
//        if ($_gtdSession->get('gtd_response_ticketid')) {
            $xoopsTpl->assign('gtd_response_ticketid', $_gtdSession->get('gtd_response_ticketid'));
            $xoopsTpl->assign('gtd_response_message', $message);
            $xoopsTpl->assign('gtd_response_status', $_gtdSession->get('gtd_response_status'));
            $xoopsTpl->assign('gtd_ticket_status', $_gtdSession->get('gtd_response_status'));
            $xoopsTpl->assign('gtd_response_ownership', $_gtdSession->get('gtd_response_ownership'));
            $xoopsTpl->assign('gtd_response_timespent', $_gtdSession->get('gtd_response_timespent'));
            $xoopsTpl->assign('gtd_response_private', $_gtdSession->get('gtd_response_private'));
//        }
		
        $xoopsTpl->assign('xoops_module_header', $gtd_module_header);
        $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
        require(XOOPS_ROOT_PATH.'/footer.php');
    }
    break;

case 'staffEdit':
    //Is current user staff member?
    if (!$hMembership->isStaffMember($xoopsUser->getVar('uid'), $ticketInfo->getVar('department'))) {
        redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_NODEPTPERM);
        exit();
    }
    
    if (!$response =& $hResponse->get($responseid)) {
        redirect_header(GTD_BASE_URL."/ticket.php?id=$ticketid", 3, _GTD_ERROR_INV_RESPONSE);
    }
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_RESPONSE_EDIT, $ticketInfo->getVar('department'))){
        $message = _GTD_MESSAGE_NO_EDIT_RESPONSE;
        redirect_header(GTD_BASE_URL."/ticket.php?id=$ticketid", 3, $message);
    }
    
    $xoopsOption['template_main'] = 'gtd_editResponse.html';             // Always set main template before including the header
    require(XOOPS_ROOT_PATH . '/header.php');
    
    $hStatus =& gtdGetHandler('status');
    $crit = new Criteria('', '');
    $crit->setSort('description');
    $crit->setOrder('ASC');
    $statuses =& $hStatus->getObjects($crit);
    $aStatuses = array();
    foreach($statuses as $status){
        $aStatuses[$status->getVar('id')] = array('id' => $status->getVar('id'),
                                                  'desc' => $status->getVar('description'),
                                                  'state' => $status->getVar('state'));
    }
    $xoopsTpl->assign('gtd_statuses', $aStatuses);
    $xoopsTpl->assign('gtd_responseid', $responseid);
    $xoopsTpl->assign('gtd_ticketID', $ticketid);
    $xoopsTpl->assign('gtd_responseMessage', $response->getVar('message', 'e'));
    $xoopsTpl->assign('gtd_timeSpent', $response->getVar('timeSpent'));
    $xoopsTpl->assign('gtd_status', $ticketInfo->getVar('status'));
    $xoopsTpl->assign('gtd_has_owner', $has_owner);
    $xoopsTpl->assign('gtd_responsePrivate', (($response->getVar('private') == 1) ? _GTD_TEXT_YES : _GTD_TEXT_NO));
    $xoopsTpl->assign('gtd_currentUser', $uid);
    $xoopsTpl->assign('gtd_allowUpload', 0);
    $xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
    //$xoopsTpl->assign('gtd_text_subject', _GTD_TEXT_SUBJECT);
    //$xoopsTpl->assign('gtd_text_description', _GTD_TEXT_DESCRIPTION);
    
    $aElements = array();
    $errors = array();
    if($validateErrors =& $_gtdSession->get('gtd_validateError')){
        foreach($validateErrors as $fieldname=>$error){
            if(!empty($error['errors'])){
                $aElements[] = $fieldname;
                foreach($error['errors'] as $err){
                    $errors[$fieldname] = $err;
                }
            }
        }
        $xoopsTpl->assign('gtd_errors', $errors);
    } else {
        $xoopsTpl->assign('gtd_errors', null);
    }
    
    $elements = array('response', 'timespent');
    foreach($elements as $element){         // Foreach element in the predefined list
        $xoopsTpl->assign("gtd_element_$element", "formButton");
        foreach($aElements as $aElement){   // Foreach that has an error
            if($aElement == $element){      // If the names are equal
                $xoopsTpl->assign("gtd_element_$element", "validateError");
                break;
            }
        }
    }   
    

    $hResponseTpl =& gtdGetHandler('responseTemplates');          // Used to display responseTemplates
    $crit = new Criteria('uid', $uid);
    $crit->setSort('name');
    $responseTpl =& $hResponseTpl->getObjects($crit);
    
    $aResponseTpl = array();
    foreach($responseTpl as $response){
        $aResponseTpl[] = array('id'=>$response->getVar('id'),
            'uid'=>$response->getVar('uid'),
            'name'=>$response->getVar('name'),
            'response'=>$response->getVar('response'));
    }
    $has_responseTpl = count($responseTpl) > 0;
    unset($responseTpl);
    $displayTpl =& $hResponseTpl->get($refresh);
        
    $xoopsTpl->assign('gtd_response_text', ($refresh !=0 ? $displayTpl->getVar('response', 'e') : ''));
    $xoopsTpl->assign('gtd_responseTpl',  $aResponseTpl);
    $xoopsTpl->assign('gtd_hasResponseTpl', count($aResponseTpl) > 0);
    $xoopsTpl->assign('gtd_refresh', $refresh);
    $xoopsTpl->assign('xoops_module_header', $gtd_module_header);
    $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
    
    require(XOOPS_ROOT_PATH.'/footer.php');                             //Include the page footer
    break;

case 'staffEditSave':
    require_once(GTD_CLASS_PATH.'/validator.php');
    $v['response'][] = new ValidateLength($_POST['response'], 2, 50000);
    $v['timespent'][] = new ValidateNumber($_POST['timespent']);
    
    $responseStored = false;
    
    //Is current user staff member?
    if (!$hMembership->isStaffMember($xoopsUser->getVar('uid'), $ticketInfo->getVar('department'))) {
        redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_NODEPTPERM);
        exit();
    }
    
    //Retrieve the original response
    if (!$response =& $hResponse->get($responseid)) {
        redirect_header(GTD_BASE_URL."/ticket.php?id=".$ticketInfo->getVar('id'), 3, _GTD_ERROR_INV_RESPONSE);
    }
    
    //Copy original ticket and response objects
    $oldresponse    = $response;
    $oldticket      = $ticketInfo;
    
    $url = "response.php?op=staffEditSave&amp;id=$ticketid&amp;responseid=$responseid";
    $ticketReopen = $changeOwner = $ticketClosed = $newStatus = false;
    
    //Store current fields in session
    $_gtdSession->set('gtd_response_ticketid', $oldresponse->getVar('ticketid'));
    $_gtdSession->set('gtd_response_uid', $response->getVar('uid'));
    $_gtdSession->set('gtd_response_message', $_POST['response']);
    
    //Check if the ticket status has been changed
    if($_POST['status'] <> $ticketInfo->getVar('status')){
        $ticketInfo->setVar('status', $_POST['status']);
        $newStatus = true;
        
        if($_POST['status'] == 2) { //Closed Ticket
            $ticketInfo->setVar('closedBy', $xoopsUser->getVar('uid'));
            $ticketClosed = true;
        }
        
        if($oldticket->getVar('status') == 2) { //Ticket reopened
            $ticketReopen = true;
        }
     }
    $_gtdSession->set('gtd_response_status', $ticketInfo->getVar('status'));        // Store in session
    
    //Check if the current user is claiming the ticket
    if (isset($_POST['claimOwner']) && $_POST['claimOwner'] > 0) {
        if ($_POST['claimOwner'] != $oldticket->getVar('ownership')) {
            $oldOwner = $oldticket->getVar('ownership');
            $ticketInfo->setVar('ownership',$_POST['claimOwner']);
            $changeOwner = true;
        }
    }
    $_gtdSession->set('gtd_response_ownership', $ticketInfo->getVar('ownership'));  // Store in session
    
    // Check the timespent
    if (isset($_POST['timespent'])) {
        $timespent = intval($_POST['timespent']);
        $totaltime = $oldticket->getVar('totalTimeSpent') - $oldresponse->getVar('timeSpent') + $timespent;
        $ticketInfo->setVar('totalTimeSpent', $totaltime);
        $response->setVar('timeSpent', $timespent);
    }
    $_gtdSession->set('gtd_response_timespent', $response->getVar('timeSpent'));
    $_gtdSession->set('gtd_responseStored', true);
    
    // Perform each validation
    $fields = array();
    $errors = array();
    foreach($v as $fieldname=>$validator){
        if(!gtdCheckRules($validator, $errors)){
            // Mark field with error
            $fields[$fieldname]['haserrors'] = true;
            $fields[$fieldname]['errors'] = $errors;
        } else {
            $fields[$fieldname]['haserrors'] = false;
        }
    }
    
    if(!empty($errors)){
        $_gtdSession->set('gtd_validateError', $fields);
        $message = _GTD_MESSAGE_VALIDATE_ERROR;
        header("Location: ".GTD_BASE_URL."/response.php?id=$ticketid&responseid=". $response->getVar('id') ."&op=staffEdit");
        exit();
    }
    
    
     $ticketInfo->setVar('lastUpdated', time());
     
     if ($hTicket->insert($ticketInfo)) {
        if ($newStatus) {
            $_eventsrv->trigger('update_status', array(&$ticketInfo, $oldStatus));
        }
        if ($ticketClosed) {
            $_eventsrv->trigger('close_ticket', array(&$ticketInfo));
        }
        if ($ticketReopen) {
            $_eventsrv->trigger('reopen_ticket', array(&$ticketInfo));
        }
        if ($changeOwner) {
            $_eventsrv->trigger('update_owner', array(&$ticketInfo, $oldOwner));
        }
        
        $message = $_POST['response'];
        $message .= "\n".sprintf(_GTD_RESPONSE_EDIT, $xoopsUser->getVar('uname'), $ticketInfo->lastUpdated());
        
        $response->setVar('message', $message);
        if(isset($_POST['timespent'])){
            $response->setVar('timeSpent', intval($_POST['timespent']));    
        }
        $response->setVar('updateTime', $ticketInfo->getVar('lastUpdated'));
        
        if ($hResponse->insert($response)) {
            $_eventsrv->trigger('edit_response', array(&$ticketInfo, &$response, &$oldticket, &$oldresponse));
            $message = _GTD_MESSAGE_EDITRESPONSE;
            $url = "ticket.php?id=$ticketid";
            $responseStored = true;
        } else {
            $message = _GTD_MESSAGE_EDITRESPONSE_ERROR;
        }
    } else {
        $message = _GTD_MESSAGE_EDITTICKET_ERROR;
    }
    _clearResponseFromSession();
    redirect_header($url, 3, $message);
    
    
    break;

    
default:
    break;
}

function _setResponseToSession(&$ticket, &$errors)
{
    global $xoopsUser, $_gtdSession;
    $_gtdSession->set('gtd_response_ticketid', $ticket->getVar('id'));
    $_gtdSession->set('gtd_response_uid', $xoopsUser->getVar('uid'));
    $_gtdSession->set('gtd_response_message', ( isset($_POST['response']) ? $_POST['response'] : '' ) );
    $_gtdSession->set('gtd_response_private', ( isset($_POST['private'])? $_POST['private'] : 0 ));    
    $_gtdSession->set('gtd_response_timespent', ( isset($_POST['timespent']) ? $_POST['timespent'] : 0 ));
    $_gtdSession->set('gtd_response_ownership', ( isset($_POST['claimOwner']) && intval($_POST['claimOwner']) > 0 ? $_POST['claimOwner'] : 0) );    
    $_gtdSession->set('gtd_response_status',  $_POST['status'] );
    $_gtdSession->set('gtd_response_private', $_POST['private'] );
    $_gtdSession->set('gtd_validateError', $errors);
}

function _clearResponseFromSession()
{
    global $_gtdSession;
    $_gtdSession->del('gtd_response_ticketid');
    $_gtdSession->del('gtd_response_uid');
    $_gtdSession->del('gtd_response_message');
    $_gtdSession->del('gtd_response_timespent');
    $_gtdSession->del('gtd_response_ownership');
    $_gtdSession->del('gtd_response_status');
    $_gtdSession->del('gtd_response_private');
    $_gtdSession->del('gtd_validateError');
}
?>