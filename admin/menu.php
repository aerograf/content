<?php
use Xmf\Request;

$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');

$adminmenu[] = [
    'title' => _MI_CONTENT_ADMIN_HOME,
    'link'  => 'admin/index.php',
    'desc'  => _MI_CONTENT_ADMIN_HOME_DESC,
    'icon'  => $pathIcon32 . 'home.png'
];

$adminmenu[] = [
    'title' => _MI_CONTENT_ADMENU3,
    'link'  => 'admin/manage_content.php',
    'icon'  => $pathIcon32 . 'category.png'
];

$adminmenu[] = [
    'title' => _MI_CONTENT_ADMENU1,
    'link'  => 'admin/add_content.php',
    'icon'  => $pathIcon32 . 'wizard.png'
];

$adminmenu[] = [
    'title' => _MI_CONTENT_ADMENU4,
    'link'  => 'admin/order_menu.php',
    'icon'  => $pathIcon32 . 'compfile.png'
];

$adminmenu[] = [
    'title' => _MI_CONTENT_ADMENU5,
    'link'  => 'admin/manage_permissions.php',
    'icon'  => $pathIcon32 . 'permissions.png'
];

$adminmenu[] = [
    'title' => _MI_CONTENT_ADMIN_ABOUT,
    'link'  => 'admin/about.php',
    'desc'  => _MI_CONTENT_ADMIN_ABOUT_DESC,
    'icon'  => $pathIcon32 . 'about.png'
];