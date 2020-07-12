<?php
//$Id: index.php,v 1.89.2.4 2005/11/28 16:22:06 ackbarr Exp $
require_once('header.php');
require_once(GTD_CLASS_PATH.'/logService.php');
require_once(GTD_CLASS_PATH.'/notificationService.php');
require_once(GTD_CLASS_PATH.'/cacheService.php');
require_once(GTD_CLASS_PATH.'/staffService.php');
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');

// Setup event handlers for page
$_eventsrv->advise('batch_dept', gtd_logService::singleton());
$_eventsrv->advise('batch_dept', gtd_notificationService::singleton());
$_eventsrv->advise('batch_genre', gtd_logService::singleton());
$_eventsrv->advise('batch_genre', gtd_notificationService::singleton());
$_eventsrv->advise('batch_status', gtd_logService::singleton());
$_eventsrv->advise('batch_status', gtd_notificationService::singleton());
$_eventsrv->advise('batch_status', gtd_cacheService::singleton());
$_eventsrv->advise('batch_status', gtd_staffService::singleton());
$_eventsrv->advise('batch_owner', gtd_logService::singleton());
$_eventsrv->advise('batch_owner', gtd_notificationService::singleton());
$_eventsrv->advise('batch_response', gtd_logService::singleton());
$_eventsrv->advise('batch_response', gtd_notificationService::singleton());
$_eventsrv->advise('batch_response', gtd_staffService::singleton());
$_eventsrv->advise('batch_delete_ticket', gtd_notificationService::singleton());



//Initialise Necessary Data Handler Classes
$hStaff       =& gtdGetHandler('staff');
$hXoopsMember =& xoops_gethandler('member');        
$hDepartments =& gtdGetHandler('department');
$hMembership  =& gtdGetHandler('membership');
$hTickets     =& gtdGetHandler('ticket');
$hTicketList  =& gtdGetHandler('ticketList');
$hSavedSearch =& gtdGetHandler('savedSearch');

//Determine default 'op' (if none is specified)
$uid     = 0;
if ($xoopsUser) {
    $uid = $xoopsUser->getVar('uid');
    if ($gtd_isStaff) {
        $op = 'staffMain';
    } else {
        $op = 'userMain';
    }
} else {
    $op = 'anonMain';
}

// Page Global Variables
$status_opt   = array(_GTD_TEXT_SELECT_ALL => -1, _GTD_STATUS0 => 0, _GTD_STATUS1 => 1, _GTD_STATUS2 => 2);
$state_opt    = array(_GTD_TEXT_SELECT_ALL => -1, _GTD_STATE1 => 1, _GTD_STATE2 => 2);
$sort_columns = array();
$sort_order   = array('ASC', 'DESC');
$vars         = array('op', 'limit', 'start', 'sort', 'order', 'refresh');
$all_users    = array();
$refresh      = $start = $limit = 0;
$sort         = '';
$order        = '';

//Initialize Variables
foreach ($vars as $var) {
    if (isset($_REQUEST[$var])) {
        $$var = $_REQUEST[$var];
    }
}

//Ensure Criteria Fields hold valid values
$limit = intval($limit);
$start = intval($start);
$sort  = strtolower($sort);
$order = (in_array(strtoupper($order), $sort_order) ? $order : 'ASC');

$displayName =& $xoopsModuleConfig['gtd_displayName'];    // Determines if username or real name is displayed

switch($op) {

case 'staffMain':              
    staffmain_display();
    break;

case 'staffViewAll':
    staffviewall_display();
    break;

case 'userMain':
    usermain_display();
    break;
    
case 'userViewAll':
    userviewall_display();
    break;

case 'setdept':
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }
    
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_EDIT)){
        $message = _GTD_MESSAGE_NO_EDIT_TICKET;
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, $message);
    }
    
    if (isset($_POST['setdept'])) {
        setdept_action();
    } else {
        setdept_display();     
    }
    break;

case 'setgenre':
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_GENRE)){
        $message = _GTD_MESSAGE_NO_CHANGE_GENRE;
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, $message);
    }
    
    if (isset($_POST['setgenre'])) {
        setgenre_action();
    } else {
        setgenre_display();
    }
    break;

case 'setstatus':
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_STATUS)){
        $message = _GTD_MESSAGE_NO_CHANGE_STATUS;
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, $message);
    }
    
    if (isset($_POST['setstatus'])) {
        setstatus_action();
    } else {
        setstatus_display();
    }
    break;

case 'setowner':
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_OWNERSHIP)){
        $message = _GTD_MESSAGE_NO_CHANGE_OWNER;
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, $message);
    }
    
    if (isset($_POST['setowner'])) {
        setowner_action();
    } else {
        setowner_display();
    }
    break;

case 'addresponse':
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_RESPONSE_ADD)){
        $message = _GTD_MESSAGE_NO_ADD_RESPONSE;
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, $message);
    }
    
    if (isset($_POST['addresponse'])) {
        addresponse_action();
    } else {
        addresponse_display();
    }
    break;

case 'delete':
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }
    
    if(!$hasRights = $gtd_staff->checkRoleRights(_GTD_SEC_TICKET_DELETE)){
        $message = _GTD_MESSAGE_NO_DELETE_TICKET;
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, $message);
    }
    
    if (isset($_POST['delete'])) {
        delete_action();
    } else {
        delete_display();
    }
    break;
    
case 'anonMain':
    $config_handler =& xoops_gethandler('config');
    $xoopsConfigUser = array();
    $crit = new CriteriaCompo(new Criteria('conf_name', 'allow_register'), 'OR');
    $crit->add(new Criteria('conf_name', 'activation_type'), 'OR');
    $myConfigs =& $config_handler->getConfigs($crit);
    
    foreach($myConfigs as $myConf){
        $xoopsConfigUser[$myConf->getVar('conf_name')] = $myConf->getVar('conf_value');
    }
    
    if ($xoopsConfigUser['allow_register'] == 0) {
    	header("Location: ".GTD_BASE_URL."/error.php");
    } else {
        header("Location: ".GTD_BASE_URL."/addTicket.php");
    }
    exit();
    break;
default:
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3);
    break;
}
/**
 * Perform data validation and update data store
 */
