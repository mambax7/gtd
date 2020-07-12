<?php
//$Id: menu.php,v 1.16 2005/08/22 21:10:36 eric_juden Exp $
$adminmenu[1]['title']  = _MI_GTD_ADMIN_ABOUT;
$adminmenu[1]['link']   = 'admin/index.php?op=about';
//$adminmenu[2]['title']  = _MI_GTD_MENU_BLOCKS;
//$adminmenu[2]['link']   = 'admin/index.php?op=blocks';
$adminmenu[3]['title']  = _MI_GTD_MENU_MANAGE_DEPARTMENTS;
$adminmenu[3]['link']   = "admin/department.php?op=manageDepartments";
$adminmenu[4]['title']  = _MI_GTD_MENU_MANAGE_FILES;
$adminmenu[4]['link']   = "admin/file.php?op=manageFiles";
$adminmenu[5]['title']  = _MI_GTD_MENU_MANAGE_STAFF;
$adminmenu[5]['link']   = "admin/staff.php?op=manageStaff";
$adminmenu[6]['title']  = _MI_GTD_TEXT_NOTIFICATIONS;
$adminmenu[6]['link']   = "admin/notifications.php";
$adminmenu[7]['title']  = _MI_GTD_TEXT_MANAGE_STATUSES;
$adminmenu[7]['link']   = "admin/status.php?op=manageStatus";
$adminmenu[8]['title']  = _MI_GTD_TEXT_MANAGE_FIELDS;
$adminmenu[8]['link']   = "admin/fields.php";
$adminmenu[9]['title']  = _MI_GTD_MENU_CHECK_TABLES;
$adminmenu[9]['link']   = "admin/upgrade.php?op=checkTables";
$adminmenu[10]['title']  = _MI_GTD_MENU_MIMETYPES;
$adminmenu[10]['link']   = "admin/mimetypes.php";
$adminmenu[11]['title']  = _MI_GTD_MENU_MAIL_EVENTS;
$adminmenu[11]['link']   = "admin/index.php?op=mailEvents";
?>