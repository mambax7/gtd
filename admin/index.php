<?php   
//$Id: index.php,v 1.142 2005/05/09 20:23:21 eric_juden Exp $
include('../../../include/cp_header.php');          
include_once('admin_header.php');           
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');
define('MAX_STAFF_RESPONSETIME', 5);
define('MAX_STAFF_CALLSCLOSED', 5);

global $HTTP_GET_VARS, $xoopsModule;
$module_id = $xoopsModule->getVar('mid');

$op = 'default';

if ( isset( $_REQUEST['op'] ) )
{
    $op = $_REQUEST['op'];
}

switch ( $op )
{
    case "about":
        about();
        break;
        
    case "mailEvents":
        mailEvents();
        break;
          
    case "searchMailEvents":
        searchMailEvents();
        break;

    case "blocks":
        require 'myblocksadmin.php';
        break;
        
	case "createdir":
	    createdir();
    	break;
    
    case "setperm":
        setperm();
        break;
    
    case "manageFields":
        manageFields();
        break;
               
    default:
        gtd_default();
        break;
}

function modifyTicketFields()
{
    //xoops_cp_header();
        //echo "not created yet";
    xoops_cp_footer();
}

function displayEvents($mailEvents, $mailboxes)
{
    echo "<table width='100%' cellspacing='1' class='outer'>";
    if(count($mailEvents) > 0){
        echo "<tr><th colspan='4'>"._AM_GTD_TEXT_MAIL_EVENTS."</th></tr>";
        echo "<tr class='head'><td>"._AM_GTD_TEXT_MAILBOX."</td>
                              <td>"._AM_GTD_TEXT_EVENT_CLASS."</td>
                              <td>"._AM_GTD_TEXT_DESCRIPTION."</td>
                              <td>"._AM_GTD_TEXT_TIME."</td>
             </tr>";
        
        $class = 'odd';
        foreach($mailEvents as $event){
            echo "<tr class='". $class ."'><td>".$mailboxes[$event->getVar('mbox_id')]->getVar('emailaddress')."</td>
                      <td>".gtdGetEventClass($event->getVar('event_class'))."</td>
                      <td>".$event->getVar('event_desc')."</td>
                      <td>".$event->posted()."</td>
                  </tr>";
            $class = ($class == 'odd') ? 'even' : 'odd';
        }
        
    } else {
        echo "<tr><th>"._AM_GTD_TEXT_MAIL_EVENTS."</th></tr>";
        echo "<tr><td class='odd'>"._AM_GTD_NO_EVENTS."</td></tr>";
    }
    echo "</table><br />";
    echo "<a href='index.php?op=searchMailEvents'>"._AM_GTD_SEARCH_EVENTS."</a>";
}
    

function mailEvents()
{
    global $oAdminButton;
    // Will display the last 50 mail events
    $hMailEvent =& gtdGetHandler('mailEvent');
    $hDeptMbox =& gtdGetHandler('departmentMailBox');
    $mailboxes =& $hDeptMbox->getObjects(null, true);
    
    $crit = new Criteria('', '');
    $crit->setLimit(50);
    $crit->setOrder('DESC');
    $crit->setSort('posted');
    $mailEvents =& $hMailEvent->getObjects($crit);
    
    xoops_cp_header();
    echo $oAdminButton->renderButtons('mailEvents');
    
    displayEvents($mailEvents, $mailboxes);
    
    gtd_adminFooter();
    xoops_cp_footer();
}

