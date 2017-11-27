<?php

function content_block_nav()
{
    global $xoopsDB, $xoopsModule, $xoopsTpl, $_GET, $xoopsUser;
        
    $block = [];
    $myts  = MyTextSanitizer::getInstance();
    if ($xoopsModule && ($xoopsModule->name() == 'Content' || $xoopsModule->dirname() == 'content')) {
        $result = $xoopsDB->query("SELECT CASE parent_id WHEN 0 THEN storyid ELSE parent_id END 'sortorder' FROM "
                            . $xoopsDB->prefix('content')
                            . " WHERE visible='1' AND storyid="
                            . $_GET['id']);
        list($currentParent) = $xoopsDB->fetchRow($result);
    } else {
        $currentParent = '';
    }
        
    $result = $xoopsDB->query("SELECT child.link, child.storyid, child.blockid, child.title, child.visible, child.parent_id, child.address,CASE parent.parent_id WHEN 0 THEN parent.blockid ELSE child.blockid END 'menu_block', CASE child.parent_id WHEN 0 THEN child.storyid ELSE child.parent_id END 'menu_id' child.newwindow FROM "
                        . $xoopsDB->prefix('content')
                              . ' child LEFT JOIN '
                        . $xoopsDB->prefix('content')
                        . " parent ON child.parent_id = parent.storyid WHERE child.visible='1' ORDER BY menu_block, menu_id, parent_id, blockid");
    $module_handler        = xoops_getHandler('module');
    $groupPermHandler      = xoops_getHandler('groupperm');
    $module                = $module_handler->getByDirname('content');
    $xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds('content_page_view', $groups, $module->getVar('mid'));
    
    while ($tcontent = $xoopsDB->fetchArray($result)) {
        if (in_array($tcontent['storyid'], $allowedItems)) {
            $link = [];
            if ($tcontent['address'] && $tcontent['link'] != 1) {
                $contentURL = $tcontent['address'];
            } else {
                $contentURL = XOOPS_URL . '/modules/content/index.php?id=' . $tcontent['storyid'];
            }
            $link['id']            = $tcontent['storyid'];
            $link['title']         = $myts->makeTboxData4Show($tcontent['title']);
            $link['newwindow']     = $tcontent['newwindow'];
            $link['parent']        = $tcontent['parent_id'];
            $link['currentParent'] = $currentParent;
            if ($_GET['id']) {
                $link['currentPage'] = $_GET['id'];
            }
            $link['address'] = $contentURL;
              
            $block['links'][] = $link;
        }
    }
    if ($xoopsModule) {
        $module_id = $xoopsModule->getVar('mid');
    } else {
        $sql = 'SELECT mid FROM ' . $xoopsDB->prefix('modules') . " WHERE dirname='content'";
        $result = $xoopsDB->query($sql);
        list($module_id) = $xoopsDB->fetchArray($result);
    }

    $gperm_handler = xoops_getHandler('groupperm');
    if ($xoopsUser) {
        $groups = $xoopsUser->getGroups();
    } else {
        $groups = XOOPS_GROUP_ANONYMOUS;
    }
    
    if ($gperm_handler->checkRight('module_admin', $module_id, $groups, 1)) {
        $block['links'][] = [
            'title'         => '<font style="color:#FF9933;">Add main menu item</font>',
            'address'       => XOOPS_URL . '/modules/content/admin/index.php?op=submit&id=0&return=1',
            'parent'        => 0,
            'currentParent' => $currentParent,
            'currentPage'   => $_GET['id']
                            ];
    }
    return $block;
}
