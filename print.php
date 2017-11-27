<?php

include __DIR__ . '/../../mainfile.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (empty($id)) {
    redirect_header('index.php');
}

    global $xoopsConfig, $xoopsModule, $xoopsDB;
    $result = $xoopsDB->queryF('SELECT storyid, title, text, visible, nohtml, nosmiley, nobreaks, nocomments, link, address FROM '
                               . $xoopsDB->prefix('content')
                               . " WHERE storyid=$id");
    list($storyid, $title, $text, $visible, $nohtml, $nosmiley, $nobreaks, $nocomments, $link, $address) = $xoopsDB->fetchRow($result);
    
   echo '<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">';
   echo '<html>';
   echo '<head>';
   echo '	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';
   echo '	<title>' . $title . '-' . $xoopsConfig['sitename'] . '-' . _MD_CONTENT_PRINTERFRIENDLY . '</title>';
   echo '	<meta name="AUTHOR" content="' . $xoopsConfig['sitename'] . '"/>';
   echo '	<meta name="COPYRIGHT" content="Copyright (c) 2005' . $xoopsConfig['sitename'] . '"/>';
   echo '	<meta name="DESCRIPTION" content="' . $xoopsConfig['slogan'] . '"/>';
   echo '	<meta name="GENERATOR" content="' . XOOPS_VERSION . '"/>';
   echo '	<link rel="stylesheet" type="text/css" media="screen" href="' . XOOPS_URL . '/modules/content/assets/css/print.css" />';
   echo '</head>';
   

    
   echo '<body bgcolor="#FFFFFF" text="#000000" topmargin="10" style="font:12px arial, helvetica, san serif;" onLoad="window.print()">';
   echo '<table style="border:1px solid #000000;text-align:center;width:640px;padding:10;border-spacing:1;">';
   echo '	<tr>';
   echo '		<td style="text-align:left;">';
   echo '		<strong>' . $title . '</strong></td>';
   echo '	</tr>';
   echo '	<tr style="vertical-align:top;">';
   echo '		<td style="padding-top:0px;">';
   
   if (1 == $link) {
       $includeContent = XOOPS_ROOT_PATH . '/modules/content/content/' . $address;
       if (file_exists($includeContent)) {
           ob_start();
           include $includeContent;
           $content = ob_get_contents();
           ob_end_clean();
       }
       echo $content;
   } else {
       echo $text;
   }
   
   echo '</td></tr></table>';
   echo '<table style="border:0;text-align:center;width:640px;padding:10;border-spacing:1;"><tr><td>';
   printf(_MD_CONTENT_THISCOMESFROM, $xoopsConfig['sitename']);
   echo '<br><a href="'
              . XOOPS_URL
              . '/">'
              . XOOPS_URL
              . '</a><br><br>'
              . _MD_CONTENT_URLFORSTORY
              . '<br><a href="'
              . XOOPS_URL
              . '/modules/'
              . $xoopsModule->dirname()
              . '/index.php?id='
              . $id
              . '">'
              . XOOPS_URL
              . '/modules/'
              . $xoopsModule->dirname()
              . '/index.php?id='
              . $id
              . '</a>';
   echo '</td></tr></table></body>';
   echo '</html>';
