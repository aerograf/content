<?php

include_once 'admin_header.php';
xoops_cp_header();
// ------------------------------------------------------------------------- //
// Switch Statement for the different operations                             //
// ------------------------------------------------------------------------- //
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
global $op;
switch ($op) {
    // ------------------------------------------------------------------------- //
    // Delete File - Confirmation Question                                    //
    // ------------------------------------------------------------------------- //
    case 'delfile':
    $action  = 'history.go(-1)';
    $hiddens = [
                'address'   => $address,
                'op'        => 'delfileok',
                'loc'       => $loc,
                'showshort' => $showshort
                ];
        xoops_confirm($hiddens, 'manage_files.php', _AM_CONTENT_RUSUREDELF, _YES, true, $action);
        break;
    
    // ------------------------------------------------------------------------- //
    // Delete it definitely                                                      //
    // ------------------------------------------------------------------------- //
    case 'delfileok':
        if ($loc == 1) {
            $dir = XOOPS_ROOT_PATH . '/modules/content/headers/';
        } else {
            $dir = XOOPS_ROOT_PATH . '/modules/content/content/';
        }
        @unlink($dir . '/' . $address);
        xoops_result('<h4>' . _AM_CONTENT_FDELETED . '</h4>');
        echo '<script>
						opts = window.opener.document.ctform["' . (($loc == 1) ? 'header_img' : 'address') . '"].options;
						for (i = 0; opt = opts[i]; i++){
							if ("' . $address . '" == opt.value){
								opts[i] = null;
								break;
							}
						}
				  </script>';
        show_form();
        break;
        
    // ------------------------------------------------------------------------- //
    // Show new link Page                                                        //
    // ------------------------------------------------------------------------- //
    default:
        show_form();
    require_once __DIR__ . '/footer.php';
        break;
}

function show_form()
{
    global $loc, $showshort;
    $form = new XoopsThemeForm(_AM_CONTENT_DELFILE, 'form_name', 'manage_files.php');
    
    $address_select = new XoopsFormSelect(_AM_CONTENT_URL, 'address');
    if ($loc == 1) {
        $folder = dir('../headers/');
    } else {
        $folder = dir('../content/');
    }
    while ($file = $folder->read()) {
        if ($file != '.' && $file != '..') {
            $address_select->addOption($file, $file);
        }
    }
    $folder->close();
    $form->addElement($address_select);
    
    $delfile = 'delfile';
    $form->addElement(new XoopsFormHidden('op', $delfile));
    $form->addElement(new XoopsFormHidden('loc', $loc));
    $form->addElement(new XoopsFormHidden('showshort', $showshort));
    $submit = new XoopsFormButton('', 'submit', _AM_CONTENT_DELETE, 'submit');
    $form->addElement($submit);
    $form->display();
}

require_once __DIR__ . '/footer.php';