function setdept_action()
{
    global $_eventsrv;
	
	//Sanity Check: tickets and department are supplied
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    if (!isset($_POST['department'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_DEPARTMENT);
    }
    
    $tickets  = _cleanTickets($_POST['tickets']);
    $oTickets =& gtdGetTickets($tickets);
    $ret      = gtdSetDept($tickets, $_POST['department']);
    if ($ret) {
        $_eventsrv->trigger('batch_dept', array(@$oTickets, $_POST['department']));
        if (count($oTickets)>1) {
            redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_UPDATE_DEPARTMENT);
        } else {
            redirect_header(GTD_BASE_URL."/ticket.php?id=".$oTickets[0]->getVar('id'), 3, _GTD_MESSAGE_UPDATE_DEPARTMENT);
        }
        end();
    }
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_UPDATE_DEPARTMENT_ERROR);
}

/**
 * Render form for the setdept ticket action
 */
function setdept_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;

        
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
        
    $hDepartments =& gtdGetHandler('department');
    $depts = $hDepartments->getObjects();
    $tplDepts = array();
    foreach ($depts as $dept) {
        $tplDepts[$dept->getVar('id')] = $dept->getVar('department');
    }
    unset($depts);
    
    
    
    $xoopsOption['template_main'] = 'gtd_setdept.html';   // Set template
    require(XOOPS_ROOT_PATH.'/header.php');                  // Include the page header       
    $xoopsTpl->assign('gtd_department_options', $tplDepts);
    $xoopsTpl->assign('gtd_tickets', implode($_POST['tickets'], ',')); 
    require(XOOPS_ROOT_PATH.'/footer.php');   
}

function setgenre_action()
{
    global $_eventsrv;
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    if (!isset($_POST['genre'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_GENRE);
    }
    $tickets  = _cleanTickets($_POST['tickets']);
    $oTickets =& gtdGetTickets($tickets);
    $ret      = gtdSetPriority($tickets, $_POST['genre']);
    if ($ret) {
        $_eventsrv->trigger('batch_genre', array(@$oTickets, $_POST['genre']));
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_UPDATE_GENRE);
    }
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_UPDATE_GENRE_ERROR); 
}

function setgenre_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
        
    //Make sure that some tickets were selected        
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
        
    //Get Array of genres/descriptions
    $aPriority  = array(1 => _GTD_GENRE1, 2 => _GTD_GENRE2, 3 => _GTD_GENRE3);
        
    $xoopsOption['template_main'] = 'gtd_setgenre.html';    // Set template
    require(XOOPS_ROOT_PATH.'/header.php');
    $xoopsTpl->assign('gtd_genres_desc', $aPriority);
    $xoopsTpl->assign('gtd_genres', array_keys($aPriority));
    $xoopsTpl->assign('gtd_genre', 4);
    $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
    $xoopsTpl->assign('gtd_tickets', implode($_POST['tickets'], ','));
    require(XOOPS_ROOT_PATH.'/footer.php');
}

function setstatus_action()
{
    global $_eventsrv;
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    if (!isset($_POST['status'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_STATUS);
    }
    $tickets  = _cleanTickets($_POST['tickets']);
    $oTickets =& gtdGetTickets($tickets);
    $ret      = gtdSetStatus($tickets, $_POST['status']);
    if ($ret) {
        $_eventsrv->trigger('batch_status', array(&$oTickets, $_POST['status']));
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_UPDATE_STATUS);
        end();
    }    
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_UPDATE_STATUS_ERROR);
    
}

function setstatus_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
    $hStatus =& gtdGetHandler('status');
    $crit = new Criteria('', '');
    $crit->setOrder('ASC');
    $crit->setSort('description');
    $statuses =& $hStatus->getObjects($crit);
        
    //Make sure that some tickets were selected        
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    //Get Array of Status/Descriptions
    $aStatus = array();
    foreach($statuses as $status){
        $aStatus[$status->getVar('id')] = $status->getVar('description');
    }
        
    $xoopsOption['template_main'] = 'gtd_setstatus.html'; // Set template
    require(XOOPS_ROOT_PATH.'/header.php');
    $xoopsTpl->assign('gtd_status_options', $aStatus);
    $xoopsTpl->assign('gtd_tickets', implode($_POST['tickets'], ','));
    require(XOOPS_ROOT_PATH.'/footer.php');
}

function setowner_action()
{
    global $_eventsrv;
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    if (!isset($_POST['owner'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_OWNER);
    }
    $tickets  = _cleanTickets($_POST['tickets']);
    $oTickets = gtdGetTickets($tickets);
    $ret      = gtdSetOwner($tickets, $_POST['owner']);
   
    if ($ret) {
        $_eventsrv->trigger('batch_owner', array(&$oTickets, $_POST['owner']));
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_ASSIGN_OWNER);
        end();
    }    
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_ASSIGN_OWNER_ERROR);
}

function setowner_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
                
    //Make sure that some tickets were selected        
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
        
    $hTickets     =& gtdGetHandler('ticket');
    $hMember      =& gtdGetHandler('membership');
    $hXoopsMember =& xoops_gethandler('member');
        
    $depts = $hTickets->getTicketDepartments($_POST['tickets']);
    $users =& $hMember->membershipByDept($depts);
    
    $aOwners = array();
    foreach($users as $user){
        $aOwners[$user->getVar('uid')] = $user->getVar('uid');
    }
    $crit  = new Criteria('uid', "(". implode(array_keys($aOwners), ',') .")", 'IN');
    $owners =& gtdGetUsers($crit, $xoopsModuleConfig['gtd_displayName']);
        
    $a_users = array();
    foreach($owners as $owner_id=>$owner_name) {
        $a_users[$owner_id] = $owner_name;
    }
    unset($users);
    unset($owners);
    unset($aOwners);
        
    $xoopsOption['template_main'] = 'gtd_setowner.html'; // Set template
    require(XOOPS_ROOT_PATH.'/header.php');
    $xoopsTpl->assign('gtd_staff_ids', $a_users);
    $xoopsTpl->assign('gtd_tickets', implode($_POST['tickets'], ','));
    require(XOOPS_ROOT_PATH.'/footer.php'); 
}

function addresponse_action()
{
    global $_eventsrv, $_gtdSession;
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    if (!isset($_POST['response'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_RESPONSE);
    }
    $private = isset($_POST['private']);
    
    $tickets  =& _cleanTickets($_POST['tickets']);
    $oTickets =& gtdGetTickets($tickets);
    $ret      = gtdAddResponse($tickets, $_POST['response'], $_POST['timespent'], $private);
    if ($ret) {
        $_gtdSession->del('gtd_batch_addresponse');
        $_gtdSession->del('gtd_batch_response');
        $_gtdSession->del('gtd_batch_timespent');
        $_gtdSession->del('gtd_batch_private');
        
        $_eventsrv->trigger('batch_response', array($oTickets, $_POST['response'], $_POST['timespent'], $private));
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_ADDRESPONSE);
        end();
    }
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_ADDRESPONSE_ERROR);    
    
}