function searchMailEvents()
{
    global $oAdminButton;
    xoops_cp_header();
    echo $oAdminButton->renderButtons('mailEvents');
    
    if(!isset($_POST['searchEvents'])){
        
        $stylePath = include_once XOOPS_ROOT_PATH.'/include/calendarjs.php';
        echo '<link rel="stylesheet" type="text/css" media="all" href="'.$stylePath.'" /><!--[if gte IE 5.5000]><script src="iepngfix.js" language="JavaScript" type="text/javascript"></script><![endif]-->';
    
        echo "<form method='post' action='".GTD_ADMIN_URL."/index.php?op=searchMailEvents'>";
        
        echo "<table width='100%' cellspacing='1' class='outer'>";
        echo "<tr><th colspan='2'>"._AM_GTD_SEARCH_EVENTS."</th></tr>";
        echo "<tr><td width='20%' class='head'>"._AM_GTD_TEXT_MAILBOX."</td>
                  <td class='odd'><input type='text' size='55' name='email' class='formButton'></td></tr>";
        echo "<tr><td class='head'>"._AM_GTD_TEXT_DESCRIPTION."</td>
                  <td class='even'><input type='text' size='55' name='description' class='formButton'></td></tr>";
        echo "<tr><td class='head'>"._AM_GTD_SEARCH_BEGINEGINDATE."</td>
                  <td class='odd'><input type='text' name='begin_date' id='begin_date' size='10' maxlength='10' value='".formatTimestamp(time(), 'mysql')."' />
                                  <a href='' onclick='return showCalendar(\"begin_date\");'><img src='".GTD_IMAGE_URL."/calendar.png' alt='Calendar image' name='calendar' style='vertical-align:bottom;border:0;background:transparent' /></a>&nbsp;";
        gtdDrawHourSelect("begin_hour", 12);
        gtdDrawMinuteSelect("begin_minute");
        gtdDrawModeSelect("begin_mode");
        echo "<tr><td class='head'>"._AM_GTD_SEARCH_ENDDATE."</td>
                  <td class='even'><input type='text' name='end_date' id='end_date' size='10' maxlength='10' value='".formatTimestamp(time(), 'mysql')."' />
                                  <a href='' onclick='return showCalendar(\"end_date\");'><img src='".GTD_IMAGE_URL."/calendar.png' alt='Calendar image' name='calendar' style='vertical-align:bottom;border:0;background:transparent' /></a>&nbsp;";
        gtdDrawHourSelect("end_hour", 12);
        gtdDrawMinuteSelect("end_minute");
        gtdDrawModeSelect("end_mode");
        echo "<tr><td class='foot' colspan='2'><input type='submit' name='searchEvents' value='"._AM_GTD_BUTTON_SEARCH."' /></td></tr>";
        echo "</table>";
        echo "</form>";
        
        gtd_adminFooter();
        xoops_cp_footer();
    } else {
        $hMailEvent =& gtdGetHandler('mailEvent');
        $hDeptMbox =& gtdGetHandler('departmentMailBox');
        $mailboxes =& $hDeptMbox->getObjects(null, true);
    
        $begin_date = explode( '-', $_POST['begin_date']);
        $end_date = explode('-', $_POST['end_date']);
        $begin_hour = gtdChangeHour($_POST['begin_mode'], $_POST['begin_hour']);
        $end_hour = gtdChangeHour($_POST['end_mode'], $_POST['end_hour']);
        
        // Get timestamps to search by
        $begin_time = mktime($begin_hour, $_POST['begin_minute'], 0, $begin_date[1], $begin_date[2], $begin_date[0]);
        $end_time = mktime($end_hour, $_POST['end_minute'], 0, $end_date[1], $end_date[2], $end_date[0]);
        
        $crit = new CriteriaCompo(new Criteria('posted', $begin_time, '>='));
        $crit->add(new Criteria('posted', $end_time, '<='));
        if($_POST['email'] != ''){
            $email = $_POST['email'];
            $crit->add(new Criteria('emailaddress', "%$email%", "LIKE", "d"));
        }
        if($_POST['description'] != ''){
            $description = $_POST['description'];
            $crit->add(new Criteria('event_desc', "%$description%", "LIKE"));
        }
        $crit->setOrder('DESC');
        $crit->setSort('posted');
        if(isset($email)){
            $mailEvents =& $hMailEvent->getObjectsJoin($crit);
        } else {
            $mailEvents =& $hMailEvent->getObjects($crit);
        }
        
        displayEvents($mailEvents, $mailboxes);
    
        gtd_adminFooter();
        xoops_cp_footer();
    }
}

/**
 * changes hour to am/pm
 *
 * @param int $mode, 1-am, 2-pm
 * @param int $hour hour of the day
 *
 * @return hour in 24 hour mode
 */
function gtdChangeHour($mode, $hour)
{
    $mode = intval($mode);
    $hour = intval($hour);
    
    if($mode == 2){
        $hour = $hour + 12;
        return $hour;
    }
    return $hour;
}

function gtdDrawHourSelect($name, $lSelect="-1")
{
    echo "<select name='".$name."'>";
    for($i = 1; $i <= 12; $i++){
        if($lSelect == $i){
            $selected = "selected='selected'";
        } else {
            $selected = '';
        }
        echo "<option value='".$i."'".$selected.">".$i."</option>";
    }
    echo "</select>";
}

