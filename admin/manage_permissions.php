<?php

include_once 'admin_header.php';
include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
if (!class_exists('XoopsGroupPermForm')) {
    include_once $GLOBALS['xoops']->path('class/xoopsform/grouppermform.php');
}
global $xoopsUser, $xoopsModule, $xoopsDB, $op;
global $xoopsModuleConfig;

$module_id        = $xoopsModule->getVar('mid');
$module_handler   = xoops_getHandler('module');
$groupPermHandler = xoops_getHandler('groupperm');
$module           = $module_handler->getByDirname('content');
// ------------------------------------------------------------------------- //
// Update Content -> Show Content Page                                       //
// ------------------------------------------------------------------------- //
if ('update' === $op) {
    foreach ($id as $storyid) {
        $groupPermHandler->DeleteByModule($module->getVar('mid'), 'content_page_view', $storyid);

        foreach ($group_read[$storyid] as $group) {
            $groupPermHandler->addRight('content_page_view', $storyid, $group, $module->getVar('mid'));
        }
        if ('2' == $xoopsModuleConfig['cont_permits_advnaced']) {
            $groupPermHandler->DeleteByModule($module->getVar('mid'), 'content_page_write', $storyid);
            foreach ($group_write[$storyid] as $group) {
                $groupPermHandler->addRight('content_page_write', $storyid, $group, $module->getVar('mid'));
            }

            $groupPermHandler->DeleteByModule($module->getVar('mid'), 'content_page_add', $storyid);
            foreach ($group_add[$storyid] as $group) {
                $groupPermHandler->addRight('content_page_add', $storyid, $group, $module->getVar('mid'));
            }

            $groupPermHandler->DeleteByModule($module->getVar('mid'), 'content_admin', null);
            foreach ($group_admin as $group) {
                $groupPermHandler->addRight('content_admin', null, $group, $module->getVar('mid'));
            }
        }
    }

    redirect_header('manage_permissions.php', 2, _AM_CONTENT_DBUPDATED);
} else {
    // ------------------------------------------------------------------------- //
    // Show Manage Content Form                                                  //
    // ------------------------------------------------------------------------- //

    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
    echo '<script language="JavaScript" src="../assets/js/prototype.js"></script>';
    echo '
			<style>
				.hideme{
					display:none;
				}
			</style>
			<script language="JavaScript">
			///////////////////////////////////////
			//RollOver Routines
			//Initialize variables
			var rollOverImages = new Array(), overIndicator = "_o";
			//Create New RollOver Object
			function createRollOver(img) {if (!rollOverImages[img.name] && document.images) rollOverImages[img.name] = new rollOver(img.src);}		
			//Function for Rollover On State
			function imgOn(imgName) {if (document.images && rollOverImages[imgName]) $(imgName).src = rollOverImages[imgName].on.src;}
			//Function for Rollover Off State
			function imgOff(imgName) {if (document.images && rollOverImages[imgName]) $(imgName).src = rollOverImages[imgName].off.src;}
			//Constructor
			function rollOver(on) {this.on = new Image();this.on.src = on.substring(0,on.lastIndexOf(".")) + overIndicator + on.substring(on.lastIndexOf("."),on.length); this.off = new Image();this.off.src = on;}
			//////////////////////////////////////////
			
			progress_icon = new Image();
			progress_icon.src = "' . XOOPS_URL . '/modules/content/assets/images/indicator_arrows.gif";
			
			function showitems(itemName){
				$("control-" + itemName).src = progress_icon.src;
				window.setTimeout("process_showitems(\'" + itemName + "\')", 100);
			}
			
			function process_showitems(itemName){
				elements = document.getElementsByClassName("parent-" + itemName, $("displaytable"))
				if (elements && Element.hasClassName(elements[0], "hideme")){
					for (i=0; ele = elements[i]; i++){
						Element.removeClassName(ele, "hideme");
					}
					imgOn("control-" + itemName);
				} else {
					hide_items(elements, itemName)
					imgOff("control-" + itemName);
				}
			}
			
			function hide_items(items, itemNum){
				var ele, i;
				for (i=0; ele = items[i]; i++){
					if (!Element.hasClassName(ele, "hideme")){
						Element.addClassName(ele, "hideme");
					}
					if (ele.id.length > 0){
						var elements = document.getElementsByClassName("parent-" + ele.id, $("displaytable"))
						if (elements.length > 0)
							hide_items(elements, ele.id)
					}
				}
			}
			
			function showallitems(){
				$("control-all").src = progress_icon.src;
				window.setTimeout("process_showallitems()", 100);
			}
			
			function process_showallitems(){
				if (document.getElementsByClassName("hideme", $("displaytable")).length > 0){
					elements = $("displaytable").getElementsByTagName("tr")
					for (i=0; ele = elements[i]; i++){
						if (Element.hasClassName(elements[i], "hideme")){
							Element.removeClassName(ele, "hideme");
						}
					}
					elements = elements = document.getElementsByClassName("folder", $("displaytable"))
					for (i=0; ele = elements[i]; i++){
						imgOn(ele.id);
					}
					imgOn("control-all");
				} else {
					elements = $("displaytable").getElementsByTagName("tr")
					for (i=0; ele = elements[i]; i++){
						if (!Element.hasClassName(elements[i], "even") && !Element.hasClassName(elements[i], "head")){
							Element.addClassName(ele, "hideme");
						}
					}
					elements = elements = document.getElementsByClassName("folder", $("displaytable"))
					for (i=0; ele = elements[i]; i++){
						imgOff(ele.id);
					}
					imgOff("control-all");
				}
			}
			
			</script>';
    global $xoopsDB;
    $myts = MyTextSanitizer::getInstance();

    $permitadmin = new XoopsFormSelectGroup(_AM_CONTENT_PERMS, 'group_admin', true, $groupPermHandler->getGroupIds('content_admin', null, $module->getVar('mid')), 3, true);

    echo '' . showMenu() . "
			<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer'>
				<tr class='even'>
					<td colspan='2'>" . displayFilterForm() . "</td>
				</tr><form method='post'>";
    if ('2' == $xoopsModuleConfig['cont_permits_advnaced']) {
        echo "
				<tr class='head'>
					<td colspan='2'><strong>Global permissions</strong></td>
				</tr>
				<tr class='even'>
					<td><strong>Admin</strong></td>
					<td>" . $permitadmin->render() . '</td>
				</tr>
			</table> ';
    }
    echo "<table border='0' cellpadding='0' cellspacing='1' width='100%' class='outer' id='displaytable'>";
    if (isset($filterSQL)) {
        $filterSQL = ' WHERE ' . $filterSQL;
    }
    echo "
				<tr class='head'>
					<td><a href='#' onclick='showallitems();return false;'><img onload='createRollOver(this)' name='control-all' id='control-all' src='../assets/images/folder.png' alt='' border='0' align='absmiddle'></a>&nbsp;<b>" . _AM_CONTENT_LINKNAME . "</b></td>
					<td width='60' nowrap><b><nobr>" . _AM_CONTENT_PERMITREAD . '</nobr></b></td>';
    if ('2' == $xoopsModuleConfig['cont_permits_advnaced']) {
        echo "
					<td width='60' nowrap><b><nobr>" . _AM_CONTENT_PERMITWRITE . "</nobr></b></td>
					<td width='60' nowrap><b><nobr>Add Permissions</nobr></b></td>";
    }
    echo '		</tr>';
    $contentItems = [];
    $result       = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM " . $xoopsDB->prefix('content') . ' ' . $filterSQL . ' ORDER BY blockid');
    while ($tcontent = $xoopsDB->fetchArray($result)) {
        $contentItems[] = $tcontent;
    }

    $sortedContent = $contentItems;
    if ('' == $filterSQL) {
        $sortedContent = return_children($contentItems, 0);
    }

    unset($contentItems);
    $contentItems = [];
    $result       = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM " . $xoopsDB->prefix('content') . ' ORDER BY visible DESC, blockid');
    while ($tcontent = $xoopsDB->fetchArray($result)) {
        $contentItems[] = $tcontent;
    }
    $allItems = return_children($contentItems, 0);
    foreach ($sortedContent as $tcontent) {
        if ((isset($tcontent['depth']) && 0 == $tcontent['depth']) || '' != $filterSQL) {
            print_item($tcontent, $xoopsModule->dirname(), $allItems, $myts, $groupPermHandler->getGroupIds('content_page_view', $tcontent['storyid'], $module->getVar('mid')), $groupPermHandler->getGroupIds('content_page_write', $tcontent['storyid'], $module->getVar('mid')),
                       $groupPermHandler->getGroupIds('content_page_add', $tcontent['storyid'], $module->getVar('mid')));
            foreach (return_children($contentItems, $tcontent['storyid'], 1) as $child) {
                print_item($child, $xoopsModule->dirname(), $allItems, $myts, $groupPermHandler->getGroupIds('content_page_view', $child['storyid'], $module->getVar('mid')), $groupPermHandler->getGroupIds('content_page_write', $child['storyid'], $module->getVar('mid')),
                           $groupPermHandler->getGroupIds('content_page_add', $child['storyid'], $module->getVar('mid')));
            }
        }
    }

    echo "</table><br />
	  	<div align='center'>
			<input type='hidden' name='op' value='update' />
			<input type='submit' name='submit' value=" . _SUBMIT . ' /></div>';
    echo '</form><br>';

    require_once __DIR__ . '/footer.php';
}