function addresponse_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin, $_gtdSession;
    $hResponseTpl =& gtdGetHandler('responseTemplates');
    $ticketVar    = 'gtd_batch_addresponse';        
    $tpl          = 0;
    $uid          = $xoopsUser->getVar('uid');
    
    //Make sure that some tickets were selected
    if (!isset($_POST['tickets'])) {
        if (!$tickets = $_gtdSession->get($ticketVar)) {
            redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
        }     
    } else {
        $tickets = $_POST['tickets'];
    }
    
    //Store tickets in session so they won't be in URL
    $_gtdSession->set($ticketVar, $tickets);

    //Check if a predefined response was selected
    if (isset($_REQUEST['tpl'])) {
        $tpl = $_REQUEST['tpl'];
    }
        
    $xoopsOption['template_main'] = 'gtd_batch_response.html';
    require(XOOPS_ROOT_PATH.'/header.php');
    $xoopsTpl->assign('gtd_tickets', implode($tickets, ','));
    $xoopsTpl->assign('gtd_formaction', basename(__FILE__));
    $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
    $xoopsTpl->assign('gtd_timespent', ($timespent =$_gtdSession->get('gtd_batch_timespent') ? $timespent: ''));
    $xoopsTpl->assign('gtd_responseTpl', $tpl);
    
    //Get all staff defined templates
    $crit = new Criteria('uid', $uid);
    $crit->setSort('name');
    $responseTpl =& $hResponseTpl->getObjects($crit, true);
    
    //Fill Response Template Array
    $tpls = array();
    $tpls[0] = '------------------';
    
    foreach($responseTpl as $key=>$obj) {
        $tpls[$key] = $obj->getVar('name');
    }
    $xoopsTpl->assign('gtd_responseTpl_options', $tpls);   
    //Get response message to display
    if (isset($responseTpl[$tpl])) {    // Display Template Text
        $xoopsTpl->assign('gtd_response_message', $responseTpl[$tpl]->getVar('response', 'e'));
    } else {
        if ($response = $_gtdSession->get('gtd_batch_response')) {  //Display Saved Text
            $xoopsTpl->assign('gtd_response_message', $response);
        }
    }
    
    //Private Message?
    $xoopsTpl->assign('gtd_private', ($private = $_gtdSession->get('gtd_batch_private') ? $private : false));
     
    require(XOOPS_ROOT_PATH.'/footer.php');    
}

function delete_action()
{
    global $_eventsrv;
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_NO_TICKETS);
    }
    
    $tickets  = _cleanTickets($_POST['tickets']);
    $oTickets =& gtdGetTickets($tickets);
    $ret      = gtdDeleteTickets($tickets);
    if ($ret) {
        $_eventsrv->trigger('batch_delete_ticket', array(@$oTickets));
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_DELETE_TICKETS);
        end();
    }
    redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _GTD_MESSAGE_DELETE_TICKETS_ERROR);    
}

function delete_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
        
    //Make sure that some tickets were selected
    if (!isset($_POST['tickets'])) {
        redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_MESSAGE_NO_TICKETS);
    }

    $hiddenvars = array('tickets' => implode($_POST['tickets'], ','), 
        'delete' => _GTD_BUTTON_SET,
        'op' => 'delete');

    ob_start();
    xoops_confirm($hiddenvars, GTD_BASE_URL."/".basename(__FILE__), _GTD_MESSAGE_TICKET_DELETE_CNFRM);
    $formtext = ob_get_contents();
    ob_end_clean();        
    $xoopsOption['template_main'] = 'gtd_deletetickets.html';
    require(XOOPS_ROOT_PATH.'/header.php');
    $xoopsTpl->assign('gtd_delform', $formtext);
    require(XOOPS_ROOT_PATH.'/footer.php');
}


/**
 * @todo make SmartyNewsRenderer class
 */
function getAnnouncements($topicid, $limit=5, $start=0)
{
    global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsTpl;
    $module_handler = xoops_gethandler('module');
   
    if(!$count =& $module_handler->getByDirname('news') || $topicid == 0){
        $xoopsTpl->assign('gtd_useAnnouncements', false);
        return false;
    }
    include_once XOOPS_ROOT_PATH.'/modules/news/class/class.newsstory.php';
    $news_version = round($count->getVar('version') / 100, 2);
    
    switch ($news_version){
        case "1.1":
            $sarray = NewsStory::getAllPublished($limit, $start, $topicid);
        break;
            
        case "1.21":
        default:
            $sarray = NewsStory::getAllPublished($limit, $start, false, $topicid);        
    }
       
    $scount = count($sarray);
    for ( $i = 0; $i < $scount; $i++ ) {
    	$story = array();
    	$story['id'] = $sarray[$i]->storyid();
    	$story['poster'] = $sarray[$i]->uname();
    	if ( $story['poster'] != false ) {
    		$story['poster'] = "<a href='".XOOPS_URL."/userinfo.php?uid=".$sarray[$i]->uid()."'>".$story['poster']."</a>";
    	} else {
    		$story['poster'] = $xoopsConfig['anonymous'];
    	}
    	$story['posttime'] = formatTimestamp($sarray[$i]->published());
    	$story['text'] = $sarray[$i]->hometext();
    	$introcount = strlen($story['text']);
    	$fullcount = strlen($sarray[$i]->bodytext());
    	$totalcount = $introcount + $fullcount;
    	$morelink = '';
    	if ( $fullcount > 1 ) {
    		$morelink .= '<a href="'.XOOPS_URL.'/modules/news/article.php?storyid='.$sarray[$i]->storyid().'';
    		$morelink .= '">'._GTD_ANNOUNCE_READMORE.'</a> | ';
    		//$morelink .= sprintf(_NW_BYTESMORE,$totalcount);
    		//$morelink .= ' | ';
    	}
    	$ccount = $sarray[$i]->comments();
    	$morelink .= '<a href="'.XOOPS_URL.'/modules/news/article.php?storyid='.$sarray[$i]->storyid().'';
        $morelink2 = '<a href="'.XOOPS_URL.'/modules/news/article.php?storyid='.$sarray[$i]->storyid().'';
    	if ( $ccount == 0 ) {
    		$morelink .= '">'._GTD_COMMMENTS.'</a>';
    	} else {
    		if ( $fullcount < 1 ) {
    			if ( $ccount == 1 ) {
    				$morelink .= '">'._GTD_ANNOUNCE_READMORE.'</a> | '.$morelink2.'">'._GTD_ANNOUNCE_ONECOMMENT.'</a>';
    			} else {
    				$morelink .= '">'._GTD_ANNOUNCE_READMORE.'</a> | '.$morelink2.'">';
    				$morelink .= sprintf(_GTD_ANNOUNCE_NUMCOMMENTS, $ccount);
    				$morelink .= '</a>';
    			}
    		} else {
    			if ( $ccount == 1 ) {
    				$morelink .= '">'._GTD_ANNOUNCE_ONECOMMENT.'</a>';
    			} else {
    				$morelink .= '">';
    				$morelink .= sprintf(_GTD_ANNOUNCE_NUMCOMMENTS, $ccount);
    				$morelink .= '</a>';
    			}
    		}
    	}
    	$story['morelink'] = $morelink;
    	$story['adminlink'] = '';
    	if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid()) ) {
    		$story['adminlink'] = $sarray[$i]->adminlink();
    	}
        //$story['mail_link'] = 'mailto:?subject='.sprintf(_NW_INTARTICLE,$xoopsConfig['sitename']).'&amp;body='.sprintf(_NW_INTARTFOUND, $xoopsConfig['sitename']).':  '.XOOPS_URL.'/modules/news/article.php?storyid='.$sarray[$i]->storyid();
    	$story['imglink'] = '';
    	$story['align'] = '';
    	if ( $sarray[$i]->topicdisplay() ) {
    		$story['imglink'] = $sarray[$i]->imglink();
    		$story['align'] = $sarray[$i]->topicalign();
    	}
    	$story['title'] = $sarray[$i]->textlink().'&nbsp;:&nbsp;'."<a href='".XOOPS_URL."/modules/news/article.php?storyid=".$sarray[$i]->storyid()."'>".$sarray[$i]->title()."</a>";
    	$story['hits'] = $sarray[$i]->counter();
    	// The line below can be used to display a Permanent Link image
        // $story['title'] .= "&nbsp;&nbsp;<a href='".XOOPS_URL."/modules/news/article.php?storyid=".$sarray[$i]->storyid()."'><img src='".XOOPS_URL."/modules/news/images/x.gif' alt='Permanent Link' /></a>";
    
    	$xoopsTpl->append('gtd_announcements', $story);
    	$xoopsTpl->assign('gtd_useAnnouncements', true);
    	unset($story);
    }
}