function gtdDrawMinuteSelect($name)
{
    $lSum = 0;
    
    echo "<select name='".$name."'>";
    for($i = 0; $lSum <= 50; $i++){
        if($i == 0){
            echo "<option value='00' selected='selected'>00</option>";
        } else {
            $lSum = $lSum + 5;
            echo "<option value='".$lSum."'>".$lSum."</option>";
        }
    }
    echo "</select>";
}
            
function gtdDrawModeSelect($name, $sSelect='AM')
{
    echo "<select name='".$name."'>";
    if($sSelect == 'AM'){
        echo "<option value='1' selected='selected'>AM</option>";
        echo "<option value='2'>PM</option>";
    } else {
        echo "<option value='1'>AM</option>";
        echo "<option value='2' selected='selected'>PM</option>";
    }
}

function gtd_default()
{
    global $xoopsModuleConfig, $oAdminButton;
    xoops_cp_header();
    echo $oAdminButton->renderButtons('index');
    $displayName =& $xoopsModuleConfig['gtd_displayName'];    // Determines if username or real name is displayed
    
    $stylePath = GTD_BASE_URL.'/styles/gtd.css';
    echo '<link rel="stylesheet" type="text/css" media="all" href="'.$stylePath.'" /><!--[if gte IE 5.5000]><script src="iepngfix.js" language="JavaScript" type="text/javascript"></script><![endif]-->';

    global $xoopsUser, $xoopsDB;
    $hTickets =& gtdGetHandler('ticket');
    $hStatus =& gtdGetHandler('status');
    
    $crit = new Criteria('', '');
    $crit->setSort('description');
    $crit->setOrder('ASC');
    $statuses =& $hStatus->getObjects($crit);
    $table_class = array('odd', 'even');
    echo "<table border='0' width='100%'>";
    echo "<tr><td width='50%' valign='top'>";
    echo "<div id='ticketInfo'>";
    echo "<table border='0' style='width: 100%;' cellspacing='8' class='outer'>
          <tr><th colspan='2'>". _AM_GTD_TEXT_TICKET_INFO ."</th></tr>";
    $class = "odd";
    $totalTickets = 0;
    foreach($statuses as $status){
        $crit = new Criteria('status', $status->getVar('id'));
        $numTickets =& $hTickets->getCount($crit);
        $totalTickets += $numTickets;
        

        echo "<tr class='".$class."'><td>".$status->getVar('description')."</td><td>".$numTickets."</td></tr>";
        if($class == "odd"){
            $class = "even";
        } else {
            $class = "odd";
        }
    }
    echo "<tr class='foot'><td>"._AM_GTD_TEXT_TOTAL_TICKETS."</td><td>".$totalTickets."</td></tr>";
    echo "</table></div><br />";
    
//Introduction
    echo "<td valign='top'>";

    echo "<table border='1' style='width: 100%;' cellspacing='1' class='outer'>
          <tr><th colspan='2'>Introduction &agrave; My Inscription</th></tr>
	  <td>My Inscription est un systeme de gestion d'inscription pour les Associations de danse.</br>
Con&ccedil;u pour g&eacute;rer 3 types de cours. La Salsa, le Rock et les stages. My Inscription propose &eacute;galement un systeme de r&eacute;duction par montant ou par %.</br>
<u><b>Fonctionnement global</b></u></br>
1 - Cr&eacute;ez une Section</br>
La section est Type, une ann&eacute;e ....</br>
Exemple : Cours 2010/2011</br>
Exemple : Stages</br>
2 - Affiliez un professeur &agrave; une section</br>
3 - Ajoutez des cours &agrave; cette section</br>
</br>
Pour plus d'informations une notice d&eacute;taill&eacute; est <a href='notice.txt'>en t&eacute;l&eacute;chargement.</a></td>";
    echo "</table><br />";
    echo "<td valign='top'>";
   
   //Temps de réponses aux inscriptions 
    
    /* $hStaff =& gtdGetHandler('staff');
    $hResponses =& gtdGetHandler('responses');
    echo "</td><td valign='top'>";    // Outer table
    echo "<div id='timeSpent'>";    // Start inner top-left cell
    echo "<table border='0' width='100%' cellspacing='1' class='outer'>
          <tr><th colspan='2'>". _AM_GTD_TEXT_RESPONSE_TIME ."</th></tr>";
          
    $sql = sprintf('SELECT u.uid, u.uname, u.name, (s.responseTime / s.ticketsResponded) as AvgResponseTime FROM %s u INNER JOIN %s s ON u.uid = s.uid WHERE ticketsResponded > 0 ORDER BY AvgResponseTime', $xoopsDB->prefix('users'), $xoopsDB->prefix('gtd_staff'));
    $ret = $xoopsDB->query($sql, MAX_STAFF_RESPONSETIME);
    $i = 0;
    while (list($uid, $uname, $name, $avgResponseTime) = $xoopsDB->fetchRow($ret)) {
        $class = $table_class[$i % 2];
        echo "<tr class='$class'><td>". gtdCheckDisplayName($displayName, $name,$uname) ."</td><td align='right'>". gtdFormatTime($avgResponseTime) ."</td></tr>";
        $i++;
    }
    echo "</table></div><br />"; // End inner top-left cell
    echo "</td></tr><tr><td valign='top'>"; // End first, start second cell */
    
    //Get Calls Closed block
    $sql = sprintf('SELECT SUM(callsClosed) FROM %s', $xoopsDB->prefix('gtd_staff'));
    $ret = $xoopsDB->query($sql);
    if (list($totalStaffClosed) = $xoopsDB->fetchRow($ret)) {
        if ($totalStaffClosed) {
            $sql = sprintf('SELECT u.uid, u.uname, u.name, s.callsClosed FROM %s u INNER JOIN %s s ON u.uid = s.uid WHERE s.callsClosed > 0 ORDER BY s.callsClosed DESC', $xoopsDB->prefix('users'), $xoopsDB->prefix('gtd_staff'));
            $ret = $xoopsDB->query($sql, MAX_STAFF_CALLSCLOSED);
            echo "<div id='callsClosed'>";
            echo "<table border='0' width='95%' cellspacing='1' class='outer'>
                  <tr><th colspan='2'>". _AM_GTD_TEXT_TOP_CLOSERS ."</th></tr>";
            $i = 0;
            while (list($uid, $uname, $name, $callsClosed) = $xoopsDB->fetchRow($ret)) {
                $class = $table_class[$i % 2];
                echo "<tr class='$class'><td>". gtdCheckDisplayName($displayName, $name,$uname) ."</td><td align='right'>". $callsClosed. ' ('.round(($callsClosed/$totalStaffClosed)*100, 2) ."%)</td></tr>";
                $i++;
            }
            echo "</table></div><br />"; // End inner table top row
            echo "</td><td valign='top'>"; // End top row of outer table
            
            $sql = sprintf('SELECT u.uid, u.uname, u.name, (s.responseTime / s.ticketsResponded) as AvgResponseTime FROM %s u INNER JOIN %s s ON u.uid = s.uid WHERE ticketsResponded > 0 ORDER BY AvgResponseTime DESC', $xoopsDB->prefix('users'), $xoopsDB->prefix('gtd_staff'));
            $ret = $xoopsDB->query($sql, MAX_STAFF_RESPONSETIME);
            echo "<div id='leastCallsClosed'>";
            echo "<table border='0' width='100%' cellspacing='1' class='outer'> 
                  <tr><th colspan='2'>". _AM_GTD_TEXT_RESPONSE_TIME_SLOW ."</th></tr>";
            $i = 0;
            while (list($uid, $uname, $name, $avgResponseTime) = $xoopsDB->fetchRow($ret)) {
                $class = $table_class[$i % 2];
                echo "<tr class='$class'><td>". gtdCheckDisplayName($displayName, $name,$uname) ."</td><td align='right'>". gtdFormatTime($avgResponseTime) ."</td></tr>";
                $i++;
            }
            echo "</table></div>";  // End first cell, second row of inner table
        }
    }
    echo "</td></tr></table><br />";   // End second cell, second row of inner table
    
    $crit = new Criteria('state', '2', '<>', 's');
    $crit->setSort('genre');
    $crit->setOrder('ASC');
    $crit->setLimit(10);
    $highPriority =& $hTickets->getObjects($crit);
    $has_highPriority = (count($highPriority) > 0);
    if($has_highPriority){
        echo "<div id='highPriority'>";
        echo "<table border='0' width='100%' cellspacing='1' class='outer'>
              <tr><th colspan='8'>". _AM_GTD_TEXT_HIGH_GENRE ."</th></tr>";
        echo "<tr class='head'><td>". _AM_GTD_TEXT_GENRE ."</td><td>". _AM_GTD_TEXT_ELAPSED ."</td><td>". _AM_GTD_TEXT_STATUS ."</td><td>". _AM_GTD_TEXT_DEPARTMENT ."</td><td>". _AM_GTD_TEXT_OWNER ."</td><td>". _AM_GTD_TEXT_LAST_UPDATED ."</td><td>". _AM_GTD_TEXT_LOGGED_BY ."</td></tr>";
        $i = 0;
        foreach($highPriority as $ticket){
               if($ticket->isOverdue()){
                   $class = $table_class[$i % 2] . " overdue";
               } else {
                   $class = $table_class[$i % 2];
                }
               $genre_url = "<img src='".GTD_IMAGE_URL."/genre". $ticket->getVar('genre') .".png' alt='". $ticket->getVar('genre') ."' />";
               if($dept = $ticket->getDepartment()){
                   $dept_url = sprintf("<a href='".GTD_BASE_URL."/index.php?op=staffViewAll&amp;dept=". $dept->getVar('id') ."' target='_BLANK'>%s</a>", $dept->getVar('department'));
               } else {
                   $dept_url = _AM_GTD_TEXT_NO_DEPT;
               }
               if($ticket->getVar('ownership') <> 0){
                   $owner_url = sprintf("<a href='".XOOPS_URL."/userinfo.php?uid=". $ticket->getVar('uid') ."' target='_BLANK'>%s</a>", gtdGetUsername($ticket->getVar('ownership'), $displayName));
               } else {
                   $owner_url = _AM_GTD_TEXT_NO_OWNER;
               }
               $user_url = sprintf("<a href='".XOOPS_URL."/userinfo.php?uid=". $ticket->getVar('uid') ."' target='_BLANK'>%s</a>", gtdGetUsername($ticket->getVar('uid'), $displayName));
               echo "<tr class='$class'><td>". $genre_url ."</td>
                         <td>". $ticket->elapsed() ."</td>
                         <td>". gtdGetStatus($ticket->getVar('status')) ."</td>
                         <td>". $dept_url ."</td>
                         <td>". $owner_url ." </td>
                         <td>". $ticket->lastUpdated() ."</td>
                         <td>". $user_url ."</td>
                     </tr>";
              $i++;
        }
        echo "</table></div>";
    }
    
    pathConfiguration();
    
    gtd_adminFooter();
    xoops_cp_footer();
}

