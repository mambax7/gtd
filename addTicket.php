<?php
//$Id: addTicket.php,v 1.81.2.1 2005/11/26 15:04:15 ackbarr Exp $
if(isset($_GET['deptid'])){
    $dept_id = intval($_GET['deptid']);
}

if(isset($_GET['view_id'])){
    $view_id = intval($_GET['view_id']);
    setCookie("gtd_logMode", $view_id,time()+60*60*24*30);
    if(isset($dept_id)){
        header("Location: addTicket.php&deptid=$dept_id");
    } else {
        header("Location: addTicket.php");
    }
} else {    
    if(!isset($_COOKIE['gtd_logMode'])){
        setCookie("gtd_logMode", 1, time()+60*60*24*30);
    } else {
        setCookie("gtd_logMode", $_COOKIE['gtd_logMode'], time()+60*60*24*30);
    }
}

	/*print_r($_POST); 
		//die();*/


require_once('header.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/logService.php');
require_once(GTD_CLASS_PATH.'/cacheService.php');

$_eventsrv->advise('new_ticket', gtd_notificationService::singleton());
$_eventsrv->advise('new_ticket', gtd_logService::singleton());
$_eventsrv->advise('new_ticket', gtd_cacheService::singleton());
$_eventsrv->advise('new_response', gtd_logService::singleton());
$_eventsrv->advise('new_response', gtd_notificationService::singleton());
$_eventsrv->advise('update_owner', gtd_notificationService::singleton());
$_eventsrv->advise('update_owner', gtd_logService::singleton());

$hTicket =& gtdGetHandler('ticket');
$hStaff =& gtdGetHandler('staff');
$hGroupPerm =& xoops_gethandler('groupperm');
$hMember =& xoops_gethandler('member');
$hMembership =& gtdGetHandler('membership');
$hFieldDept =& gtdGetHandler('ticketFieldDepartment');

$module_id = $xoopsModule->getVar('mid'); 

