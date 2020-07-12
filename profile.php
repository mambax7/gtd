<?php
//$Id: profile.php,v 1.41 2005/10/18 19:27:30 eric_juden Exp $
require_once('header.php');
include_once(GTD_BASE_PATH.'/functions.php');

// Disable module caching in smarty
$xoopsConfig['module_cache'][$xoopsModule->getVar('mid')] = 0;

if($xoopsUser){
    $responseTplID = 0;
    
    $op = 'default';
    if(isset($_REQUEST['op'])){
        $op = $_REQUEST['op'];
    }
    
    if(isset($_GET['responseTplID'])){
        $responseTplID = intval($_GET['responseTplID']);
    }
    
    $xoopsOption['template_main'] = 'gtd_staff_profile.html';   // Set template
    require(XOOPS_ROOT_PATH.'/header.php');                     // Include the page header
    
    $numResponses = 0;
    $uid = $xoopsUser->getVar('uid');
    $hStaff =& gtdGetHandler('staff');
    if (!$staff =& $hStaff->getByUid($uid)) {
        redirect_header(GTD_BASE_URL."/index.php", 3, _GTD_ERROR_INV_STAFF);
        exit();
    }
    $hTicketList =& gtdGetHandler('ticketList');
    $hResponseTpl =& gtdGetHandler('responseTemplates');
    $crit = new Criteria('uid', $uid);
    $crit->setSort('name');
    $responseTpl =& $hResponseTpl->getObjects($crit);
    
    foreach($responseTpl as $response){
        $aResponseTpl[] = array('id'=>$response->getVar('id'),
                              'uid'=>$response->getVar('uid'),
                              'name'=>$response->getVar('name'),
                              'response'=>$response->getVar('response'));
    }
    $has_responseTpl = count($responseTpl) > 0;
    unset($responseTpl);
    
    $displayTpl =& $hResponseTpl->get($responseTplID);
    
    switch($op){
        case "responseTpl":
            if(isset($_POST['updateResponse'])){
                if(isset($_POST['attachSig'])){
                    $staff->setVar('attachSig', $_POST['attachSig']);
                    if(!$hStaff->insert($staff)){
                        $message = _GTD_MESSAGE_UPDATE_SIG_ERROR;
                    }
                }
                if($_POST['name'] == '' || $_POST['replyText'] == ''){
                    redirect_header(GTD_BASE_URL."/profile.php", 3, _GTD_ERROR_INV_TEMPLATE);
                }
                if($_POST['responseid'] != 0){
                    $updateTpl =& $hResponseTpl->get($_POST['responseid']);      
                } else {
                    $updateTpl =& $hResponseTpl->create();
                }
                $updateTpl->setVar('uid', $uid);
                $updateTpl->setVar('name',$_POST['name']);
                $updateTpl->setVar('response',$_POST['replyText']);
                if($hResponseTpl->insert($updateTpl)){
                    $message = _GTD_MESSAGE_RESPONSE_TPL;
                } else {
                    $message = _GTD_MESSAGE_RESPONSE_TPL_ERROR;
                }
                redirect_header(GTD_BASE_URL."/profile.php", 3, $message);
            } else {        // Delete response template
                $hResponseTpl =& gtdGetHandler('responseTemplates');
                $displayTpl =& $hResponseTpl->get($_POST['tplID']);
                if($hResponseTpl->delete($displayTpl)){
                    $message = _GTD_MESSAGE_DELETE_RESPONSE_TPL;
                } else {
                    $message = _GTD_MESSAGE_DELETE_RESPONSE_TPL_ERROR;
                }
                redirect_header(GTD_BASE_URL."/profile.php", 3, $message);
            }
        break;
            
        case "updateNotification":
            $notArray = (is_array($_POST['notifications']) ?  $_POST['notifications'] : array(0));
            $notValue = array_sum($notArray);
            $staff->setVar('notify', $notValue);
            if(isset($_POST['email']) && $_POST['email'] <> $staff->getVar('email')){
                $staff->setVar('email', $_POST['email']);
            }
            if(!$hStaff->insert($staff)){
                $message = _GTD_MESSAGE_UPDATE_EMAIL_ERROR;
                
            }
            $message = _GTD_MESSAGE_NOTIFY_UPDATE;
            redirect_header(GTD_BASE_URL."/profile.php", 3, $message);
            break;
            
        case "addTicketList":
            if(isset($_POST['savedSearch']) && ($_POST['savedSearch'] != 0)){
                $searchid = intval($_POST['savedSearch']);
                $ticketList =& $hTicketList->create();
                $ticketList->setVar('uid', $xoopsUser->getVar('uid'));
                $ticketList->setVar('searchid', $searchid);
                $ticketList->setVar('weight', $hTicketList->createNewWeight($xoopsUser->getVar('uid')));
                
                if($hTicketList->insert($ticketList)){
                    header("Location: ".GTD_BASE_URL."/profile.php");
                } else {
                    redirect_header(GTD_BASE_URL."/profile.php", 3, _GTD_MSG_ADD_TICKETLIST_ERR);
                }
            }
        break;
        
        case "editTicketList":
            if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0){
                $listID = intval($_REQUEST['id']);
            } else {
                redirect_header(GTD_BASE_URL."/profile.php", 3, _GTD_MSG_NO_ID);
            }
        break;
        
        case "deleteTicketList":
            if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0){
                $listID = intval($_REQUEST['id']);
            } else {
                redirect_header(GTD_BASE_URL."/profile.php", 3, _GTD_MSG_NO_ID);
            }
            $ticketList =& $hTicketList->get($listID);
            if($hTicketList->delete($ticketList, true)){
                header("Location: ".GTD_BASE_URL."/profile.php");
            } else {
                redirect_header(GTD_BASE_URL."/profile.php", 3, _GTD_MSG_DEL_TICKETLIST_ERR);
            }
        break;
        
        case "changeListWeight":
            if(isset($_REQUEST['id']) && $_REQUEST['id'] != 0){
                $listID = intval($_REQUEST['id']);
            } else {
                redirect_header(GTD_BASE_URL."/profile.php", 3, _GTD_MSG_NO_ID);
            }
            $up = false;
            if(isset($_REQUEST['up'])){
                $up = $_REQUEST['up'];
            }
            $hTicketList->changeWeight($listID, $up);
            header("Location: ".GTD_BASE_URL."/profile.php");
        break;
            
        default:
            $xoopsTpl->assign('gtd_responseTplID', $responseTplID);
            $module_header = '<!--[if gte IE 5.5000]><script src="iepngfix.js" language="JavaScript" type="text/javascript"></script><![endif]-->';
            $xoopsTpl->assign('gtd_imagePath', XOOPS_URL .'/modules/gtd/images/');
            $xoopsTpl->assign('gtd_has_sig', $staff->getVar('attachSig'));
            if(isset($aResponseTpl)){
                $xoopsTpl->assign('gtd_responseTpl', $aResponseTpl);
            } else {
                $xoopsTpl->assign('gtd_responseTpl', 0);
            }
            $xoopsTpl->assign('gtd_hasResponseTpl', (isset($aResponseTpl)) ? count($aResponseTpl) > 0 : 0);
            if(!empty($responseTplID)){
                $xoopsTpl->assign('gtd_displayTpl_id', $displayTpl->getVar('id'));
                $xoopsTpl->assign('gtd_displayTpl_name', $displayTpl->getVar('name'));
                $xoopsTpl->assign('gtd_displayTpl_response', $displayTpl->getVar('response', 'e'));
            } else {
                $xoopsTpl->assign('gtd_displayTpl_id', 0);
                $xoopsTpl->assign('gtd_displayTpl_name', '');
                $xoopsTpl->assign('gtd_displayTpl_response', '');
            }
            $xoopsTpl->assign('xoops_module_header', $module_header);
            $xoopsTpl->assign('gtd_callsClosed', $staff->getVar('callsClosed'));
            $xoopsTpl->assign('gtd_numReviews', $staff->getVar('numReviews'));
            $xoopsTpl->assign('gtd_responseTime', gtdFormatTime( ($staff->getVar('ticketsResponded') ? $staff->getVar('responseTime') / $staff->getVar('ticketsResponded') : 0)));
            $notify_method = $xoopsUser->getVar('notify_method');
            $xoopsTpl->assign('gtd_notify_method', ($notify_method == 1) ? _GTD_NOTIFY_METHOD1 : _GTD_NOTIFY_METHOD2);
    
            if(($staff->getVar('rating') == 0) || ($staff->getVar('numReviews') == 0)){
                $xoopsTpl->assign('gtd_rating', 0);
            } else {
                $xoopsTpl->assign('gtd_rating', intval($staff->getVar('rating')/$staff->getVar('numReviews')));
            }
            $xoopsTpl->assign('gtd_uid', $xoopsUser->getVar('uid'));
            $xoopsTpl->assign('gtd_rating0', _GTD_RATING0);
            $xoopsTpl->assign('gtd_rating1', _GTD_RATING1);
            $xoopsTpl->assign('gtd_rating2', _GTD_RATING2);
            $xoopsTpl->assign('gtd_rating3', _GTD_RATING3);
            $xoopsTpl->assign('gtd_rating4', _GTD_RATING4);
            $xoopsTpl->assign('gtd_rating5', _GTD_RATING5);
            $xoopsTpl->assign('gtd_staff_email', $staff->getVar('email'));
            $xoopsTpl->assign('gtd_savedSearches', $aSavedSearches);
            
            $myRoles =& $hStaff->getRoles($xoopsUser->getVar('uid'), true);
            $hNotification =& gtdGetHandler('notification');
            $settings =& $hNotification->getObjects(null, true);
            
            $templates =& $xoopsModule->getInfo('_email_tpl');
            $has_notifications = count($templates);
            
            // Check that notifications are enabled by admin
            $i = 0;
            $staff_enabled = true;
            foreach($templates as $template_id=>$template){
                if($template['category'] == 'dept'){
                    $staff_setting = $settings[$template_id]->getVar('staff_setting');
                    if($staff_setting == 4){
                        $staff_enabled = false;
                    } elseif($staff_setting == 2){
                        $staff_options = $settings[$template_id]->getVar('staff_options');
                        foreach($staff_options as $role){
                            if(array_key_exists($role, $myRoles)){
                                $staff_enabled = true;
                                break;
                            } else {
                                $staff_enabled = false;
                            }
                        }
                    }
                    
                    $deptNotification[] = array('id'=> $template_id,
                                                'name'=>$template['name'],
                                                'category'=>$template['category'],
                                                'template'=>$template['mail_template'],
                                                'subject'=>$template['mail_subject'],
                                                'bitValue'=>(pow(2, $template['bit_value'])),
                                                'title'=>$template['title'],
                                                'caption'=>$template['caption'],
                                                'description'=>$template['description'],
                                                'isChecked'=>($staff->getVar('notify') & pow(2, $template['bit_value'])) > 0,
                                                'staff_setting'=> $staff_enabled);
                }
            }
            if($has_notifications){
                $xoopsTpl->assign('gtd_deptNotifications', $deptNotification);
            } else {
                $xoopsTpl->assign('gtd_deptNotifications', 0);
            }
                    
            $hReview  =& gtdGetHandler('staffReview');
            $hMembers =& xoops_gethandler('member');
            $crit = new Criteria('staffid', $xoopsUser->getVar('uid'));
            $crit->setSort('id');
            $crit->setOrder('DESC');
            $crit->setLimit(5);
            
            $reviews =& $hReview->getObjects($crit);
            
            $displayName =& $xoopsModuleConfig['gtd_displayName'];    // Determines if username or real name is displayed
            
            foreach ($reviews as $review) {
                $reviewer = $hMembers->getUser($review->getVar('submittedBy'));
                $xoopsTpl->append('gtd_reviews', array('rating' => $review->getVar('rating'), 
                            'ratingdsc' => gtdGetRating($review->getVar('rating')),
                            'submittedBy' => ($reviewer ? gtdGetUsername($reviewer, $displayName) : $xoopsConfig['anonymous']),
                            'submittedByUID' => $review->getVar('submittedBy'),
                            'responseid' => $review->getVar('responseid'),
                            'comments' => $review->getVar('comments'),
                            'ticketid' => $review->getVar('ticketid')));
            }
            $xoopsTpl->assign('gtd_hasReviews', (count($reviews) > 0));
            
            // Ticket Lists
            $ticketLists =& $hTicketList->getListsByUser($xoopsUser->getVar('uid'));
            $aMySavedSearches = array();
            $mySavedSearches = gtdGetGlobalSavedSearches();
            $has_savedSearches = count($aMySavedSearches > 0);
            $ticketListCount = count($ticketLists);
            $aTicketLists = array();
            $aUsedSearches = array();
            $eleNum = 0;
            foreach($ticketLists as $ticketList){
                $weight = $ticketList->getVar('weight');
                $searchid = $ticketList->getVar('searchid');
                $aTicketLists[$ticketList->getVar('id')] = array('id' => $ticketList->getVar('id'),
                                                                 'uid' => $ticketList->getVar('uid'),
                                                                 'searchid' => $searchid,
                                                                 'weight' => $weight,
                                                                 'name' => $mySavedSearches[$ticketList->getVar('searchid')]['name'],
                                                                 'hasWeightUp' => (($eleNum != $ticketListCount - 1) ? true : false),
                                                                 'hasWeightDown' => (($eleNum != 0) ? true : false),
                                                                 'hasEdit' => (($mySavedSearches[$ticketList->getVar('searchid')]['uid'] != -999) ? true : false));
                $eleNum++;
                $aUsedSearches[$searchid] = $searchid;
            }
            unset($ticketLists);
            
            // Take used searches to get unused searches
            $aSearches = array();
            foreach($mySavedSearches as $savedSearch){
                if(!in_array($savedSearch['id'], $aUsedSearches)){
                    if($savedSearch['id'] != ""){
                        $aSearches[$savedSearch['id']] = $savedSearch;
                    }
                }
            }
            $hasUnusedSearches = count($aSearches) > 0;
            $xoopsTpl->assign('gtd_ticketLists', $aTicketLists);
            $xoopsTpl->assign('gtd_hasTicketLists', count($aTicketLists) > 0);
            $xoopsTpl->assign('gtd_unusedSearches', $aSearches);
            $xoopsTpl->assign('gtd_hasUnusedSearches', $hasUnusedSearches);
            $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
        break;
    }
} else {
    redirect_header(XOOPS_URL .'/user.php', 3);
}

require(XOOPS_ROOT_PATH.'/footer.php');

?>