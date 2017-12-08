<?php

include_once dirname(dirname(__DIR__)) . '/mainfile.php';

$GLOBALS['xoopsOption']['template_main'] = 'ct_index.tpl';

include_once XOOPS_ROOT_PATH . '/header.php';
$tabdata = tabMaker();
$xoopsTpl->assign('tabs', $tabdata);

/**
 * @return array
 */
function tabMaker()
{
    global $xoopsDB;
    $thisId = $_REQUEST['id'];
    $query1 = $xoopsDB->query('SELECT parent_id FROM '
                              . $xoopsDB->prefix('content')
                              . ' WHERE storyid='
                              . $thisId);
    while ($myrow1 = $xoopsDB->fetchArray($query1)) {
        $thisId_parent = $myrow1['parent_id'];
    }

    $tabs = [];
    $query = $xoopsDB->query('SELECT storyid, parent_id, blockid, submenu, title, visible FROM '
                             . $xoopsDB->prefix('content')
                             . ' WHERE storyid=' . $thisId
                             . ' OR parent_id=' . $thisId
                             . ' OR storyid=' . $thisId_parent
                             . ' OR parent_id= CASE WHEN ' . $thisId_parent . ' >0 THEN ' . $thisId_parent . ' ELSE ' . $thisId
                             . ' END AND visible=1 ORDER BY parent_id, blockid');
    $q=1;
    while ($myrow = $xoopsDB->fetchArray($query)) {
        $visible = $myrow['visible'];
        if (1 == $visible) {
        $tabs[$q]['storyid']   = $myrow['storyid'];
        $tabs[$q]['parent_id'] = $myrow['parent_id'];
        $tabs[$q]['blockid'] = $myrow['blockid'];
        $tabs[$q]['submenu']   = $myrow['submenu'];
        $tabs[$q]['title']     = $myrow['title'];
        $q++;
        } else {
        }
    }
    return $tabs;
}

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

if (0 != $id) {
    $result = $xoopsDB->queryF('SELECT storyid, title, text, visible, nohtml, nosmiley, nobreaks, nocomments, link, address FROM '
                               . $xoopsDB->prefix('content')
                               . ' WHERE storyid='
                               . $id);
} else {
    $result = $xoopsDB->queryF('SELECT storyid FROM '
                               . $xoopsDB->prefix('content')
                               . ' WHERE homepage=1');
    list($storyid) = $xoopsDB->fetchRow($result);
    $link_restore  = 'Location: index.php?id=' . $storyid;
    header($link_restore, true, 301);
    exit();
}

