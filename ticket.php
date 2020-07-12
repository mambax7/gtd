<?php
//$Id: ticket.php,v 1.138 2005/10/24 18:10:37 eric_juden Exp $
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

if(!$xoopsUser){
    redirect_header(XOOPS_URL .'/user.php?xoops_redirect='.htmlspecialchars($xoopsRequestUri), 3);     
}

$xoopsVersion = substr(XOOPS_VERSION, 6);
intval($xoopsVersion);

global $ticketInfo;

$hStaff         =& gtdGetHandler('staff');
$member_handler =& xoops_gethandler('member');
$hTickets       =& gtdGetHandler('ticket');
if(!$ticketInfo     =& $hTickets->get($gtd_id)){
    redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_INV_TICKET);
}

/* CALCUL DU A PAYER*/
if ((float)$ticketInfo->getVar('montant_reduc') > 0.0)
	$aPayer = $ticketInfo->getVar('pvp') - $ticketInfo->getVar('montant_reduc');
else
	$aPayer = $ticketInfo->getVar('pvp') - ((float)$ticketInfo->getVar('taux_reduc') * $ticketInfo->getVar('pvp') / 100);
$xoopsLogger->addExtra('ticket.php:66', "aPayer=$aPayer");
$ticketInfo->setVar('a_payer', $aPayer);
$xoopsLogger->addExtra('ticket.php:68', "aPayer via getVar" . $aPayer);

/* CALCUL DE LA MARGE*/
if ((float)$ticketInfo->getVar('montant_reduc') > 0.0)
	$Marge = $ticketInfo->getVar('pvp') - $ticketInfo->getVar('montant_reduc') - $ticketInfo->getVar('net_hotel');
else
	$Marge = $ticketInfo->getVar('pvp') - ((float)$ticketInfo->getVar('taux_reduc') * $ticketInfo->getVar('pvp') / 100) - $ticketInfo->getVar('net_hotel');
$ticketInfo->setVar('marge', $Marge);
$xoopsLogger->addExtra('ticket.php:77', "Marge = $Marge");
/* FIN DES CALCULS*/

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

$has_ticketFiles = false;
$files = $ticketInfo->getFiles();
$aFiles = array();
foreach($files as $file){
    if($file->getVar('responseid') == 0){
        $has_ticketFiles = true;
    }
    
    $filename_full = $file->getVar('filename');
    if($file->getVar('responseid') != 0){
        $removeText = $file->getVar('ticketid')."_".$file->getVar('responseid')."_";
    } else {
        $removeText = $file->getVar('ticketid')."_";
    }
    $filename = str_replace($removeText, '', $filename_full);
    $filesize = round(filesize(GTD_UPLOAD_PATH."/".$filename_full)/1024, 2);
    
    $aFiles[] = array('id'=>$file->getVar('id'),
                      'filename'=>$filename,
                      'filename_full'=>$filename_full,
                      'ticketid'=>$file->getVar('ticketid'),
                      'responseid'=>$file->getVar('responseid'),
                      'path'=>'viewFile.php?id='. $file->getVar('id'),
                      'size'=>$filesize." "._GTD_SIZE_KB);
}
$has_files = count($files) > 0;
unset($files);
$message = '';
       
if($isStaff) {
    //** BTW - What does $giveOwnership do here?
    $giveOwnership = false;
    if(isset($_GET['op'])){
        $op = $_GET['op'];
    } else {
        $op = "staff";
    }

    //Retrieve all responses to current ticket
    $responses = $ticketInfo->getResponses();
    foreach($responses as $response){
        if($has_files){
            $hasFiles = false;
            foreach($aFiles as $file){
                if($file['responseid'] == $response->getVar('id')){
                    $hasFiles = true;
                    break;
                }
            }
        } else {
            $hasFiles = false;
        }
        
        $aResponses[] = array('id'=>$response->getVar('id'),
                          'uid'=>$response->getVar('uid'),
                          'uname'=>'',
                          'ticketid'=>$response->getVar('ticketid'),
                          'message'=>$response->getVar('message'),
                          'timeSpent'=>$response->getVar('timeSpent'),
                          'updateTime'=>$response->posted('m'),
                          'userIP'=>$response->getVar('userIP'),
                          'user_sig'=>'',
                          'user_avatar' => '',
                          'attachSig'=>'',
                          'staffRating'=>'',
                          'private'=>$response->getVar('private'),
                          'hasFiles' => $hasFiles);
        $all_users[$response->getVar('uid')] = '';
    }
    
    $all_users[$ticketInfo->getVar('uid')] = '';
    $all_users[$ticketInfo->getVar('ownership')] = '';
    $all_users[$ticketInfo->getVar('closedBy')] = '';
    
    $has_responses = count($responses) > 0;
    unset($responses);
    
    
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
    
    // Get list of user's last submitted tickets
    $crit = new CriteriaCompo(new Criteria('uid', $ticketInfo->getVar('uid')));
    $crit->setSort('posted');
    $crit->setOrder('DESC');
    $crit->setLimit(5);
    $lastTickets =& $hTickets->getObjects($crit);
    foreach($lastTickets as $ticket){
        
        $dept = $ticket->getVar('department');
        if (isset($departments[$dept])) {
            $dept = $departments[$dept]->getVar('department');
            $hasUrl = true;
        } else {
            $dept = _GTD_TEXT_NO_DEPT;
            $hasUrl = false;
        }
	    $aLastTickets[] = array('id'=>$ticket->getVar('id'),
	                            'dossier'=>$ticket->getVar('dossier'),
	                            'status'=>gtdGetStatus($ticket->getVar('status')),
	                            'department'=>$dept,
	                            'dept_url'=>($hasUrl ? XOOPS_URL . '/modules/gtd/index.php?op=staffViewAll&amp;dept=' . $ticket->getVar('department') : ''),
	                            'url'=>XOOPS_URL . '/modules/gtd/ticket.php?id=' . $ticket->getVar('id')); 
    }
    $has_lastTickets = count($lastTickets);
}

