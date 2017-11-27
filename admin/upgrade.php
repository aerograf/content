<?

	include_once "admin_header.php";
    xoops_cp_header();
    $adminObject = \Xmf\Module\Admin::getInstance();	
    $adminObject->displayNavigation(basename(__FILE__));
		echo '<div>';
		echo "<h4>"._AM_CONTENT_ADMINTITLE."</h4>" . showMenu();
		echo "Page title:&nbsp;";
		if(!FieldExists("newwindow", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `newwindow` tinyint(1) NOT NULL default '0' AFTER `submenu`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
		}

		if(!FieldExists("ptitle", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `ptitle` VARCHAR(255) default NULL AFTER `title`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
			echo '<font color="#800000">' . _AM_CONTENT_CREATED . '</font>';
		}else{
			echo '<font color="#339933">' ._AM_CONTENT_YES . '</font>';
		}
		echo "<br>";
		echo "Error page:&nbsp;";
		if(!FieldExists("epage", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `epage` tinyint(1) default '0' AFTER `homepage`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
			echo '<font color="#800000">' . _AM_CONTENT_CREATED . '</font>';
		}else{
			echo '<font color="#339933">' ._AM_CONTENT_YES . '</font>';
		}

		echo "<br>";
		echo "Last modified:&nbsp;";
		if(!FieldExists("date", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `date` DATETIME DEFAULT NULL AFTER `newwindow`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
			echo '<font color="#800000">' . _AM_CONTENT_CREATED . '</font>';
		}else{
			echo '<font color="#339933">' ._AM_CONTENT_YES . '</font>';
		}

		echo "<br>";
		echo "Associate Module:&nbsp;";
		if(!FieldExists("assoc_module", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `assoc_module` int(8) unsigned default NULL AFTER `date`;");
		
			$xoopsDB->queryF($sql) or die(mysqli_error());
			echo '<font color="#800000">' . _AM_CONTENT_CREATED . '</font>';
		}else{
			echo '<font color="#339933">' ._AM_CONTENT_YES . '</font>';
		}

		echo "<br>";
		echo "Header Image:&nbsp;";
		if(!FieldExists("header_img", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `header_img` VARCHAR(255) default NULL AFTER `assoc_module`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
			echo '<font color="#800000">' . _AM_CONTENT_CREATED . '</font>';
		}else{
			echo '<font color="#339933">' ._AM_CONTENT_YES . '</font>';
		}
		if(!FieldExists("ptitle", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `ptitle` varchar(255) default NULL AFTER `title`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
		}
		if(!FieldExists("keywords", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `keywords` longtext AFTER `text`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());}
		if(!FieldExists("page_description", $xoopsDB->prefix('content'))){
			$sql = sprintf("ALTER TABLE " . $xoopsDB->prefix('content') . " ADD `page_description` longtext AFTER `keywords`;");
			$xoopsDB->queryF($sql) or die(mysqli_error());
		}
		echo "<br><br>" . _AM_CONTENT_DBUPGRADED . ": <strong>"._MI_CONTENT_VERSION."</strong>";
		echo '</div><br>';
		require_once __DIR__ . '/footer.php';
