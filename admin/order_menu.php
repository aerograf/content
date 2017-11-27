<?php

    include_once "admin_header.php";
    if (isset($_GET['op']) && $_GET['op'] == "order") {
        for ($j = 1; $j <= $_POST['total']; $j++) {
            if ($_POST['type'.$j] == "module") {
                if (!$result = $xoopsDB->query("UPDATE "
                                  . $xoopsDB->prefix('modules')
                                  . " SET weight = '"
                                  . $_POST['priority'.$j]
                                  . "' WHERE mid = '"
                                  . $_POST['id'.$j]
                                  . "'")) {
                    echo _AM_CONTENT_ERRORINSERT;
                }
            } elseif ($_POST['type'.$j] == "content") {
                if (!$result = $xoopsDB->query("UPDATE "
                                  . $xoopsDB->prefix('content')
                                  . " SET blockid = '"
                                  . $_POST['priority'.$j]
                                  . "' WHERE storyid = '"
                                  . $_POST['id'.$j]
                                  . "'")) {
                    echo _AM_CONTENT_ERRORINSERT;
                }
            }
        }
        redirect_header("order_menu.php", 2, _AM_CONTENT_DBUPDATED);
    } else {
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));
        global $xoopsUser, $xoopsModule, $xoopsDB;
        $menuModule         = [];
        $module_handler     = xoops_gethandler('module');
        $criteria           = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('weight', 0, '>'));
        $modules            = $module_handler->getObjects($criteria, true);
        $moduleperm_handler = xoops_gethandler('groupperm');
        $groups             = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $read_allowed       = $moduleperm_handler->getItemIds('module_read', $groups);
        foreach (array_keys($modules) as $i) {
            if (in_array($i, $read_allowed)) {
                $menuModule[$i]['text']     = $modules[$i]->getVar('name');
                $menuModule[$i]['url']      = XOOPS_URL."/modules/".$modules[$i]->getVar('dirname');
                $menuModule[$i]['priority'] = $modules[$i]->getVar('weight');
                
                echo $modules[$i]->getVar('id');
                $menuModule[$i]['id']   = $modules[$i]->getVar('mid');
                $menuModule[$i]['type'] = "module";
                $sublinks               = $modules[$i]->subLink();
                if ((count($sublinks) > 0) && (!empty($xoopsModule)) && ($i == $xoopsModule->getVar('mid'))) {
                    foreach ($sublinks as $sublink) {
                        $menuModule[$i]['sublinks'][] = [
                                                      'text' => $sublink['name'],
                                                      'url' => XOOPS_URL.'/modules/'.$modules[$i]->getVar('dirname').'/'.$sublink['url']
                                                      ];
                    }
                } else {
                    $menuModule[$i]['sublinks'] = [];
                }
            }
        }
        
        $block = [];
        $myts  = MyTextSanitizer::getInstance();
        
        if ($xoopsModule && ($xoopsModule->name() == "Content" || $xoopsModule->dirname() == "content") && isset($_GET['id'])) {
            $result = $xoopsDB->query("SELECT CASE parent_id WHEN 0 THEN storyid ELSE parent_id END 'sortorder' FROM "
                            . $xoopsDB->prefix('content')
                            . " WHERE visible='1' AND storyid="
                            . $_GET['id']);
            list($currentParent) = $xoopsDB->fetchRow($result);
        }
        
        $result = $xoopsDB->query("SELECT storyid, blockid, title, visible, parent_id, address, blockid AS 'menu_block', parent_id AS 'menu_id' FROM "
                        . $xoopsDB->prefix('content')
                        . " WHERE visible=1 AND parent_id = 0 ORDER BY menu_block, menu_id, parent_id, blockid");
    
        global $j;
        $menu = [];
        while ($tcontent = $xoopsDB->fetchArray($result)) {
            if ($tcontent['parent_id'] == 0) {
                $menu[] = [
                'text' => $myts->makeTboxData4Show($tcontent['title']),
                'url' => XOOPS_URL . "/modules/content/index.php?id=" . $tcontent['storyid'],
                'priority' => $tcontent['menu_block'],
                'id' => $tcontent['storyid'],
                'type' => "content"
                ];
                $j = count($menu);
            } else {
                $menu[$j-1]['sublinks'][] = [
                                  'text' => $myts->makeTboxData4Show($tcontent['title']),
                                  'url' => XOOPS_URL . "/modules/content/index.php?id=" . $tcontent['storyid']
                                  ];
            }
        }
        
        $allmenus = array_merge($menuModule, $menu);
        
        foreach ($allmenus as $key => $row) {
            $priority[$key]  = $row['priority'];
        }

        array_multisort($priority, SORT_ASC, $allmenus);
        echo "" . showMenu() . "
			 <table width='100%' border='0' cellpadding='0' cellspacing='1' class='outer'>
			 	<tr class='even'>
					<td><strong>" . _AM_CONTENT_LINKNAME . "</strong></td>
					<td align='center'><strong>" . _AM_CONTENT_LINKID . "</strong></td>
					<td align='center'><strong>" . _AM_CONTENT_CNTTYP . "</strong></td>
				</tr>
				<form action='?op=order' method='post'><input type='hidden' name='total' value='" . count($allmenus) . "'>";
        $i = 1;
        foreach ($allmenus as $menuitem) {
            echo "
				<tr class='odd'>
					<td><input type='hidden' name='id" . $i . "' value='" . $menuitem['id'] . "'><a href='" . $menuitem['url'] . "'>" . $menuitem['text'] . "</a></td>
					<td align='center'><input type='text' name='priority" . $i . "' value='" . $menuitem['priority'] . "'size='3'></td>
					<td align='center'><input type='hidden' name='type" . $i . "' value='" . $menuitem['type'] . "'>" . $menuitem['type'] . "</td>
				</tr>\n";
            $i++;
        }
        echo "
				<tr class='even'>
					<td colspan='3' align='center'><input type='submit' value='Submit'></td>
			  	</tr></form>
			</table><br>";
        require_once __DIR__ . '/footer.php';
    }