/**
 * @param $tcontent
 * @param $dirname
 * @param $allMenuItems
 * @param $txtSant
 * @param $page_groups_read
 * @param $page_groups_write
 * @param $page_groups_add
 */
function print_item($tcontent, $dirname, $allMenuItems, $txtSant, $page_groups_read, $page_groups_write, $page_groups_add)
{
    global $xoopsModuleConfig;
    $permitRead  = new XoopsFormSelectGroup(_AM_CONTENT_PERMS, 'group_read[' . $tcontent['storyid'] . ']', true, $page_groups_read, 3, true);
    $permitWrite = new XoopsFormSelectGroup(_AM_CONTENT_PERMS, 'group_write[' . $tcontent['storyid'] . ']', true, $page_groups_write, 3, true);
    $permitAdd   = new XoopsFormSelectGroup(_AM_CONTENT_PERMS, 'group_add[' . $tcontent['storyid'] . ']', true, $page_groups_add, 3, true);

    echo '
			<tr ';
    if (isparent($allMenuItems, $tcontent['storyid'])) {
        echo 'id="' . $tcontent['storyid'] . '" ';
    }

    echo " class='" . ((!isset($tcontent['depth']) || 0 == $tcontent['depth']) ? 'even' : 'odd parent-' . $tcontent['parent_id'] . (('1' == $xoopsModuleConfig['cont_collapse']) ? ' hideme ' : '')) . "'>";
    if (!isset($tcontent['depth'])) {
        $tcontent['depth'] = 0;
    }
    echo "  <td><input type='hidden' name='id[]' value='" . $tcontent['storyid'] . "' />";
    if (isset($tcontent['depth']) && isparent($allMenuItems, $tcontent['storyid'])) {
        if (isset($tcontent['depth']) && 0 != $tcontent['depth']) {
            echo '<img src="../assets/images/spacer.gif" alt="" width="' . ($tcontent['depth'] * 8) . '" height="10" border="0" align="absmiddle">';
            echo '<img src="../assets/images/child_mark.png" alt="" width="6" height="17" border="0" align="absmiddle">';
        }
        echo '<a href="#" onclick="showitems('
             . $tcontent['storyid']
             . ');return false;"><img onload="createRollOver(this)" name="control-'
             . $tcontent['storyid']
             . '" id="control-'
             . $tcontent['storyid']
             . '" src="../assets/images/folder.png" alt="" border="0" align="absmiddle" class="folder"></a>';
    } else {
        if (isset($tcontent['depth']) && 0 != $tcontent['depth']) {
            echo '<img src="../assets/images/spacer.gif" alt="" width="' . ($tcontent['depth'] * 8) . '" height="10" border="0" align="absmiddle">';
            echo '<img src="../assets/mages/child_mark.png" alt="" width="6" height="17" border="0" align="absmiddle">';
        }
        echo '<img src="../assets/images/page.png" alt="" border="0" align="absmiddle" height="16" width="16" >';
    }
    echo "
				<a href='edit_content.php?id=" . $tcontent['storyid'] . "'>" . $txtSant->makeTboxData4Show($tcontent['title'], 0, 0, 0) . '</a></td>
				<td>' . $permitRead->render() . '</td>';
    if ('2' == $xoopsModuleConfig['cont_permits_advnaced']) {
        echo '
				<td>' . $permitWrite->render() . '</td>
				<td>' . $permitAdd->render() . '</td>';
    }

    echo '</tr>';
}
