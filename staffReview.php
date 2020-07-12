<?php
//$Id: staffReview.php,v 1.14 2005/10/18 19:27:30 eric_juden Exp $
require_once('header.php');
require_once(GTD_CLASS_PATH.'/logService.php');
require_once(GTD_CLASS_PATH.'/staffService.php');

$_eventsrv->advise('new_response_rating', gtd_logService::singleton());
$_eventsrv->advise('new_response_rating', gtd_staffService::singleton());

if($xoopsUser){
    if(isset($_POST['submit'])){
        if(isset($_POST['staffid'])){
            $staffid = intval($_POST['staffid']);
        }
        if(isset($_POST['ticketid'])){
            $ticketid = intval($_POST['ticketid']);
        }
        if(isset($_POST['responseid'])){
            $responseid = intval($_POST['responseid']);
        }
        if(isset($_POST['rating'])){
            $rating = intval($_POST['rating']);   
        }
        if(isset($_POST['comments'])){
            $comments = $_POST['comments'];
        }
        $hStaffReview =& gtdGetHandler('staffReview');
        $review =& $hStaffReview->create();
        $review->setVar('staffid', $staffid);
        $review->setVar('rating', $rating);
        $review->setVar('ticketid', $ticketid);
        $review->setVar('responseid', $responseid);
        $review->setVar('comments', $comments);
        $review->setVar('submittedBy', $xoopsUser->getVar('uid'));
        $review->setVar('userIP', getenv("REMOTE_ADDR"));
        if($hStaffReview->insert($review)){
            $message = _GTD_MESSAGE_ADD_STAFFREVIEW;
            $_eventsrv->trigger('new_response_rating', array(&$review));
        } else {
            $message = _GTD_MESSAGE_ADD_STAFFREVIEW_ERROR;
        }
        redirect_header(GTD_BASE_URL."/ticket.php?id=$ticketid", 3, $message);
    } else {
        $xoopsOption['template_main'] = 'gtd_staffReview.html';   // Set template
        require(XOOPS_ROOT_PATH.'/header.php');                     // Include
        
        if(isset($_GET['staff'])){
            $xoopsTpl->assign('gtd_staffid', intval($_GET['staff']));
        }
        if(isset($_GET['ticketid'])){
            $xoopsTpl->assign('gtd_ticketid', intval($_GET['ticketid']));
        }
        if(isset($_GET['responseid'])){
            $xoopsTpl->assign('gtd_responseid', intval($_GET['responseid']));
        }
        
        $xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
        $xoopsTpl->assign('xoops_module_header', $gtd_module_header);
        $xoopsTpl->assign('gtd_baseURL', GTD_BASE_URL);
        
        require(XOOPS_ROOT_PATH.'/footer.php');
    }
} else {    // If not a user
    redirect_header(XOOPS_URL .'/user.php', 3);
}
?>