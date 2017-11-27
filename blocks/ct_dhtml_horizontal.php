<?php

function site_block_horz_dhtml_nav()
{
    $MyList = "";
    global $xoopsDB, $xoopsModule, $xoopsTpl, $_GET, $xoopsUser, $xoopsConfig;
    //-------------- Modules --------------
    $menuModule         = [];
    $module_handler     = xoops_gethandler('module');
    $criteria           = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('weight', 0, '>'));
    $criteria->add(new Criteria('isactive', 1));
    $modules            = $module_handler->getObjects($criteria, true);
    $moduleperm_handler = xoops_gethandler('groupperm');
    $groups             = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $read_allowed       = $moduleperm_handler->getItemIds('module_read', $groups);
    foreach (array_keys($modules) as $i) {
        if (in_array($i, $read_allowed)) {
            $menuModule[$i]['title']    = $modules[$i]->getVar('name');
            $menuModule[$i]['url']      = XOOPS_URL . "/modules/" . $modules[$i]->getVar('dirname') . "/";
            $menuModule[$i]['priority'] = $modules[$i]->getVar('weight');
            $menuModule[$i]['id']       = $modules[$i]->getVar('id');
            $menuModule[$i]['type']     = "module";
            $sublinks                   = $modules[$i]->subLink();
            if (count($sublinks) > 0) {
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
    $result = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM "
                      . $xoopsDB->prefix('content')
                      . " WHERE visible = 1 ORDER BY blockid");
    $contentItems          = [];
    $groupPermHandler      = xoops_gethandler('groupperm');
    $module                = $module_handler->getByDirname('content');
    ($xoopsUser) ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds("content_page_view", $groups, $module->getVar("mid"));
    while ($tcontent        = $xoopsDB->fetchArray($result)) {
        if (in_array($tcontent["storyid"], $allowedItems)) {
            $contentItems[] = $tcontent;
        }
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

function print_menu($menuItems, $fullList, $level, $depth)
{
    $MyList = "";
    if ($level + 1 > $depth) {
        $depth = $level + 1;
    }
    if ($level == 0) {
        $MyList .= "<ul id=\"menu-h\" class=\"horizontal\">";
    }
    foreach ($menuItems as $menuItem) {
        $currentPosition = "";
        $currentPosition++;
        if ($menuItem['type'] == 'content') {
            if ($menuItem['address'] && $menuItem['link'] != 1) {
                $contentURL = $menuItem['address'];
            } else {
                $contentURL = XOOPS_URL . "/modules/content/index.php?id=" . $menuItem['storyid'];
            }
        } else {
            $contentURL = $menuItem['url'];
        }
        
        $MyList .= "\n\t<li";
        if ($level == 0) {
            $MyList .= " class=\"nav-item-" . $currentPosition . "\"";
        }
        $MyList .= "><a href=\"" . $contentURL . "\">" . $menuItem['title'] . "</a>";
        if ($menuItem['type'] == 'content') {
            if (return_children($fullList, $menuItem['storyid'])) {
                $MyList .= "<ul>" . print_menu(return_children($fullList, $menuItem['storyid']), $fullList, $level + 1, $depth) . "</ul>";
            }
        } else {
            if ($menuItem['sublinks']) {
                $MyList .= "<ul>\n";
                foreach ($menuItem['sublinks'] as $sublink) {
                    $MyList .= "<li><a href=\"" . $sublink['url'] . "\">" . $sublink['title'] . "</a></li>\n";
                }
                $MyList .= "</ul>\n";
            }
        }
        $MyList .= "</li>\n";
    }
    if ($level == 0) {
        $MyList .= "</ul>";
    }
    return $MyList;
}
