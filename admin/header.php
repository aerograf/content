<?php
include '../../../include/cp_header.php' ;

if (!isset($moduleDirName)) {
    $moduleDirName = basename(dirname(__DIR__));
}

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    include_once XOOPS_ROOT_PATH . '/class/template.php' ;
    $xoopsTpl = new XoopsTpl() ;
}

$moduleHelper->loadLanguage('modinfo');
$moduleHelper->loadLanguage('admin');

xoops_cp_header();

$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/assets/css/admin.css') ;
