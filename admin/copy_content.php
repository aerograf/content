<?php

include_once 'admin_header.php';

// ------------------------------------------------------------------------- //
// Switch Statement for the different operations                             //
// ------------------------------------------------------------------------- //
$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
global $op;
switch ($op) {
    // ------------------------------------------------------------------------- //
    // Delete it definitely                                                      //
    // ------------------------------------------------------------------------- //
    case 'copy':
        global $xoopsDB;
        $result = $xoopsDB->query('SELECT * FROM '
                                  . $xoopsDB->prefix('content')
                                  . ' WHERE storyid='
                                  . (int)$id);
        $oldrecord = $xoopsDB->fetchArray($result);
        
        foreach ($oldrecord as $key => $value) {
            if ('storyid' != $key) {
                if (isset($dbFields)) {
                    $dbFields .= ', ';
                    $dbValues .= ', ';
                }
                $dbFields .= '`' . $key . '`';
                $dbValues .= "'" . (('title' == $key) ? 'Copy of ' . addslashes($value) : addslashes($value)) . "'";
            }
        }
        
        $result = $xoopsDB->query('INSERT INTO '
                                  . $xoopsDB->prefix('content')
                                  . ' ('
                                  . $dbFields
                                  . ') VALUES ('
                                  . $dbValues
                                  . ')');
        
        $newId = $xoopsDB->getInsertId();

        $module_handler   = xoops_getHandler('module');
        $groupPermHandler = xoops_getHandler('groupperm');
        $module           = $module_handler->getByDirname('content');
        $allowedGroups    = $groupPermHandler->getGroupIds('content_page_view', $id, $module->getVar('mid'));
        
        foreach ($allowedGroups as $group) {
            $groupPermHandler->addRight('content_page_view', $newId, $group, $module->getVar('mid'));
        }

        redirect_header('edit_content.php?id=' . $newId . '&return=' . $return, 2, _AM_CONTENT_DBUPDATED);
        break;
        
    // ------------------------------------------------------------------------- //
    // Delete Content - Confirmation Question                                    //
    // ------------------------------------------------------------------------- //
    default:
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();
    $adminObject->displayNavigation(basename(__FILE__));
        $action      = 'history.go(-1)';
    $hiddens     = [
        'id' => (int)$id,
        'op' => 'copy'
                    ];
        xoops_confirm($hiddens, 'copy_content.php', _AM_CONTENT_COPYCONTENT, _YES, true, $action);
    require_once __DIR__ . '/footer.php';
        break;
}
