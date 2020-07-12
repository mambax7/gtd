<?php
//$Id: constants.php,v 1.16.2.1 2005/11/26 17:11:25 ackbarr Exp $

/**
 *Global Application Constants
 *
 *@author Brian Wahoff
 *@access Public
 */

define('GTD_DIR_NAME', 'gtd');

define('GTD_SITE_URL', XOOPS_URL);

//Application Folders
define('GTD_BASE_PATH', XOOPS_ROOT_PATH.'/modules/'. GTD_DIR_NAME);
define('GTD_CLASS_PATH', GTD_BASE_PATH.'/class');
define('GTD_BASE_URL', GTD_SITE_URL .'/modules/'. GTD_DIR_NAME);
define('GTD_UPLOAD_PATH', XOOPS_ROOT_PATH."/uploads/".GTD_DIR_NAME);
define('GTD_INCLUDE_PATH', GTD_BASE_PATH.'/include');
define('GTD_INCLUDE_URL', GTD_BASE_URL.'/include');
define('GTD_IMAGE_PATH', GTD_BASE_PATH.'/images');
define('GTD_IMAGE_URL', GTD_BASE_URL.'/images');
define('GTD_ADMIN_URL', GTD_BASE_URL.'/admin');
define('GTD_ADMIN_PATH', GTD_BASE_PATH.'/admin');
define('GTD_PEAR_PATH', GTD_CLASS_PATH.'/pear');
define('GTD_CACHE_PATH', XOOPS_ROOT_PATH.'/cache');
define('GTD_CACHE_URL', GTD_SITE_URL .'/cache');
define('GTD_SCRIPT_URL', GTD_BASE_URL.'/scripts');
define('GTD_JPSPAN_PATH', GTD_INCLUDE_PATH.'/jpspan');

//Control Types
define('GTD_CONTROL_TXTBOX',0);
define('GTD_CONTROL_TXTAREA', 1);
define('GTD_CONTROL_SELECT', 2);
define('GTD_CONTROL_MULTISELECT', 3);
define('GTD_CONTROL_YESNO', 4);
define('GTD_CONTROL_CHECKBOX', 5);
define('GTD_CONTROL_RADIOBOX', 6);
define('GTD_CONTROL_DATETIME', 7);
define('GTD_CONTROL_FILE', 8);

//Notification Settings
define('GTD_NOTIF_STAFF_DEPT', 2);
define('GTD_NOTIF_STAFF_OWNER', 3);
define('GTD_NOTIF_STAFF_NONE', 4);

define('GTD_NOTIF_USER_YES', 1);
define('GTD_NOTIF_USER_NO', 2);

define('GTD_NOTIF_NEWTICKET', 1);
define('GTD_NOTIF_DELTICKET', 2);
define('GTD_NOTIF_EDITTICKET', 3);
define('GTD_NOTIF_NEWRESPONSE', 4);
define('GTD_NOTIF_EDITRESPONSE', 5);
define('GTD_NOTIF_EDITSTATUS', 6);
define('GTD_NOTIF_EDITGENRE', 7);
define('GTD_NOTIF_EDITOWNER', 8);
define('GTD_NOTIF_CLOSETICKET', 9);
define('GTD_NOTIF_MERGETICKET', 10);

define('GTD_GLOBAL_UID', -999);   // refers to all users
define('GTD_DEFAULT_GENRE', 4);
define('GTD_DEFAULT_MODE_PAIEMENT', 1);
define('GTD_DEFAULT_ECHEANCE', 1);

define('GTD_QRY_STAFF_HIGHGENRE', 0);
define('GTD_QRY_STAFF_NEW', 1);
define('GTD_QRY_STAFF_MINE', 2);
define('GTD_QRY_STAFF_ALL', -1);

define('GTD_STATE_UNRESOLVED', 1);
define('GTD_STATE_RESOLVED', 2);

define('GTD_CONSTANTS_INCLUDED', 1);
?>