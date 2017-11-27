<?php

$pathIcon32 = Xmf\Module\Admin::iconUrl('', 32);
if (!class_exists('\Xoops', false)) {
    echo "<div class='adminfooter'>\n" . "  <div style='text-align:center;'>\n" . "    <a href='https://xoops.org' rel='external'><img src='{$pathIcon32}xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS'></a>\n" . "  </div>\n" . '  ' . _AM_MODULEADMIN_ADMIN_FOOTER . "\n" . "</div>\n";
}

xoops_cp_footer();
