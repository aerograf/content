<?php

	require_once "../../mainfile.php";
	$id        = intval($_REQUEST['id']);
	$return    = intval($_REQUEST['return']);
	$showshort = intval($_REQUEST['showshort']);

	include XOOPS_ROOT_PATH . "/modules/content/admin/edit_content.php";
