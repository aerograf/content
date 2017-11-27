<?php

$moduleDirName = basename(__DIR__);

$modversion    = [
     'version'              => 1.5,
     'module_status'        => 'Beta 1',
     'status_version'       => 'Intermediate version',
     'release_date'         => '2017/25/11',
     'name'                 => _MI_CONTENT_NAME,
     'description'          => _MI_CONTENT_DESC,
     'help'                 => 'page=help',
     'credits'              => 'The Handcoders, Reinarz & Associates, Aerograf',
     'author'               => 'Ben Brown - <span style="font-size:12px;">Refactored by: Kostas Ksilas</span>',
     'license'              => 'GNU GPL 2.0',
     'license_url'          => 'www.gnu.org/licenses/gpl-2.0.html/',
     'image'                => 'assets/images/logo_module.png',
     'official'             => 0,
     'dirname'              => basename(__DIR__),
     'author_website_url'   => 'http://www.mykerkyra.gr',
     'author_website_name'  => 'MyKerkyra.gr',
     'module_website_url'   => 'http://www.mykerkyra.gr',
     'module_website_name'  => 'MyKerkyra.gr',
     'module_website_url'   => 'www.xoops.org/',
     'module_website_name'  => 'XOOPS',
     'support_site_url'     => 'https://xoops.org/newbb/',
     'support_site_name'    => 'XOOPS Project',
     'min_php'              => '5.5',
     'min_xoops'            => '2.5.9',
     'min_admin'            => '1.2',
     'min_db'               => ['mysql' => '5.5'],
     'modicons16'           => 'assets/icons/16',
     'modicons32'           => 'assets/icons/32',
    // ------------------- Mysql -----------------------------
     'sqlfile'              => ['mysql' => 'sql/mysql.sql'],
     'tables'               => [$moduleDirName],
    // ------------------- Admin Menu -------------------
     'system_menu'          => 1,
     'hasAdmin'             => 1,
     'adminindex'           => 'admin/index.php',
     'adminmenu'            => 'admin/menu.php',
    // ------------------- Main Menu -------------------
     'hasMain'              => 1,
    // ------------------- Search ---------------------------
     'hasSearch'            => 1,
     'search'               => [
                                'file' => 'include/search.inc.php',
                                'func' => 'newbb_search',
                                ],
     'use_smarty'           => 1,
];

    // ------------------- Submenu Items ---------------------------
global $xoopsDB, $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig;
$result = $xoopsDB->query('SELECT storyid, title, homepage, submenu FROM ' . $xoopsDB->prefix('content') . " WHERE homepage='0' AND submenu='1' ORDER BY title");
$i = 1;
while (list($storyid, $title) = $xoopsDB->fetchRow($result)) {
    $modversion['sub'][$i]['name'] = $title;
    $modversion['sub'][$i]['url']  = 'index.php?id=' . $storyid . '';
    $i++;
}

$modversion['templates'][1] = [
        'file'        => 'ct_index.tpl',
        'description' => '_MI_CONTENT_TEMP_NAME1',
];

