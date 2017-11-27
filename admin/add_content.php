<?php

include_once 'admin_header.php';

// ------------------------------------------------------------------------- //
// Switch Statement for the different operations                             //
// ------------------------------------------------------------------------- //
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
global $op, $showshort,$_POST;


if ($op == 'add' || $op == 'link') {
    $myts        = MyTextSanitizer::getInstance();

    $title       = $myts->makeTboxData4Save($title);
    $ptitle      = $myts->makeTboxData4Save($ptitle);
    $message     = $myts->makeTboxData4Save($message);
    $keywords    = $myts->makeTboxData4Save($keywords);
    $description = $myts->makeTboxData4Save($description);
    $externalURL = $myts->makeTboxData4Save($externalURL);
    
    if ($externalURL=='') {
        $externalURL='';
    }
    
    $result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('content') . '');
    $rows   = mysqli_num_rows($result);

    $hp = ($rows == 0)? 1 : 0;
    
    if ($_FILES[imageupload]) {
        $uploadpath      = XOOPS_ROOT_PATH . '/modules/content/headers/';
        $source          = $_FILES[imageupload][tmp_name];
        $fileupload_name = $_FILES[imageupload][name];
        if (($source != 'none') && ($source != '')) {
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
    
    $sqlinsert= 'INSERT INTO '
                . $xoopsDB->prefix('content')
                . " (parent_id, ptitle, title, keywords, page_description, text, visible, homepage, nohtml, nosmiley, nobreaks, nocomments, link, address, submenu, newwindow, date, assoc_module, header_img) VALUES ('"
                . intval($parent_id)
                . "','"
                . $ptitle
                . "','"
                . $title
                . "','"
                . $keywords
                . "','"
                . $description
                . "','"
                . $message
                . "','"
                . intval($visible)
                . "','"
                . $hp
                . "','"
                . intval($nohtml)
                . "','"
                . intval($nosmiley)
                . "','"
                . intval($nobreaks)
                . "', '"
                . intval($nocomments)
                . "','0','"
                . $externalURL
                . "','"
                . intval($submenu)
                . "','"
                . intval($newwindow)
                . "',NOW(),'"
                . intval($assoc_module)
                . "', '"
                . $header_img
                . "')";
    
    if (!$result = $xoopsDB->query($sqlinsert)) {
        echo _AM_CONTENT_ERRORINSERT;
    }
    $newId = $xoopsDB->getInsertId();
    
    if ($xoopsModuleConfig['cont_permits_advnaced'] > 0) {
        $module_handler   = xoops_getHandler('module');
        $groupPermHandler = xoops_getHandler('groupperm');
        $module           = $module_handler->getByDirname('content');
        
        foreach ($group_read_perms as $group) {
            $groupPermHandler->addRight('content_page_view', $newId, $group, $module->getVar('mid'));
        }
        if ($xoopsModuleConfig['cont_permits_advnaced'] == 2) {
            foreach ($group_write_perms as $group) {
                $groupPermHandler->addRight('content_page_write', $newId, $group, $module->getVar('mid'));
            }
        }
    }
    
    
    if (isset($return) && $return == 1) {
        echo "<script>window.opener.location.href='/modules/content/index.php?id=" . $newId . "';window.close();</script>";
    } else {
        redirect_header('manage_content.php' . (isset($showshort) ? '?showshort=' . $showshort : ''), 2, _AM_CONTENT_DBUPDATED);
    }
} elseif ($op == 'pagewrap') {
    $myts = MyTextSanitizer::getInstance();
    
    if ($_FILES[fileupload]) {
        $uploadpath      = XOOPS_ROOT_PATH . '/modules/content/content/';
        $source          = $_FILES[fileupload][tmp_name];
        $fileupload_name = $_FILES[fileupload][name];
        if (($source != 'none') && ($source != '')) {
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
    
    $title       = $myts->makeTboxData4Save($title);
    $address     = $myts->makeTboxData4Save($address);
    $keywords    = $myts->makeTboxData4Save($keywords);
    $description = $myts->makeTboxData4Save($description);
    
    $result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('content') . '');
    $rows   = mysqli_num_rows($result);

    $hp = ($rows == 0) ? 1 : 0;

    $sqlinsert= 'INSERT INTO '
                . $xoopsDB->prefix('content')
                . " (parent_id,title,keywords, page_description ,text,visible,homepage,nohtml,nosmiley,nocomments,link,address,submenu,date) VALUES ('"
                . intval($parent_id)
                . "','"
                . $title
                . "','"
                . $keywords
                . "','"
                . $description
                . "','0','"
                . intval($visible)
                . "','"
                . $hp
                . "','0','0','"
                . intval($nocomments)
                . "','1','"
                . $address
                . "','"
                . intval($submenu)
                . "', NOW())";
    if (!$result = $xoopsDB->query($sqlinsert)) {
        echo _AM_CONTENT_ERRORINSERT;
    }
    $newId = $xoopsDB->getInsertId();

    if ($xoopsModuleConfig['cont_permits_advnaced'] > 0) {
        $module_handler   = xoops_getHandler('module');
        $groupPermHandler = xoops_getHandler('groupperm');
        $module           = $module_handler->getByDirname('content');
        
        foreach ($group_read_perms as $group) {
            $groupPermHandler->addRight('content_page_view', $newId, $group, $module->getVar('mid'));
        }
        if ($xoopsModuleConfig['cont_permits_advnaced'] == 2) {
            foreach ($group_write_perms as $group) {
                $groupPermHandler->addRight('content_page_write', $newId, $group, $module->getVar('mid'));
            }
        }
    }

    if (isset($return) && $return == 1) {
        echo "<script>window.opener.location.href='/modules/content/index.php?id=" . $newId . "';window.close();</script>";
    } else {
        redirect_header('manage_content.php' . (isset($showshort) ? '?showshort=' . $showshort : ''), 2, _AM_CONTENT_DBUPDATED);
    }
} else {
    // ------------------------------------------------------------------------- //
    // Show add content Page                                                     //
    // ------------------------------------------------------------------------- //
    global $xoopsDB, $xoopsModuleConfig, $xoopsUser, $xoopsModule, $_GET;
    $menuModule     = [];
    $module_handler = xoops_getHandler('module');
    $criteria       = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('isactive', 1));
    $modules        = $module_handler->getList($criteria);
    asort($modules);
  
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
    echo '<script language="JavaScript" src="../assets/js/prototype.js"></script>';

    $currentParent = 0;
    if (isset($id)) {
        $result = $xoopsDB->query("SELECT CASE parent_id WHEN 0 THEN storyid ELSE parent_id END 'sortorder' FROM "
                        . $xoopsDB->prefix('content')
                        . " WHERE visible='1' AND storyid="
                        . $_GET['id']);
        list($currentParent) = $xoopsDB->fetchRow($result);
    }

    $contentItems = [];
    $result = $xoopsDB->query("SELECT *, blockid AS priority, 'content' AS type FROM "
                        . $xoopsDB->prefix('content')
                              . ' ORDER BY visible DESC, blockid');
    while ($tcontent   = $xoopsDB->fetchArray($result)) {
        $contentItems[] = $tcontent;
    }
    $allMenuItems     = return_children($contentItems, 0);

    $form             = new XoopsThemeForm(_AM_CONTENT_ADDCONTENT, 'form_name', 'add_content.php');
    $categoria_select = new XoopsFormSelect(_AM_CONTENT_POSITION, 'parent_id', $currentParent);
    $categoria_select->addOption('', _AM_CONTENT_MAINMENU);
    foreach ($allMenuItems as $ct_item) {
        $categoria_select->addOption($ct_item['storyid'], str_repeat('&nbsp;&nbsp;', $ct_item['depth'] + 1) . str_repeat('-', $ct_item['depth']) . $ct_item['title']);
    }

    $text_box    = new XoopsFormText(_AM_CONTENT_LINKNAME, 'title', 50, 255);
    
    $opProcedure = new XoopsFormRadio(_AM_CONTENT_CNTTYP, 'op');
    $opProcedure->addOption('add', 'Content');
    $opProcedure->addOption('link', 'Link');
    $opProcedure->addOption('pagewrap', 'Pagewrap');

    $ptext_box       = new XoopsFormText(_AM_CONTENT_PAGENAME, 'ptitle', 50, 255);
    $keywords_box    = new XoopsFormText(_AM_CONTENT_KEYWORDS, 'keywords', 50, 255);
    $description_box = new XoopsFormTextArea(_AM_CONTENT_PAGEDESCRIPTION, 'description', '', 5, 80);

    $url_box         = new XoopsFormText(_AM_CONTENT_EXTURL, 'externalURL', 50, 255);

    $newwindow_checkbox = new XoopsFormCheckBox('', 'newwindow', 0);
    $newwindow_checkbox->addOption(1, _AM_CONTENT_NEWWINDOW);

    $visible_checkbox = new XoopsFormCheckBox('', 'visible', 1);
    $visible_checkbox->addOption(1, _AM_CONTENT_VISIBLE);

    include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

    $editor_configs           = [];
    $editor_configs['name']   = 'message';
    $editor_configs['value']  = isset($message)?$message:'';
    $editor_configs['rows']   = isset($rows) ? $rows : 35;
    $editor_configs['cols']   = isset($cols) ? $cols : 60;
    $editor_configs['width']  = '100%';
    $editor_configs['height'] = '400px';
        
        
    if (isset($message)) {
        $editor_configs['value'] = $message;
    }
    $editor = new XoopsFormEditor(_AM_CONTENT_CONTENT, $xoopsModuleConfig['cont_form_options'], $editor_configs);

        
    //user permissions
    $module_handler   = xoops_getHandler('module');
    $groupPermHandler = xoops_getHandler('groupperm');
    $module           = $module_handler->getByDirname('content');
    
    $readpermits  = new XoopsFormSelectGroup(_AM_CONTENT_PERMITREAD, 'group_read_perms', true, 1, 4, true);
    $writepermits = new XoopsFormSelectGroup(_AM_CONTENT_PERMITWRITE, 'group_write_perms', true, 1, 4, true);
    
    $option_tray = new XoopsFormElementTray(_OPTIONS, '<br>');
    $option_tray->addElement($newwindow_checkbox);
    $option_tray->addElement($visible_checkbox);
    
    if ($xoopsModuleConfig['cont_form_options'] != 'textarea') {
        $nohtmlb = new XoopsFormHidden(_DISABLEHTML, 0);
        $nosmile = new XoopsFormHidden(_DISABLESMILEY, 0);
    } else {
        $nohtml_checkbox = new XoopsFormCheckBox('', 'nohtml', 0);
        $nohtml_checkbox->addOption(1, _DISABLEHTML);
        $option_tray->addElement($nohtml_checkbox);
    }
    if ($xoopsModuleConfig['cont_form_options'] != 'textarea') {
        $form->addElement(new XoopsFormHidden('nobreaks', 1));
    } else {
        $breaks_checkbox = new XoopsFormCheckBox('', 'nobreaks', 0);
        $breaks_checkbox->addOption(1, _AM_CONTENT_DISABLEBREAKS);
        $option_tray->addElement($breaks_checkbox);
    }

    $comments_checkbox = new XoopsFormCheckBox('', 'nocomments', 0);
    $comments_checkbox->addOption(1, _AM_CONTENT_DISABLECOM);
    $option_tray->addElement($comments_checkbox);

    if (isset($return) && $return == 1) {
        $return_field = new XoopsFormHidden('return', 1);
    }

    $submit = new XoopsFormButton(_SUBMIT, 'submit', _SUBMIT, 'submit');
    
    $modules_select = new XoopsFormSelect(_AM_CONTENT_MODULENAME, 'assoc_module');
    $modules_select->addOption('', _AM_CONTENT_NONE);
    
    foreach ($modules as $key => $value) {
        $modules_select->addOption($key, $value);
    }
    
    $address_select = new XoopsFormSelect(_AM_CONTENT_SELECTFILE, 'address');
    $address_select->addOption('', _AM_CONTENT_NONE);
    $folder = dir('../content/');
    while ($file = $folder->read()) {
        if ($file != '.' && $file != '..') {
            $address_select->addOption($file, '' . $file . '');
        }
    }
    $folder->close();
    $uplfile = new XoopsFormFile(_AM_CONTENT_UPLOADFILE, 'fileupload', 500000);
    
    $header_img = new XoopsFormSelect(_AM_CONTENT_SELECTIMG, 'header_img');
    $folder = dir('../headers/');
    $header_img->addOption('', _AM_CONTENT_NONE);
    while ($file = $folder->read()) {
        if ($file != '.' && $file != '..') {
            $header_img->addOption($file, '' . $file . '');
        }
    }
    $folder->close();
    $uplimage = new XoopsFormFile(_AM_CONTENT_UPLOADIMG, 'imageupload', 500000);
    
    echo showMenu();
    echo "<table class='outer' style='width:100%;border:0;padding:0;border-spacing:1;'>";
    echo '<form action="add_content.php" method="post" name="ctform" id="ctform" enctype="multipart/form-data">';
    show_form_line($categoria_select);
    show_form_line($text_box);
    echo '	<tr>
				<td class="even" style="vertical-align:top;width:170;"><strong>' . _AM_CONTENT_CNTTYP . '</strong></td>
				<td class="even">
					<select id="op" name="op" onchange="showform(this.options[this.selectedIndex].value)">
						<option value="">Please Select</option>
						<option value="add">Content</option>
						<option value="link">Link</option>
						<option value="pagewrap">Pagewrap</option>
					</select></td>
		  	</tr>';
    
    echo '<tbody id="link" style="display:none;">';
    show_form_line($url_box);
    show_form_line($modules_select);
    show_form_line($option_tray);
    echo '</tbody>';

    echo '<tbody id="contentt" style="display:none;">';
    show_form_line($ptext_box);
    show_form_line($keywords_box);
    show_form_line($description_box);
    show_form_line($header_img);
    show_form_line($uplimage);
    show_form_line($editor);
    show_form_line($option_tray);
    echo '</tbody>';

    echo '<tbody id="pagewrap" style="display:none;">';
        
    show_form_line($address_select);
    show_form_line($uplfile);
    show_form_line($option_tray);
        
    echo '</tbody>';

    echo '<tbody id="both" style="display:none;">';
        
    if ($xoopsModuleConfig['cont_permits_advnaced'] > 0) {
        show_form_line($readpermits);
        if ($xoopsModuleConfig['cont_permits_advnaced'] == 2) {
            show_form_line($writepermits);
        }
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
    if (isset($nohtmlb)) {
        echo $nohtmlb->render() . $nosmile->render();
    }
    if (isset($return_field)) {
        echo $return_field->render();
    }
    if (isset($showshort)) {
        echo "<input type='hidden' name='showshort' value='" . $showshort . "' />";
    }
    echo '</form><br>';

    require_once __DIR__ . '/footer.php';
}
