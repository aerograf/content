<?php

$current_page_id = '';
/**
 * @param $options
 * @return array|\SystemBlock|\XoopsBlock|\XoopsObject
 */
function site_block_section_nav($options)
{
    global $xoopsDB, $xoopsModule, $xoopsTpl, $_GET, $xoopsUser, $xoopsConfig, $block, $current_page_id, $padding;
    $padding               = $options[0];
    $moduleHandler        = xoops_getHandler('module');
    $result = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM "
                      . $xoopsDB->prefix('content')
                              . ' WHERE visible = 1 ORDER BY blockid');
    $contentItems          = [];
    $groupPermHandler      = xoops_getHandler('groupperm');
    $module                = $moduleHandler->getByDirname('content');
    $xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds('content_page_view', $groups, $module->getVar('mid'));
    while ($tcontent        = $xoopsDB->fetchArray($result)) {
        if (in_array($tcontent['storyid'], $allowedItems)) {
            $contentItems[] = $tcontent;
        }
    }

    if ($xoopsModule) {
        $result = $xoopsDB->query('SELECT storyid FROM '
                                  . $xoopsDB->prefix('content')
                                  . ' WHERE visible=1 AND assoc_module = '
                                  . $xoopsModule->getVar('mid'));
        if ($xoopsDB->getRowsNum($result) > 0) {
            list($current_page_id) = $xoopsDB->fetchRow($result);
        }
        if (!isset($current_page_id)) {
            $current_page_id = $_GET['id'];
        }

        global $allParents;
        if (isset($current_page_id)) {
            $allParents = find_all_parents($contentItems, $current_page_id);
        }

        $menu = return_children_sec($contentItems, find_top_parent_sec($contentItems, $current_page_id));
        if ($menu) {
            foreach ($menu as $key => $row) {
                $priority[$key]  = $row['priority'];
            }

            array_multisort($priority, SORT_ASC, $menu);

            $block                          = [];
            $block['ct_depth']              = 0;
            $block['ct_section_title']      = find_title_sec($contentItems, find_top_parent_sec($contentItems, $current_page_id));
            $block['ct_section_title_link'] = find_url_sec($contentItems, find_top_parent_sec($contentItems, $current_page_id));
            $block['ct_section_id']         = find_top_parent_sec($contentItems, $current_page_id);
            $block['ct_section_menu']       = print_sec_menu($menu, $contentItems, 0, $block['ct_depth']);
        }
    }
    return $block;
}

/**
 * @param $items
 * @param $item_id
 * @return array
 */
function find_all_parents($items, $item_id)
{
    $parents[] = $item_id;
    $parent    = $parents[0];
    while ($parent = find_parent_sec($items, $parent)) {
        echo $parent;
        $parents[] = $parent;
    }
    return $parents;
}

/**
 * @param $items
 * @param $parent_id
 * @return array
 */
function return_children_sec($items, $parent_id)
{
    $myItems = [];
    foreach ($items as $item) {
        if ($item['parent_id'] == $parent_id) {
            $myItems[] = $item;
        }
    }
    return $myItems;
}

/**
 * @param $items
 * @param $item_id
 * @return string
 */
function find_top_parent_sec($items, $item_id)
{
    $top_parent = '';
    for ($parent = $item_id; 0 <> $parent; $parent = find_parent_sec($items, $parent)) {
        $top_parent = $parent;
    }
    return $top_parent;
}

/**
 * @param $items
 * @param $item_id
 * @return mixed
 */
function find_parent_sec($items, $item_id)
{
    foreach ($items as $item) {
        if ($item['storyid'] == $item_id) {
            $parent = $item['parent_id'];
            break;
        }
    }
    return $parent;
}

/**
 * @param $items
 * @param $item_id
 * @return mixed
 */
function find_title_sec($items, $item_id)
{
    foreach ($items as $item) {
        if ($item['storyid'] == $item_id) {
            $title = $item['title'];
            break;
        }
    }
    return $title;
}

/**
 * @param $items
 * @param $item_id
 * @return string
 */
function find_url_sec($items, $item_id)
{
    foreach ($items as $item) {
        if ($item['storyid'] == $item_id) {
            if ($item['address'] && 1 != $item['link']) {
                $itemURL = $item['address'];
            } else {
                $itemURL = XOOPS_URL . '/modules/content/index.php?id=' . $item['storyid'];
            }
            break;
        }
    }
    return $itemURL;
}

/**
 * @param $menuItems
 * @param $fullList
 * @param $level
 * @param $depth
 * @return string
 */
function print_sec_menu($menuItems, $fullList, $level, $depth)
{
    global $_GET, $current_page_id, $allParents, $padding;
    $MyList = '';
    if ($level + 1 > $depth) {
        $depth = $level + 1;
    }

    $my_style = 'menuSub';
    if (0 == $level) {
        $my_style = 'menuMain';
    }

    foreach ($menuItems as $menuItem) {
        if ($menuItem['address'] && 1 != $menuItem['link']) {
            $contentURL = $menuItem['address'];
        } else {
            $contentURL = XOOPS_URL . '/modules/content/index.php?id=' . $menuItem['storyid'];
        }
        $MyList .= '<a class="' . $my_style . '"  style="padding-left : ' . ($depth * $padding) . 'px;"';
        if (1 == $menuItem['newwindow']) {
            $MyList .= ' target="_blank"';
        }
        $MyList .= ' href="' . $contentURL . '"';
        if ($menuItem['storyid'] == $current_page_id) {
            $MyList .= ' id="current-nav-item"';
        }
        $MyList .= '>' . $menuItem['title'] . "</a>\n";
        $children = return_children_sec($fullList, $menuItem['storyid']);
        if ($children) {
            if (in_array($menuItem['storyid'], $allParents)) {
                $MyList .= print_sec_menu($children, $fullList, $level + 1, $depth);
            }
        }
    }
    return $MyList;
}

/**
 * @param $options
 * @return string
 */
function edit_block_sec_nav($options)
{
    $form  = '&nbsp;' . _MB_CONTENT_PADDING . '&nbsp;<input type="text" name="options[]" value="' . $options[0] . '" size="5" />&nbsp;pixels';

    return $form;
}