if($xoopsUser){
    if(!isset($dept_id)){
        $dept_id = gtdGetMeta("default_department");
    }

    if(isset($_GET['saveTicket']) && $_GET['saveTicket'] == 1){
        _saveTicket();
    }
    
    if(!isset($_POST['addTicket'])){                           // Initial load of page
        $xoopsOption['template_main'] = 'gtd_addTicket.html';             // Always set main template before including the header
        include(XOOPS_ROOT_PATH . '/header.php');
        
        $hDepartments  =& gtdGetHandler('department');    // Department handler
        $crit = new Criteria('','');
        $crit->setSort('department');
        $departments =& $hDepartments->getObjects($crit);
        if(count($departments) == 0){
            $message = _GTD_MESSAGE_NO_DEPTS;
            redirect_header(GTD_BASE_URL."/index.php", 3, $message);
        }
        $aDept = array();
        $myGroups =& $hMember->getGroupsByUser($xoopsUser->getVar('uid'));
        if(($isStaff) && ($xoopsModuleConfig['gtd_deptVisibility'] == 0)){     // If staff are not applied
            foreach($departments as $dept){
                $deptid = $dept->getVar('id');
                $aDept[] = array('id'=>$deptid,
                                 'department'=>$dept->getVar('department'));
            }
        } else {
            foreach($departments as $dept){
                $deptid = $dept->getVar('id');
                foreach($myGroups as $group){   // Check for user to be in multiple groups
                    if($hGroupPerm->checkRight(_GTD_GROUP_PERM_DEPT, $deptid, $group, $module_id)){
                    		//Assign the first value to $dept_id incase the default department property not set
                    		if ($dept_id == null) {
                    			$dept_id = $deptid;
                    		}
                        $aDept[] = array('id'=>$deptid,
                                         'department'=>$dept->getVar('department'));
                        break;
                    }
                }
            }
        }


        // User Dept visibility check
        if(empty($aDept)){
            $message = _GTD_MESSAGE_NO_DEPTS;
            redirect_header(GTD_BASE_URL."/index.php", 3, $message);
        }
        
        $xoopsTpl->assign('gtd_isUser', true);
		include(XOOPS_ROOT_PATH.'/include/calendarjs.php');
        
        if($isStaff){
            $checkStaff =& $hStaff->getByUid($xoopsUser->getVar('uid'));
            if(!$hasRights = $checkStaff->checkRoleRights(_GTD_SEC_TICKET_ADD)){
                $message = _GTD_MESSAGE_NO_ADD_TICKET;
                redirect_header(GTD_BASE_URL."/index.php", 3, $message);
            }
            unset($checkStaff);
            
            if($hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_OWNERSHIP, $dept_id)){
               $staff =& $hMembership->xoopsUsersByDept($dept_id);
                
                $aOwnership = array();
                $aOwnership[0] = _GTD_NO_OWNER;
                foreach($staff as $stf){
                    $aOwnership[$stf->getVar('uid')] = $stf->getVar('uname');
                }
                $xoopsTpl->assign('gtd_aOwnership', $aOwnership);
            } else {
                $xoopsTpl->assign('gtd_aOwnership', false);
            }
        }
        
        $has_mimes = false;
        if($xoopsModuleConfig['gtd_allowUpload']){
            // Get available mimetypes for file uploading
            $hMime =& gtdGetHandler('mimetype');
            $gtd =& gtdGetModule();
            $mid = $gtd->getVar('mid');
            if(!$isStaff){
                $crit = new Criteria('mime_user', 1);
            } else {
                $crit = new Criteria('mime_admin', 1);
            }
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
        
        $xoopsTpl->assign('gtd_has_logUser', false);
        if($isStaff){
            $checkStaff =& $hStaff->getByUid($xoopsUser->getVar('uid'));
            if($hasRights = $checkStaff->checkRoleRights(_GTD_SEC_TICKET_LOGUSER)){
                $xoopsTpl->assign('gtd_has_logUser', true);
            }
            unset($checkStaff);
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
                array('desc' => $field->getVar('description'),
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

$j_tarifs = '';
foreach($departments as $dept){
	$dpt = $dept->getVar('id');
	$t1 = $dept->getVar('tarif_1');
	$t2 = $dept->getVar('tarif_2');
	$t3 = $dept->getVar('tarif_3');
	if ($j_tarifs == '')
		$j_tarifs = "[{dept: '$dpt', tarif_1: $t1,  tarif_2: $t2,  tarif_3: $t3}";
	else
		$j_tarifs .= ",{dept: '$dpt', tarif_1: $t1,  tarif_2: $t2,  tarif_3: $t3}";
}
$j_tarifs .= ']';

        $javascript = "<script type=\"text/javascript\" src=\"". GTD_BASE_URL ."/include/functions.js\"></script>
<script type=\"text/javascript\" src='".GTD_SCRIPT_URL."/addTicketDeptChange.php?client'></script>
<script type=\"text/javascript\">
<!--
function departments_onchange() 
{
    dept = xoopsGetElementById('departments');
    var wl = new gtdweblib(fieldHandler);
    wl.customfieldsbydept(dept.value);\n";
	
    if($isStaff){
        $javascript .= "var w = new gtdweblib(staffHandler);
        w.staffbydept(dept.value);\n";
    }
$javascript .= "}

var staffHandler = { 
    staffbydept: function(result){";
        if($isStaff){
            if (isset($_COOKIE['gtd_logMode']) && $_COOKIE['gtd_logMode'] == 2 && $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_OWNERSHIP, $dept_id)) {   
                $javascript .= "var sel = gE('owner');";
                $javascript .= "gtdFillStaffSelect(sel, result);\n";
            }
        }
    $javascript .= "}
}

var fieldHandler = {
    customfieldsbydept: function(result){
        var tbl = gE('tbl_custom');\n";
        if ($isStaff && isset($_COOKIE['gtd_logMode']) && $_COOKIE['gtd_logMode'] == 2) {       
            $javascript.="var beforeele = gE('privResponse');\n";
        } else {
            $javascript.="var beforeele = gE('position_custom');\n";
        }
        $javascript.="tbody = tbl.tBodies[0];\n";
        $javascript .="gtdFillCustomFlds(tbody, result, beforeele);
		window.setTimeout('ajoute_onchange();', 200);
    }
}

var tarifs=$j_tarifs;

function customs_onchange()
{
	var HRock = 0;
	var HSalsa = 0;
	var HStage = 0;
	
	for (var i=1; i<10; i++)
	{
		var f = document.getElementById('Rock_'+i);
		if (f != null)
			if (f.selectedIndex > 0)
				HRock += parseInt(f.options[f.selectedIndex].value);
	}
	
	for (var i=1; i<10; i++)
	{
		var f = document.getElementById('Salsa_'+i);
		if (f != null)
			if (f.selectedIndex > 0)
				HSalsa += parseInt(f.options[f.selectedIndex].value);
	}

	for (var i=1; i<10; i++)
	{
		var f = document.getElementById('Stage_'+i);
		if (f != null)
			if (f.selectedIndex > 0)
				HStage = parseFloat(f.options[f.selectedIndex].value);
	}
	
	var reduc = 0;
	for (var i=1; i<10; i++)
	{
		
		var els = document.getElementsByName('Reduc_'+i);
		for (j=0; j < els.length; j++)
			if (! els[j].value.match(/.*[%].*/) && els[j].checked)
				reduc += parseFloat(els[j].value);	// Transformer en entier
	}
	
	var reducp = 0;
	for (var i=1; i<10; i++)
	{
		var els = document.getElementsByName('Reduc_'+i);
		for (j=0; j < els.length; j++)
			if (els[j].value.match(/.*[%].*/) && els[j].checked)
				reducp += parseFloat(els[j].value.replace('%',''));	// Transformer en entier
	}
	
	var heures = document.getElementById('heures');
	if (heures)
		heures.value = 0;

	var pvp = document.getElementById('pvp');
	if (pvp)
		pvp.value = 0.00;
	
	if (HRock != 0  || HSalsa != 0)
	{
		var heures = document.getElementById('heures');
		if (heures)
			heures.value = 0 + HRock + HSalsa;
		var depts = document.getElementById('departments');
		var dept = depts.options[depts.selectedIndex].value;
		var tarif = false;
		for (i = 0; i < tarifs.length; i++)
			if (tarifs[i].dept == dept)
				tarif = tarifs[i];
		if (tarif)
		{
			switch(heures.value)
			{
				case '1' :
					pvp.value = tarif.tarif_1;
					break;
				case '2' :
					pvp.value = tarif.tarif_2;
					break;
				case '3' :
					pvp.value = tarif.tarif_3;
					break;
				default:
					pvp.value = 'Hors tarif';
			}
		}
	}
	else
	{
		var pvp = document.getElementById('pvp');
		if (pvp)
			pvp.value = HStage;
	}
	if ((reduc > 0) && (pvp.value != 'Hors tarif') && (pvp.value > 0))
		pvp.value -= reduc;
	if ((reducp > 0) && (pvp.value != 'Hors tarif') && (pvp.value > 0))
		pvp.value -= pvp.value - (pvp.value * ((100 - reducp)/100));
	if (pvp.value < 0)
		pvp.value = 0;
}

function ajoute_onchange()
{
	for (var i=1; i<10; i++)
	{
		if (document.getElementById('Rock_'+i))
			gtdDOMAddEvent(xoopsGetElementById('Rock_'+i), 'change', customs_onchange, true);
	}
	for (var i=1; i<10; i++)
	{
		if (document.getElementById('Salsa_'+i))
			gtdDOMAddEvent(xoopsGetElementById('Salsa_'+i), 'change', customs_onchange, true);
	}
	for (var i=1; i<10; i++)
	{
		if (document.getElementById('Stage_'+i))
			gtdDOMAddEvent(xoopsGetElementById('Stage_'+i), 'change', customs_onchange, true);
	}
	for (var i=1; i<10; i++)
	{
		var els = document.getElementsByName('Reduc_'+i);
		for (j=0; j < els.length; j++)
			gtdDOMAddEvent(xoopsGetElementById(els[j].id), 'click', customs_onchange, true);
	}
}

function window_onload()
{
    gtdDOMAddEvent(xoopsGetElementById('departments'), 'change', departments_onchange, true);
}

window.setTimeout('window_onload()', 1500);
//-->
</script>";  
        $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
        $xoopsTpl->assign('gtd_includeURL', GTD_INCLUDE_URL);
        $xoopsTpl->assign('xoops_module_header', $javascript. $gtd_module_header);
        $xoopsTpl->assign('gtd_allowUpload', $xoopsModuleConfig['gtd_allowUpload']);
        $xoopsTpl->assign('gtd_text_lookup', _GTD_TEXT_LOOKUP);
        $xoopsTpl->assign('gtd_text_email', _GTD_TEXT_EMAIL);
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
        $xoopsTpl->assign('gtd_default_dept', $dept_id);
        $xoopsTpl->assign('gtd_currentUser', $xoopsUser->getVar('uid'));
        $xoopsTpl->assign('gtd_numTicketUploads', $xoopsModuleConfig['gtd_numTicketUploads']);
	if($isStaff)
		{
	$xoopsTpl->assign('gtd_echeances', array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));  
	$xoopsTpl->assign('gtd_echeances_desc', array('1'=>_GTD_MODALITE1, '2'=>_GTD_MODALITE2, '3'=>_GTD_MODALITE3, '4'=>_GTD_MODALITE4, '5'=>_GTD_MODALITE5
	, '6'=>_GTD_MODALITE6, '7'=>_GTD_MODALITE7, '8'=>_GTD_MODALITE8, '9'=>_GTD_MODALITE9, '10'=>_GTD_MODALITE10_AVERT));
	}
        if(isset($_POST['logFor'])){
            $uid = $_POST['logFor'];
            $username = $xoopsUser->getUnameFromId($uid);
            $xoopsTpl->assign('gtd_username', $username);
            $xoopsTpl->assign('gtd_user_id', $uid);
        } else {
            $uid = $xoopsUser->getVar('uid');
            $username = $xoopsUser->getVar('uname');
            $xoopsTpl->assign('gtd_username', $username);
            $xoopsTpl->assign('gtd_user_id', $uid);
        }
        $xoopsTpl->assign('gtd_isStaff', $isStaff);
        if(!isset($_COOKIE['gtd_logMode'])){
            $xoopsTpl->assign('gtd_logMode', 1);
        } else {
            $xoopsTpl->assign('gtd_logMode', $_COOKIE['gtd_logMode']);
        }
        if($isStaff){
            if(isset($_COOKIE['gtd_logMode']) && $_COOKIE['gtd_logMode'] == 2){
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
            }
            $xoopsTpl->assign('gtd_savedSearches', $aSavedSearches);
        }
		
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
	
        // Champs obligatoires
        $elements = array('genre');
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
            $xoopsTpl->assign('gtd_ticket_uid', $ticket['uid']);
            $xoopsTpl->assign('gtd_ticket_username', $xoopsUser->getUnameFromId($ticket['uid']));
            $xoopsTpl->assign('gtd_ticket_subject', stripslashes($ticket['subject']));
	    $xoopsTpl->assign('gtd_ticket_nom_danseur', stripslashes($ticket['nom_danseur']));
	    $xoopsTpl->assign('gtd_ticket_prenom_danseur', stripslashes($ticket['prenom_danseur']));
	    $xoopsTpl->assign('gtd_ticket_nom_danseuse', stripslashes($ticket['nom_danseuse']));
	    $xoopsTpl->assign('gtd_ticket_prenom_danseuse', stripslashes($ticket['prenom_danseuse']));
	    $xoopsTpl->assign('gtd_ticket_mode_paiement', $ticket['mode_paiement']);
	    $xoopsTpl->assign('gtd_ticket_echeance', $ticket['echeance']);
            $xoopsTpl->assign('gtd_ticket_observations_paiement', stripslashes($ticket['observations_paiement']));	     
	    $xoopsTpl->assign('gtd_ticket_pvp', stripslashes($ticket['pvp']));
            $xoopsTpl->assign('gtd_ticket_montant_reduc', stripslashes($ticket['montant_reduc']));	     
	    $xoopsTpl->assign('gtd_ticket_taux_reduc', stripslashes($ticket['taux_reduc']));
            $xoopsTpl->assign('gtd_ticket_net_hotel', stripslashes($ticket['net_hotel']));  	    
            $xoopsTpl->assign('gtd_ticket_description', stripslashes($ticket['description']));
            $xoopsTpl->assign('gtd_ticket_department', $ticket['department']);
            $xoopsTpl->assign('gtd_ticket_genre', $ticket['genre']);
        } else {
            $xoopsTpl->assign('gtd_ticket_uid', $uid);
            $xoopsTpl->assign('gtd_ticket_username', $username);
            $xoopsTpl->assign('gtd_ticket_subject', null);
	    $xoopsTpl->assign('gtd_ticket_nom_danseur', null);
	    $xoopsTpl->assign('gtd_ticket_prenom_danseur',null);
	    $xoopsTpl->assign('gtd_ticket_nom_danseuse', null);
	    $xoopsTpl->assign('gtd_ticket_prenom_danseuse', null);
	    $xoopsTpl->assign('gtd_ticket_mode_paiement', GTD_DEFAULT_MODE_PAIEMENT);
            $xoopsTpl->assign('gtd_echeance', GTD_DEFAULT_ECHEANCE);
	    $xoopsTpl->assign('gtd_ticket_echeance', null);
            $xoopsTpl->assign('gtd_ticket_observations_paiement', null);     
	    $xoopsTpl->assign('gtd_ticket_pvp', null);
            $xoopsTpl->assign('gtd_ticket_montant_reduc', null);     
	    $xoopsTpl->assign('gtd_ticket_taux_reduc', null);
            $xoopsTpl->assign('gtd_ticket_net_hotel', null);
            $xoopsTpl->assign('gtd_ticket_description', null);
            $xoopsTpl->assign('gtd_ticket_department', null);
            $xoopsTpl->assign('gtd_ticket_genre', GTD_DEFAULT_GENRE);
        }
        
        if($response =& $_gtdSession->get('gtd_response')){
            $xoopsTpl->assign('gtd_response_uid', $response['uid']);
            $xoopsTpl->assign('gtd_response_message', $response['message']);
            $xoopsTpl->assign('gtd_response_timespent', $response['timeSpent']);
            $xoopsTpl->assign('gtd_response_userIP', $response['userIP']);
            $xoopsTpl->assign('gtd_response_private', $response['private']);
            $xoopsTpl->assign('gtd_ticket_status', $response['status']);
            $xoopsTpl->assign('gtd_ticket_ownership', $response['owner']);
        } else {
            $xoopsTpl->assign('gtd_response_uid', null);
            $xoopsTpl->assign('gtd_response_message', null);
            $xoopsTpl->assign('gtd_response_timeSpent', null);
            $xoopsTpl->assign('gtd_response_userIP', null);
            $xoopsTpl->assign('gtd_response_private', null);
            $xoopsTpl->assign('gtd_ticket_status', 1);
            $xoopsTpl->assign('gtd_ticket_ownership', 0);
        }
        
        require(XOOPS_ROOT_PATH.'/footer.php');                             //Include the page footer
    } else {
        $dept_id = intval($_POST['departments']);
        
        require_once(GTD_CLASS_PATH.'/validator.php');

          $v = array();			// TODO : vérifier la longueur d'un champ affiche avertissement en rouge
	$v['genre'][] = new ValidateLength($_POST['genre'], 1, 1);
        
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
                array('desc' => $field->getVar('description'),
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
        
        _saveTicket($aFields);      // Save ticket information in a session
        
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
            header("Location: ".GTD_BASE_URL."/addTicket.php");
            exit();
        }
		
        //$hTicket =& gtdGetHandler('ticket');
		$xoopsLogger->addExtra('CREATE', 'Avant creation du ticket');
        $ticket =& $hTicket->create();
        $ticket->setVar('uid', $_POST['user_id']);
        $ticket->setVar('subject', $_POST['subject']);
		$ticket->setVar('nom_danseur', $_POST['nom_danseur']);
		$ticket->setVar('prenom_danseur', $_POST['prenom_danseur']);
		$ticket->setVar('nom_danseuse', $_POST['nom_danseuse']);
		$ticket->setVar('prenom_danseuse', $_POST['prenom_danseuse']);
		$ticket->setVar('mode_paiement', $_POST['mode_paiement']);
		$ticket->setVar('echeance', $_POST['echeance']);
		$ticket->setVar('observations_paiement', $_POST['observations_paiement']);
		$ticket->setVar('pvp', $_POST['pvp']);
	
		$ticket->setVar('montant_reduc', $_POST['montant_reduc']);
		$ticket->setVar('taux_reduc', $_POST['taux_reduc']);
		$ticket->setVar('net_hotel', $_POST['net_hotel']);
        $ticket->setVar('description', $_POST['description']);
        $ticket->setVar('department', $dept_id);
        $ticket->setVar('genre', $_POST['genre']);
        if($isStaff && $_COOKIE['gtd_logMode'] == 2){
            $ticket->setVar('status', $_POST['status']);    // Set status
            if (isset($_POST['owner'])) {  //Check if user claimed ownership
                if ($_POST['owner'] > 0) {
                    $oldOwner = 0;
                    $_gtdSession->set('gtd_oldOwner', $oldOwner);
                    $ticket->setVar('ownership', $_POST['owner']);
                    $_gtdSession->set('gtd_changeOwner', true);
                }
            }
            $_gtdSession->set('gtd_ticket_ownership', $_POST['owner']);  // Store in session
        } else {
            $ticket->setVar('status', 1);
        }
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
        $xoopsLogger->addExtra('CREATE', 'Avant insertion');
		$xoopsLogger->addExtra('AddTicket.php ligne 492', "op = " . $ticket->getVar('echeance'));
		
		/*PRINT DU TICKET 
		echo "<pre>";
		print_r($ticket);
		echo "</pre>";
		die();*/
        if($hTicket->insert($ticket)){
			$xoopsLogger->addExtra('CREATE', 'Apres insertion OK');
            $hMember =& xoops_gethandler('member');
            $newUser =& $hMember->getUser($ticket->getVar('uid'));
            $ticket->addSubmitter($newUser->getVar('email'), $newUser->getVar('uid'));
            
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
            
            
            if ($_gtdSession->get('gtd_changeOwner')) {
                $oldOwner = $_gtdSession->get('gtd_oldOwner');
                $_eventsrv->trigger('update_owner', array(&$ticket, $oldOwner));
                $_gtdSession->del('gtd_changeOwner');
                $_gtdSession->del('gtd_oldOwner');
                $_gtdSession->del('gtd_ticket_ownership');
            }
            
            // Add response
            if($isStaff && $_COOKIE['gtd_logMode'] == 2){     // Make sure user is a staff member and is using advanced form
                if($_POST['response'] != ''){                   // Don't run if no value for response
                    $hResponse =& gtdGetHandler('responses');
                    $newResponse =& $hResponse->create();
                    $newResponse->setVar('uid', $xoopsUser->getVar('uid'));
                    $newResponse->setVar('ticketid', $ticket->getVar('id'));
                    $newResponse->setVar('message', $_POST['response']);
                    $newResponse->setVar('timeSpent', $_POST['timespent']);
                    $newResponse->setVar('updateTime', $ticket->getVar('posted'));
                    $newResponse->setVar('userIP', $ticket->getVar('userIP'));
                    if(isset($_POST['private'])){
                        $newResponse->setVar('private', $_POST['private']);
                    }
                    if($hResponse->insert($newResponse)){
                        $_eventsrv->trigger('new_response', array(&$ticket, &$newResponse));
                        $_gtdSession->del('gtd_response');
                    }
                }
            }
                        
            $_gtdSession->del('gtd_ticket');
            $_gtdSession->del('gtd_validateError');
            $_gtdSession->del('gtd_custFields');
            
            $message = _GTD_MESSAGE_ADDTICKET;
			$xoopsLogger->addExtra('CREATE', 'Fin insertion');
        } else {
            //$_gtdSession->set('gtd_ticket', $ticket);
            $message = _GTD_MESSAGE_ADDTICKET_ERROR . $ticket->getHtmlErrors();     // Unsuccessfully added new ticket
        }
        redirect_header(GTD_BASE_URL."/index.php", 5, $message);
    }
} else {    // If not a user
    $config_handler =& xoops_gethandler('config');
    //$xoopsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
    $xoopsConfigUser = array();
    $crit = new CriteriaCompo(new Criteria('conf_name', 'allow_register'), 'OR');
    $crit->add(new Criteria('conf_name', 'activation_type'), 'OR');
    $myConfigs =& $config_handler->getConfigs($crit);
    
    foreach($myConfigs as $myConf){
        $xoopsConfigUser[$myConf->getVar('conf_name')] = $myConf->getVar('conf_value');
    }
    if ($xoopsConfigUser['allow_register'] == 0) {    // Use to doublecheck that anonymous users are allowed to register
    	header("Location: ".GTD_BASE_URL."/error.php");    
    } else {
        header("Location: ".GTD_BASE_URL."/anon_addTicket.php"); 
    }
    exit();
}

function _saveTicket($fields = "")
{
    global $_gtdSession, $isStaff;
    $_gtdSession->set('gtd_ticket', 
                  array('uid' => $_POST['user_id'],
                        'subject' => $_POST['subject'],
			'nom_danseur' =>  $_POST['nom_danseur'],
			'prenom_danseur' =>  $_POST['prenom_danseur'],
			'nom_danseuse' =>  $_POST['nom_danseuse'],
			'prenom_danseuse' =>  $_POST['prenom_danseuse'],
			'mode_paiement' =>  $_POST['mode_paiement'],
			'echeance' =>  $_POST['echeance'],
			'observations_paiement' =>  $_POST['observations_paiement'],
			'pvp' =>  $_POST['pvp'],
			'montant_reduc' =>  $_POST['montant_reduc'],
			'taux_reduc' =>  $_POST['taux_reduc'],
			'net_hotel' =>  $_POST['net_hotel'],
                        'description' => htmlspecialchars($_POST['description'], ENT_QUOTES),
                        'department' => $_POST['departments'],
                        'genre' => $_POST['genre']));
                          
    if($isStaff && $_COOKIE['gtd_logMode'] == 2){
        $_gtdSession->set('gtd_response', 
                      array('uid' => $_POST['user_id'],
                            'message' => $_POST['response'],
                            'timeSpent' => $_POST['timespent'],
                            'userIP' => getenv("REMOTE_ADDR"),
                            'private' => (isset($_POST['private'])) ? 1 : 0,
                            'status' => $_POST['status'],
                            'owner' => $_POST['owner']));
    }
    
    if($fields != ""){
        $_gtdSession->set('gtd_custFields', $fields);
    }
    
    return true;
}
?>