function getDepartmentName($dept)
{
    //BTW - I don't like that we rely on the global $depts variable to exist.
    // What if we moved this into the DepartmentsHandler class?
    global $depts;
    if(isset($depts[$dept])){     // Make sure that ticket has a department
        $department = $depts[$dept]->getVar('department');
    } else {    // Else, fill it with 0
        $department = _GTD_TEXT_NO_DEPT;
    }
    return $department;
}

function _cleanTickets($tickets)
{
    $t_tickets = explode(',', $tickets);
    $ret   = array();
    foreach($t_tickets as $ticket) {
        $ticket = intval($ticket);
        if ($ticket) {
            $ret[] = $ticket;
        }
    }
    unset($t_tickets);
    return $ret;
}

function staffmain_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
    global $limit, $start, $refresh, $displayName, $gtd_isStaff, $_gtdSession, $_eventsrv, $gtd_module_header;

    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }

    $gtdConfig = gtdGetModuleConfig();
    $aSavedSearches =& gtdGetSavedSearches();
    $allSavedSearches =& gtdGetGlobalSavedSearches();
    
    $hDepartments =& gtdGetHandler('department');
    $hTickets     =& gtdGetHandler('ticket');
    $hTicketList  =& gtdGetHandler('ticketList');

    //Set Number of items in each section
    if ($limit == 0) {
        $limit = $gtdConfig['gtd_staffTicketCount'];
    } elseif ($limit == -1) {
        $limit = 0;
    }
    $uid = $xoopsUser->getVar('uid');
    $depts       =& $hDepartments->getObjects(null, true);
    $genre    =& $hTickets->getStaffTickets($uid, GTD_QRY_STAFF_HIGHGENRE, $start, $limit);
    $ticketLists =& $hTicketList->getListsByUser($uid);
    $all_users   = array();
    
    $tickets = array();
    $i = 0;
    foreach($ticketLists as $ticketList){
        $searchid = $ticketList->getVar('searchid');
        //Make sure that ticket list exists in $allSavedSearches
        if (isset($allSavedSearches[$searchid]) && is_array($allSavedSearches[$searchid])) {
            $crit     = $allSavedSearches[$searchid]['search'];
            $searchname = $allSavedSearches[$searchid]['name'];
            $searchOnCustFields = $allSavedSearches[$searchid]['hasCustFields'];
            $crit->setLimit($limit);
            $newTickets = $hTickets->getObjectsByStaff($crit, false, $searchOnCustFields);
            $tickets[$i] = array();
            $tickets[$i]['tickets'] = array();
            $tickets[$i]['searchid'] = $searchid;
            $tickets[$i]['searchname'] = $searchname;
            $tickets[$i]['tableid'] = _safeHTMLId($searchname);
            $tickets[$i]['hasTickets'] = count($newTickets) > 0;
            $j = 0;
            foreach($newTickets as $ticket){
                $dept = @$depts[$ticket->getVar('department')];
                $tickets[$i]['tickets'][$j] = array('id'   => $ticket->getVar('id'),
                                        'uid'           => $ticket->getVar('uid'),
                                        'pvp'  => $ticket->getVar('pvp'),
                                        'nom_danseur'  => $ticket->getVar('nom_danseur'),
                                        'nom_danseuse'  => $ticket->getVar('nom_danseuse'),
                                        'prenom_danseur'  => $ticket->getVar('prenom_danseur'),
                                        'prenom_danseuse'  => $ticket->getVar('prenom_danseuse'),
                                        'description'   => $ticket->getVar('description'),
                                        'department'    => _safeDepartmentName($dept),
                                        'departmentid'  => $ticket->getVar('department'),
                                        'departmenturl' => gtdMakeURI('index.php', array('op' => 'staffViewAll', 'dept'=> $ticket->getVar('department'))),
                                        'genre'      => $ticket->getVar('genre'),
                                        'status'        => gtdGetStatus($ticket->getVar('status')),
                                        'posted'        => $ticket->posted(),
                                        'ownership'     => _GTD_MESSAGE_NOOWNER,
                                        'ownerid'       => $ticket->getVar('ownership'),
                                        'closedBy'      => $ticket->getVar('closedBy'),
                                        'totalTimeSpent'=> $ticket->getVar('totalTimeSpent'),
                                        'uname'         => '',
                                        'userinfo'      => GTD_SITE_URL . '/userinfo.php?uid=' . $ticket->getVar('uid'),
                                        'ownerinfo'     => '',
                                        'url'           => GTD_BASE_URL . '/ticket.php?id=' . $ticket->getVar('id'),
                                        'overdue'       => $ticket->isOverdue());
                $all_users[$ticket->getVar('uid')] = '';
                $all_users[$ticket->getVar('ownership')] = '';
                $all_users[$ticket->getVar('closedBy')] = '';
                $j++;
            }
            $i++;
            unset($newTickets);
        }
    }
    
    //Retrieve all member information for the current page
    if (count($all_users)) {
        $crit  = new Criteria('uid', "(". implode(array_keys($all_users), ',') .")", 'IN');
        $users =& gtdGetUsers($crit, $displayName);
    } else {
        $users = array();
    }        
    
	//Update tickets with user information
    for($i=0; $i<count($ticketLists);$i++){
        for($j=0;$j<count($tickets[$i]['tickets']);$j++) {
           if (isset($users[ $tickets[$i]['tickets'][$j]['uid'] ])) {
                $tickets[$i]['tickets'][$j]['uname'] = $users[$tickets[$i]['tickets'][$j]['uid']];
            } else {
                $tickets[$i]['tickets'][$j]['uname'] = $xoopsConfig['anonymous'];
            }
            if ($tickets[$i]['tickets'][$j]['ownerid']) {
                if (isset($users[$tickets[$i]['tickets'][$j]['ownerid']])) {
                    $tickets[$i]['tickets'][$j]['ownership'] = $users[$tickets[$i]['tickets'][$j]['ownerid']];
                    $tickets[$i]['tickets'][$j]['ownerinfo'] = XOOPS_URL.'/userinfo.php?uid=' . $tickets[$i]['tickets'][$j]['ownerid'];
                }
            }
        }
    }
    
    $xoopsOption['template_main'] = 'gtd_staff_index.html';   // Set template
    require(XOOPS_ROOT_PATH.'/header.php');                     // Include the page header    
    if($refresh > 0){
        $gtd_module_header .= "<meta http-equiv=\"Refresh\" content=\"$refresh;url=".XOOPS_URL."/modules/gtd/index.php?refresh=$refresh\">";
    }  
    $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
    $xoopsTpl->assign('gtd_ticketLists', $tickets);
    $xoopsTpl->assign('gtd_hasTicketLists', count($tickets) > 0);
    $xoopsTpl->assign('gtd_refresh', $refresh);  
    $xoopsTpl->assign('xoops_module_header',$gtd_module_header);
    $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
    $xoopsTpl->assign('gtd_uid', $xoopsUser->getVar('uid'));
    $xoopsTpl->assign('gtd_current_file', basename(__FILE__));
    $xoopsTpl->assign('gtd_savedSearches', $aSavedSearches);
    $xoopsTpl->assign('gtd_allSavedSearches', $allSavedSearches);
    
    getAnnouncements($gtdConfig['gtd_announcements']);
    
    require(XOOPS_ROOT_PATH.'/footer.php');
}