$modversion['blocks'][1] = [
         'file'         => 'ct_navigation.php',
         'blocks'       => _MI_CONTENT_BNAME1,
         'description'  => _MI_CONTENT_BNAME1_DESC,
         'show_func'    => 'content_block_nav',
         'template'     => 'ct_nav_block.tpl'
];
$modversion['blocks'][] = [
         'file'         => 'ct_sitenavigation.php',
         'name'         => _MI_CONTENT_BNAME2,
         'description'  => _MI_CONTENT_BNAME2_DESC,
         'show_func'    => 'site_block_nav',
         'edit_func'    => 'edit_block_nav',
         'options'      => '10',
         'template'     => 'ct_site_nav_block.tpl'
];
$modversion['blocks'][] = [
         'file'         => 'ct_dhtml_sitenavigation.php',
         'name'         => _MI_CONTENT_BNAME3,
         'description'  => _MI_CONTENT_BNAME3_DESC,
         'show_func'    => 'site_block_dhtml_nav',
         'template'     => 'ct_dhtml_site_nav_block.tpl'
];
$modversion['blocks'][] = [
         'file'         => 'ct_section_navigation.php',
         'name'         => _MI_CONTENT_BNAME4,
         'description'  => _MI_CONTENT_BNAME4_DESC,
         'show_func'    => 'site_block_section_nav',
         'edit_func'    => 'edit_block_sec_nav',
         'options'      => '10',
         'template'     => 'ct_section_nav_block.tpl'
];
$modversion['blocks'][] = [
         'file'         => 'ct_dhtml_horizontal.php',
         'name'         => _MI_CONTENT_BNAME5,
         'description'  => _MI_CONTENT_BNAME5_DESC,
         'show_func'    => 'site_block_horz_dhtml_nav',
         'template'     => 'ct_dhtml_horz_site_nav_block.tpl'
];
$modversion['blocks'][] = [
         'file'         => 'ct_top_navigation.php',
         'name'         => _MI_CONTENT_BNAME6,
         'description'  => _MI_CONTENT_BNAME6_DESC,
         'show_func'    => 'content_block_top_nav',
         'template'     => 'ct_top_navigation.tpl'
];

    // ------------------- Comments -------------------
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'id';
$modversion['comments']['pageName'] = 'index.php';

    // ------------------- Editor to use -------------------
$modversion['config'][1]['name']        = 'cont_form_options';
$modversion['config'][1]['title']       = '_MI_CONTENT_FORM_OPTIONS';
$modversion['config'][1]['description'] = '_MI_CONTENT_FORM_OPTIONS_DESC';
$modversion['config'][1]['formtype']    = 'select';
$modversion['config'][1]['valuetype']   = 'text';
xoops_load('xoopseditorhandler');
$editorHandler = XoopsEditorHandler::getInstance();
$modversion['config'][1]['options']     = array_flip($editorHandler->getList());
$modversion['config'][1]['default']     = 'fckeditor';

$modversion['config'][2]['name']        = 'cont_crumbs';
$modversion['config'][2]['title']       = '_MI_CONTENT_CRUMBS';
$modversion['config'][2]['formtype']    = 'yesno';
$modversion['config'][2]['valuetype']   = 'int';
$modversion['config'][2]['default']     = 1;

$modversion['config'][3]['name']        = 'cont_title';
$modversion['config'][3]['title']       = '_MI_CONTENT_SHOWTITLE';
$modversion['config'][3]['formtype']    = 'yesno';
$modversion['config'][3]['valuetype']   = 'int';
$modversion['config'][3]['default']     = 1;

$modversion['config'][4]['name']        = 'cont_collapse';
$modversion['config'][4]['title']       = '_MI_CONTENT_COLLAPSE';
$modversion['config'][4]['formtype']    = 'yesno';
$modversion['config'][4]['valuetype']   = 'int';
$modversion['config'][4]['default']     = 1;

$modversion['config'][5]['name']        = 'cont_permits_advnaced';
$modversion['config'][5]['title']       = '_MI_CONTENT_LEVELS';
$modversion['config'][5]['description'] = '_MI_CONTENT_LEVELS_DESC';
$modversion['config'][5]['formtype']    = 'select';
$modversion['config'][5]['valuetype']   = 'int';
$modversion['config'][5]['default']     = 1;
$modversion['config'][5]['options']     = [
            '_MI_CONTENT_NONE'     => 0,
            '_MI_CONTENT_BASIC'    => 1,
            '_MI_CONTENT_ADVANCED' => 2
];

$modversion['config'][6]['name']        = 'cont_edit_height';
$modversion['config'][6]['title']       = '_MI_CONTENT_POP_H';
$modversion['config'][6]['description'] = '_MI_CONTENT_POP_DESC';
$modversion['config'][6]['formtype']    = 'textbox';
$modversion['config'][6]['valuetype']   = 'text';
$modversion['config'][6]['default']     = 0;

$modversion['config'][7]['name']        = 'cont_edit_width';
$modversion['config'][7]['title']       = '_MI_CONTENT_POP_W';
$modversion['config'][7]['description'] = '_MI_CONTENT_POP_DESC';
$modversion['config'][7]['formtype']    = 'textbox';
$modversion['config'][7]['valuetype']   = 'text';
$modversion['config'][7]['default']     = 0;
