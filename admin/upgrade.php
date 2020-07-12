<?php
//$Id: upgrade.php,v 1.26.2.3 2005/11/28 16:54:57 eric_juden Exp $
include('../../../include/cp_header.php');          
include_once('admin_header.php');    
include_once(XOOPS_ROOT_PATH . '/class/pagenav.php');

global $xoopsModule;
$module_id = $xoopsModule->getVar('mid');

$op = 'default';

if ( isset( $_REQUEST['op'] ) )
{
    $op = $_REQUEST['op'];
}

switch ( $op )
{
    case "checkTables":       
        checkTables();
        break;
    
    case "upgradeDB":   
        upgradeDB();
        break;
    
    default:
        header("Location: ".GTD_BASE_URL."/admin/index.php");
        break;
}

function _renameTable($oldName, $newName)
{
    global $xoopsDB;
    $qry = _runQuery(sprintf("ALTER table %s RENAME %s", $xoopsDB->prefix($oldName), $xoopsDB->prefix($newName)),
                     sprintf(_AM_GTD_MSG_RENAME_TABLE, $oldName, $newName),
                     sprintf(_AM_GTD_MSG_RENAME_TABLE_ERR, $oldName));   
                            
    return $qry;
}

function _runQuery($query, $goodmsg, $badmsg)
{
    global $xoopsDB;
    $ret = $xoopsDB->query($query);
    if (! $ret) {
        echo "<li class='err'>$badmsg</li>";
        return false;
    } else {
        echo "<li class='ok'>$goodmsg</li>";
        return true;
    }   
}

function checkTables()
{
    global $xoopsModule, $oAdminButton;
    xoops_cp_header();
    echo $oAdminButton->renderButtons('');
    //1. Determine previous release
    if (!gtdTableExists('gtd_meta')) {
        $ver = '0.5';
    } else {
        if (!$ver = gtdGetMeta('version')) {
            echo('Unable to determine previous version.');
        }
    }
    
    $currentVer = round($xoopsModule->getVar('version') / 100, 2);

    printf('<h2>'._AM_GTD_CURRENTVER.'</h2>', $currentVer);
    printf('<h2>'._AM_GTD_DBVER.'</h2>', $ver);

    
    if ($ver == $currentVer) {
        //No updates are necessary
        echo '<div>'._AM_GTD_DB_NOUPDATE.'</div>';
        
    } elseif ( $ver < $currentVer) {
        //Needs to upgrade
        echo '<div>'._AM_GTD_DB_NEEDUPDATE.'</div>';
        echo "<form method=\"post\" action=\"upgrade.php\"><input type=\"hidden\" name=\"op\" value=\"upgradeDB\" /><input type=\"submit\" value=\"". _AM_GTD_UPDATE_NOW . "\" onclick='javascript:_openProgressWindow();' /></form>";
    } else {
        //Tried to downgrade
        echo '<div>'._AM_GTD_DB_NEEDINSTALL.'</div>';
    }
    
    gtd_adminFooter();
    xoops_cp_footer();
}

echo "<script type='text/javascript'>
function _openProgressWindow()
{
    newwindow = openWithSelfMain('upgradeProgress.php','progress','430','100', true);
}
    </script>";