function pathConfiguration()
{
	global $xoopsModule, $xoopsConfig;
	
	// Upload and Images Folders

    $paths = array();
    $paths[_AM_GTD_PATH_TICKETATTACH] = GTD_UPLOAD_PATH;
    $paths[_AM_GTD_PATH_EMAILTPL] = GTD_BASE_PATH."/language/{$xoopsConfig['language']}";
	
	echo "<h3>"._AM_GTD_PATH_CONFIG."</h3>";
	echo "<table width='100%' class='outer' cellspacing='1' cellpadding='3' border='0' ><tr>";
	echo "<td class='bg3'><b>" . _AM_GTD_TEXT_DESCRIPTION . "</b></td>";
	echo "<td class='bg3'><b>" . _AM_GTD_TEXT_PATH . "</b></td>";
	echo "<td class='bg3' align='center'><b>" . _AM_GTD_TEXT_STATUS . "</b></td></tr>";

    foreach($paths as $desc=>$path) {
        echo "<tr><td class='odd'>$desc</td>";
	    echo "<td class='odd'>$path</td>";
	    echo "<td class='even' style='text-align: center;'>" . gtd_admin_getPathStatus($path) . "</td></tr>";
	}
	
	echo "</table>";
	echo "<br />";
	
	echo "</div>";
}

function about()
{
    global $oAdminButton;
    xoops_cp_header();
    echo $oAdminButton->renderButtons();
    require_once(GTD_ADMIN_PATH."/about.php");
}

function createdir()
{
    $path = $_GET['path'];
	$res = gtd_admin_mkdir($path);
	
	$msg = ($res)?_AM_GTD_PATH_CREATED:_AM_GTD_PATH_NOTCREATED;
	redirect_header(GTD_ADMIN_URL.'/index.php', 2, $msg . ': ' . $path);
	exit();
}

function setperm()
{
    $path = $_GET['path'];
    $res = gtd_admin_chmod($path, 0777);
    $msg = ($res ? _AM_GTD_PATH_PERMSET : _AM_GTD_PATH_NOTPERMSET);
    redirect_header(GTD_ADMIN_URL.'/index.php', 2, $msg . ': ' . $path);
    exit();
}
?>