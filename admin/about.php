<?php

include '../../../include/cp_header.php';
include '../../../class/xoopsformloader.php';

$adminObject = Xmf\Module\Admin::getInstance();
xoops_cp_header();

$adminObject->displayNavigation(basename(__FILE__));
\Xmf\Module\Admin::setPaypal('6KJ7RW5DR3VTJ');
$adminObject->displayAbout(false);

xoops_cp_footer();