function _safeHTMLId($orig_text)
{
    //Only allow alphanumeric characters
    $match = array('/[^a-zA-Z0-9]]/', '/\s/');
    $replace = array('', '');
    
    $htmlID = preg_replace($match, $replace, $orig_text);
    
    return $htmlID;
}

function _safeDepartmentName($deptObj)
{
    if (is_object($deptObj)) {
        $department = $deptObj->getVar('department');
    } else {    // Else, fill it with 0
        $department = _GTD_TEXT_NO_DEPT;
    }
    return $department;    
}

function staffviewall_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
    global $gtd_isStaff, $sort_order, $start, $limit, $gtd_module_header, $state_opt, $aSavedSearches;
    if (!$gtd_isStaff) {
        redirect_header(GTD_BASE_URL."/".basename(__FILE__), 3, _NOPERM);
    }

	//Sanity Check: sort / order column valid
	$sort  = @$_REQUEST['sort'];
	$order = @$_REQUEST['order'];
	
	$sort_columns = array('id' => 'DESC', 'genre' => 'DESC', 'elapsed' => 'ASC', 'lastupdate' => 'ASC', 'status' => 'ASC', 'pvp' => 'ASC' , 'prenom_danseuse' => 'ASC' , 'nom_danseur' => 'ASC' , 'prenom_danseur' => 'ASC' , 'nom_danseuse' => 'ASC' , 'department' => 'ASC', 'ownership' => 'ASC', 'uid' => 'ASC');
    $sort  = array_key_exists(strtolower($sort), $sort_columns) ? $sort : 'id';
    $order = (in_array(strtoupper($order), $sort_order) ? $order : $sort_columns[$sort]);
    
    $uid       = $xoopsUser->getVar('uid');    
    $dept      = intval(isset($_REQUEST['dept']) ? $_REQUEST['dept'] : 0);
    $status    = intval(isset($_REQUEST['status']) ? $_REQUEST['status'] : -1);
    $ownership = intval(isset($_REQUEST['ownership']) ? $_REQUEST['ownership'] : -1);
    $state     = intval(isset($_REQUEST['state']) ? $_REQUEST['state'] : -1);
    
    $gtdConfig  =& gtdGetModuleConfig();
    $hTickets     =& gtdGetHandler('ticket');
    $hMembership  =& gtdGetHandler('membership');
    
    if ($limit == 0) {
        $limit = $gtdConfig['gtd_staffTicketCount'];
    } elseif ($limit == -1) {
        $limit = 0;
    }
	
	//Prepare Database Query and Querystring
    $crit     = new CriteriaCompo(new Criteria('uid', $uid, '=', 'j'));    
    $qs       = array('op' => 'staffViewAll', //Common Query String Values
		            'start' => $start,
		            'limit' => $limit);
 
    if ($dept) {
        $qs['dept'] = $dept;
        $crit->add(new Criteria('department', $dept, '=', 't'));
    }
    if ($status != -1) {
        $qs['status'] = $status;
        $crit->add(new Criteria('status', $status, '=', 't'));
    }
    if ($ownership != -1) {
        $qs['ownership'] = $ownership;
        $crit->add(new Criteria('ownership', $ownership, '=', 't'));
    }
    
    if($state != -1){
        $qs['state'] = $state;
        $crit->add(new Criteria('state', $state, '=', 's'));
    }
    
    $crit->setLimit($limit);
    $crit->setStart($start);
    $crit->setSort($sort);
    $crit->setOrder($order);
	    
    //Setup Column Sorting Vars
    $tpl_cols = array();
    foreach ($sort_columns as $col=>$initsort) {
        $col_qs = array('sort' => $col);
		//Check if we need to sort by current column
        if ($sort == $col) {
            $col_qs['order'] = ($order == $sort_order[0] ? $sort_order[1]: $sort_order[0]);
            $col_sortby = true;
        } else {
            $col_qs['order'] = $initsort;
            $col_sortby = false;
        }
        $tpl_cols[$col] = array('url'=>gtdMakeURI(basename(__FILE__), array_merge($qs, $col_qs)),
                        'urltitle' => _GTD_TEXT_SORT_TICKETS,
                        'sortby' => $col_sortby,
                        'sortdir' => strtolower($col_qs['order']));
    }
    
       
    $allTickets  = $hTickets->getObjectsByStaff($crit, true);
    $count       = $hTickets->getCountByStaff($crit);
    $nav         = new XoopsPageNav($count, $limit, $start, 'start', "op=staffViewAll&amp;limit=$limit&amp;sort=$sort&amp;order=$order&amp;dept=$dept&amp;status=$status&amp;ownership=$ownership");
    $tickets     = array();
    $allUsers    = array();
    $depts       =& $hMembership->membershipByStaff($xoopsUser->getVar('uid'), true);    //All Departments for Staff Member
    
    foreach($allTickets as $ticket){
        $deptid = $ticket->getVar('department');
        $tickets[] = array('id'=>$ticket->getVar('id'),
            'uid'=>$ticket->getVar('uid'),
	    'pvp'  => $ticket->getVar('pvp'),
            'prenom_danseuse'  => $ticket->getVar('prenom_danseuse'),
            'nom_danseur'  => $ticket->getVar('nom_danseur'),
	    'prenom_danseur'  => $ticket->getVar('prenom_danseur'),
	    'nom_danseuse'  => $ticket->getVar('nom_danseuse'),
            'description'=>$ticket->getVar('description'),
            'department'=>_safeDepartmentName($depts[$deptid]),
            'departmentid'=> $deptid,
            'departmenturl'=>gtdMakeURI('index.php', array('op' => 'staffViewAll', 'dept'=> $deptid)),
            'genre'=>$ticket->getVar('genre'),
            'status'=>gtdGetStatus($ticket->getVar('status')),
            'posted'=>$ticket->posted(),
            'ownership'=>_GTD_MESSAGE_NOOWNER,
            'ownerid' => $ticket->getVar('ownership'),
            'closedBy'=>$ticket->getVar('closedBy'),
            'closedByUname'=>'',
            'totalTimeSpent'=>$ticket->getVar('totalTimeSpent'),
            'uname'=>'',
            'userinfo'=>GTD_SITE_URL . '/userinfo.php?uid=' . $ticket->getVar('uid'),
            'ownerinfo'=>'',
            'url'=>GTD_BASE_URL . '/ticket.php?id=' . $ticket->getVar('id'),
            'elapsed' => $ticket->elapsed(),
            'lastUpdate' => $ticket->lastUpdate(),
            'overdue' => $ticket->isOverdue());
        $allUsers[$ticket->getVar('uid')] = '';
        $allUsers[$ticket->getVar('ownership')] = '';
        $allUsers[$ticket->getVar('closedBy')] = '';     
    }
    $has_allTickets = count($allTickets) > 0;
    unset($allTickets);
	
    //Get all member information needed on this page    
    $crit  = new Criteria('uid', "(". implode(array_keys($allUsers), ',') .")", 'IN');
    $users =& gtdGetUsers($crit, $gtdConfig['gtd_displayName']);
	unset($allUsers);
	
	$staff_opt =& gtdGetStaff($gtdConfig['gtd_displayName']);
    
    for($i=0;$i<count($tickets);$i++) {
        if (isset($users[$tickets[$i]['uid']])) {
            $tickets[$i]['uname'] = $users[$tickets[$i]['uid']]; 
        } else {
            $tickets[$i]['uname'] = $xoopsConfig['anonymous'];
        }
        if ($tickets[$i]['ownerid']) {
            if (isset($users[$tickets[$i]['ownerid']])) {
                $tickets[$i]['ownership'] = $users[$tickets[$i]['ownerid']];
                $tickets[$i]['ownerinfo'] = GTD_SITE_URL.'/userinfo.php?uid=' . $tickets[$i]['ownerid'];
            }
        }
        if ($tickets[$i]['closedBy']) {
            if (isset($users[$tickets[$i]['closedBy']])) {
                $tickets[$i]['closedByUname'] = $users[$tickets[$i]['closedBy']];
            }
        }
    }
                          
    $xoopsOption['template_main'] = 'gtd_staff_viewall.html';   // Set template
    require(XOOPS_ROOT_PATH.'/header.php');                     // Include the page header
    
    $javascript = "<script type=\"text/javascript\" src=\"". GTD_BASE_URL ."/include/functions.js\"></script>