$xoopsLogger->addExtra('ticket.php:241', "aPayer=$aPayer, op = $op");
switch($op)
{
    case "addEmail":
        //TODO: Add email validator to make sure supplying a valid email address
    
        if($_POST['newEmail'] == ''){
            $message = _GTD_MESSAGE_NO_EMAIL;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        
        if(!$newUser = gtdEmailIsXoopsUser($_POST['newEmail'])){      // If a user doesn't exist with this email
            $user_id = 0;
        } else {
            $user_id = $newUser->getVar('uid');
        }
        
        // Check that the email doesn't already exist for this ticket
        $hTicketEmails =& gtdGetHandler('ticketEmails');
        $crit = new CriteriaCompo(new Criteria('ticketid', $gtd_id));
        $crit->add(new Criteria('email', $_POST['newEmail']));
        $existingUsers =& $hTicketEmails->getObjects($crit);
        if(count($existingUsers) > 0){
            $message = _GTD_MESSAGE_EMAIL_USED;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        
        // Create new ticket email object
        $newSubmitter =& $hTicketEmails->create();
        $newSubmitter->setVar('email', $_POST['newEmail']);
        $newSubmitter->setVar('uid', $user_id);
        $newSubmitter->setVar('ticketid', $gtd_id);
        $newSubmitter->setVar('suppress', 0);
        if($hTicketEmails->insert($newSubmitter)){
            $message = _GTD_MESSAGE_ADDED_EMAIL;
            header("Location: ".GTD_BASE_URL."/ticket.php?id=$gtd_id#emailNotification");
        } else {
            $message = _GTD_MESSAGE_ADDED_EMAIL_ERROR;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id#emailNotification", 3, $message);
        }
    break;
    
    case "changeSuppress":
        if(!$isStaff){
            $message = _GTD_MESSAGE_NO_MERGE_TICKET;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }

        $hTicketEmails =& gtdGetHandler('ticketEmails');        
        $crit = new CriteriaCompo(new Criteria('ticketid', $_GET['id']));
        $crit->add(new Criteria('email', $_GET['email']));
        $suppressUser =& $hTicketEmails->getObjects($crit);
        
        foreach($suppressUser as $sUser){
            if($sUser->getVar('suppress') == 0){
                $sUser->setVar('suppress', 1);
            } else {
                $sUser->setVar('suppress', 0);
            }
            if(!$hTicketEmails->insert($sUser, true)){
                $message = _GTD_MESSAGE_ADD_EMAIL_ERROR;
                redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id#emailNotification", 3, $message);
            }
        }
        header("Location: ".GTD_BASE_URL."/ticket.php?id=$gtd_id#emailNotification");
    break;
    
    case "delete":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_DELETE, $ticketInfo->getVar('department'))){
            $message = _GTD_MESSAGE_NO_DELETE_TICKET;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        if(isset($_POST['delete_ticket'])){
            if($hTickets->delete($ticketInfo)){
                $message = _GTD_MESSAGE_DELETE_TICKET;
                $_eventsrv->trigger('delete_ticket', array(&$ticketInfo));
            } else {
                $message = _GTD_MESSAGE_DELETE_TICKET_ERROR;
            }
        } else {
            $message = _GTD_MESSAGE_DELETE_TICKET_ERROR;
        }
        redirect_header(GTD_BASE_URL."/index.php", 3, $message);
    break;
    
    case "edit":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_EDIT, $ticketInfo->getVar('department'))){
            $message = _GTD_MESSAGE_NO_EDIT_TICKET;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        $hDepartments  =& gtdGetHandler('department');    // Department handler
        $custFields =& $ticketInfo->getCustFieldValues(true);
        
        if(!isset($_POST['editTicket'])){
            $xoopsOption['template_main'] = 'gtd_editTicket.html';             // Always set main template before including the header
            require(XOOPS_ROOT_PATH . '/header.php');
            
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
            
            $elements = array('nom_danseur', 'prenom_danseur', 'nom_danseuse', 'prenom_danseuse','mode_paiement', 'echeance', 'observation_paiement', 'pvp', 'montant_reduc', 'taux_reduc', 'net_hotel', 'description', 'a_payer', 'marge');
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
            if ($ticket =& $_gtdSession->get('gtd_ticket')) {
                $xoopsTpl->assign('gtd_ticketID', $gtd_id);
                $xoopsTpl->assign('gtd_ticket_subject', $ticket['subject']);
		$xoopsTpl->assign('gtd_ticket_nom_danseur', $ticket['nom_danseur']);
		$xoopsTpl->assign('gtd_ticket_prenom_danseur', $ticket['prenom_danseur']);
		$xoopsTpl->assign('gtd_ticket_nom_danseuse', $ticket['nom_danseuse']);
		$xoopsTpl->assign('gtd_ticket_prenom_danseuse', $ticket['prenom_danseuse']);
		$xoopsTpl->assign('gtd_ticket_mode_paiement', $ticket['mode_paiement']);
		$xoopsTpl->assign('gtd_ticket_echeance', $ticket['echeance']);
		$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticket['observations_paiement']);
		$xoopsTpl->assign('gtd_ticket_pvp', $ticket['pvp']);
		$xoopsTpl->assign('gtd_ticket_a_payer', $ticket['a_payer']);
		$xoopsTpl->assign('gtd_ticket_marge', $ticket['marge']);
		$xoopsTpl->assign('gtd_ticket_montant_reduc', $ticket['montant_reduc']);
		$xoopsTpl->assign('gtd_ticket_taux_reduc', $ticket['taux_reduc']);
		$xoopsTpl->assign('gtd_ticket_net_hotel', $ticket['net_hotel']);
                $xoopsTpl->assign('gtd_ticket_description', $ticket['description']);
                $xoopsTpl->assign('gtd_ticket_department', $ticket['department']);
                $xoopsTpl->assign('gtd_departmenturl', 'index.php?op=staffViewAll&amp;dept='. $ticket['department']);
                $xoopsTpl->assign('gtd_ticket_genre', $ticket['genre']);
            } else {
                $xoopsTpl->assign('gtd_ticketID', $gtd_id);
                $xoopsTpl->assign('gtd_ticket_subject', $ticketInfo->getVar('subject'));
		$xoopsTpl->assign('gtd_ticket_nom_danseur', $ticketInfo->getVar('nom_danseur'));
		$xoopsTpl->assign('gtd_ticket_prenom_danseur', $ticketInfo->getVar('prenom_danseur'));
		$xoopsTpl->assign('gtd_ticket_nom_danseuse', $ticketInfo->getVar('nom_danseuse'));
		$xoopsTpl->assign('gtd_ticket_prenom_danseuse', $ticketInfo->getVar('prenom_danseuse'));
		$xoopsTpl->assign('gtd_ticket_mode_paiement', $ticketInfo->getVar('mode_paiement'));	
		$xoopsTpl->assign('gtd_ticket_echeance', $ticketInfo->getVar('echeance'));
		$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticketInfo->getVar('observations_paiement'));
		$xoopsTpl->assign('gtd_ticket_pvp', $ticketInfo->getVar('pvp'));
		$xoopsTpl->assign('gtd_ticket_a_payer', $aPayer);
		$xoopsTpl->assign('gtd_ticket_marge', $Marge);
		$xoopsTpl->assign('gtd_ticket_montant_reduc', $ticketInfo->getVar('montant_reduc'));
		$xoopsTpl->assign('gtd_ticket_taux_reduc', $ticketInfo->getVar('taux_reduc'));		
		$xoopsTpl->assign('gtd_ticket_net_hotel', $ticketInfo->getVar('net_hotel'));
                $xoopsTpl->assign('gtd_ticket_description', $ticketInfo->getVar('description', 'e'));
                $xoopsTpl->assign('gtd_ticket_department', $ticketInfo->getVar('department'));
                $xoopsTpl->assign('gtd_departmenturl', 'index.php?op=staffViewAll&amp;dept='. $ticketInfo->getVar('department'));
                $xoopsTpl->assign('gtd_ticket_genre', $ticketInfo->getVar('genre'));
            }
                                             
            //** BTW - why do we need gtd_allowUpload in the template if it will be always set to 0?
            //$xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
            $xoopsTpl->assign('gtd_allowUpload', 0);
            $xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
            $xoopsTpl->assign('gtd_departments', $aDept);
            $xoopsTpl->assign('gtd_genres', array(3,2,1));
            $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));
	    $xoopsTpl->assign('gtd_mode_paiements', array(1, 2));
            $xoopsTpl->assign('gtd_mode_paiements_desc', array('1' => _GTD_MODE_PAIEMENT1, '2' => _GTD_MODE_PAIEMENT2));
	$xoopsTpl->assign('gtd_echeances', array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));  
	$xoopsTpl->assign('gtd_echeances_desc', array('1'=>_GTD_MODALITE1, '2'=>_GTD_MODALITE2, '3'=>_GTD_MODALITE3, '4'=>_GTD_MODALITE4, '5'=>_GTD_MODALITE5
	, '6'=>_GTD_MODALITE6, '7'=>_GTD_MODALITE7, '8'=>_GTD_MODALITE8, '9'=>_GTD_MODALITE9, '10'=>_GTD_MODALITE10));  
            
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
            
            if ($savedFields =& $_gtdSession->get('gtd_custFields')) {
                $custFields = $savedFields;
            }                
            $xoopsTpl->assign('gtd_hasCustFields', (!empty($custFields)) ? true : false);
            $xoopsTpl->assign('gtd_custFields', $custFields);
            $xoopsTpl->assign('gtd_uploadPath', GTD_UPLOAD_PATH);
            $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
            
            require(XOOPS_ROOT_PATH.'/footer.php');
        } else {
            require_once(GTD_CLASS_PATH.'/validator.php');
            
            /*$v = array(); //Champs obligatoires
	    $v['nom_danseur'][] = new ValidateLength($_POST['nom_danseur'], 2, 100);
	    $v['nom_danseuse'][] = new ValidateLength($_POST['nom_danseuse'], 1, 100);
	    $v['prenom_danseur'][] = new ValidateLength($_POST['prenom_danseur'], 2, 100);
	    $v['prenom_danseuse'][] = new ValidateLength($_POST['prenom_danseuse'], 1, 10);
	    $v['mode_paiement'][] = new ValidateLength($_POST['mode_paiement'], 1, 100);
	    $v['echeance'][] = new ValidateLength($_POST['echeance'], 0, 100);
	    $v['pvp'][] = new ValidateLength($_POST['pvp'], 1, 100);*/

            
            $aFields = array();
            foreach($custFields as $field){
                $fieldname = $field['fieldname'];
                $value = $_POST[$fieldname];
                
                $fileid = '';
                $filename = '';
                $file = '';
                if($field['controltype'] == GTD_CONTROL_FILE){
                    $file = split("_", $value);
                    $fileid = ((isset($file[0]) && $file[0] != "") ? $file[0] : "");
                    $filename = ((isset($file[1]) && $file[1] != "") ? $file[1] : "");
                }
                
                if($field['validation'] != ""){
                    $v[$fieldname][] = new ValidateRegex($_POST[$fieldname], $field['validation'], $field['required']);
                }
                
                $aFields[$field['fieldname']] = 
                    array('id' => $field['id'],
                          'name' => $field['name'],
                          'description' => $field['description'],
                          'fieldname' => $field['fieldname'],
                          'controltype' => $field['controltype'],
                          'datatype' => $field['datatype'],
                          'required' => $field['required'],
                          'fieldlength' => $field['fieldlength'],
                          'weight' => $field['weight'],
                          'fieldvalues' => $field['fieldvalues'],
                          'defaultvalue' => $field['defaultvalue'],
                          'validation' => $field['validation'],
                          'value' => $value,
                          'fileid' => $fileid,
                          'filename' => $filename
                         );
            }
            unset($custFields);
            
            $_gtdSession->set('gtd_custFields', $aFields);
            $_gtdSession->set('gtd_ticket', 
            array('nom_danseur' => $_POST['nom_danseur'],		  
		  'prenom_danseur' => $_POST['prenom_danseur'],
		  'nom_danseuse' => $_POST['nom_danseuse'],
		  'prenom_danseuse' => $_POST['prenom_danseuse'],
		  'mode_paiement' => $_POST['mode_paiement'],
		  'echeance' => $_POST['echeance'],
		  'observations_paiement' => $_POST['observations_paiement'],
		  'pvp' => $_POST['pvp'],
		  'montant_reduc' => $_POST['montant_reduc'],
		  'taux_reduc' => $_POST['taux_reduc'],
		  'net_hotel' => $_POST['net_hotel'],
		  'description' => htmlspecialchars($_POST['description'], ENT_QUOTES),
                  'department' => $_POST['departments'],
                  'genre' => $_POST['genre']));
                  
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
                header("Location: ".GTD_BASE_URL."/ticket.php?id=$gtd_id&op=edit");
                exit();
            }
            
            $oldTicket = array('id'=>$ticketInfo->getVar('id'),
			      'nom_danseur'=>$ticketInfo->getVar('nom_danseur'),
			      'prenom_danseur'=>$ticketInfo->getVar('prenom_danseur'),
			      'nom_danseuse'=>$ticketInfo->getVar('nom_danseuse'),
                              'prenom_danseuse'=>$ticketInfo->getVar('prenom_danseuse'),
			      'mode_paiement'=>$ticketInfo->getVar('mode_paiement'),
                              'echeance'=>$ticketInfo->getVar('echeance'),
			      'observations_paiement'=>$ticketInfo->getVar('observations_paiement'),
                              'pvp'=>$ticketInfo->getVar('pvp'),
                              'montant_reduc'=>$ticketInfo->getVar('montant_reduc'),
                              'taux_reduc'=>$ticketInfo->getVar('taux_reduc'),
                              'net_hotel'=>$ticketInfo->getVar('net_hotel'),				      
                               'description'=>$ticketInfo->getVar('description', 'n'),
                               'genre'=>$ticketInfo->getVar('genre'),
                               'status'=>gtdGetStatus($ticketInfo->getVar('status')),
                               'department'=>$department->getVar('department'),
                               'department_id'=>$department->getVar('id'));
            
            // Change ticket info to new info
	    $ticketInfo->setVar('nom_danseur', $_POST['nom_danseur']);
	    $ticketInfo->setVar('prenom_danseur', $_POST['prenom_danseur']);
	    $ticketInfo->setVar('nom_danseuse', $_POST['nom_danseuse']);	    
	    $ticketInfo->setVar('prenom_danseuse', $_POST['prenom_danseuse']);
	    $ticketInfo->setVar('mode_paiement', $_POST['mode_paiement']);
	    $ticketInfo->setVar('echeance', $_POST['echeance']);
	    $ticketInfo->setVar('observations_paiement', $_POST['observations_paiement']);
	    $ticketInfo->setVar('pvp', $_POST['pvp']);
	    $ticketInfo->setVar('montant_reduc', $_POST['montant_reduc']);
	    $ticketInfo->setVar('taux_reduc', $_POST['taux_reduc']);
	    $ticketInfo->setVar('net_hotel', $_POST['net_hotel']);
            $ticketInfo->setVar('description', $_POST['description']);
            $ticketInfo->setVar('department', $_POST['departments']);
            $ticketInfo->setVar('genre', $_POST['genre']);
            $ticketInfo->setVar('posted', time());

            if($hTickets->insert($ticketInfo)){
                $_eventsrv->trigger('edit_ticket', array(&$oldTicket, &$ticketInfo));
                $message = _GTD_MESSAGE_EDITTICKET;     // Successfully updated ticket
                
                // Update custom fields
                $hTicketValues = gtdGetHandler('ticketValues');
                $ticketValues = $hTicketValues->get($gtd_id);
                
                if(is_object($ticketValues)){
                    foreach($aFields as $field){
                        $ticketValues->setVar($field['fieldname'], $_POST[$field['fieldname']]);
                    }
                
                    if(!$hTicketValues->insert($ticketValues)){
                        $message = _GTD_MESSAGE_NO_CUSTFLD_ADDED;
                    }
                }
                
                $_gtdSession->del('gtd_ticket');
                $_gtdSession->del('gtd_validateError');
                $_gtdSession->del('gtd_custFields');
            } else {
                $message = _GTD_MESSAGE_EDITTICKET_ERROR . $ticketInfo->getHtmlErrors();     // Unsuccessfully updated ticket
            }
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
    break;
    
    case "merge":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_MERGE, $ticketInfo->getVar('department'))){
            $message = _GTD_MESSAGE_NO_MERGE;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        if($_POST['ticket2'] == ''){
            $message = _GTD_MESSAGE_NO_TICKET2;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        
        $ticket2_id = intval($_POST['ticket2']);
        if($newTicket = $ticketInfo->merge($ticket2_id)){
            $returnTicket = $newTicket;
            $message = _GTD_MESSAGE_MERGE;
            $_eventsrv->trigger('merge_tickets', array($gtd_id, $ticket2_id, $returnTicket));
        } else {
            $returnTicket = $gtd_id;
            $message = _GTD_MESSAGE_MERGE_ERROR;
        }
        redirect_header(GTD_BASE_URL."/ticket.php?id=$returnTicket", 3, $message);
        
    break;
    
    case "ownership":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_OWNERSHIP, $ticketInfo->getVar('department'))){
            $message = _GTD_MESSAGE_NO_CHANGE_OWNER;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
            
        if(isset($_POST['uid'])){
            $uid = intval($_POST['uid']);
        } else {
            $message = _GTD_MESSAGE_NO_UID;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        if($ticketInfo->getVar('ownership') <> 0){
            $oldOwner = $ticketInfo->getVar('ownership');
        } else {
            $oldOwner = _GTD_NO_OWNER;
        }
        
        $ticketInfo->setVar('ownership', $uid);
        $ticketInfo->setVar('lastUpdated', time());
        if($hTickets->insert($ticketInfo)){
            $_eventsrv->trigger('update_owner', array(&$ticketInfo, $oldOwner));
            $message = _GTD_MESSAGE_UPDATE_OWNER; 
        }
        redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        
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
	$xoopsTpl->assign('gtd_ticket_prenom_danseur', $ticketInfo->getVar('prenom_danseur'));
	$xoopsTpl->assign('gtd_ticket_nom_danseuse', $ticketInfo->getVar('nom_danseuse'));	
	$xoopsTpl->assign('gtd_ticket_prenom_danseuse', $ticketInfo->getVar('prenom_danseuse'));
	$xoopsTpl->assign('gtd_ticket_mode_paiement', $ticketInfo->getVar('mode_paiement'));
	$xoopsTpl->assign('gtd_ticket_echeance', $ticketInfo->getVar('echeance'));
	$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticketInfo->getVar('observations_paiement'));
	$xoopsTpl->assign('gtd_ticket_pvp', $ticketInfo->getVar('pvp'));
	$xoopsTpl->assign('gtd_ticket_a_payer', $aPayer);
	$xoopsTpl->assign('gtd_ticket_marge', $Marge);
	$xoopsTpl->assign('gtd_ticket_montant_reduc', $ticketInfo->getVar('montant_reduc'));
	$xoopsTpl->assign('gtd_ticket_taux_reduc', $ticketInfo->getVar('taux_reduc'));
	$xoopsTpl->assign('gtd_ticket_net_hotel', $ticketInfo->getVar('net_hotel'));
        $xoopsTpl->assign('gtd_ticket_description', $ticketInfo->getVar('description'));
        $xoopsTpl->assign('gtd_ticket_department', $department->getVar('department'));
        $xoopsTpl->assign('gtd_ticket_genre', $ticketInfo->getVar('genre'));
        $xoopsTpl->assign('gtd_ticket_status', gtdGetStatus($ticketInfo->getVar('status')));
        $xoopsTpl->assign('gtd_ticket_lastUpdated', $ticketInfo->lastUpdated('m'));
        $xoopsTpl->assign('gtd_ticket_posted', $ticketInfo->posted('m'));
	$xoopsTpl->assign('gtd_user_avatar', XOOPS_URL .'/uploads/' .(($submitUser && $submitUser->getVar('user_avatar') != "")?$submitUser->getVar('user_avatar') : 'blank.gif'));
	$xoopsTpl->assign('gtd_mode_paiements', array(1, 2));
	$xoopsTpl->assign('gtd_mode_paiements_desc', array('1' => _GTD_MODE_PAIEMENT1, '2' => _GTD_MODE_PAIEMENT2));
	$xoopsTpl->assign('gtd_echeances', array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));  
	$xoopsTpl->assign('gtd_echeances_desc', array('1'=>_GTD_MODALITE1, '2'=>_GTD_MODALITE2, '3'=>_GTD_MODALITE3, '4'=>_GTD_MODALITE4, '5'=>_GTD_MODALITE5
	, '6'=>_GTD_MODALITE6, '7'=>_GTD_MODALITE7, '8'=>_GTD_MODALITE8, '9'=>_GTD_MODALITE9, '10'=>_GTD_MODALITE10));  
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
    
    case "updatePriority":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_ADD)){
            $message = _GTD_MESSAGE_NO_ADD_TICKET;
            redirect_header(GTD_BASE_URL."/index.php", 3, $message);
        }
        
        if(isset($_POST['genre'])){
            $genre = $_POST['genre'];
        } else {
            $message = _GTD_MESSAGE_NO_GENRE;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        $oldPriority = $ticketInfo->getVar('genre');
        $ticketInfo->setVar('genre', $genre);
        $ticketInfo->setVar('lastUpdated', time());
        if($hTickets->insert($ticketInfo)){
            $_eventsrv->trigger('update_genre', array(&$ticketInfo, $oldPriority));
            $message = _GTD_MESSAGE_UPDATE_GENRE; 
        } else {
            $message = _GTD_MESSAGE_UPDATE_GENRE_ERROR .". ";
        }
        redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
    break;
    
    case "updateStatus":
        $hTicketEmails =& gtdGetHandler('ticketEmails');
        $crit = new CriteriaCompo(new Criteria('ticketid', $gtd_id));
        $crit->add(new Criteria('uid', $xoopsUser->getVar('uid')));
        $ticketEmails =& $hTicketEmails->getObjects($crit);
		if(count($ticketEmails > 0) || $gtd_staff->checkRoleRights(_GTD_SEC_RESPONSE_ADD, $ticketInfo->getVar('department'))){
            if($_POST['response'] <> ''){
                $userIP     = getenv("REMOTE_ADDR");
                $newResponse =& $ticketInfo->addResponse($xoopsUser->getVar('uid'), $gtd_id, $_POST['response'], 
                $ticketInfo->getVar('lastUpdated'), $userIP, 0, 0, true);
                        
                if(is_object($newResponse)){
                    $_eventsrv->trigger('new_response', array(&$ticketInfo, &$newResponse));
                    $message = _GTD_MESSAGE_ADDRESPONSE;
                }
                else
                	$message = _GTD_MESSAGE_ADDRESPONSE_ERROR;
        	$message .= '<br />';
            }
        }

        if(count($ticketEmails > 0) || $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_STATUS, $ticketInfo->getVar('department'))){
            $ticket_close = $ticket_reopen = false;
            $hStatus =& gtdGetHandler('status');
            $hStaff =& gtdGetHandler('staff');
            if($_POST['status'] != $ticketInfo->getVar('status')){  
                $oldStatus =& $hStatus->get($ticketInfo->getVar('status'));
                $ticketInfo->setVar('status', $_POST['status']);
                $ticketInfo->setVar('lastUpdated', time());
                
                // TODO: Change this to look for resolved tickets
                // Retrieve status object using $_POST['status']
                $newStatus =& $hStatus->get(intval($_POST['status']));
                
                if($newStatus->getVar('state') == 2){    // RESOLVED
                    $ticketInfo->setVar('closedBy', $xoopsUser->getVar('uid'));     // Update closedBy field in Ticket table
                    $ticket_close = true;
                } elseif($oldStatus->getVar('state') == 2 && $newStatus->getVar('state') == 1){
                    $ticket_reopen = true;
                    $ticketInfo->setVar('overdueTime', $ticketInfo->getVar('posted') + ($xoopsModuleConfig['gtd_overdueTime'] *60*60));
                }
                
                if($hTickets->insert($ticketInfo, true)){
                    if ($ticket_close) {
                        $_eventsrv->trigger('close_ticket', array(&$ticketInfo)); 
                    } elseif($ticket_reopen) {
                        $_eventsrv->trigger('reopen_ticket', array(&$ticketInfo));
                    } else {
                        $_eventsrv->trigger('update_status', array(&$ticketInfo, &$oldStatus, &$newStatus));
                    }
                    $message .= _GTD_MESSAGE_UPDATE_STATUS;
                } else {
                    $message .= _GTD_MESSAGE_UPDATE_STATUS_ERROR .". ";
                } 
            } 
         } else {
            $message .= _GTD_MESSAGE_NO_CHANGE_STATUS;
        }     
        redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
    break;
    
    case "staff":
        $hStatus =& gtdGetHandler('status');
        $_eventsrv->trigger('view_ticket', array(&$ticketInfo));
        $xoopsOption['template_main'] = 'gtd_staff_ticketDetails.html';   // Set template
        require(XOOPS_ROOT_PATH.'/header.php');                     // Include
        
        //TODO: Wrap this into a class/function
        $xoopsDB =& Database::getInstance();
        $users = array();     
        $_users = $member_handler->getUsers(new Criteria('uid', "(". implode(array_keys($all_users), ',') . ")", 'IN'), true);
        foreach ($_users as $key=>$_user) {
            if (($displayName == 2) && ($_user->getVar('name') <> '')) {
                $users[$key] = array('uname' => $_user->getVar('name'),
                                'user_sig' => $_user->getVar('user_sig'),
                                'user_avatar' => $_user->getVar('user_avatar'));
            } else {
                $users[$key] = array('uname' => $_user->getVar('uname'),
                                'user_sig' => $_user->getVar('user_sig'),
                                'user_avatar' => $_user->getVar('user_avatar'));
              }
          }
 
        $crit = new Criteria('','');
        $crit->setSort('department');
        $alldepts = $hDepartments->getObjects($crit);
        foreach($alldepts as $dept){
            $aDept[$dept->getVar('id')] = $dept->getVar('department');
        }
        unset($_users);            
        $staff = array();       
        $_staff = $hStaff->getObjects(new Criteria('uid', "(". implode(array_keys($all_users), ',') . ")", 'IN'), true);
        foreach($_staff as $key=>$_user) {
            $staff[$key] = $_user->getVar('attachSig');
        }
        unset($_staff);
        $staffReviews =& $ticketInfo->getReviews();
    
        $myTs =& MyTextSanitizer::getInstance();
        //Update arrays with user information
        if(count($aResponses) > 0){
            for($i=0;$i<count($aResponses);$i++) {
            	if(isset($users[$aResponses[$i]['uid']])){      // Add uname to array
                $aResponses[$i]['uname'] = $users[$aResponses[$i]['uid']]['uname'];
                    $aResponses[$i]['user_sig'] = $myTs->displayTarea($users[$aResponses[$i]['uid']]['user_sig'], true);
                    $aResponses[$i]['user_avatar'] = XOOPS_URL .'/uploads/' . ($users[$aResponses[$i]['uid']]['user_avatar'] ? $users[$aResponses[$i]['uid']]['user_avatar'] : 'blank.gif');
                } else {
                    $aResponses[$i]['uname'] = $xoopsConfig['anonymous'];
                }
                $aResponses[$i]['staffRating'] = _GTD_RATING0;
                
                if(isset($staff[$aResponses[$i]['uid']])){       // Add attachSig to array
                    $aResponses[$i]['attachSig'] = $staff[$aResponses[$i]['uid']];
                }
                
                if(count($staffReviews) > 0){                   // Add staffRating to array
                    foreach($staffReviews as $review){
                        if($aResponses[$i]['id'] == $review->getVar('responseid')){
                            $aResponses[$i]['staffRating'] = gtdGetRating($review->getVar('rating'));
                        }
                    }
                }
            }
        }
        
        for($i=0;$i<count($aMessages);$i++){        // Fill other values for log messages
            if(isset($users[$aMessages[$i]['uid']])){
                $aMessages[$i]['uname'] = $users[$aMessages[$i]['uid']]['uname'];
            } else {
                $aMessages[$i]['uname'] = $xoopsConfig['anonymous'];
            }
        }
        
        if($xoopsModuleConfig['gtd_staffTicketActions'] == 1){
            for($i=0;$i<count($aOwnership);$i++){
                if(isset($users[$aOwnership[$i]['uid']])){
                    $aOwnership[$i]['uname'] = $users[$aOwnership[$i]['uid']]['uname'];
                }
            }
        }
        
        // Get list of users notified of changes to ticket
        $hTicketEmails =& gtdGetHandler('ticketEmails');
        $crit = new Criteria('ticketid', $gtd_id);
        $crit->setOrder('ASC');
        $crit->setSort('email');
        $notifiedUsers =& $hTicketEmails->getObjects($crit);
        $aNotified = array();
        foreach($notifiedUsers as $nUser){
            $aNotified[] = array('email' => $nUser->getVar('email'),
                                 'suppress' => $nUser->getVar('suppress'),
                                 'suppressUrl' => XOOPS_URL."/modules/gtd/ticket.php?id=$gtd_id&amp;op=changeSuppress&amp;email=".$nUser->getVar('email'));
        }
        
        $uid = $xoopsUser->getVar('uid');
        $xoopsTpl->assign('gtd_uid', $uid);

        // Smarty variables
        $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
        $xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
        $xoopsTpl->assign('gtd_imagePath', XOOPS_URL .'/modules/gtd/images/');
        $xoopsTpl->assign('xoops_module_header',$gtd_module_header);
        $xoopsTpl->assign('gtd_ticketID', $gtd_id);
        $xoopsTpl->assign('gtd_ticket_uid', $ticketInfo->getVar('uid'));
        $submitUser =& $member_handler->getUser($ticketInfo->getVar('uid'));
        $xoopsTpl->assign('gtd_user_avatar', XOOPS_URL .'/uploads/' .(($submitUser && $submitUser->getVar('user_avatar') != "")?$submitUser->getVar('user_avatar') : 'blank.gif'));
        $xoopsTpl->assign('gtd_ticket_subject', $ticketInfo->getVar('subject', 's'));
		$xoopsTpl->assign('gtd_ticket_nom_danseur', $ticketInfo->getVar('nom_danseur'));
		$xoopsTpl->assign('gtd_ticket_prenom_danseur', $ticketInfo->getVar('prenom_danseur'));
		$xoopsTpl->assign('gtd_ticket_nom_danseuse', $ticketInfo->getVar('nom_danseuse'));
		$xoopsTpl->assign('gtd_ticket_prenom_danseuse', $ticketInfo->getVar('prenom_danseuse'));
		$xoopsTpl->assign('gtd_ticket_mode_paiement', $ticketInfo->getVar('mode_paiement'));
		$xoopsTpl->assign('gtd_ticket_echeance', $ticketInfo->getVar('echeance'));
		$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticketInfo->getVar('observations_paiement'));
		$xoopsTpl->assign('gtd_ticket_pvp', $ticketInfo->getVar('pvp'));
		$xoopsTpl->assign('gtd_ticket_montant_reduc', $ticketInfo->getVar('montant_reduc'));
		$xoopsTpl->assign('gtd_ticket_a_payer', $aPayer);
		$xoopsTpl->assign('gtd_ticket_marge', $Marge);
		$xoopsTpl->assign('gtd_ticket_taux_reduc', $ticketInfo->getVar('taux_reduc'));		
		$xoopsTpl->assign('gtd_ticket_net_hotel', $ticketInfo->getVar('net_hotel'));
        $xoopsTpl->assign('gtd_ticket_description', $ticketInfo->getVar('description'));
        $xoopsTpl->assign('gtd_ticket_department', (isset($departments[$ticketInfo->getVar('department')]) ? $departments[$ticketInfo->getVar('department')]->getVar('department') : _GTD_TEXT_NO_DEPT));
        $xoopsTpl->assign('gtd_departmenturl', 'index.php?op=staffViewAll&amp;dept='. $ticketInfo->getVar('department'));
	$xoopsTpl->assign('gtd_departmentid', $ticketInfo->getVar('department'));
        $xoopsTpl->assign('gtd_departments', $aDept);
	$xoopsTpl->assign('gtd_ticket_genre', $ticketInfo->getVar('genre'));
        $xoopsTpl->assign('gtd_ticket_status', $ticketInfo->getVar('status'));
        $xoopsTpl->assign('gtd_text_status', gtdGetStatus($ticketInfo->getVar('status')));
        $xoopsTpl->assign('gtd_ticket_userIP', $ticketInfo->getVar('userIP'));
        $xoopsTpl->assign('gtd_ticket_lastUpdated', $ticketInfo->lastUpdated('m'));
        $xoopsTpl->assign('gtd_genres', array(3, 2, 1));
        $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));
	$xoopsTpl->assign('gtd_mode_paiements', array(1));
        $xoopsTpl->assign('gtd_mode_paiements_desc', array('1' => _GTD_MODE_PAIEMENT1, '2' => _GTD_MODE_PAIEMENT2));
	$xoopsTpl->assign('gtd_echeances', array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));  
	$xoopsTpl->assign('gtd_echeances_desc', array('1'=>_GTD_MODALITE1, '2'=>_GTD_MODALITE2, '3'=>_GTD_MODALITE3, '4'=>_GTD_MODALITE4, '5'=>_GTD_MODALITE5
	, '6'=>_GTD_MODALITE6, '7'=>_GTD_MODALITE7, '8'=>_GTD_MODALITE8, '9'=>_GTD_MODALITE9, '10'=>_GTD_MODALITE10));  
        $xoopsTpl->assign('gtd_ticket_posted', $ticketInfo->posted('m'));
        if($giveOwnership){
	$xoopsTpl->assign('gtd_ticket_ownerUid', $owner->getVar('uid'));
	$xoopsTpl->assign('gtd_ticket_ownership', gtdGetUsername($owner, $displayName));
	$xoopsTpl->assign('gtd_ownerinfo', XOOPS_URL . '/userinfo.php?uid=' . $owner->getVar('uid'));
        }
        $xoopsTpl->assign('gtd_ticket_closedBy', $ticketInfo->getVar('closedBy'));
        $xoopsTpl->assign('gtd_ticket_totalTimeSpent', $ticketInfo->getVar('totalTimeSpent'));
        $xoopsTpl->assign('gtd_userinfo', XOOPS_URL . '/userinfo.php?uid=' . $ticketInfo->getVar('uid')); 
        $xoopsTpl->assign('gtd_username', (($user)?gtdGetUsername($user, $displayName):$xoopsConfig['anonymous']));
        $xoopsTpl->assign('gtd_userlevel', (($user)?$user->getVar('level'):0));
        $xoopsTpl->assign('gtd_email', (($user)?$user->getVar('email'):''));
        $xoopsTpl->assign('gtd_ticket_details', sprintf(_GTD_TEXT_TICKETDETAILS, $gtd_id));
        $xoopsTpl->assign('gtd_notifiedUsers', $aNotified);
        $xoopsTpl->assign('gtd_savedSearches', $aSavedSearches);

        if(isset($aMessages)){
            $xoopsTpl->assign('gtd_logMessages', $aMessages);
        } else {
            $xoopsTpl->assign('gtd_logMessages', 0);
        }
        $xoopsTpl->assign('gtd_aOwnership', $aOwnership);
        if($has_responses){
            $xoopsTpl->assign('gtd_aResponses', $aResponses);
        }
        if($has_files){
            $xoopsTpl->assign('gtd_aFiles', $aFiles);
            $xoopsTpl->assign('gtd_hasTicketFiles', $has_ticketFiles);
            
        } else {
            $xoopsTpl->assign('gtd_aFiles', false);
            $xoopsTpl->assign('gtd_hasTicketFiles', false);
        }
        $xoopsTpl->assign('gtd_claimOwner', $xoopsUser->getVar('uid'));
        $xoopsTpl->assign('gtd_hasResponses', $has_responses);
        $xoopsTpl->assign('gtd_hasFiles', $has_files);
        $xoopsTpl->assign('gtd_hasTicketFiles', $has_ticketFiles);
        $xoopsTpl->assign('gtd_filePath', XOOPS_URL . '/uploads/gtd/');
        $module_dir = $xoopsModule->getVar('mid');
        $xoopsTpl->assign('gtd_admin', $xoopsUser->isAdmin($module_dir));
        $xoopsTpl->assign('gtd_has_lastSubmitted', $has_lastTickets);
        $xoopsTpl->assign('gtd_lastSubmitted', $aLastTickets);
        $xoopsTpl->assign('xoops_pagetitle', $xoopsModule->getVar('name') . ' - ' . $ticketInfo->getVar('subject'));
        $xoopsTpl->assign('gtd_showActions', $xoopsModuleConfig['gtd_staffTicketActions']);
        
        $xoopsTpl->assign('gtd_has_changeOwner', false);
        if($ticketInfo->getVar('uid') == $xoopsUser->getVar('uid')){
            $xoopsTpl->assign('gtd_has_addResponse', true);
        } else {
            $xoopsTpl->assign('gtd_has_addResponse', false);
        }
        $xoopsTpl->assign('gtd_has_editTicket', false);
        $xoopsTpl->assign('gtd_has_deleteTicket', false);
        $xoopsTpl->assign('gtd_has_changePriority', false);
        $xoopsTpl->assign('gtd_has_changeStatus', false);
        $xoopsTpl->assign('gtd_has_editResponse', false);
        $xoopsTpl->assign('gtd_has_mergeTicket', false);
        $colspan = 5;
        
        $checkRights = array(
            _GTD_SEC_TICKET_OWNERSHIP => array('gtd_has_changeOwner', false),
            _GTD_SEC_RESPONSE_ADD => array('gtd_has_addResponse', true),
            _GTD_SEC_TICKET_EDIT => array('gtd_has_editTicket', true),
            _GTD_SEC_TICKET_DELETE => array('gtd_has_deleteTicket', true),
            _GTD_SEC_TICKET_MERGE => array('gtd_has_mergeTicket', true),
            _GTD_SEC_TICKET_GENRE => array('gtd_has_changePriority', true),
            _GTD_SEC_TICKET_STATUS => array('gtd_has_changeStatus', false),
            _GTD_SEC_RESPONSE_EDIT => array('gtd_has_editResponse', false),
            _GTD_SEC_FILE_DELETE => array('gtd_has_deleteFile', false));
        
        // See if this user is accepted for this ticket
        $hTicketEmails =& gtdGetHandler('ticketEmails');
        $crit = new CriteriaCompo(new Criteria('ticketid', $gtd_id));
        $crit->add(new Criteria('uid', $xoopsUser->getVar('uid')));
        $ticketEmails =& $hTicketEmails->getObjects($crit);
        
        foreach ($checkRights as $right=>$desc) {
            if(($right == _GTD_SEC_RESPONSE_ADD) && (count($ticketEmails) > 0)){
                //Is this user in the ticket emails list (should be treated as a user)
                $xoopsTpl->assign($desc[0], true);
                $colspan ++;
                continue;
            }
            if(($right == _GTD_SEC_TICKET_STATUS) && count($ticketEmails > 0)){
                //Is this user in the ticket emails list (should be treated as a user)
                $xoopsTpl->assign($desc[0], true);
                $colspan ++;
                continue;
            }
            if ($hasRights = $gtd_staff->checkRoleRights($right, $ticketInfo->getVar('department'))) {
                $xoopsTpl->assign($desc[0], true);
            } else {
                if ($desc[1]) {
                    $colspan --;
                }
                
            }
            
        }
        $xoopsTpl->assign('gtd_actions_colspan', $colspan);
        
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
        
        $custFields =& $ticketInfo->getCustFieldValues();
        $xoopsTpl->assign('gtd_hasCustFields', (!empty($custFields)) ? true : false);
        $xoopsTpl->assign('gtd_custFields', $custFields);
        $xoopsTpl->assign('gtd_uploadPath', GTD_UPLOAD_PATH);
        
        require(XOOPS_ROOT_PATH.'/footer.php'); 
    break;
    
    case "user":
        // Check if user has permission to view ticket
        $hTicketEmails =& gtdGetHandler('ticketEmails');
        $crit = new CriteriaCompo(new Criteria('ticketid', $gtd_id));
        $crit->add(new Criteria('uid', $xoopsUser->getVar('uid')));
        $ticketEmails =& $hTicketEmails->getObjects($crit);
        if(count($ticketEmails) == 0){
            redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_INV_USER);
        }
    
        $xoopsOption['template_main'] = 'gtd_user_ticketDetails.html';   // Set template
        require(XOOPS_ROOT_PATH.'/header.php');                     // Include
        $responses = $ticketInfo->getResponses();
        foreach($responses as $response){
            $hasFiles = false;
            foreach($aFiles as $file){
                if($file['responseid'] == $response->getVar('id')){
                    $hasFiles = true;
                    break;
                }
            }
            
            $staffReview =& $hStaffReview->getReview($gtd_id, $response->getVar('id'), $xoopsUser->getVar('uid'));
            if (count($staffReview) > 0) {
                $review = $staffReview[0];
            }
            $responseOwner =& $member_handler->getUser($response->getVar('uid'));
            
            $aResponses[] = array('id'=>$response->getVar('id'),
                                  'uid'=>$response->getVar('uid'),
                                  'uname'=>gtdGetUsername($responseOwner, $displayName),
                                  'ticketid'=>$response->getVar('ticketid'),
                                  'message'=>$response->getVar('message'),
                                  'timeSpent'=>$response->getVar('timeSpent'),
                                  'updateTime'=>$response->posted('m'),
                                  'userIP'=>$response->getVar('userIP'),
                                  'rating'=>(isset($review)?gtdGetRating($review->getVar('rating')):0),
                                  'user_sig'=>$responseOwner->getVar('user_sig'),
                                  'private'=>$response->getVar('private'), 
                                  'hasFiles' => $hasFiles,
                                  'user_avatar' => XOOPS_URL .'/uploads/' .(($responseOwner)?$responseOwner->getVar('user_avatar') : 'blank.gif'));
                                  
            $all_users[$response->getVar('uid')] = '';
        }
        
        if (isset($review)) {
            unset($review);
        }
        
        $has_responses = count($responses) > 0;
        unset($responses);
        
        // Smarty variables
        $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
        $reopenTicket = $xoopsModuleConfig['gtd_allowReopen'] && $ticketInfo->getVar('status') == 2;
        $xoopsTpl->assign('gtd_reopenTicket', $reopenTicket);
        $xoopsTpl->assign('gtd_allowResponse', ($ticketInfo->getVar('status') != 2) || $reopenTicket);
        $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
        $xoopsTpl->assign('xoops_module_header',$gtd_module_header);
        $xoopsTpl->assign('gtd_ticketID', $gtd_id);
        $xoopsTpl->assign('gtd_ticket_uid', $ticketInfo->getVar('uid'));
        $xoopsTpl->assign('gtd_ticket_subject', $ticketInfo->getVar('subject'));
	$xoopsTpl->assign('gtd_ticket_nom_danseur', $ticketInfo->getVar('nom_danseur'));
	$xoopsTpl->assign('gtd_ticket_prenom_danseur', $ticketInfo->getVar('prenom_danseur'));
	$xoopsTpl->assign('gtd_ticket_nom_danseuse', $ticketInfo->getVar('nom_danseuse'));
	$xoopsTpl->assign('gtd_ticket_prenom_danseuse', $ticketInfo->getVar('prenom_danseuse'));
	$xoopsTpl->assign('gtd_ticket_mode_paiement', $ticketInfo->getVar('mode_paiement'));
	$xoopsTpl->assign('gtd_ticket_echeance', $ticketInfo->getVar('echeance'));
	$xoopsTpl->assign('gtd_ticket_observations_paiement', $ticketInfo->getVar('observations_paiement'));
	$xoopsTpl->assign('gtd_ticket_pvp', $ticketInfo->getVar('pvp'));
	$xoopsTpl->assign('gtd_ticket_a_payer', $aPayer);
	$xoopsTpl->assign('gtd_ticket_marge', $Marge);
	$xoopsTpl->assign('gtd_ticket_montant_reduc', $ticketInfo->getVar('montant_reduc'));
	$xoopsTpl->assign('gtd_ticket_taux_reduc', $ticketInfo->getVar('taux_reduc'));		
	$xoopsTpl->assign('gtd_ticket_net_hotel', $ticketInfo->getVar('net_hotel'));
        $xoopsTpl->assign('gtd_ticket_description', $ticketInfo->getVar('description'));
        $xoopsTpl->assign('gtd_ticket_department', $department->getVar('department'));
        $xoopsTpl->assign('gtd_ticket_genre', $ticketInfo->getVar('genre'));
        $xoopsTpl->assign('gtd_ticket_status', gtdGetStatus($ticketInfo->getVar('status')));
        $xoopsTpl->assign('gtd_ticket_posted', $ticketInfo->posted('m'));
        $xoopsTpl->assign('gtd_ticket_lastUpdated', $ticketInfo->posted('m'));
        $xoopsTpl->assign('gtd_userinfo', XOOPS_URL . '/userinfo.php?uid=' . $ticketInfo->getVar('uid'));
        $xoopsTpl->assign('gtd_username', $user->getVar('uname'));
        $xoopsTpl->assign('gtd_email', $user->getVar('email'));
        $xoopsTpl->assign('gtd_genres', array(3, 2, 1));
        $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));
	$xoopsTpl->assign('gtd_mode_paiements', array(1, 2));
        $xoopsTpl->assign('gtd_mode_paiements_desc', array('1' => _GTD_MODE_PAIEMENT1, '2' => _GTD_MODE_PAIEMENT2));
	$xoopsTpl->assign('gtd_echeances', array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));  
	$xoopsTpl->assign('gtd_echeances_desc', array('1'=>_GTD_MODALITE1, '2'=>_GTD_MODALITE2, '3'=>_GTD_MODALITE3, '4'=>_GTD_MODALITE4, '5'=>_GTD_MODALITE5
	, '6'=>_GTD_MODALITE6, '7'=>_GTD_MODALITE7, '8'=>_GTD_MODALITE8, '9'=>_GTD_MODALITE9, '10'=>_GTD_MODALITE10));  
	$xoopsTpl->assign('gtd_user_avatar', XOOPS_URL .'/uploads/' .(($submitUser && $submitUser->getVar('user_avatar') != "")?$submitUser->getVar('user_avatar') : 'blank.gif'));
		
        $xoopsTpl->assign('gtd_uid', $xoopsUser->getVar('uid'));
        if($has_responses){
            $xoopsTpl->assign('gtd_aResponses', $aResponses);
        }
        if($has_files){
            $xoopsTpl->assign('gtd_aFiles', $aFiles);
            $xoopsTpl->assign('gtd_hasTicketFiles', $has_ticketFiles);     
        } else {
            $xoopsTpl->assign('gtd_aFiles', false);
            $xoopsTpl->assign('gtd_hasTicketFiles', false);
        }
        $xoopsTpl->assign('gtd_claimOwner', $xoopsUser->getVar('uid'));
        $xoopsTpl->assign('gtd_hasResponses', $has_responses);
        $xoopsTpl->assign('gtd_hasFiles', $has_files);
        $xoopsTpl->assign('gtd_filePath', XOOPS_URL . '/uploads/gtd/');
        $xoopsTpl->assign('xoops_pagetitle', $xoopsModule->getVar('name') . ' - ' . $ticketInfo->getVar('subject'));
        $xoopsTpl->assign('gtd_ticket_details', sprintf(_GTD_TEXT_TICKETDETAILS, $gtd_id));
        
        $custFields =& $ticketInfo->getCustFieldValues();
        $xoopsTpl->assign('gtd_hasCustFields', (!empty($custFields)) ? true : false);
        $xoopsTpl->assign('gtd_custFields', $custFields);
        $xoopsTpl->assign('gtd_uploadPath', GTD_UPLOAD_PATH);
        $xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
        
        require(XOOPS_ROOT_PATH.'/footer.php');
    break;
    
    case "userResponse":
        if(isset($_POST['newResponse'])){
            // Check if user has permission to view ticket
            $hTicketEmails =& gtdGetHandler('ticketEmails');
            $crit = new Criteria('ticketid', $gtd_id);
            $ticketEmails =& $hTicketEmails->getObjects($crit);
            $canChange = false;
            foreach($ticketEmails as $ticketEmail){
                if($xoopsUser->getVar('uid') == $ticketEmail->getVar('uid')){
                    $canChange = true;
                    break;
                }
            }

            $hStatus =& gtdGetHandler('status');
            if($canChange){
                $oldStatus =& $hStatus->get($ticketInfo->getVar('status'));
                if($oldStatus->getVar('state') == 2){     //If the ticket is resolved
                    $ticketInfo->setVar('closedBy', 0);
                    $ticketInfo->setVar('status', 1);
                    $ticketInfo->setVar('overdueTime', $ticketInfo->getVar('posted') + ($xoopsModuleConfig['gtd_overdueTime'] *60*60));
                } elseif(isset($_POST['closeTicket']) && $_POST['closeTicket'] == 1){ // If the user closes the ticket
                    $ticketInfo->setVar('closedBy', $ticketInfo->getVar('uid'));
                    $ticketInfo->setVar('status', 3);   // Todo: make moduleConfig for default resolved status?
                }
                $ticketInfo->setVar('lastUpdated', $ticketInfo->lastUpdated('m'));
                
                if($hTickets->insert($ticketInfo, true)){   // Insert the ticket                  
                    $newStatus =& $hStatus->get($ticketInfo->getVar('status'));
                    
                    if($newStatus->getVar('state') == 2){
                        $_eventsrv->trigger('close_ticket', array(&$ticketInfo));
                    }elseif($oldStatus->getVar('id') <> $newStatus->getVar('id') && $newStatus->getVar('state') <> 2){
                        $_eventsrv->trigger('update_status', array(&$ticketInfo, &$oldStatus, &$newStatus));
                    }
                }
                if($_POST['userResponse'] <> ''){       // If the user does not add any text in the response
                    $newResponse =& $hResponses->create();
                    $newResponse->setVar('uid', $xoopsUser->getVar('uid'));
                    $newResponse->setVar('ticketid', $gtd_id);
                    $newResponse->setVar('message', $_POST['userResponse']);
            //      $newResponse->setVar('updateTime', $newResponse->posted('m'));
                    $newResponse->setVar('updateTime', time());
                    $newResponse->setVar('userIP', getenv("REMOTE_ADDR"));
                    
                    if($hResponses->insert($newResponse)){
                        $_eventsrv->trigger('new_response', array(&$ticketInfo, &$newResponse));
                        $message = _GTD_MESSAGE_USER_MOREINFO;
                        
                        if($xoopsModuleConfig['gtd_allowUpload']){    // If uploading is allowed
                            if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
                                if (!$ret = $ticketInfo->checkUpload('userfile', $allowed_mimetypes, $errors)) {
                                    $errorstxt = implode('<br />', $errors);
                                    
                                    $message = sprintf(_GTD_MESSAGE_FILE_ERROR, $errorstxt);
                                    redirect_header(GTD_BASE_URL."/addTicket.php", 5, $message);
                                }
                                $file = $ticketInfo->storeUpload('userfile', $newResponse->getVar('id'), $allowed_mimetypes);
                            }
                        }
                    } else {
                        $message = _GTD_MESSAGE_USER_MOREINFO_ERROR;
                    }
                } else {
                    if($newStatus->getVar('state') != 2){
                        $message = _GTD_MESSAGE_USER_NO_INFO;
                    } else {
                        $message = _GTD_MESSAGE_UPDATE_STATUS;
                    }
                }
            } else {
                $message = _GTD_MESSAGE_NOT_USER;   
            }
            redirect_header("ticket.php?id=$gtd_id", 3, $message);
        }
    break;
    
    case "deleteFile":
        if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_FILE_DELETE, $ticketInfo->getVar('department'))){
            $message = _GTD_MESSAGE_NO_DELETE_FILE;
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, $message);
        }
        
        if(!isset($_GET['fileid'])){
            $message = '';
            redirect_header(GTD_BASE_URL."/ticket.phpid=$gtd_id", 3, $message);
        }
        
        if(isset($_GET['field'])){      // Remove filename from custom field
            $field = $_GET['field'];
            $hTicketValues =& gtdGetHandler('ticketValues');
            $ticketValues =& $hTicketValues->get($gtd_id);
                
            $ticketValues->setVar($field, "");
            $hTicketValues->insert($ticketValues, true);
        }
        
        $hFile =& gtdGetHandler('file');
        $fileid = intval($_GET['fileid']);
        $file =& $hFile->get($fileid);
        
        if(!$hFile->delete($file, true)){
            redirect_header(GTD_BASE_URL."/ticket.php?id=$gtd_id", 3, _GTD_MESSAGE_DELETE_FILE_ERR);       
        }
        $_eventsrv->trigger('delete_file', array(&$file)); 
        header("Location: ".GTD_BASE_URL."/ticket.php?id=$gtd_id");
        
    break;
    
    default:
        redirect_header(GTD_BASE_URL."/index.php", 3);
    break;
}
?>