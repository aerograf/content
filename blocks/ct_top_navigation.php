<?php

/**
 * @return array
 */
function content_block_top_nav()
{
    global $xoopsDB, $xoopsModule, $xoopsTpl, $_GET, $xoopsUser;
    $moduleHandler = xoops_getHandler('module');
    $block = [];
    $_GET['id'] = '';
    $myts  = MyTextSanitizer::getInstance();
    if ($xoopsModule && ('Content' === $xoopsModule->name() || 'content' === $xoopsModule->dirname())) {
        $result = $xoopsDB->query("SELECT CASE parent_id WHEN 0 THEN storyid ELSE parent_id END 'sortorder' FROM "
                            . $xoopsDB->prefix('content')
                            . " WHERE visible='1' AND storyid="
                            . $_GET['id']);
        list($currentParent) = $xoopsDB->fetchRow($result);
    } else {
        $currentParent = '';
    }

    $result = $xoopsDB->query('SELECT link, storyid, blockid, title, visible, parent_id, address, newwindow FROM '
                              . $xoopsDB->prefix('content')
                              . " WHERE visible='1' and parent_id = 0 ORDER BY blockid");
    $groupPermHandler      = xoops_getHandler('groupperm');
    $module                = $moduleHandler->getByDirname('content');
    $xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds('content_page_view', $groups, $module->getVar('mid'));

    while ($tcontent = $xoopsDB->fetchArray($result)) {
        if (in_array($tcontent['storyid'], $allowedItems)) {
            $link = [];
            if ($tcontent['address'] && 1 != $tcontent['link']) {
                $contentURL = $tcontent['address'];
            } else {
                $contentURL = XOOPS_URL . '/modules/content/index.php?id=' . $tcontent['storyid'];
            }
            $link['id']            = $tcontent['storyid'];
            $link['title']         = $myts->makeTboxData4Show($tcontent['title']);
            $link['parent']        = $tcontent['parent_id'];
            $link['newwindow']     = $tcontent['newwindow'];
            $link['currentParent'] = $currentParent;
            if ($_GET['id']) {
                $link['currentPage'] = $_GET['id'];
            }
            $link['address']  = $contentURL;
            $block['links'][] = $link;
        }
    }
    return $block;
}