<script type=\"text/javascript\" src='".GTD_SCRIPT_URL."/changeSelectedState.php?client'></script>
<script type=\"text/javascript\">
<!--
function states_onchange()
{
    state = xoopsGetElementById('state');
    var sH = new gtdweblib(stateHandler);
    sH.statusesbystate(state.value);
}

var stateHandler = {
    statusesbystate: function(result){
        var statuses = gE('status');
        gtdFillSelect(statuses, result);
    }
}

function window_onload()
{
    gtdDOMAddEvent(xoopsGetElementById('state'), 'change', states_onchange, true);
}

window.setTimeout('window_onload()', 1500);
//-->
</script>";
    
    $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
    $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
    $xoopsTpl->assign('gtd_cols', $tpl_cols);
    $xoopsTpl->assign('gtd_allTickets', $tickets);
    $xoopsTpl->assign('gtd_has_tickets', $has_allTickets);
    $xoopsTpl->assign('gtd_genres', array(3, 2, 1));
    $xoopsTpl->assign('xoops_module_header',$javascript.$gtd_module_header);
    $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));
    if($limit != 0){
        $xoopsTpl->assign('gtd_pagenav', $nav->renderNav());
    }
    $xoopsTpl->assign('gtd_limit_options', array(-1 => _GTD_TEXT_SELECT_ALL, 10 => '10', 15 => '15', 20 => '20', 30 => '30'));
    $xoopsTpl->assign('gtd_filter', array('department' => $dept,
            'status' => $status,
            'state' => $state,
            'ownership' => $ownership,
            'limit' => $limit,
            'start' => $start,
            'sort' => $sort,
            'order' => $order));
    
    $xoopsTpl->append('gtd_department_values', 0);
    $xoopsTpl->append('gtd_department_options', _GTD_TEXT_SELECT_ALL);
    
    if($gtdConfig['gtd_deptVisibility'] == 1){    // Apply dept visibility to staff members?
        $hMembership =& gtdGetHandler('membership');
        $depts =& $hMembership->getVisibleDepartments($xoopsUser->getVar('uid'));
    }
    
    foreach($depts as $gtd_id=>$obj) {
        $xoopsTpl->append('gtd_department_values', $gtd_id);
        $xoopsTpl->append('gtd_department_options', $obj->getVar('department'));
    }

    $xoopsTpl->assign('gtd_ownership_options', array_values($staff_opt));
    $xoopsTpl->assign('gtd_ownership_values', array_keys($staff_opt));
    $xoopsTpl->assign('gtd_state_options', array_keys($state_opt));
    $xoopsTpl->assign('gtd_state_values', array_values($state_opt));
    $xoopsTpl->assign('gtd_savedSearches', $aSavedSearches);

    $hStatus =& gtdGetHandler('status');
    $crit = new Criteria('', '');
    $crit->setSort('description');
    $crit->setOrder('ASC');
    $statuses =& $hStatus->getObjects($crit);
    
    $xoopsTpl->append('gtd_status_options', _GTD_TEXT_SELECT_ALL);
    $xoopsTpl->append('gtd_status_values', -1);
    foreach($statuses as $status){
        $xoopsTpl->append('gtd_status_options', $status->getVar('description'));
        $xoopsTpl->append('gtd_status_values', $status->getVar('id'));
    }

    $xoopsTpl->assign('gtd_department_current', $dept);
    $xoopsTpl->assign('gtd_status_current', $status);
    $xoopsTpl->assign('gtd_current_file', basename(__FILE__));
    $xoopsTpl->assign('gtd_text_allTickets', _GTD_TEXT_ALL_TICKETS);
    
    require(XOOPS_ROOT_PATH.'/footer.php');
}

