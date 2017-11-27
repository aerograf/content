<?php

require_once __DIR__ . '/header.php';

$adminObject = \Xmf\Module\Admin::getInstance();
$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();	

require_once __DIR__ . '/footer.php';
