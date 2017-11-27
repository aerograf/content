<?php

/**
 * @param $options
 * @return array
 */
function site_block_nav($options)
{
    global $xoopsDB, $xoopsModule, $xoopsTpl, $_GET, $xoopsUser, $xoopsConfig, $padding;
    $padding = $options[0];
    //-------------- Modules --------------
    $menuModule         = [];
    $moduleHandler     = xoops_getHandler('module');
    $criteria           = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('weight', 0, '>'));
    $criteria->add(new Criteria('isactive', 1));
    $modules            = $moduleHandler->getObjects($criteria, true);
    $modulepermHandler = xoops_getHandler('groupperm');
    $groups             = $xoopsUser ? $xoopsUser-> getGroups() : [XOOPS_GROUP_ANONYMOUS];
    $read_allowed       = $modulepermHandler->getItemIds('module_read', $groups);
    foreach (array_keys($modules) as $i) {
        if (in_array($i, $read_allowed)) {
            $menuModule[$i]['title']    = $modules[$i]->getVar('name');
            $menuModule[$i]['url']      = XOOPS_URL . '/modules/' . $modules[$i]->getVar('dirname') . '/';
            $menuModule[$i]['priority'] = $modules[$i]->getVar('weight');
            $menuModule[$i]['id']       = $modules[$i]->getVar('id');
            $menuModule[$i]['type']     = 'module';

            $sublinks = $modules[$i]->subLink();
            if ((count($sublinks) > 0) && (!empty($xoopsModule)) && ($i == $xoopsModule->getVar('mid'))) {
                foreach ($sublinks as $sublink) {
                    $menuModule[$i]['sublinks'][] = [
                                          'title' => $sublink['name'],
                                          'url'   => XOOPS_URL . '/modules/' . $modules[$i]->getVar('dirname') . '/' . $sublink['url']
                                          ];
                }
            } else {
                $menuModule[$i]['sublinks'] = [];
            }
        }
    }

    //-------------- Content --------------
    //bpo - added visible else not shown items appear in menu
    $result = $xoopsDB->query("SELECT title, storyid, parent_id, homepage, nohtml, nosmiley, nobreaks, nocomments, link, address, submenu, visible, blockid AS priority, 'content' AS type FROM "
                      . $xoopsDB->prefix('content')
                              . ' where visible = 1 ORDER BY blockid');

    $contentItems = [];

    //davinci27 - Add new permission handlers
    $groupPermHandler      = xoops_getHandler('groupperm');
    $module                = $moduleHandler->getByDirname('content');
    $xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds('content_page_view', $groups, $module->getVar('mid'));
    while ($tcontent = $xoopsDB->fetchArray($result)) {
        if (in_array($tcontent['storyid'], $allowedItems)) {
            $contentItems[] = $tcontent;
        }
    }
    global $allParents, $currentpage;
    if ($xoopsModule && ('Content' === $xoopsModule->name() || 'content' === $xoopsModule->dirname())) {
        $currentpage = $_GET['id'];
    } elseif ($xoopsModule) {
        $result = $xoopsDB->query('SELECT storyid FROM '
                                  . $xoopsDB->prefix('content')
                                  . ' WHERE visible=1 AND assoc_module = '
                                  . $xoopsModule->getVar('mid'));
        if ($xoopsDB->getRowsNum($result) > 0) {
            list($currentpage) = $xoopsDB->fetchRow($result);
        }
    }

    if (isset($currentpage)) {
        $allParents = find_all_parents($contentItems, $currentpage);
    }
    
    $menu = array_merge($menuModule, return_children($contentItems, 0));
    
    foreach ($menu as $key => $row) {
        $priority[$key]  = $row['priority'];
    }
    
    array_multisort($priority, SORT_ASC, $menu);
    $block             = [];
    $block['ct_depth'] = 0;
    $block['ct_menu']  = print_menu($menu, $contentItems, 0, $block['ct_depth']);

    return $block;
}

/**
 * @param $items
 * @param $parent_id
 * @return array
 */
function return_children($items, $parent_id)
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
 * @return array
 */
function find_all_parents($items, $item_id)
{
    $parents[] = $item_id;
    $parent    = $parents[0];
    while ($parent = find_parent_sec($items, $parent)) {
        $parents[] = $parent;
    }
    return $parents;
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
 * @param $menuItems
 * @param $fullList
 * @param $level
 * @param $depth
 * @return string
 */
function print_menu($menuItems, $fullList, $level, $depth)
{
    global $xoopsModule, $xoopsRequestUri, $xoopsDB, $xoopsUser, $allParents, $padding;

    $TempMyList = '';
    $MyList     = '';
    foreach ($menuItems as $menuItem) {
        $temp = ($level * 9) + 3;
        if ('content' === $menuItem['type']) {
            if (0 == $menuItem['link'] && $menuItem['address']) {
                $contentURL = $menuItem['address'];
            } else {
                $contentURL = XOOPS_URL . '/modules/content/index.php?id=' . $menuItem['storyid'];
            }
            if (0 == $level) {
                $MyList .= "\n\t<a class=\"menuMain\" href=\"" . $contentURL . '">' . $menuItem['title'] . '</a>';
            } else {
                $MyList .= "\n\t<a class=\"menuSub\" style=\"padding-left : " . ($level * $padding) . 'px;" href="' . $contentURL . '">' . $menuItem['title'] . '</a>';
            }
            $children = return_children($fullList, $menuItem['storyid']);
            if ($children) {
                if (in_array($menuItem['storyid'], $allParents)) {
                    $MyList .= '' . print_menu($children, $fullList, $level + 1, $depth) . '';
                }
            }
        } else { // Its a Module
            $contentURL = $menuItem['url'];
            $MyList .= "\n\t<a class=\"menuMain\" href=\"" . $contentURL . '">' . $menuItem['title'] . '</a>';
            if ($menuItem['sublinks']) {
                foreach ($menuItem['sublinks'] as $sublink) {
                    $MyList .= '<a class="menuSub" href="' . $sublink['url'] . '">' . $sublink['title'] . "</a>\n";
                }
            }
        }
    }
    if (0 == $level) {
        if (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin()) {
            $MyList .= "\n\t<a class=\"menuMain\" href=\"" . XOOPS_URL . '/modules/content/admin/index.php?op=submit&id=0&return=1' . '">' . _MB_CONTENT_MENUADDITEM . '</a>';
        }
    }
    return $MyList;
}

/**
 * @param $options
 * @return string
 */
function edit_block_nav($options)
{
    $form  = '&nbsp;' . _MB_CONTENT_PADDING . '&nbsp;<input type="text" name="options[]" value="' . $options[0] . '" size="5" />&nbsp;pixels';

    return $form;
}