function usermain_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
    global $gtd_module_header;

    
    $xoopsOption['template_main'] = 'gtd_user_index.html';    // Set template
    require(XOOPS_ROOT_PATH.'/header.php');                     // Include the page header
    
    $gtdConfig = gtdGetModuleConfig();
    $hStaff =& gtdGetHandler('staff');
    //Déclaration des départements pour le template
	$hDepartments =& gtdGetHandler('department'); 
	$depts    =& $hDepartments->getObjects(null, true);
       

    
    $staffCount =& $hStaff->getObjects();
    if (count($staffCount) == 0) {
        $xoopsTpl->assign('gtd_noStaff', true);
    }
    /**
     * @todo remove calls to these three classes and use the ones in beginning
     */
    $member_handler =& xoops_gethandler('member');        
    $hDepartments =& gtdGetHandler('department');
    $hTickets =& gtdGetHandler('ticket');
        
    $userTickets =& $hTickets->getMyUnresolvedTickets($xoopsUser->getVar('uid'), true);
    
    //Envoi dans le template
    foreach($userTickets as $ticket){
        $aUserTickets[] = array('id'=>$ticket->getVar('id'),
            'uid'=>$ticket->getVar('uid'),
	    'dossier'=>$ticket->getVar('dossier'),
            'nom_danseur'=>$ticket->getVar('nom_danseur'),
	    'prenom_danseur'=>$ticket->getVar('prenom_danseur'),
	    'prenom_danseuse'=>$ticket->getVar('prenom_danseuse'),
	    'depart'=>$ticket->getVar('depart'),
	    'mode_paiement'=>$ticket->getVar('mode_paiement'),
	    'echeance'=>$ticket->getVar('echeance'),
	    'pvp'=>$ticket->getVar('pvp'),
                        'department'=>_safeDepartmentName($depts[$ticket->getVar('department')]),
                        'departmentid'=> $ticket->getVar('department'),
                        'departmenturl'=>gtdMakeURI(basename(__FILE__), array('op' => 'userViewAll', 'dept'=> $ticket->getVar('department'))),      
            'status'=>gtdGetStatus($ticket->getVar('status')),
            'genre'=>$ticket->getVar('genre'),
            'posted'=>$ticket->posted());
    }
    $has_userTickets = count($userTickets) > 0;        
    if($has_userTickets){ 
        $xoopsTpl->assign('gtd_userTickets', $aUserTickets);
    } else {
        $xoopsTpl->assign('gtd_userTickets', 0);
    }
    $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
    $xoopsTpl->assign('gtd_has_userTickets', $has_userTickets);
    $xoopsTpl->assign('gtd_genres', array(3, 2, 1));
    $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));        
    $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
    $xoopsTpl->assign('xoops_module_header',$gtd_module_header);
        
    getAnnouncements($gtdConfig['gtd_announcements']);
        
    require(XOOPS_ROOT_PATH.'/footer.php');                     //Include the page footer    
}

