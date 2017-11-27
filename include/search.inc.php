<?php

/**
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $userid
 * @return array
 */
function content_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB, $xoopsConfig;

    if (file_exists(XOOPS_ROOT_PATH . '/modules/content/language/' . $xoopsConfig['language'] . '/main.php')) {
        include XOOPS_ROOT_PATH . '/modules/content/language/' . $xoopsConfig['language'] . '/main.php';
    } elseif (file_exists(XOOPS_ROOT_PATH . '/modules/content/language/english/main.php')) {
        include XOOPS_ROOT_PATH . '/modules/content/language/english/main.php';
    }
  
    $sql = 'SELECT storyid, title, text FROM '
           . $xoopsDB->prefix('content')
           . " WHERE visible='1'";

    if (0 != $userid) {
        $sql .= " AND storyid='0' ";
    }

    // because count() returns 1 even if a supplied variable
    // is not an array, we must check if $querryarray is really an array
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((text LIKE '%$queryarray[0]%' OR title LIKE '%$queryarray[0]%')";
        for ($i=1;$i<$count;$i++) {
            $sql .= " $andor ";
            $sql .= "(text LIKE '%$queryarray[$i]%' OR title LIKE '%$queryarray[$i]%')";
        }
        $sql .= ')';
    }
  
    $sql   .= ' ORDER BY storyid ASC';
    $result = $xoopsDB->query($sql, $limit, $offset);
    $ret    = [];
    $i      = 0;
  
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = '';
        $ret[$i]['link']  = 'index.php?id=' . $myrow['storyid'];
        $ret[$i]['title'] = $myrow['title'];
        $ret[$i]['text']    = $myrow['text'];
        $ret[$i]['uid']     = '0';
        $i++;
    }
    return $ret;
}
