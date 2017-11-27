<?php

include_once __DIR__ . '/admin_header.php';

// ------------------------------------------------------------------------- //
// Switch Statement for the different operations                             //
// ------------------------------------------------------------------------- //
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
global $op, $showshort,$xoopsModuleConfig;
$menuModule     = [];
$moduleHandler = xoops_getHandler('module');
$criteria       = new CriteriaCompo(new Criteria('hasmain', 1));
$criteria->add(new Criteria('isactive', 1));
$modules        = $moduleHandler->getList($criteria);
asort($modules);


$groupPermHandler      = xoops_getHandler('groupperm');
$module                = $moduleHandler->getByDirname('content');
$xoopsUser ? $groups = $xoopsUser->getGroups() : $groups = XOOPS_GROUP_ANONYMOUS;

$allowedItems = $groupPermHandler->getItemIds('content_page_write', $groups, $module->getVar('mid'));

if (!$groupPermHandler->checkRight('content_page_write', $id, $groups, $module->getVar('mid'))) {
    redirect_header(XOOPS_URL, 2, _NOPERM, false);
}
// ------------------------------------------------------------------------- //
// Do the edit of the Content                                                //
// ------------------------------------------------------------------------- //
if ('add' === $op || 'link' === $op) {
    $myts = MyTextSanitizer::getInstance();

    $title       = $myts->htmlSpecialChars($title);
    $ptitle      = $myts->htmlSpecialChars($ptitle);
    $keywords    = $myts->htmlSpecialChars($keywords);
    $description = $myts->htmlSpecialChars($description);
    $nohtml      = isset($nohtml)? (int)$nohtml :0;
    $newwindow   = isset($newwindow)? (int)$newwindow :0;
    $nosmiley    = isset($nosmiley)? (int)$nosmiley :0;
    $nobreaks    = isset($nobreaks)? (int)$nobreaks :0;
    $submenu     = isset($submenu)? (int)$submenu :0;
    
    if ($_FILES['imageupload']) {
        $uploadpath      = XOOPS_ROOT_PATH . '/modules/content/headers/';
        $source          = $_FILES['imageupload']['tmp_name'];
        $fileupload_name = $_FILES['imageupload']['name'];
        if (('none' !== $source) && ('' != $source)) {
            $dest=$uploadpath.$fileupload_name;
            if (file_exists($uploadpath.$fileupload_name)) {
                redirect_header('add_content.php', 2, _AM_CONTENT_ERRORUPL);
            } else {
                if (copy($source, $dest)) {
                    $header_img = $fileupload_name;
                } else {
                    redirect_header('add_content.php', 2, _AM_CONTENT_ERRORUPL);
                }
                unlink($source);
            }
        }
    }
    
    if ('add' === $op) {
        $externalURL = '';
    }
    
    $sqlinsert= 'UPDATE '
                . $xoopsDB->prefix('content')
                . " SET page_description='"
                . addslashes($description)
                . "', keywords='"
                . $keywords
                . "', parent_id='"
                . (int)$parent_id . "', title='"
                . $title
                . "', ptitle='"
                . $ptitle
                . "', text='"
                . addslashes($message)
                . "', visible='"
                . (int)$visible . "', nohtml='"
                . (int)$nohtml . "', nosmiley='"
                . (int)$nosmiley . "', nobreaks='"
                . (int)$nobreaks . "', nocomments='"
                . (int)$nocomments . "', address='"
                . $externalURL
                . "', submenu='"
                . (int)$submenu . "', newwindow='"
                . (int)$newwindow . "', date=NOW(), link=0, header_img='"
                . $header_img
                . "' WHERE storyid='"
                . (int)$id . "'";
    
    if (!$result = $xoopsDB->query($sqlinsert)) {
        echo _AM_CONTENT_ERRORINSERT;
    }
    if (isset($return) && 1 == $return) {
        echo '<script>window.opener.location.reload(true);window.close();</script>';
    } else {
        redirect_header('manage_content.php' . (isset($showshort) ? '?showshort=' . $showshort : ''), 2, _AM_CONTENT_DBUPDATED);
    }
} elseif ('pagewrap' === $op) {
    $myts = MyTextSanitizer::getInstance();
    
    $title       = $myts->htmlSpecialChars($title);
    $address     = $myts->htmlSpecialChars($address);
    $keywords    = $myts->htmlSpecialChars($keywords);
    $description = $myts->htmlSpecialChars($description);
    
    
    if ($_FILES[fileupload]) {
        $uploadpath      = XOOPS_ROOT_PATH . '/modules/content/content/';
        $source          = $_FILES[fileupload][tmp_name];
        $fileupload_name = $_FILES[fileupload][name];
        if (('none' !== $source) && ('' != $source)) {
            $dest=$uploadpath.$fileupload_name;
            if (file_exists($uploadpath.$fileupload_name)) {
                redirect_header('add_content.php', 2, _AM_CONTENT_ERRORUPL);
            } else {
                if (copy($source, $dest)) {
                    $address = $fileupload_name;
                } else {
                    redirect_header('add_content.php', 2, _AM_CONTENT_ERRORUPL);
                }
                unlink($source);
            }
        }
    }
    
    $sqlinsert = 'UPDATE '
                 . $xoopsDB->prefix('content')
                 . " SET page_description='"
                 . $description
                 . "', keywords='"
                 . $keywords
                 . "',parent_id='"
                 . (int)$parent_id . "', title='"
                 . $title
                 . "', visible='"
                 . (int)$visible . "', nocomments='"
                 . (int)$nocomments . "', address='"
                 . $address
                 . "', submenu='"
                 . $submenu
                 . "', date=NOW(), link=1 WHERE storyid='"
                 . (int)$id . "'";
    if (!$result = $xoopsDB->query($sqlinsert)) {
        echo _AM_CONTENT_ERRORINSERT;
    }
    redirect_header('manage_content.php' . (isset($showshort) ? '?showshort=' . $showshort : ''), 2, _AM_CONTENT_DBUPDATED);
} else {
    // ------------------------------------------------------------------------- //
    // Show Edit Content Page                                                    //
    // ------------------------------------------------------------------------- //

    global $xoopsDB, $xoopsModuleConfig, $op, $showshort,$myts;
    include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

    $myts = MyTextSanitizer::getInstance();

    $result = $xoopsDB->query('SELECT storyid, parent_id, ptitle, title, keywords, page_description, text, visible, nohtml, nosmiley, nobreaks, nocomments, address, submenu, newwindow, assoc_module, link, header_img FROM '
                              . $xoopsDB->prefix('content')
                              . ' WHERE storyid='
                              . (int)$id);

    list($storyid, $parent_id, $ptitle, $title, $keywords, $description, $text, $visible, $nohtml, $nosmiley, $nobreaks, $nocomments, $externalURL, $submenu, $newwindow, $assoc_module, $link, $header_img) = $xoopsDB->fetchRow($result);

    $contentItems = [];
    $result       = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM "
                            . $xoopsDB->prefix('content')
                                    . ' ORDER BY visible DESC, blockid');
    while ($tcontent   = $xoopsDB->fetchArray($result)) {
        $contentItems[] = $tcontent;
    }
    $allMenuItems = return_children($contentItems, 0);
    
    $title       = $myts->htmlSpecialChars($title);
    $message     = $text;
    $keywords    = $myts->htmlSpecialChars($keywords);
    $description = $myts->htmlSpecialChars(stripslashes($description));
    
    $form             = new XoopsThemeForm(_AM_CONTENT_EDITCONTENT, 'form_name', 'edit_content.php');
    $categoria_select = new XoopsFormSelect(_AM_CONTENT_POSITION, 'parent_id', $parent_id);
    $categoria_select->addOption('', _AM_CONTENT_MAINMENU);
    foreach ($allMenuItems as $ct_item) {
        $categoria_select->addOption($ct_item['storyid'], str_repeat('&nbsp;&nbsp;', $ct_item['depth'] + 1) . str_repeat('-', $ct_item['depth']) . $ct_item['title']);
    }
    $form->addElement($categoria_select);
    $text_box  = new XoopsFormText(_AM_CONTENT_LINKNAME, 'title', 50, 255, $title);
    $ptext_box = new XoopsFormText(_AM_CONTENT_PAGENAME, 'ptitle', 50, 255, $ptitle);
    
    $keywords_box    = new XoopsFormText(_AM_CONTENT_KEYWORDS, 'keywords', 50, 255, $keywords);
    $description_box = new XoopsFormTextArea(_AM_CONTENT_PAGEDESCRIPTION, 'description', $description, 5, 80);
    
    $form->addElement($text_box);
    $url_box = new XoopsFormText(_AM_CONTENT_EXTURL, 'externalURL', 50, 255, $externalURL);
    $form->addElement($url_box);
    
    $newwindow_checkbox = new XoopsFormCheckBox('', 'newwindow', $newwindow);
    $newwindow_checkbox->addOption(1, _AM_CONTENT_NEWWINDOW);

    $visible_checkbox = new XoopsFormCheckBox('', 'visible', $visible);
    $visible_checkbox->addOption(1, _AM_CONTENT_VISIBLE);

    
    $editor_configs           = [];
    $editor_configs['name']   = 'message';
    $editor_configs['value']  = stripslashes($message);
    $editor_configs['rows']   = isset($rows) ? $rows : 35;
    $editor_configs['cols']   = isset($cols) ? $cols : 60;
    $editor_configs['width']  = '100%';
    $editor_configs['height'] = '400px';
        
    $editor    = new XoopsFormEditor(_AM_CONTENT_CONTENT, $xoopsModuleConfig['cont_form_options'], $editor_configs);
        
    $option_tray = new XoopsFormElementTray(_OPTIONS, '<br />');
    $option_tray->addElement($newwindow_checkbox);
    $option_tray->addElement($visible_checkbox);
    
    if ('textarea' !== $xoopsModuleConfig['cont_form_options']) {
        $nohtmlb = new XoopsFormHidden(_DISABLEHTML, 0);
        $nosmile = new XoopsFormHidden(_DISABLESMILEY, 0);
    } else {
        $nohtml_checkbox = new XoopsFormCheckBox('', 'nohtml', 0);
        $nohtml_checkbox->addOption(1, _DISABLEHTML);
        $option_tray->addElement($nohtml_checkbox);
    }
    if ('textarea' !== $xoopsModuleConfig['cont_form_options']) {
        $form->addElement(new XoopsFormHidden('nobreaks', 1));
    } else {
        $breaks_checkbox = new XoopsFormCheckBox('', 'nobreaks', 0);
        $breaks_checkbox->addOption(1, _AM_CONTENT_DISABLEBREAKS);
        $option_tray->addElement($breaks_checkbox);
    }

    $comments_checkbox = new XoopsFormCheckBox('', 'nocomments', $nocomments);
    $comments_checkbox->addOption(1, _AM_CONTENT_DISABLECOM);
    $option_tray->addElement($comments_checkbox);
    
    if (isset($return) && 1 == $return) {
        $return_field = new XoopsFormHidden('return', 1);
    }
    
    $editid = new XoopsFormHidden('id', $storyid);
    
    $submit = new XoopsFormButton('', 'submit', _SUBMIT, 'submit');
    
    $modules_select = new XoopsFormSelect(_AM_CONTENT_MODULENAME, 'assoc_module', $assoc_module);
    $modules_select->addOption('', _AM_CONTENT_NONE);
    
    foreach ($modules as $key => $value) {
        $modules_select->addOption($key, $value);
    }
    
    $form->addElement($submit);
    
    $address_select = new XoopsFormSelect(_AM_CONTENT_SELECTFILE, 'address', $externalURL);
    $address_select->addOption('', _AM_CONTENT_NONE);
    $folder       = dir('../content/');
    while ($file     = $folder->read()) {
        if ('.' !== $file && '..' !== $file) {
            $address_select->addOption($file, '' . $file . '');
        }
    }
    $folder->close();
    
    $uplfile = new XoopsFormFile(_AM_CONTENT_UPLOADFILE, 'fileupload', 500000);
    
    $header_img = new XoopsFormSelect(_AM_CONTENT_SELECTIMG, 'header_img', $header_img);
    $folder   = dir(XOOPS_ROOT_PATH . '/modules/content/headers/');
    $header_img->addOption('', _AM_CONTENT_NONE);
    while ($file = $folder->read()) {
        if ('.' !== $file && '..' !== $file) {
            $header_img->addOption($file, '' . $file . '');
        }
    }
    $folder->close();
    $uplimage = new XoopsFormFile(_AM_CONTENT_UPLOADIMG, 'imageupload', 500000);
  
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
  
    $Link_Files  = 'manage_files.php?loc=1&showshort=1';
    $Link_Page   = 'manage_files.php?showshort=1';
    $Link_Del    = '<img src="../assets/images/delete.png" alt=' . _AM_CONTENT_DELETEFILES . ' title=' . _AM_CONTENT_DELETEFILES . '>';
  
    echo "<h4><img src='../assets/images/add.png' alt=" . _AM_CONTENT_ADD . '> ' . _AM_CONTENT_EDIT . ' - ' . _AM_CONTENT_ADMINTITLE . '</h4>' . showMenu();
    echo "<table class='outer' style='width:100%;border:0;padding:0;border-spacing:1;'>";
    echo '<form action="edit_content.php" method="post" name="ctform" id="ctform" enctype="multipart/form-data">';
    show_form_line($categoria_select);
    show_form_line($text_box);
    echo '	<tr>
				<td class="even" style="vertical-align:top;width:170;"><strong>' . _AM_CONTENT_CNTTYP . '</strong></td>
				<td class="even">
					<select id="op" name="op" onchange="showform(this.options[this.selectedIndex].value)">
						<option value="add"' . ((1 != $link && (!isset($externalURL) || '' === trim($externalURL))) ? ' selected' : '') . '>Content</option>
						<option value="link"' . ((1 != $link && isset($externalURL) && strlen(trim($externalURL)) > 0) ? ' selected' : '') . '>Link</option>
						<option value="pagewrap"' . ((1 == $link) ? ' selected' : '') . '>Pagewrap</option>
					</select></td>
		  	</tr>';
    echo '<tbody id="link" ' . ((1 != $link && isset($externalURL) && strlen(trim($externalURL)) > 0) ? '' : ' style="display:none;"') . '>';
    show_form_line($url_box);
    show_form_line($modules_select);
    echo '</tbody>';

    echo '<tbody id="contentt"' . ((1 != $link && (!isset($externalURL) || '' === trim($externalURL))) ? '' : ' style="display:none;"') . '>';
    show_form_line($ptext_box);
    show_form_line($keywords_box);
    show_form_line($description_box);
    echo '<tr>
				<td class="even"><strong>' . $header_img->getCaption() . '</strong></td>
				<td class="even">' . $header_img->render() . '&nbsp;&nbsp;&nbsp;<a href=' . $Link_Files . '>' . $Link_Del . '</a></td>
			  </tr>';
    show_form_line($uplimage);
    show_form_line($editor);
    echo '</tbody>';

    echo '<tbody id="pagewrap" ' . ((1 == $link) ? '' : ' style="display:none;"') . '>';
    echo '<tr>
				<td class="even"><strong>' . $address_select->getCaption() . '</strong></td>
				<td class="even">' . $address_select->render() . '&nbsp;&nbsp;&nbsp;<a href=' . $Link_Page . '>' . $Link_Del . '</a></td>
			  </tr>';
    show_form_line($uplfile);
    echo '</tbody>';

    echo '<tbody id="both">';
    show_form_line($option_tray);
    echo $editid->render();
    echo isset($nohtmlb) ? $nohtmlb->render():'' . isset($nosmile) ? $nosmile->render():'';
    if (isset($return_field)) {
        echo $return_field->render();
    }
    show_form_line($submit);
    echo '</tbody>';

    echo "</table>
		  <script language='JavaScript'>
		  <!--
			function showform(det){
				if (det == 'add'){
					$('contentt').style.display = '';
					if (FCKeditorAPI.GetInstance('message'))
						FCKeditorAPI.GetInstance('message').MakeEditable();
				}else{
					$('contentt').style.display = 'none';
				}
					
				if (det == 'link')
					$('link').style.display = '';
				else
					$('link').style.display = 'none';
				
				if (det == 'pagewrap')
					$('pagewrap').style.display = '';
				else
					$('pagewrap').style.display = 'none';
				
				if (det != '')
					$('both').style.display = '';
				else
					$('both').style.display = 'none';
			}
			
		//-->
		</script>
			";
    if (isset($showshort)) {
        echo "<input type='hidden' name='showshort' value='" . $showshort . "' />";
    }
    echo '</form>';
    echo "  
	<script src='../assets/js/prototype.js' type='text/javascript'/>
	<script>
  <!--
			function newWindow(filePath,winName,winProperties){NewWin = window.open(filePath,winName,winProperties); NewWin.moveTo(50,50); NewWin.focus();}
	//-->
	</script>";

    require_once __DIR__ . '/footer.php';
}