function userviewall_display()
{
    global $xoopsOption, $xoopsTpl, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsUserIsAdmin;
    global $gtd_module_header, $sort, $order, $sort_order, $limit, $start, $state_opt, $state;
    
    $xoopsOption['template_main'] = 'gtd_user_viewall.html';    // Set template
    require(XOOPS_ROOT_PATH.'/header.php');                     // Include the page header

    //Sanity Check: sort column valid
    $sort_columns = array('id' => 'DESC', 'genre' => 'DESC', 'elapsed' => 'ASC', 'lastupdate' => 'ASC', 'status' => 'ASC', 'dossier' => 'ASC' , 'prenom_danseuse' => 'ASC' , 'nom_danseur' => 'ASC' , 'prenom_danseur' => 'ASC' , 'nom_danseuse' => 'ASC' , 'department' => 'ASC', 'ownership' => 'ASC', 'uid' => 'ASC');
    $sort  = array_key_exists($sort, $sort_columns) ? $sort : 'id';
    $order = @$_REQUEST['order'];
    $order = (in_array(strtoupper($order), $sort_order) ? $order : $sort_columns[$sort]);
    $uid   = $xoopsUser->getVar('uid');
    
    $hDepartments =& gtdGetHandler('department');
    $hTickets      =& gtdGetHandler('ticket');
    $hStaff       =& gtdGetHandler('staff');
    
    $dept     = intval(isset($_REQUEST['dept']) ? $_REQUEST['dept'] : 0);
    $status   = intval(isset($_REQUEST['status']) ? $_REQUEST['status'] : -1);
    $state    = intval(isset($_REQUEST['state']) ? $_REQUEST['state'] : -1);
    $depts    =& $hDepartments->getObjects(null, true);
    
    if ($limit == 0) {
        $limit = 10;
    } elseif ($limit == -1) {
        $limit = 0;
    }
	
	//Prepare Database Query and Querystring
    $crit     = new CriteriaCompo(new Criteria ('uid', $uid));    
    $qs       = array('op' => 'userViewAll', //Common Query String Values
		            'start' => $start,
		            'limit' => $limit);
 
    if ($dept) {
        $qs['dept'] = $dept;
        $crit->add(new Criteria('department', $dept, '=', 't'));
    }
    if ($status != -1) {
        $qs['status'] = $status;
        $crit->add(new Criteria('status', $status, '=', 't'));
    }
    
    if($state != -1){
        $qs['state'] = $state;
        $crit->add(new Criteria('state', $state, '=', 's'));
    }
 
    $crit->setLimit($limit);
    $crit->setStart($start);
    $crit->setSort($sort);
    $crit->setOrder($order);
	    
    //Setup Column Sorting Vars
    $tpl_cols = array();
    foreach ($sort_columns as $col => $initsort) {
        $col_qs = array('sort' => $col);
		//Check if we need to sort by current column
        if ($sort == $col) {
            $col_qs['order'] = ($order == $sort_order[0] ? $sort_order[1]: $sort_order[0]);
            $col_sortby = true;
        } else {
            $col_qs['order'] = $initsort;
            $col_sortby = false;
        }
        $tpl_cols[$col] = array('url'=>gtdMakeURI(basename(__FILE__), array_merge($qs, $col_qs)),
                        'urltitle' => _GTD_TEXT_SORT_TICKETS,
                        'sortby' => $col_sortby,
                        'sortdir' => strtolower($col_qs['order']));
    }

    $xoopsTpl->assign('gtd_cols', $tpl_cols);   
    $staffCount =& $hStaff->getObjects();
    if(count($staffCount) == 0){
        $xoopsTpl->assign('gtd_noStaff', true);
    }

    $userTickets =& $hTickets->getObjects($crit);
    foreach($userTickets as $ticket){
        $aUserTickets[] = array('id'=>$ticket->getVar('id'),
                        'uid'=>$ticket->getVar('uid'),
			'pvp'  => $ticket->getVar('pvp'),
			'nom_danseuse'  => $ticket->getVar('nom_danseuse'),
                        'prenom_danseuse'  => $ticket->getVar('prenom_danseuse'),
                        'nom_danseur'  => $ticket->getVar('nom_danseur'),
			'prenom_danseur'  => $ticket->getVar('prenom_danseur'),
			'ownership'  => $ticket->getVar('ownership'),
			'ownerid'  => $ticket->getVar('ownership'),
                        'status'=>gtdGetStatus($ticket->getVar('status')),
                        'department'=>_safeDepartmentName($depts[$ticket->getVar('department')]),
                        'departmentid'=> $ticket->getVar('department'),
                        'departmenturl'=>gtdMakeURI(basename(__FILE__), array('op' => 'userViewAll', 'dept'=> $ticket->getVar('department'))),                        
                        'genre'=>$ticket->getVar('genre'),
                        'posted'=>$ticket->posted(),
                        'elapsed'=>$ticket->elapsed());
    }
    $has_userTickets = count($userTickets) > 0;        
    if($has_userTickets){ 
        $xoopsTpl->assign('gtd_userTickets', $aUserTickets);
    } else {
        $xoopsTpl->assign('gtd_userTickets', 0);
    }
    
    $javascript = "<script type=\"text/javascript\" src=\"". GTD_BASE_URL ."/include/functions.js\"></script>
<script type=\"text/javascript\" src='".GTD_SCRIPT_URL."/changeSelectedState.php?client'></script>
<script type=\"text/javascript\">
<!--
function states_onchange()
{
    state = xoopsGetElementById('state');
    var sH = new gtdweblib(stateHandler);
    sH.statusesbystate(state.value);
}

var stateHandler = {
    statusesbystate: function(result){
        var statuses = gE('status');
        gtdFillSelect(statuses, result);
    }
}

function window_onload()
{
    gtdDOMAddEvent(xoopsGetElementById('state'), 'change', states_onchange, true);
}

window.setTimeout('window_onload()', 1500);
//-->
</script>";
    
    $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
    $xoopsTpl->assign('gtd_has_userTickets', $has_userTickets);
    $xoopsTpl->assign('gtd_viewAll', true);
    $xoopsTpl->assign('gtd_genres', array(3, 2, 1));
    $xoopsTpl->assign('gtd_genres_desc', array('3' => _GTD_GENRE3, '2' => _GTD_GENRE2, '1' => _GTD_GENRE1));        
    $xoopsTpl->assign('gtd_imagePath', GTD_IMAGE_URL .'/');
    $xoopsTpl->assign('xoops_module_header',$javascript.$gtd_module_header);
    $xoopsTpl->assign('gtd_limit_options', array(-1 => _GTD_TEXT_SELECT_ALL, 10 => '10', 15 => '15', 20 => '20', 30 => '30'));
    $xoopsTpl->assign('gtd_filter', array('department' => $dept,
            'status' => $status,
            'limit' => $limit,
            'start' => $start,
            'sort' => $sort,
            'order' => $order,
            'state' => $state));
    $xoopsTpl->append('gtd_department_values', 0);
    $xoopsTpl->append('gtd_department_options', _GTD_TEXT_SELECT_ALL);
    
    //$depts = getVisibleDepartments($depts);
    $hMembership =& gtdGetHandler('membership');
    $depts =& $hMembership->getVisibleDepartments($xoopsUser->getVar('uid'));
    foreach($depts as $gtd_id=>$obj) {
        $xoopsTpl->append('gtd_department_values', $gtd_id);
        $xoopsTpl->append('gtd_department_options', $obj->getVar('department'));
    }
    
    $hStatus =& gtdGetHandler('status');
    $crit = new Criteria('', '');
    $crit->setSort('description');
    $crit->setOrder('ASC');
    $statuses =& $hStatus->getObjects($crit);
    
    $xoopsTpl->append('gtd_status_options', _GTD_TEXT_SELECT_ALL);
    $xoopsTpl->append('gtd_status_values', -1);
    foreach($statuses as $status){
        $xoopsTpl->append('gtd_status_options', $status->getVar('description'));
        $xoopsTpl->append('gtd_status_values', $status->getVar('id'));
    } 

    $xoopsTpl->assign('gtd_department_current', $dept);
    $xoopsTpl->assign('gtd_status_current', $status);    
    $xoopsTpl->assign('gtd_state_options', array_keys($state_opt));
    $xoopsTpl->assign('gtd_state_values', array_values($state_opt));            
                
    require(XOOPS_ROOT_PATH.'/footer.php');
}
?>