list($storyid, $title, $text, $visible, $nohtml, $nosmiley, $nobreaks, $nocomments, $link, $address) = $xoopsDB->fetchRow($result);

    global $xoopsModuleConfig, $xoopsModule, $xoopsUser, $xoopsConfig;
    $moduleHandler = xoops_getHandler('module');

    $id          = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 0;

    $contentItems          = [];
    $groupPermHandler      = xoops_getHandler('groupperm');
    $module                = $moduleHandler->getByDirname('content');
    $xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;
    $allowedItems          = $groupPermHandler->getItemIds('content_page_view', $groups, $module->getVar('mid'));

    if (0 != $id && !in_array($id, $allowedItems)) {
        redirect_header(XOOPS_URL, 2, _NOPERM, true);
    }

    $result = $xoopsDB->query('SELECT storyid, title, parent_id FROM ' . $xoopsDB->prefix('content'));

    while ($item = $xoopsDB->fetchArray($result)) {
        $allItems[] = $item;
    }

    if (0 != $id) {
        $result = $xoopsDB->queryF('SELECT storyid, ptitle, title, keywords,page_description,text, visible, nohtml, nosmiley, nobreaks, nocomments, link, address, date, header_img FROM '
                                   . $xoopsDB->prefix('content')
                                   . ' WHERE storyid='
                                   . $id);
    } else {
        $result = $xoopsDB->queryF('SELECT storyid, ptitle, title, keywords,page_description, text, visible, nohtml, nosmiley, nobreaks, nocomments, link, address, date, header_img FROM '
                                   . $xoopsDB->prefix('content')
                                   . ' WHERE homepage=1');
    }
    $showerror= isset($_GET['showerror'])? (int)$_GET['showerror'] :0;
    if (1 == $showerror || 0 == $xoopsDB->getRowsNum($result)) {
        $result = $xoopsDB->queryF('SELECT storyid, ptitle, title, keywords,page_description, text, visible, nohtml, nosmiley, nobreaks, nocomments, link, address, date, header_img FROM '
                                   . $xoopsDB->prefix('content')
                                   . ' WHERE epage=1');
        list($myid, $ptitle, $title, $keywords, $description, $text, $visible, $nohtml, $nosmiley, $nobreaks, $nocomments,
             $link, $address, $date, $header) = $xoopsDB->fetchRow($result);
        $id = $myid;
    } else {
        list($storyid, $ptitle, $title, $keywords, $description, $text, $visible, $nohtml, $nosmiley, $nobreaks, $nocomments,
             $link, $address, $date, $header) = $xoopsDB->fetchRow($result);
        $id = $storyid;
    }
    //---- Link to mail & print & image ----
    $link_mail     = 'mailto:?subject='
                   . sprintf(_MD_CONTENT_INTARTIGO, $xoopsConfig['sitename'])
                   . '&amp;body='
                   . sprintf(_MD_CONTENT_INTARTFOUND, $xoopsConfig['sitename'])
                   . ':  '
                   . XOOPS_URL
                   . '/modules/content/index.php?id='
                   . $id;
    $link_print    = 'print.php?id=' . $id;
    $link_image    = XOOPS_URL . '/modules/content/assets/images/';
    $link_addpage  = XOOPS_URL . '/modules/content/admin/add_content.php?id=' . $id . '&return=1&showshort=1';
    $link_editpage = XOOPS_URL . '/modules/content/edit_content.php?id=' . $id . '&return=1&showshort=1';
    
    if (1 == $link) {
        $includeContent = XOOPS_ROOT_PATH . '/modules/content/content/' . $address;
        if (file_exists($includeContent)) {
            ob_start();
            include $includeContent;
            $content = ob_get_contents();
            ob_end_clean();

            $xoopsTpl->assign('xoops_pagetitle', $title);

            if (1 == $xoopsModuleConfig['cont_title']) {
                if (isset($ptitle)) {
                    $xoopsTpl->assign('title', $ptitle);
                } else {
                    $xoopsTpl->assign('title', $title);
                }
            }

    if (is_object($xoTheme)) {
			$xoTheme->addMeta('meta', 'keywords', $keywords);
			$xoTheme->addMeta('meta', 'description', $description);
		} else {
			$xoopsTpl->assign('xoops_meta_keywords','keywords', $keywords);
			$xoopsTpl->assign('xoops_meta_description', $description);
		}

            $xoopsTpl->assign('content', $content);
            $xoopsTpl->assign('nocomments', $nocomments);
            $xoopsTpl->assign('mail_link', $link_mail);
            $xoopsTpl->assign('link_print', $link_print);
            $xoopsTpl->assign('lang_printerpage', _MD_CONTENT_PRINTERFRIENDLY);
            $xoopsTpl->assign('lang_sendstory', _MD_CONTENT_SENDSTORY);
            $xoopsTpl->assign('link_image', $link_image);
            $xoopsTpl->assign('link_addpage', $link_addpage);
            $xoopsTpl->assign('link_editpage', $link_editpage);
            $xoopsTpl->assign('date', $date);
            $xoopsTpl->assign('pagewrap', 1);
        } else {
            redirect_header('index.php', 1, _MD_CONTENT_FILENOTFOUND);
        }
    } else {
        //Should we show crumbs
        if (1 == $xoopsModuleConfig['cont_crumbs']) {
            $xoopsTpl->assign('breadcrumbs', array_reverse(backOneLevel($allItems, $id)));
        }

        //Should we redirect or continue with this page
        if (isset($address) && strlen($address) > 0) {
            header('location: ' . $address, true, 301);
            //echo $address;
            exit;
        }

        //$GLOBALS['xoopsOption']['template_main'] = 'ct_index.tpl';

        (isset($nohtml) && 1 == $nohtml) ? $html = 0 : $html = 1;
        (isset($nosmiley) && 1 == $nosmiley) ? $smiley = 0 : $smiley = 1;
        (isset($nobreaks) && 1 == $nobreaks) ? $breaks = 0 : $breaks = 1;

        $myts = MyTextSanitizer::getInstance();

        $contentPages = explode('[pagebreak]', $text);
        $pageCount    = count($contentPages);

        //split up the pages
        if ($pageCount > 1) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $nav = new XoopsPageNav($pageCount, 1, $currentPage, 'page', "id=$id");
            $xoopsTpl->assign('nav', $nav->renderNav());
            $xoopsTpl->assign('content', $contentPages[$currentPage]);
        } else {
            $xoopsTpl->assign('content', stripslashes($text));
        }
        $xoopsTpl->assign('xoops_pagetitle', $title);

        if (1 == $xoopsModuleConfig['cont_title']) {
            if (isset($ptitle)) {
                $xoopsTpl->assign('title', $ptitle);
            } else {
                $xoopsTpl->assign('title', $title);
            }
        }
        if (isset($header)) {
            $xoopsTpl->assign('link_header', XOOPS_URL . '/modules/content/headers/');
            $xoopsTpl->assign('header_image', $header);
        }
        
    if (is_object($xoTheme)) {
			$xoTheme->addMeta( 'meta', 'keywords', $keywords);
			$xoTheme->addMeta( 'meta', 'description', $description);
		} else {
			$xoopsTpl->assign('xoops_meta_keywords','keywords', $keywords);
			$xoopsTpl->assign('xoops_meta_description', $description);
		}

        $xoopsTpl->assign('nocomments', $nocomments);
        $xoopsTpl->assign('mail_link', $link_mail);
        $xoopsTpl->assign('link_print', $link_print);
        $xoopsTpl->assign('lang_printerpage', _MD_CONTENT_PRINTERFRIENDLY);
        $xoopsTpl->assign('lang_sendstory', _MD_CONTENT_SENDSTORY);
        $xoopsTpl->assign('link_image', $link_image);
        $xoopsTpl->assign('link_addpage', $link_addpage);
        $xoopsTpl->assign('link_editpage', $link_editpage);
        $xoopsTpl->assign('date', $date);
    }

$xoopsTpl->assign('id', $id);

include XOOPS_ROOT_PATH . '/include/comment_view.php';
include_once XOOPS_ROOT_PATH . '/footer.php';

/**
 * @param $items
 * @param $ctid
 * @return array
 */
function backOneLevel($items, $ctid)
{
    foreach ($items as $item) {
        if ($item['storyid'] == $ctid) {
            $crumbsout[] = $item;
            if (0 != $item['parent_id']) {
                $crumbsout = array_merge($crumbsout, backOneLevel($items, $item['parent_id']));
            }
            return $crumbsout;
        }
    }
}
