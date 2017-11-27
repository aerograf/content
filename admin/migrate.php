<?php

    include_once 'admin_header.php';
    if ($HTTP_POST_VARS['submit'] == 'Yes') {
        $xoopsDB->queryF('DELETE FROM '
                         . $xoopsDB->prefix('content'));
        if (!$result = $xoopsDB->query('INSERT INTO '
                                       . $xoopsDB->prefix('content')
                                       . ' (SELECT * FROM '
                                       . $xoopsDB->prefix('xt_conteudo')
                                       . ' )')) {
            redirect_header('migrate.php', 2, _AM_CONTENT_ERRORINSERT);
        } else {
            redirect_header('index.php', 2, _AM_CONTENT_DBUPDATED);
        }
    } else {
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));
        echo '<h4>' . _AM_CONTENT_ADMINTITLE . "</h4><table border='0' cellpadding='0' cellspacing='1' class='outer'>";
        echo "<form action='migrate.php' method='post'><tr class='even'><td>Migrating data will delete all content from the content database and replace it with all content in the XT Conteudo database.  Would you like to continue?</td></tr>";
        echo "<tr class='odd'><td align='center'><input type='submit' name='submit' value='Yes'>&nbsp;&nbsp;&nbsp;<input type='button' value='No' onClick=\"location.href='index.php'\"></td></tr>";
        echo '</form></table><br>';
        require_once __DIR__ . '/footer.php';
    }
