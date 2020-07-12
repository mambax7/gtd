<?php
//$Id: internas.php,v 1.138 2005/10/24 18:10:37 eric_juden Exp $
require_once('header.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/logService.php');
require_once(GTD_CLASS_PATH.'/staffService.php');
require_once(GTD_CLASS_PATH.'/cacheService.php');

$_eventsrv->advise('update_genre', gtd_notificationService::singleton());
$_eventsrv->advise('update_genre', gtd_logService::singleton());
$_eventsrv->advise('update_status', gtd_notificationService::singleton());
$_eventsrv->advise('update_status', gtd_logService::singleton());
$_eventsrv->advise('close_ticket', gtd_staffService::singleton());
$_eventsrv->advise('close_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('close_ticket', gtd_logService::singleton());
$_eventsrv->advise('close_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('new_response', gtd_logService::singleton());
$_eventsrv->advise('new_response', gtd_notificationService::singleton());
$_eventsrv->advise('edit_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('edit_ticket', gtd_logService::singleton());
$_eventsrv->advise('update_owner', gtd_notificationService::singleton());
$_eventsrv->advise('update_owner', gtd_logService::singleton());
$_eventsrv->advise('delete_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('delete_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('reopen_ticket', gtd_staffService::singleton());
$_eventsrv->advise('reopen_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('reopen_ticket', gtd_logService::singleton());
$_eventsrv->advise('view_ticket', gtd_staffService::singleton());
$_eventsrv->advise('merge_tickets', gtd_logService::singleton());
$_eventsrv->advise('merge_tickets', gtd_notificationService::singleton());
$_eventsrv->advise('delete_file', gtd_logService::singleton());

$op = "user";

// Get the id of the ticket
if(isset($_REQUEST['id'])){
    $gtd_id = intval($_REQUEST['id']);
} else {
    redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_INV_TICKET);
}

if(isset($_GET['op'])){
    $op = $_GET['op'];
}

$xoopsLogger->addExtra('internas.php', 'Ligne 46 : Operation op= $op');

if(!$xoopsUser){
    redirect_header(XOOPS_URL .'/user.php?xoops_redirect='.htmlspecialchars($xoopsRequestUri), 3);     
}

$xoopsVersion = substr(XOOPS_VERSION, 6);
intval($xoopsVersion);

global $ticketInfo;
$hStaff         =& gtdGetHandler('staff');
$member_handler =& xoops_gethandler('member');
$hTickets       =& gtdGetHandler('ticket');
$xoopsLogger->addExtra('internas.php', "Ligne 59 : Operation op= $op - gtd_id = $gtd_id");
if(!$ticketInfo     =& $hTickets->get($gtd_id)){
    redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_INV_TICKET);
}

$displayName =& $xoopsModuleConfig['gtd_displayName'];    // Determines if username or real name is displayed

$hDepartments   =& gtdGetHandler('department');
$departments    =& $hDepartments->getObjects(null, true);
$user           =& $member_handler->getUser($ticketInfo->getVar('uid'));
$hStaffReview   =& gtdGetHandler('staffReview');
$hResponses     =& gtdGetHandler('responses'); 
$hMembership    =& gtdGetHandler('membership'); 
$aResponses = array();
$all_users = array();

if (isset($departments[$ticketInfo->getVar('department')])) {
    $department = $departments[$ticketInfo->getVar('department')];
}

//Security Checkpoints to ensure no funny stuff
if (!$xoopsUser) {
    redirect_header(GTD_BASE_URL."/index.php", 3, _NOPERM);
    exit();
}



$op = ($isStaff ? 'staff' : $op);
$message = '';
       
if($isStaff) {
    //** BTW - What does $giveOwnership do here?
    $giveOwnership = false;
    if(isset($_GET['op'])){
        $op = $_GET['op'];
    } else {
        $op = "staff";
    }
    
    $all_users[$ticketInfo->getVar('uid')] = '';
    $all_users[$ticketInfo->getVar('ownership')] = '';
    $all_users[$ticketInfo->getVar('closedBy')] = '';
    
    if($owner =& $member_handler->getUser($ticketInfo->getVar('ownership'))){
        $giveOwnership = true;
    }
    
    //Retrieve all log messages from the database
    $logMessage =& $ticketInfo->getLogs();
    
    $patterns = array();
    $patterns[] = '/pri:([1-5])/';
    $replacements = array();
    $replacements = '<img src="images/genre$1.png" alt="Priority: $1" />';
    
    
    foreach($logMessage as $msg){
        $aMessages[] = array('id'=>$msg->getVar('id'),
                             'uid'=>$msg->getVar('uid'),
                             'uname'=>'',
                             //'uname'=>(($msgLoggedBy)? $msgLoggedBy->getVar('uname'):$xoopsConfig['anonymous']),
                             'ticketid'=>$msg->getVar('ticketid'),
                             'lastUpdated'=>$msg->lastUpdated('m'),
                             'action'=>preg_replace($patterns, $replacements, $msg->getVar('action')));
        $all_users[$msg->getVar('uid')] = '';
    }
    //unset($logMessage);
    
    //For assign to ownership box
    $hMembership =& gtdGetHandler('membership');
    
    global $staff;
    $staff = $hMembership->membershipByDept($ticketInfo->getVar('department'));
    
    $aOwnership = array();
    // Only run if actions are set to inline style
    if($xoopsModuleConfig['gtd_staffTicketActions'] == 1){
        $aOwnership[] = array('uid' => 0,
                              'uname' => _GTD_NO_OWNER);
        foreach($staff as $stf){
            if($stf->getVar('uid') != 0){
                $aOwnership[] = array('uid'=>$stf->getVar('uid'),
                                      'uname'=>'');
        	    $all_users[$stf->getVar('uid')] = '';
        	}
        }
    }
	$xoopsLogger->addExtra('internas.php', 'Operation op= $op');
switch($op)
{
    case "edit":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_EDIT, $ticketInfo->getVar('department'))){
            $message = _GTD_MESSAGE_NO_EDIT_TICKET;
            redirect_header(GTD_BASE_URL."/internas.php?id=$gtd_id", 3, $message);
        }
        
        if(!isset($_POST['editTicket'])){
            $xoopsOption['template_main'] = 'gtd_editInternas.html';             // Always set main template before including the header
//            require(XOOPS_ROOT_PATH . '/header.php');
				require_once XOOPS_ROOT_PATH.'/class/template.php';
				$xoopsTpl = new XoopsTpl();
            
            $crit = new Criteria('','');
            $crit->setSort('department');
            $departments =& $hDepartments->getObjects($crit);
            $hStaff =& gtdGetHandler('staff'); 
            
            foreach($departments as $dept){
                $aDept[] = array('id'=>$dept->getVar('id'),
                                 'department'=>$dept->getVar('department'));
            }
                        
            // Form validation stuff
            $errors = array();
            $aElements = array();
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
            
            $elements = array('observation_paiement');
            foreach($elements as $element){         // Foreach element in the predefined list
                $xoopsTpl->assign("gtd_element_$element", "formButton");
                foreach($aElements as $aElement){   // Foreach that has an error
                    if($aElement == $element){      // If the names are equal
                        $xoopsTpl->assign("gtd_element_$element", "validateError");
                        break;
                    }
                }
            } 
            // end form validation stuff
            
            $javascript = "<script type=\"text/javascript\" src=\"". GTD_BASE_URL ."/include/functions.js\"></script>
<script type=\"text/javascript\" src='".GTD_SCRIPT_URL."/addTicketDeptChange.php?client'></script>
<script type=\"text/javascript\">
<!--
function departments_onchange() 
{
    dept = xoopsGetElementById('departments');
    var wl = new gtdweblib(fieldHandler);
    wl.customfieldsbydept(dept.value);
}

var fieldHandler = {
    customfieldsbydept: function(result){
        
        var tbl = gE('tblEditTicket');
        var staffCol = gE('staff');";
        $javascript.="var beforeele = gE('editButtons');\n";
        $javascript.="tbody = tbl.tBodies[0];\n";
        $javascript .="gtdFillCustomFlds(tbody, result, beforeele);\n
    }
}

function window_onload()
{
    gtdDOMAddEvent(xoopsGetElementById('departments'), 'change', departments_onchange, true);
}

gtdDOMAddEvent(window, 'load', window_onload, true);
//-->
</script>";      
            if ($ticket =& $_gtdSession->get('gtd_ticket_internas')) {
                $xoopsTpl->assign('gtd_ticketID', $gtd_id);
				$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticket['observations_paiement']);
            } else {
				$xoopsTpl->assign('gtd_ticketID', $gtd_id);
				$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticketInfo->getVar('observations_paiement'));
            }
             $xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
            //** BTW - why do we need gtd_allowUpload in the template if it will be always set to 0?
            //$xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
            if(isset($_POST['logFor'])){
                $uid = $_POST['logFor'];
                $username = gtdGetUsername($uid, $displayName);
                $xoopsTpl->assign('gtd_username', $username);
                $xoopsTpl->assign('gtd_user_id', $uid);
            } else {
                $xoopsTpl->assign('gtd_username', gtdGetUsername($xoopsUser->getVar('uid'), $displayName));
                $xoopsTpl->assign('gtd_user_id', $xoopsUser->getVar('uid'));
            }
            // Used for displaying transparent-background images in IE
            $xoopsTpl->assign('xoops_module_header',$javascript . $gtd_module_header);
            $xoopsTpl->assign('gtd_isStaff', $isStaff);
            
            $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
			$xoopsTpl->assign('xoops_themecss', xoops_getcss());
//            require(XOOPS_ROOT_PATH.'/footer.php');
			$xoopsTpl->display('db:gtd_editInternas.html');
        } else {
            require_once(GTD_CLASS_PATH.'/validator.php');
            
            $v = array();
	        $_gtdSession->set('gtd_ticket_internas', 
            array('observations_paiement' => $_POST['observations_paiement']));
                  
            // Perform each validation
            $fields = array();
            $errors = array();
            foreach($v as $fieldname=>$validator) {
                if (!gtdCheckRules($validator, $errors)) {
                    //Mark field with error
                    $fields[$fieldname]['haserrors'] = true;
                    $fields[$fieldname]['errors'] = $errors;
                } else {
                    $fields[$fieldname]['haserrors'] = false;
                }
            }  
    
            if(!empty($errors)){
                $_gtdSession->set('gtd_validateError', $fields);
                $message = _GTD_MESSAGE_VALIDATE_ERROR;
                header("Location: ".GTD_BASE_URL."/internas.php?id=$gtd_id&op=edit");
                exit();
            }
            
            $oldTicket = array('id'=>$ticketInfo->getVar('id'), 'observations_paiement'=>$ticketInfo->getVar('observations_paiement'));
            
            // Change ticket info to new info
			$ticketInfo->setVar('id', $ticketInfo->getVar('id'));
			$ticketInfo->setVar('observations_paiement', $_POST['observations_paiement']);

			$xoopsLogger->addExtra('internas.php', 'appel de la fonction update_observations_paiement');
            if($hTickets->update_observations_paiement($ticketInfo)){
                $_eventsrv->trigger('edit_ticket', array(&$oldTicket, &$ticketInfo));
                $message = _GTD_MESSAGE_EDITTICKET;     // Successfully updated ticket
                
                $_gtdSession->del('gtd_ticket_internas');
                $_gtdSession->del('gtd_validateError');
                $_gtdSession->del('gtd_custFields');
            } else {
                $message = _GTD_MESSAGE_EDITTICKET_ERROR . $ticketInfo->getHtmlErrors();     // Unsuccessfully updated ticket
            }
			
            redirect_header(GTD_BASE_URL."/internas.php?id=$gtd_id", 3, $message);
        }
    break;        
    case "print":
        $config_handler =& xoops_gethandler('config');
	    $xoopsConfigMetaFooter =& $config_handler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
	    
        $patterns = array();
        $patterns[] = '/pri:([1-5])/';
        $replacements = array();
        $replacements = '<img src="images/genre$1print.png" />';
        
        foreach($logMessage as $msg){
            $msgLoggedBy =& $member_handler->getUser($msg->getVar('uid'));
            $aPrintMessages[] = array('id'=>$msg->getVar('id'),
                                 'uid'=>$msg->getVar('uid'),
                                 'uname'=>gtdGetUsername($msgLoggedBy->getVar('uid'), $displayName),
                                 'ticketid'=>$msg->getVar('ticketid'),
                                 'lastUpdated'=>$msg->lastUpdated('m'),
                                 'action'=>preg_replace($patterns, $replacements, $msg->getVar('action')));
            $all_users[$msg->getVar('uid')] = '';
        }
        unset($logMessage);
        
        require_once XOOPS_ROOT_PATH.'/class/template.php';
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->assign('gtd_imagePath', XOOPS_URL .'/modules/gtd/images/');
        $xoopsTpl->assign('gtd_lang_userlookup', 'User Lookup');
        $xoopsTpl->assign('sitename', $xoopsConfig['sitename']);
        $xoopsTpl->assign('xoops_themecss', xoops_getcss());
        $xoopsTpl->assign('xoops_url', XOOPS_URL);
        $xoopsTpl->assign('gtd_print_logMessages', $aPrintMessages);
        $xoopsTpl->assign('gtd_ticket_subject', $ticketInfo->getVar('subject'));
	$xoopsTpl->assign('gtd_ticket_nom_danseur', $ticketInfo->getVar('nom_danseur'));
	$xoopsTpl->assign('gtd_ticket_dossier', $ticketInfo->getVar('dossier'));
	$xoopsTpl->assign('gtd_ticket_prenom_danseur', $ticketInfo->getVar('prenom_danseur'));
	$xoopsTpl->assign('gtd_ticket_nom_danseuse', $ticketInfo->getVar('nom_danseuse'));	
	$xoopsTpl->assign('gtd_ticket_prenom_danseuse', $ticketInfo->getVar('prenom_danseuse'));
	$xoopsTpl->assign('gtd_ticket_depart', $ticketInfo->getVar('depart'));
	$xoopsTpl->assign('gtd_ticket_mode_paiement', $ticketInfo->getVar('mode_paiement'));
	$xoopsTpl->assign('gtd_ticket_echeance', $ticketInfo->getVar('echeance'));
	$xoopsTpl->assign('gtd_ticket_porteur_carte', $ticketInfo->getVar('porteur_carte'));
	$xoopsTpl->assign('gtd_ticket_numero_carte', $ticketInfo->getVar('numero_carte'));
	$xoopsTpl->assign('gtd_ticket_crypto_carte', $ticketInfo->getVar('crypto_carte'));
	$xoopsTpl->assign('gtd_ticket_expiration_carte', $ticketInfo->getVar('expiration_carte'));
	$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticketInfo->getVar('observations_paiement'));
	$xoopsTpl->assign('gtd_ticket_pvp', $ticketInfo->getVar('pvp'));
	$xoopsTpl->assign('gtd_ticket_montant_reduc', $ticketInfo->getVar('montant_reduc'));
	$xoopsTpl->assign('gtd_ticket_taux_reduc', $ticketInfo->getVar('taux_reduc'));		
	$xoopsTpl->assign('gtd_ticket_net_hotel', $ticketInfo->getVar('net_hotel'));
        $xoopsTpl->assign('gtd_ticket_description', $ticketInfo->getVar('description'));
        $xoopsTpl->assign('gtd_ticket_department', $department->getVar('department'));
        $xoopsTpl->assign('gtd_ticket_genre', $ticketInfo->getVar('genre'));
        $xoopsTpl->assign('gtd_ticket_status', gtdGetStatus($ticketInfo->getVar('status')));
        $xoopsTpl->assign('gtd_ticket_lastUpdated', $ticketInfo->lastUpdated('m'));
        $xoopsTpl->assign('gtd_ticket_posted', $ticketInfo->posted('m'));
        if($giveOwnership){
            $xoopsTpl->assign('gtd_ticket_ownerUid', $owner->getVar('uid'));
            $xoopsTpl->assign('gtd_ticket_ownership', gtdGetUsername($owner, $displayName));
            $xoopsTpl->assign('gtd_ownerinfo', XOOPS_URL . '/userinfo.php?uid=' . $owner->getVar('uid'));
        }
        $xoopsTpl->assign('gtd_ticket_closedBy', $ticketInfo->getVar('closedBy'));
        $xoopsTpl->assign('gtd_ticket_totalTimeSpent', $ticketInfo->getVar('totalTimeSpent'));
        $xoopsTpl->assign('gtd_userinfo', XOOPS_URL . '/userinfo.php?uid=' . $ticketInfo->getVar('uid'));        
        $xoopsTpl->assign('gtd_username', gtdGetUsername($user, $displayName));
        $xoopsTpl->assign('gtd_ticket_details', sprintf(_GTD_TEXT_TICKETDETAILS, $gtd_id));
        
        $custFields =& $ticketInfo->getCustFieldValues();
        $xoopsTpl->assign('gtd_hasCustFields', (!empty($custFields)) ? true : false);
        $xoopsTpl->assign('gtd_custFields', $custFields);

        if(isset($aMessages)){
            $xoopsTpl->assign('gtd_logMessages', $aMessages);
        } else {
            $xoopsTpl->assign('gtd_logMessages', 0);
        }
        $xoopsTpl->assign('gtd_text_claimOwner', _GTD_TEXT_CLAIM_OWNER);
        $xoopsTpl->assign('gtd_aOwnership', $aOwnership);
        
        if($has_responses){
            $users = array(); 
            $_users = $member_handler->getUsers(new Criteria('uid', '('. implode(array_keys($all_users), ',') . ')', 'IN'), true);
            foreach ($_users as $key=>$_user) {
                if (($displayName == 2) && ($_user->getVar('name') <> '')) {
                    $users[$_user->getVar('uid')] = array('uname' => $_user->getVar('name'));
                } else {
                    $users[$_user->getVar('uid')] = array('uname' => $_user->getVar('uname'));
                }
            }
            unset($_users);
            
                    
            $myTs =& MyTextSanitizer::getInstance();
            //Update arrays with user information
            if(count($aResponses) > 0){
                for($i=0;$i<count($aResponses);$i++) {
                    if(isset($users[$aResponses[$i]['uid']])){      // Add uname to array
                        $aResponses[$i]['uname'] = $users[$aResponses[$i]['uid']]['uname'];
                    } else {
                        $aResponses[$i]['uname'] = $xoopsConfig['anonymous'];
                    }
                }
            }
            $xoopsTpl->assign('gtd_aResponses', $aResponses);
        } else {
            $xoopsTpl->assign('gtd_aResponses', 0);
        }
        $xoopsTpl->assign('gtd_claimOwner', $xoopsUser->getVar('uid'));
        $xoopsTpl->assign('gtd_hasResponses', $has_responses);
        $xoopsTpl->assign('xoops_meta_robots', $xoopsConfigMetaFooter['meta_robots']);
        $xoopsTpl->assign('xoops_meta_keywords', $xoopsConfigMetaFooter['meta_keywords']);
        $xoopsTpl->assign('xoops_meta_description', $xoopsConfigMetaFooter['meta_description']);
        $xoopsTpl->assign('xoops_meta_rating', $xoopsConfigMetaFooter['meta_rating']);
        $xoopsTpl->assign('xoops_meta_author', $xoopsConfigMetaFooter['meta_author']);
        $xoopsTpl->assign('xoops_meta_copyright', $xoopsConfigMetaFooter['meta_copyright']);

        $module_dir = $xoopsModule->getVar('mid');
        $xoopsTpl->display('db:gtd_print.html');
        exit();
    break;    
    default:
        redirect_header(GTD_BASE_URL."/index.php", 3);
    break;
}
}
?>