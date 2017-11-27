<?php

include_once "admin_header.php";

// ------------------------------------------------------------------------- //
// Switch Statement for the different operations                             //
// ------------------------------------------------------------------------- //
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
global $op;
switch ($op) {
	// ------------------------------------------------------------------------- //
	// Delete it definitely                                                      //
	// ------------------------------------------------------------------------- //
	case "deleteit":
		global $xoopsDB;
		//move any orphaned content items to the same level as the deleted item
		$result       = $xoopsDB->query("SELECT parent_id FROM "
                              . $xoopsDB->prefix('content')
                              . " WHERE storyid="
                              . intval($id));
		list($parent) = $xoopsDB->fetchRow($result);
		$result       = $xoopsDB->query("UPDATE "
                              . $xoopsDB->prefix('content')
                              . " SET parent_id = "
                              . intval($parent)
                              . " WHERE parent_id="
                              . intval($id));
	  $result       = $xoopsDB->query("DELETE FROM "
                              . $xoopsDB->prefix('content')
                              . " WHERE storyid="
                              . intval($id));
		xoops_comment_delete($xoopsModule->getVar('mid'), $id);
		if (isset($return) && $return == 1) {
			echo "<script>window.opener.location.href='/';window.close();</script>";
		}else{
			redirect_header("manage_content.php", 1 ,_AM_CONTENT_DBUPDATED);
		}
		
		break;
		
	// ------------------------------------------------------------------------- //
	// Delete Content - Confirmation Question                                    //
	// ------------------------------------------------------------------------- //
	default:
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
		$confirm_params       = [];
		$confirm_params['id'] = intval($id);
		$confirm_params['op'] = 'deleteit';
		if (isset($return) && $return == 1) {
			$confirm_params['return'] = $return;
		}
		$action = 'history.go(-1)';
		xoops_confirm($confirm_params, 'delete_content.php', _AM_CONTENT_RUSUREDEL, _YES, true, $action);
    require_once __DIR__ . '/footer.php';
		break;
}
