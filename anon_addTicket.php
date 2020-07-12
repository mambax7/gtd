<?php
//$Id: anon_addTicket.php,v 1.28.2.1 2005/11/26 15:04:15 ackbarr Exp $

require_once('header.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/logService.php');
require_once(GTD_CLASS_PATH.'/cacheService.php');

$language = $xoopsConfig['language'];
include_once(XOOPS_ROOT_PATH ."/language/$language/user.php");

$config_handler =& xoops_gethandler('config');
$xoopsConfigUser = array();
$crit = new CriteriaCompo(new Criteria('conf_name', 'allow_register'), 'OR');
$crit->add(new Criteria('conf_name', 'activation_type'), 'OR');
$myConfigs =& $config_handler->getConfigs($crit);

foreach($myConfigs as $myConf){
    $xoopsConfigUser[$myConf->getVar('conf_name')] = $myConf->getVar('conf_value');
}

$_eventsrv->advise('new_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('new_ticket', gtd_logService::singleton());
$_eventsrv->advise('new_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('new_user_by_email', gtd_notificationService::singleton(), 'new_user_activation'.$xoopsConfigUser['activation_type']);

if($xoopsModuleConfig['gtd_allowAnonymous'] == 0){
    header("Location: ".GTD_BASE_URL."/inscription.php");
}

$hTicket =& gtdGetHandler('ticket');
$hGroupPerm =& xoops_gethandler('groupperm');
$hMember =& xoops_gethandler('member');
$hFieldDept =& gtdGetHandler('ticketFieldDepartment');
$module_id = $xoopsModule->getVar('mid'); 

if ($xoopsConfigUser['allow_register'] == 0) {    // Use to doublecheck that anonymous users are allowed to register
	header("Location: ".GTD_BASE_URL."/error.php");    
	exit();
}

if(!isset($dept_id)){
    $dept_id = gtdGetMeta("default_department");
}

if(!isset($_POST['addTicket'])){
    $xoopsOption['template_main'] = 'gtd_anon_addTicket.html';             // Always set main template before including the header
    include(XOOPS_ROOT_PATH . '/header.php');
    
    $hDepartments  =& gtdGetHandler('department');    // Department handler
    $crit = new Criteria('','');
    $crit->setSort('department');
    $departments =& $hDepartments->getObjects($crit);
    if(count($departments) == 0){
        $message = _GTD_MESSAGE_NO_DEPTS;
        redirect_header(GTD_BASE_URL.'/index.php', 3, $message);
    }
    
    //XOOPS_GROUP_ANONYMOUS
    foreach($departments as $dept){
        $deptid = $dept->getVar('id');
        if($hGroupPerm->checkRight(_GTD_GROUP_PERM_DEPT, $deptid, XOOPS_GROUP_ANONYMOUS, $module_id)){
            $aDept[] = array('id'=>$deptid,
                             'department'=>$dept->getVar('department'));
        }
    }
    if($xoopsModuleConfig['gtd_allowUpload']){
        // Get available mimetypes for file uploading
        $hMime =& gtdGetHandler('mimetype');
        $crit = new Criteria('mime_user', 1);
        $mimetypes =& $hMime->getObjects($crit);
        $mimes = '';
        foreach($mimetypes as $mime){
            if($mimes == ''){
                $mimes = $mime->getVar('mime_ext');
            } else {                                      
                $mimes .= ", " . $mime->getVar('mime_ext');
            }
        }
        $xoopsTpl->assign('gtd_mimetypes', $mimes);
    }
    
    // Get current dept's custom fields
    $fields =& $hFieldDept->fieldsByDepartment($dept_id, true);
    
    if (!$savedFields =& $_gtdSession->get('gtd_custFields')) {
        $savedFields = array();
    }
    
    $aFields = array();
    foreach($fields as $field){
        $values = $field->getVar('fieldvalues');
        if ($field->getVar('controltype') == GTD_CONTROL_YESNO) {
            $values = array(1 => _YES, 0 => _NO);
        }
        
        // Check for values already submitted, and fill those values in
        if(array_key_exists($field->getVar('fieldname'), $savedFields)){
            $defaultValue = $savedFields[$field->getVar('fieldname')];
        } else {
            $defaultValue = $field->getVar('defaultvalue');
        }
        
        $aFields[$field->getVar('id')] = 
            array('name' => $field->getVar('name'),
                  'desc' => $field->getVar('description'),
                  'fieldname' => $field->getVar('fieldname'),
                  'defaultvalue' => $defaultValue,
                  'controltype' => $field->getVar('controltype'),
                  'required' => $field->getVar('required'),
                  'fieldlength' => $field->getVar('fieldlength'),
                  'maxlength' => ($field->getVar('fieldlength') < 50 ? $field->getVar('fieldlength') : 50),
                  'weight' => $field->getVar('weight'),
                  'fieldvalues' => $values,
                  'validation' => $field->getVar('validation'));
    }
    $xoopsTpl->assign('gtd_custFields', $aFields);
    if(!empty($aFields)){
        $xoopsTpl->assign('gtd_hasCustFields', true);
    } else {
        $xoopsTpl->assign('gtd_hasCustFields', false);
    }
    
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
        var tbl = gE('tblAddTicket');
        var beforeele = gE('addButtons');
        tbody = tbl.tBodies[0];
        gtdFillCustomFlds(tbody, result, beforeele);
    }
}

function window_onload()
{
    gtdDOMAddEvent(xoopsGetElementById('departments'), 'change', departments_onchange, true);
}

window.setTimeout('window_onload()', 1500);
//-->
</script>";      
        
    $xoopsTpl->assign('xoops_module_header', $javascript. $gtd_module_header);
    $xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
    $xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
    $xoopsTpl->assign('gtd_departments', $aDept);
    $xoopsTpl->assign('gtd_current_file', basename(__file__));
      $xoopsTpl->assign('gtd_genres', array(3, 2, 1));
        $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));
	$xoopsTpl->assign('gtd_echeances', array(1, 2, 3, 4, 5, 6, 7, 8, 9));  
	$xoopsTpl->assign('gtd_echeances_desc', array('1'=>_GTD_MODALITE1, '2'=>_GTD_MODALITE2, '3'=>_GTD_MODALITE3, '4'=>_GTD_MODALITE4, '5'=>_GTD_MODALITE5
	, '6'=>_GTD_MODALITE6, '7'=>_GTD_MODALITE7, '8'=>_GTD_MODALITE8, '9'=>_GTD_MODALITE9));  	
	$xoopsTpl->assign('gtd_default_echeance', GTD_DEFAULT_ECHEANCE);
	$xoopsTpl->assign('gtd_mode_paiements', array(1, 2));
        $xoopsTpl->assign('gtd_mode_paiements_desc', array('1' => _GTD_MODE_PAIEMENT1, '2' => _GTD_MODE_PAIEMENT2));
        $xoopsTpl->assign('gtd_default_mode_paiement', GTD_DEFAULT_MODE_PAIEMENT);
    $xoopsTpl->assign('gtd_default_dept', gtdGetMeta("default_department"));
    $xoopsTpl->assign('gtd_includeURL', GTD_INCLUDE_URL);
    $xoopsTpl->assign('gtd_numTicketUploads', $xoopsModuleConfig['gtd_numTicketUploads']);
    
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
    
    $elements = array('subject', 'description', 'email');
    foreach($elements as $element){         // Foreach element in the predefined list
        $xoopsTpl->assign("gtd_element_$element", "formButton");
        foreach($aElements as $aElement){   // Foreach that has an error
            if($aElement == $element){      // If the names are equal
                $xoopsTpl->assign("gtd_element_$element", "validateError");
                break;
            }
        }
    }     
                    
    if ($ticket =& $_gtdSession->get('gtd_ticket')) {
        $xoopsTpl->assign('gtd_ticket_subject', stripslashes($ticket['subject']));
        $xoopsTpl->assign('gtd_ticket_description', stripslashes($ticket['description']));
        $xoopsTpl->assign('gtd_ticket_department', $ticket['department']);
        $xoopsTpl->assign('gtd_ticket_genre', $ticket['genre']);
    } else {
        $xoopsTpl->assign('gtd_ticket_uid', null);
        $xoopsTpl->assign('gtd_ticket_username', null);
        $xoopsTpl->assign('gtd_ticket_subject', null);
        $xoopsTpl->assign('gtd_ticket_description', null);
        $xoopsTpl->assign('gtd_ticket_department', null);
        $xoopsTpl->assign('gtd_ticket_genre', 4);
    }
    
    if($user =& $_gtdSession->get('gtd_user')){
        $xoopsTpl->assign('gtd_uid', $user['uid']);
        $xoopsTpl->assign('gtd_email', $user['email']);
    } else {
        $xoopsTpl->assign('gtd_uid', null);
        $xoopsTpl->assign('gtd_email', null);
    }
    include(XOOPS_ROOT_PATH . '/footer.php');  
} else {
    require_once(GTD_CLASS_PATH.'/validator.php');
    
    $v = array();
    $v['subject'][] = new ValidateLength($_POST['subject'], 2, 255);
    $v['description'][] = new ValidateLength($_POST['description'], 2);
    $v['email'][] = new ValidateEmail($_POST['email']);
    
    // Get current dept's custom fields
    $fields =& $hFieldDept->fieldsByDepartment($dept_id, true);
    $aFields = array();
    
    foreach($fields as $field){
        $values = $field->getVar('fieldvalues');
        if ($field->getVar('controltype') == GTD_CONTROL_YESNO) {
            $values = array(1 => _YES, 0 => _NO);
        }
        $fieldname = $field->getVar('fieldname');
        
        if($field->getVar('controltype') != GTD_CONTROL_FILE) {
            $checkField = $_POST[$fieldname];
        } else {
            $checkField = $_FILES[$fieldname];
        }
        
        $v[$fieldname][] = new ValidateRegex($checkField, $field->getVar('validation'), $field->getVar('required'));
        
        $aFields[$field->getVar('id')] = 
            array('name' => $field->getVar('name'),
                  'desc' => $field->getVar('description'),
                  'fieldname' => $field->getVar('fieldname'),
                  'defaultvalue' => $field->getVar('defaultvalue'),
                  'controltype' => $field->getVar('controltype'),
                  'required' => $field->getVar('required'),
                  'fieldlength' => $field->getVar('fieldlength'),
                  'maxlength' => ($field->getVar('fieldlength') < 50 ? $field->getVar('fieldlength') : 50),
                  'weight' => $field->getVar('weight'),
                  'fieldvalues' => $values,
                  'validation' => $field->getVar('validation'));
    }
    
    $_gtdSession->set('gtd_ticket', 
        array('uid' => 0,
              'subject' => $_POST['subject'],
              'description' => htmlspecialchars($_POST['description'], ENT_QUOTES),
              'department' => $_POST['departments'],
              'genre' => $_POST['genre']));
    
    $_gtdSession->set('gtd_user',
        array('uid' => 0,
              'email' => $_POST['email']));
    
    if($fields != ""){
        $_gtdSession->set('gtd_custFields', $fields);
    }
    
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
        header("Location: ".GTD_BASE_URL."/anon_addTicket.php");
        exit();
    }    
    
    //Check email address
    $user_added = false;
    if(!$xoopsUser =& gtdEmailIsXoopsUser($_POST['email'])){      // Email is already used by a member
        switch($xoopsConfigUser['activation_type']){
            case 1:
                $level = 1;
            break;
            
            case 0:
            case 2:
            default:
                $level = 0;
        }
        
        if($anon_user =& gtdXoopsAccountFromEmail($_POST['email'], '', $password, $level)){ // If new user created
            $member_handler =& xoops_gethandler('member');
            $xoopsUser =& $member_handler->loginUserMd5($anon_user->getVar('uname'), $anon_user->getVar('pass'));
            $user_added = true;
        } else {        // User not created
            $message = _GTD_MESSAGE_NEW_USER_ERR;
            redirect_header(GTD_BASE_URL.'/user.php', 3, $message);
        }
    }    
    $ticket =& $hTicket->create();
    $ticket->setVar('uid', $xoopsUser->getVar('uid'));
    $ticket->setVar('subject', $_POST['subject']);
    $ticket->setVar('description', $_POST['description']);
    $ticket->setVar('department', $_POST['departments']);
    $ticket->setVar('genre', $_POST['genre']);
    $ticket->setVar('status', 1);
    $ticket->setVar('posted', time());
    $ticket->setVar('userIP', getenv("REMOTE_ADDR"));
    $ticket->setVar('overdueTime', $ticket->getVar('posted') + ($xoopsModuleConfig['gtd_overdueTime'] *60*60));
            
    $aUploadFiles = array();
    if($xoopsModuleConfig['gtd_allowUpload']){
        foreach($_FILES as $key=>$aFile){
            $pos = strpos($key, 'userfile');
            if($pos !== false && is_uploaded_file($aFile['tmp_name'])){     // In the userfile array and uploaded file?
                if ($ret = $ticket->checkUpload($key, $allowed_mimetypes, $errors)) {
                    $aUploadFiles[$key] = $aFile;
                } else {
                    $errorstxt = implode('<br />', $errors);
                    $message = sprintf(_GTD_MESSAGE_FILE_ERROR, $errorstxt);
                    redirect_header(GTD_BASE_URL."/addTicket.php", 5, $message);
                }
            }
        }
    }
    
    if($hTicket->insert($ticket)){
        
        $ticket->addSubmitter($xoopsUser->getVar('email'), $xoopsUser->getVar('uid'));
        if(count($aUploadFiles) > 0){   // Has uploaded files? 
            foreach($aUploadFiles as $key=>$aFile){
                $file = $ticket->storeUpload($key, null, $allowed_mimetypes);
                $_eventsrv->trigger('new_file', array(&$ticket, &$file));
            }
        }
                
        // Add custom field values to db
        $hTicketValues = gtdGetHandler('ticketValues');
        $ticketValues = $hTicketValues->create();
                
        foreach($aFields as $field){
            $fieldname = $field['fieldname'];
            $fieldtype = $field['controltype'];
                    
            if($fieldtype == GTD_CONTROL_FILE){               // If custom field was a file upload
                if($xoopsModuleConfig['gtd_allowUpload']){    // If uploading is allowed
                    if(is_uploaded_file($_FILES[$fieldname]['tmp_name'])){
                        if (!$ret = $ticket->checkUpload($fieldname, $allowed_mimetypes, $errors)) {
                            $errorstxt = implode('<br />', $errors);
                            $message = sprintf(_GTD_MESSAGE_FILE_ERROR, $errorstxt);
                            redirect_header(GTD_BASE_URL."/addTicket.php", 5, $message);
                        }
                        if($file = $ticket->storeUpload($fieldname, -1, $allowed_mimetypes)){
                            $ticketValues->setVar($fieldname, $file->getVar('id') . "_" . $_FILES[$fieldname]['name']);
                        }
                    }
                }
            } else {
                $fieldvalue = $_POST[$fieldname];
                $ticketValues->setVar($fieldname, $fieldvalue);
            }
        }
        $ticketValues->setVar('ticketid', $ticket->getVar('id'));
                
        if(!$hTicketValues->insert($ticketValues)){
            $message = _GTD_MESSAGE_NO_CUSTFLD_ADDED;
        }
        
        $_eventsrv->trigger('new_ticket', array(&$ticket));
                
        $_gtdSession->del('gtd_ticket');
        $_gtdSession->del('gtd_ticket');
        $_gtdSession->del('gtd_user');
        $_gtdSession->del('gtd_validateError');
                
        $message = _GTD_MESSAGE_ADDTICKET;
    } else {
        $message = _GTD_MESSAGE_ADDTICKET_ERROR . $ticket->getHtmlErrors();     // Unsuccessfully added new ticket
    }
    if ($user_added) {            
        $_eventsrv->trigger('new_user_by_email', array($password, $xoopsUser));
    }        
            
    redirect_header(XOOPS_URL.'/user.php', 3, $message);
}  
?>