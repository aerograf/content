<?php
if ( !defined('XOOPS_URL') ) {
	include_once "../../../mainfile.php";
}	

function displayFilterForm(){
	global $filterSQL, $op, $filter, $showshort;
	if ($showshort != 1){
		$filterForm = '<form>'.
					  '<strong>' . _AM_CONTENT_FILTER . ':&nbsp;</strong>'.
					  '<input type="hidden" name="op" value="' . $op . ' ">'.
					  '<input type="text" name="filter" value="' . $filter . '"size="20">&nbsp;'.
					  '<input type="submit" value="Filter">&nbsp;<input type="button" value="Clear Filter" onclick="location.href=\'' . $_SERVER["PHP_SELF"] . '\'">'.
					  '</form>';
		if (isset($_GET["filter"])){
			$filterSQL = " title LIKE '%".$_GET["filter"]."%'";
		}
		return $filterForm;
	}
}

function showMenu(){
	global $xoopsModule, $xoopsDB, $showshort;
	$menu="";
	if ($showshort != 1){
		if(!FieldExists("ptitle", $xoopsDB->prefix('content')) ||
			!FieldExists("newwindow", $xoopsDB->prefix('content')) ||
			!FieldExists("epage", $xoopsDB->prefix('content')) ||
			!FieldExists("date", $xoopsDB->prefix('content')) || 
			!FieldExists("assoc_module", $xoopsDB->prefix('content')) ||
			!FieldExists("header_img", $xoopsDB->prefix('content')) ||
			!FieldExists("ptitle", $xoopsDB->prefix('content')) ||
			!FieldExists("keywords", $xoopsDB->prefix('content')) ||
			!FieldExists("page_description", $xoopsDB->prefix('content'))
			){
			echo '<h4 style="color:#F00">' . _C_UPGRADENOTICE . '</h4>';
		}
		
	}
	return $menu;
}

function return_children($items, $parent_id, $depth=0){
	$myItems = array();
	foreach ($items as $item) {
		if ($item['parent_id'] == $parent_id){
			$item["depth"] = $depth;
			$myItems[] = $item;
			$myItems = array_merge($myItems, return_children($items, $item["storyid"], $depth + 1));
		}
	}
	return $myItems;
}

function isparent($items, $parent_id){
	$hasChild = false;
	foreach ($items as $item) {
		if ($item['parent_id'] == $parent_id){
			$hasChild = true;
			break;
		}
	}
	return $hasChild;
}

function show_form_line($frmElement){
	echo '
			<tr>
				<td class="even" valign="top" width="170"><strong>' . $frmElement->getCaption() . '</strong></td>
				<td class="even">' . $frmElement->render() . '</td>
		  	</tr>';
}

function FieldExists($fieldname, $table){
	global $xoopsDB;
	$result=$xoopsDB->queryF("SHOW COLUMNS FROM	$table LIKE '$fieldname'");
	return($xoopsDB->getRowsNum($result) > 0);
}

if ( !defined('XOOPS_URL') ) {
	include_once "../../../../mainfile.php";
	}
function print_header(){
	global $showshort,$xoopsConfig;

	if ($showshort == 1){
		echo '	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
				<html>
				<head>
					<title>' . $xoopsConfig['sitename'] . '</title>';
					$admincss = file_exists(XOOPS_THEME_URL."/".getTheme()."/admin.css") ? XOOPS_THEME_URL."/".getTheme()."/admin.css" : XOOPS_URL."/admin.css";
					echo '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/xoops.css" />';
					echo '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.'/modules/system/style.css" />';
					echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $admincss . '" />';
					
		echo'	
					<style>
						td, a, tr, p, body, table, a, th {font:11px arial, helvetica, sans-serif;font-weight:normal;}
						.resultMsg {border:solid 1px #C00;padding:4px;margin-bottom:5px;}</style>
				</head>
				<body>
				<table width="100%" cellspacing="2" cellpadding="10" border="0">
				<tr>
					<td>';
	}else{
		xoops_cp_header();
	}

}

function print_footer(){
	global $showshort;
	if ($showshort == 1){
		echo "</td>
	</tr>
</table>	
</body>
</html>";
	
	}else{
		xoops_cp_footer();
	}
}

function ct_xoops_confirm($hiddens, $action, $msg, $submit='', $addtoken = true, $cancel = 'history.go(-1)')
{
    $submit = ($submit != '') ? trim($submit) : _SUBMIT;
    echo '
    <div class="confirmMsg">
      <h4>'.$msg.'</h4>
      <form method="post" action="'.$action.'">
    ';
    foreach ($hiddens as $name => $value) {
        if (is_array($value)) {
            foreach ($value as $caption => $newvalue) {
                echo '<input type="radio" name="'.$name.'" value="'.htmlspecialchars($newvalue).'" /> '.$caption;
            }
            echo '<br />';
        } else {
            echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'" />';
        }
    }
    if ($addtoken != false) {
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
    }
    echo '
        <input type="submit" name="confirm_submit" value="'.$submit.'" /> <input type="button" name="confirm_back" value="' . _CANCEL . '" onclick="javascript:'.$cancel.';" />
      </form>
    </div>
    ';
}
