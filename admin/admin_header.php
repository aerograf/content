<?php

if (!defined('XOOPS_URL')) {
    include_once '../../../mainfile.php';
    include XOOPS_ROOT_PATH . '/include/cp_header.php';
}
include_once XOOPS_ROOT_PATH . '/class/xoopsmodule.php';
include_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
include_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

include XOOPS_ROOT_PATH . '/modules/content/include/admin_functions.php';

if (file_exists(XOOPS_ROOT_PATH . '/modules/content/language/' . $xoopsConfig['language'] . '/admin.php')) {
    include_once(XOOPS_ROOT_PATH . '/modules/content/language/' . $xoopsConfig['language'] . '/admin.php');
    include_once(XOOPS_ROOT_PATH . '/modules/content/language/' . $xoopsConfig['language'] . '/modinfo.php');
} else {
    include_once(XOOPS_ROOT_PATH . '/modules/content/language/english/admin.php');
    include_once(XOOPS_ROOT_PATH . '/modules/content/language/english/modinfo.php');
}

$groupPermHandler      = xoops_getHandler('groupperm');
$moduleHandler         = xoops_getHandler('module');
$module                = $moduleHandler->getByDirname('content');
($xoopsUser) ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;

if (isset($_GET)) {
    foreach ($_GET as $k => $v) {
        $$k = $v;
    }
}

if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        $$k = $v;
    }
}

if (!$xoopsUser->isAdmin($module->getVar('mid')) &&
    !$groupPermHandler->checkRight('content_page_write', $id, $groups, $module->getVar('mid')) &&
    !$groupPermHandler->checkRight('content_page_add', $id, $groups, $module->getVar('mid')) &&
    !$groupPermHandler->checkRight('content_admin', null, $groups, $module->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}
