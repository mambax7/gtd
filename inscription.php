<?php
//$Id: error.php,v 1.4 2004/12/08 16:18:31 eric_juden Exp $
require_once('header.php');

$xoopsOption['template_main'] = 'gtd_inscription.html';
include(XOOPS_ROOT_PATH . '/header.php');

$xoopsTpl->assign('xoops_module_header', $gtd_module_header);
$xoopsTpl->assign('gtd_imagePath', XOOPS_URL . '/modules/gtd/images/');
$xoopsTpl->assign('gtd_message', _GTD_MESSAGE_NO_INSCRIPTION);

include(XOOPS_ROOT_PATH . '/footer.php');
?>