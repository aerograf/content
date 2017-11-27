<style type="text/css" media="screen"> 

/**************** menu coding *****************/
#menu {width: 100%;} /* Width of Individual Menu */
#menu ul ul {
	border:1px solid #FF9933; 
	background-color:#C3CEE1;
	margin-top:5px;} /* style of submenus */
/*******No Need To Edit Below Here***********/
#menu ul {list-style: none;margin: 0;padding: 0;}
#menu li {position: relative;list-style: none;} 
#menu ul ul {position: absolute;top: 0;left: 100%;width: 100%;z-index:100}
<{$block.cssul1}>{display: none;}
<{$block.cssul2}>{display: block;}
/***** General formatting only ****/
</style>
<!--[if IE]>
<style type="text/css" media="screen">
body {
behavior: url("<{$xoops_url}>/modules/content/assets/css/csshover.htc");
font-size: 100%;
} 
#menu ul li {float: left; width: 100%;}
#menu ul li a {height: 1%;} 

#menu a, #menu h2 {
font: bold small arial, helvetica, sans-serif;
} 

</style>
<![endif]-->

<div id="menu">
		<{$block.ct_menu}>
</div>
