<?php

    require_once __DIR__ . '/../../mainfile.php';
    $id        = (int)$_REQUEST['id'];
    $return    = (int)$_REQUEST['return'];
    $showshort = (int)$_REQUEST['showshort'];

    include XOOPS_ROOT_PATH . '/modules/content/admin/edit_content.php';