function upgradeDB()
{    
    global $xoopsModule, $oAdminButton;
    $xoopsDB =& Database::getInstance();
    //1. Determine previous release
    //   *** Update this in sql/mysql.sql for each release **
    if (!gtdTableExists('gtd_meta')) {
        $ver = '0.5';
    } else {
        if (!$ver = gtdGetMeta('version')) {
            exit(_AM_GTD_VERSION_ERR);
        }
    }
    
    $hStaff =& gtdGetHandler('staff');
    $hMember =& gtdGetHandler('membership');
    $hTicket =& gtdGetHandler('ticket');
    $hXoopsMember =& xoops_gethandler('member');
    $hRole =& gtdGetHandler('role');
    
    $mid = $xoopsModule->getVar('mid');
    
    xoops_cp_header();
    echo $oAdminButton->renderButtons('');
    echo "<h2>"._AM_GTD_UPDATE_DB."</h2>";
    $ret = true;
    //2. Do All Upgrades necessary to make current
    //   Break statements are omitted on purpose
    switch($ver) {      
    case '0.5':
        set_time_limit(60);
        printf("<h3>". _AM_GTD_UPDATE_TO."</h3>", '0.6' );
        echo "<ul>";
        //Create meta table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (metakey varchar(50) NOT NULL default '', metavalue varchar(255) NOT NULL default '', PRIMARY KEY (metakey)) TYPE=MyISAM;", $xoopsDB->prefix('gtd_meta')), sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_meta'), sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_meta'));
        
        //Insert Current Version into table
        $qry = $xoopsDB->query(sprintf("INSERT INTO %s values('version', %s)", $xoopsDB->prefix('gtd_meta'), $xoopsDB->quoteString($ver)));
                
        //Update gtd_responses table
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD private INT(11) NOT NULL DEFAULT '0'", $xoopsDB->prefix('gtd_responses')), sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_responses'), sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_responses')) ;
        
        //Retrieve uid's of all staff members
        $qry = $xoopsDB->query('SELECT uid FROM '. $xoopsDB->prefix('gtd_staff'). ' ORDER BY uid');
        
        //Get email addresses in user profile
        $staff = array();
        while ($arr = $xoopsDB->fetchArray($qry)) {
            $staff[$arr['uid']] = '';
        }
        $xoopsDB->freeRecordSet($qry);
        
        $query = 'SELECT uid, email FROM '. $xoopsDB->prefix('users') . ' WHERE uid IN ('. implode(array_keys($staff), ',') .')';
        $qry = $xoopsDB->query($query);
        while($arr = $xoopsDB->fetchArray($qry)) {
            $staff[$arr['uid']] = $arr['email'];
        }
        $xoopsDB->freeRecordSet($qry);
        
        //Update gtd_staff table
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD email varchar(255) NOT NULL default '' AFTER uid, ADD notify int(11) NOT NULL default '0'", $xoopsDB->prefix('gtd_staff')), sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_staff'), sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_staff')) ;
        
        //Update existing staff records
        $staff_tbl = $xoopsDB->prefix('gtd_staff');
        $notif_tbl = $xoopsDB->prefix('xoopsnotifications');
        $email_tpl = $xoopsModule->getInfo('_email_tpl');
        foreach ($staff as $uid=>$email) {
            
            //get notifications for current user
            $usernotif = 0;
            $qry = $xoopsDB->query(sprintf("SELECT DISTINCT not_category, not_event FROM %s WHERE not_uid = %u AND not_category='dept' AND not_modid=%u", $notif_tbl, $uid, $mid));
            while($arr = $xoopsDB->fetchArray($qry)) {
                //Look for current event information in $email_tpl
                foreach($email_tpl as $tpl) {
                    if (($tpl['name'] == $arr['not_event']) && ($tpl['category'] == $arr['not_category'])) {
                        $usernotif = $usernotif | pow(2, $tpl['bit_value']);
                        break;
                    }
                }
            }
            
            //Update gtd_staff with user notifications & email address
            $ret = $ret && _runQuery(sprintf("UPDATE %s SET email = %s, notify = %u WHERE uid = %u", $staff_tbl, $xoopsDB->quoteString($email), $usernotif, $uid), sprintf(_AM_GTD_MSG_UPDATESTAFF, $uid), sprintf(_AM_GTD_MSG_UPDATESTAFF_ERR, $uid));
        }
        echo "</ul>";
    case '0.6':   
        set_time_limit(60);     
        //Do DB updates to make 0.7
        printf("<h3>". _AM_GTD_UPDATE_TO."</h3>", '0.7' );
        
        echo "<ul>";
        // change table names to lowercase
        $ret = $ret && _renameTable('gtd_logMessages', 'gtd_logmessages');
        $ret = $ret && _renameTable('gtd_responseTemplates', 'gtd_responsetemplates');
        $ret = $ret && _renameTable('gtd_jStaffDept', 'gtd_jstaffdept');
        $ret = $ret && _renameTable('gtd_staffReview', 'gtd_staffreview');
        $ret = $ret && _renameTable('gtd_emailTpl', 'gtd_emailtpl');
        
        // Remove unused table - gtd_emailtpl
        $ret = $ret && _runQuery(sprintf("DROP TABLE %s", $xoopsDB->prefix('gtd_emailtpl')), 
                                sprintf(_AM_GTD_MSG_REMOVE_TABLE, 'gtd_emailtpl'),
                                sprintf(_AM_GTD_MSG_NOT_REMOVE_TABLE, 'gtd_emailtpl'));
                                
        // gtd_staff table - permTimestamp
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD permTimestamp INT(11) NOT NULL DEFAULT '0'", $xoopsDB->prefix('gtd_staff')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_staff'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_staff')) ;
        
        //Update gtd_tickets table
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s MODIFY subject VARCHAR(100) NOT NULL default ''", $xoopsDB->prefix('gtd_tickets')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_tickets'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_tickets')) ;

        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD (serverid INT(11) DEFAULT NULL,
                                                             emailHash VARCHAR(100) DEFAULT NULL,
                                                             email VARCHAR(100) DEFAULT NULL,
                                                             overdueTime INT(11) NOT NULL DEFAULT '0',
                                                             KEY emailHash (emailHash))", $xoopsDB->prefix('gtd_tickets')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_tickets'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_tickets')) ;
        
        // Create gtd_department_mailbox table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                          departmentid int(11) default NULL,
                                                          emailaddress varchar(255) default NULL,
                                                          server varchar(50) default NULL,
                                                          serverport int(11) default NULL,
                                                          username varchar(50) default NULL,
                                                          password varchar(50) default NULL,
                                                          genre tinyint(4) default NULL,
                                                          mboxtype int(11) NOT NULL default 1,
                                                          PRIMARY KEY  (id),
                                                          UNIQUE KEY id (id),
                                                          KEY departmentid (departmentid),
                                                          KEY emailaddress (emailaddress),
                                                          KEY mboxtype (mboxtype)
                                                         )TYPE=MyISAM;", $xoopsDB->prefix('gtd_department_mailbox')),
                                sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_department_mailbox'),
                                sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_department_mailbox'));
                                
        // Create gtd_mailevent table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                           mbox_id int(11) NOT NULL default '0',
                                                           event_desc text,
                                                           event_class int(11) NOT NULL default '0',
                                                           posted int(11) NOT NULL default '0',
                                                           PRIMARY KEY(id)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_mailevent')),
                                sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_mailevent'),
                                sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_mailevent'));
        
        // Create gtd_roles table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                          name varchar(35) NOT NULL default '',
                                                          description mediumtext,
                                                          tasks int(11) NOT NULL default '0',
                                                          PRIMARY KEY(id)
                                                         )TYPE=MyISAM;", $xoopsDB->prefix('gtd_roles')),
                                sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_roles'),
                                sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_roles'));
        
        // Create gtd_staffroles table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (uid int(11) NOT NULL default '0',
                                                         roleid int(11) NOT NULL default '0',
                                                         deptid int(11) NOT NULL default '0',
                                                         PRIMARY KEY(uid, roleid, deptid)
                                                        )TYPE=MyISAM;", $xoopsDB->prefix('gtd_staffroles')),
                                sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_staffroles'),
                                sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_staffroles'));
        
        // Add default roles to db
        if(!$hasRoles = gtdCreateRoles()){
            echo "<li>"._AM_GTD_MESSAGE_DEF_ROLES_ERROR."</li>";
        } else {
            echo "<li>"._AM_GTD_MESSAGE_DEF_ROLES."</li>";
        }
        
        // Set all staff members to have admin permission role
        if($staff =& $hStaff->getObjects()){
            foreach($staff as $stf){
                $uid = $stf->getVar('uid');
                $depts = $hMember->membershipByStaff($uid, true);
                if($hStaff->addStaffRole($uid, 1, 0)){
                    echo "<li>".sprintf(_AM_GTD_MSG_GLOBAL_PERMS, $uid)."</li>";
                }
                
                foreach($depts as $dept){
                    $deptid = $dept->getVar('id');
                    if($hStaff->addStaffRole($uid, 1, $deptid)){    // Departmental permissions
                        echo "<li>".sprintf(_AM_GTD_MSG_UPD_PERMS, $uid, $dept->getVar('department'))."</li>";
                    }
                }
                
                
                $stf->setVar('permTimestamp', time());        // Set initial value for permTimestamp field
                if(!$hStaff->insert($stf)){
                    echo "<li>".sprintf(_AM_GTD_MSG_UPDATESTAFF_ERR, $uid)."</li>";
                } else {
                    echo "<li>".sprintf(_AM_GTD_MSG_UPDATESTAFF, $uid)."</li>";
                }
            }
        }
        echo "</ul>";
    
    case '0.7':
        set_time_limit(60);
        //Do DB updates to make 0.71
        printf("<h3>". _AM_GTD_UPDATE_TO."</h3>", '0.71' );
        
        echo "<ul>";
        echo "</ul>";
        
    case '0.71':
        set_time_limit(60);
        //Do DB updates to make 0.75
        printf("<h3>". _AM_GTD_UPDATE_TO."</h3>", '0.75' );
        
        echo "<ul>";
        
        //Changes for php5 compabibility
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s MODIFY lastUpdated int(11) NOT NULL default '0'", $xoopsDB->prefix('gtd_logmessages')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_logmessages'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_logmessages'));      
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s MODIFY department int(11) NOT NULL default '0'", $xoopsDB->prefix('gtd_jstaffdept')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_jstaffdept'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_jstaffdept')) ;
        
        // Create table for email template information
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (notif_id int(11) NOT NULL default '0',
                                                           staff_setting int(11) NOT NULL default '0',
                                                           user_setting int(11) NOT NULL default '0',
                                                           staff_options mediumtext NOT NULL,
                                                           PRIMARY KEY (notif_id)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_notifications')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_notifications'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_notifications'));
        
        // Add gtd_status table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                           state int(11) NOT NULL default '0',
                                                           description varchar(50) NOT NULL default '',
                                                           PRIMARY KEY(id),
                                                           KEY state (state)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_status')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_status'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_status'));
        
        // Give default statuses for upgrade
        $hStatus =& gtdGetHandler('status');
        $startStatuses = array(_GTD_STATUS0 => 1, _GTD_STATUS1 => 1, _GTD_STATUS2 => 2);
        
        $count = 1;
        set_time_limit(60);
        foreach($startStatuses as $desc=>$state){
            $newStatus =& $hStatus->create();
            $newStatus->setVar('id', $count);
            $newStatus->setVar('description', $desc);
            $newStatus->setVar('state', $state);
            if(!$hStatus->insert($newStatus)){
                echo "<li>".sprintf(_AM_GTD_MSG_ADD_STATUS_ERR, $desc)."</li>";
            } else {
                echo "<li>".sprintf(_AM_GTD_MSG_ADD_STATUS, $desc)."</li>";
            }
            $count++;
        }
        
        // Change old status values to new status values
        $oldStatuses = array(2 => 3, 1 => 2, 0 => 1);
        
        foreach($oldStatuses as $cStatus=>$newStatus){
            $crit = new Criteria('status', $cStatus);
            $success = $hTicket->updateAll('status', $newStatus, $crit);
        }
        if($success){
            echo "<li>"._AM_GTD_MSG_CHANGED_STATUS."</li>";
        } else {
            echo "<li>"._AM_GTD_MSG_CHANGED_STATUS_ERR."</li>";
        }
        
        // Add gtd_ticket_submit_emails table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (ticketid int(11) NOT NULL default '0',
                                                           uid int(11) NOT NULL default '0',
                                                           email varchar(100) NOT NULL default '',
                                                           suppress int(11) NOT NULL default '0',
                                                           PRIMARY KEY(ticketid, email)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_ticket_submit_emails')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_ticket_submit_emails'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_ticket_submit_emails'));
        
  
        // Add records to gtd_ticket_submit_emails for existing tickets
    	 $count = $hTicket->getCount();
    	 $batchsize = 100;
    	 
    	 $crit = new Criteria('', '');
    	 $crit->setLimit($batchsize);
    	 $i = 0;
    
    	 while ($i <= $count) {
    	    set_time_limit(60);
    		$crit->setStart($i);
    		$tickets =& $hTicket->getObjects($crit);
    		
    		$all_users = array();        
    		foreach($tickets as $ticket){
    			$all_users[$ticket->getVar('uid')] = $ticket->getVar('uid');
    		}
    
    		$crit  = new Criteria('uid', "(". implode(array_keys($all_users), ',') .")", 'IN');
    		$users =& $hXoopsMember->getUsers($crit, true);
    
    	    foreach($users as $user){
           	    $all_users[$user->getVar('uid')] = $user->getVar('email');
    	    }
    		unset($users);
    
    		foreach($tickets as $ticket){
    		    set_time_limit(60);
                $ticket_uid = $ticket->getVar('uid');
    			if(array_key_exists($ticket_uid, $all_users)){
    				$ticket_email = $all_users[$ticket_uid];
    				$success = $ticket->addSubmitter($ticket_email, $ticket_uid);
    	        }
            }
    		unset($tickets);
    		//increment
    		$i += $batchsize;
        }
    
        set_time_limit(60);
        // Update gtd_roles Admin record with new value (2047)
        $crit = new Criteria('tasks', 511);
        $admin_roles =& $hRole->getObjects($crit);
        
        foreach($admin_roles as $role){
            $role->setVar('tasks', 2047);
            if($hRole->insert($role)){
                echo "<li>".sprintf(_AM_GTD_MSG_UPDATE_ROLE, $role->getVar('name'))."</li>";
            } else {
                echo "<li>".sprintf(_AM_GTD_MSG_UPDATE_ROLE_ERR, $role->getVar('name'))."</li>";
            }
        }
        
        set_time_limit(60);
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD (active INT(11) NOT NULL DEFAULT 1,
                                                          KEY active (active))", $xoopsDB->prefix('gtd_department_mailbox')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_department_mailbox'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_department_mailbox'));
                                
        // Add gtd_saved_searches table
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                           uid int(11) NOT NULL default '0',
                                                           name varchar(50) NOT NULL default '',
                                                           search mediumtext NOT NULL,
                                                           pagenav_vars mediumtext NOT NULL,
                                                           PRIMARY KEY(id)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_saved_searches')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_saved_searches'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_saved_searches'));
        
        set_time_limit(60);
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (fieldid int(11) NOT NULL default '0',
                                                           deptid int(11) NOT NULL default '0',
                                                           PRIMARY KEY  (fieldid, deptid)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_ticket_field_departments')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_ticket_field_departments'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_ticket_field_departments'));
    
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (ticketid int(11) NOT NULL default '0',
                                                           PRIMARY KEY  (ticketid)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_ticket_values')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_ticket_values'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_ticket_values'));

        set_time_limit(60);                                 
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                           name varchar(64) NOT NULL default '',
                                                           description tinytext NOT NULL,
                                                           fieldname varchar(64) NOT NULL default '',
                                                           controltype int(11) NOT NULL default '0',
                                                           datatype varchar(64) NOT NULL default '',
                                                           required tinyint(1) NOT NULL default '0',
                                                           fieldlength int(11) NOT NULL default '0',
                                                           weight int(11) NOT NULL default '0',
                                                           fieldvalues mediumtext NOT NULL,
                                                           defaultvalue varchar(100) NOT NULL default '',
                                                           validation mediumtext NOT NULL,
                                                           PRIMARY KEY (id),
                                                           KEY weight (weight)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_ticket_fields')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_ticket_fields'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_ticket_fields'));

        set_time_limit(60);            
        // Add notifications to new table
        set_time_limit(60);
        $hasNotifications = gtdCreateNotifications();
        
        // Make all departments visible to all groups
        $hasDeptVisibility = gtdCreateDepartmentVisibility();
        
        // Update staff permTimestamp
        $hStaff->updateAll('permTimestamp', time());        
        
        set_time_limit(60);
        //Update gtd_tickets table
        set_time_limit(60);
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s MODIFY subject VARCHAR(255) NOT NULL default ''", $xoopsDB->prefix('gtd_tickets')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_tickets'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_tickets')) ;
        
    case '0.75':
        set_time_limit(60);
        // Set default department
        $xoopsModuleConfig =& gtdGetModuleConfig();
        if(isset($xoopsModuleConfig['gtd_defaultDept']) && $xoopsModuleConfig['gtd_defaultDept'] != 0){
            $ret = gtdSetMeta('default_department', $xoopsModuleConfig['gtd_defaultDept']);
        } else {
            $hDepartments =& gtdGetHandler('department');
            $depts =& $hDepartments->getObjects();
            $aDepts = array();
            foreach($depts as $dpt){
                $aDepts[] = $dpt->getVar('id');
            }
            $ret = gtdSetMeta("default_department", $aDepts[0]);
        }
        
        $qry = $xoopsDB->query(sprintf("ALTER TABLE %s DROP PRIMARY KEY", $xoopsDB->prefix('gtd_ticket_submit_emails')));
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD PRIMARY KEY(ticketid, uid, email)", $xoopsDB->prefix('gtd_ticket_submit_emails')),
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_ticket_submit_emails'),
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_ticket_submit_emails'));
        
        
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s MODIFY department int(11) NOT NULL default '0'", $xoopsDB->prefix('gtd_jstaffdept')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_jstaffdept'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_jstaffdept')) ;
                                
        echo "<li>"._AM_GTD_MSG_CHANGED_DEFAULT_DEPT."</li>";
        
        // Add field to gtd_saved_searches for sql string
        $ret = $ret && _runQuery(sprintf("ALTER TABLE %s ADD (hasCustFields int(11) NOT NULL default '0')", 
                                $xoopsDB->prefix('gtd_saved_searches')), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE, 'gtd_saved_searches'), 
                                sprintf(_AM_GTD_MSG_MODIFYTABLE_ERR, 'gtd_saved_searches'));
                                
        // Take existing saved searches and add 'query' field
        $hSavedSearch =& gtdGetHandler('savedSearch');
        $savedSearches =& $hSavedSearch->getObjects();
        
        foreach($savedSearches as $savedSearch)
        {
            set_time_limit(60);
            $crit =& unserialize($savedSearch->getVar('search'));
            if(is_object($crit)){
                $savedSearch->setVar('query', $crit->render());
            
                if($hSavedSearch->insert($savedSearch)){
                    echo "<li>".sprintf(_AM_GTD_MSG_UPDATE_SEARCH, $savedSearch->getVar('id'))."</li>";
                } else {
                    echo "<li>".sprintf(_AM_GTD_MSG_UPDATE_SEARCH_ERR, $savedSearch->getVar('id'))."</li>";
                }
            }
        }
        unset($savedSearches);

        // Add ticket list table
        set_time_limit(60);
        $ret = $ret && _runQuery(sprintf("CREATE TABLE %s (id int(11) NOT NULL auto_increment,
                                                           uid int(11) NOT NULL default '0',
                                                           searchid int(11) NOT NULL default '0',
                                                           weight int(11) NOT NULL default '0',
                                                           PRIMARY KEY (id),
                                                           KEY ticketList (uid, searchid)
                                                          )TYPE=MyISAM;", $xoopsDB->prefix('gtd_ticket_lists')),
                                 sprintf(_AM_GTD_MSG_ADDTABLE, 'gtd_ticket_lists'),
                                 sprintf(_AM_GTD_MSG_ADDTABLE_ERR, 'gtd_ticket_lists'));
                                 
        // Add global ticket lists for staff members
        gtdCreateDefaultTicketLists();
        
    case '0.77':
        // No schema changes for 0.78
        
        echo "</ul>";
    } 
    
    $newversion = round($xoopsModule->getVar('version') / 100, 2);
    //if successful, update gtd_meta table with new ver
    if ($ret) {
        printf(_AM_GTD_UPDATE_OK, $newversion);
        $ret = gtdSetMeta('version', $newversion);
    } else {
        printf(_AM_GTD_UPDATE_ERR, $newversion);
    }
    
    gtd_adminFooter();
    xoops_cp_footer();
}

if($op == "upgradeDB"){
echo "<script language='JavaScript' type='text/javascript'>
window.onload=function()
{
	var objWindow=window.open('about:blank', 'progress', '');
	objWindow.close();
}
</script>";
}

?>