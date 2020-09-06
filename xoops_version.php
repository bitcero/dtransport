<?php
// $Id: xoops_version.php 211 2013-02-01 16:53:27Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include_once 'include/xv-header.php';

$modversion = array(

    /*
    GENERAL INFORMATION SECTION
    ---------------------------
    */
    'name'          => __('D-Transport', 'dtransport'),
    'description'   => __('Module for create a donwloads section in XOOPS','dtransport'),
    'version'       => 2.1,
    'license'       => 'GPL 2',
    'dirname'       => 'dtransport',
    'official'      => 0,
    'onUninstall'   => 'include/install.php',

    /*
    COMMON UTILITIES SECTION
    ------------------------
    These values are used by rmcommon to perform
    specific operations
    */
    'rmnative'      => 1,
    'url'           => 'http://www.eduardocortes.mx',
    'rmversion'     => array(
        'major'     => 2,
        'minor'     => 2,
        'revision'  => 25,
        'stage'     => 0,
        'name'      => 'D-Transport'
    ),
    'rewrite'       => 0,
    'updateurl'     => "https://sys.eduardocortes.mx/updates/",
    'help'          => 'readme.html',

    // AUTHOR INFORMATION
    'author'        => array(
        array(
            'name'  => 'Eduardo Cortés',
            'email' => 'i.bitcero@gmail.com',
            'url'   => 'http://www.eduardocortes.mx',
            'aka'   => 'bitcero'
        )
    ),

    // PERMISSIONS
    'permissions'   => 'include/permissions.php',

    /*
    LOGO AND ICONS
    ------------------------
    */
    'image'         => 'images/logo.png',
    'icon'          => 'fa fa-cloud-download text-green',

    /*
    SOCIAL LINKS
    ------------------------
    */
    'social'        => array(
        array(
            'title' => 'Twitter',
            'type'  => 'twitter',
            'url'   => 'http://www.twitter.com/redmexico/'
        ),
        array(
            'title' => 'Facebook',
            'type'  => 'facebook',
            'url'   => 'http://www.facebook.com/redmexico/'
        ),
        array(
            'title' => 'Instagram',
            'type'  => 'instagram',
            'url'   => 'http://www.instagram.com/mgreduardo/'
        ),
        array(
            'title' => 'LinkedIn',
            'type'  => 'linkedin',
            'url'   => 'http://www.linkedin.com/in/ecorteshervis/'
        ),
        array(
            'title' => 'GitHub',
            'type'  => 'github',
            'url'   => 'http://www.github.com/bitcero/'
        ),
        array(
            'title' => __('Web Site', 'dtransport'),
            'type'  => 'blog',
            'url'   => 'http://www.eduardocortes.mx'
        ),
    ),

    /*
    BACKEND SUPPORT
    ------------------------
    */
    'hasAdmin'      => 1,
    'adminindex'    => "admin/index.php",
    'adminmenu'     => "admin/menu.php",

    /*
    FRONT END SUPPORT
    ------------------------
    */
    'hasMain'       => 1,

    /*
    SQL FILE
    ------------------------
    */
    'sqlfile'       => array(
        'mysql' => "sql/mysql.sql"
    ),

    /*
    DATABASE TABLES
    ------------------------
    */
    'tables'        => array(
        'mod_dtransport_items',
        'mod_dtransport_edited',
        'mod_dtransport_platforms',
        'mod_dtransport_licences',
        'mod_dtransport_votedata',
        'mod_dtransport_downs',
        'mod_dtransport_tags',
        'mod_dtransport_itemtag',
        'mod_dtransport_groups',
        'mod_dtransport_logs',
        'mod_dtransport_files',
        'mod_dtransport_screens',
        'mod_dtransport_features',
        'mod_dtransport_alerts',
        'mod_dtransport_licsoft',
        'mod_dtransport_platsoft',
        'mod_dtransport_categories',
        'mod_dtransport_meta',
        'mod_dtransport_catitem'
    ),

    /*
    SMARTY TEMPLATES
    ------------------------
    */
    'templates'     => array(
        array('file' => 'dt-index.tpl','description' => ''),
        array('file' => 'dt-comments.tpl','description' => __('Show comments for download items.', 'dtransport')),
        array('file' => 'dt-header.tpl','description' => ''),
        array('file' => 'dt-list-item.tpl','description' => __('Download item to show in lists', 'dtransport')),
        array('file' => 'dt-list-explore-item.tpl','description' => __('Download item to show in lists within explore page', 'dtransport')),
        array('file' => 'dt-day-download.tpl','description' => ''),
        array('file' => 'dt-related-items.tpl','description' => __('Show a list with related download items for single item', 'dtransport')),
        array('file' => 'dt-category.tpl','description' => __('Show items within a specific category', 'dtransport')),
        array('file' => 'dt-licenses.tpl','description' => __('Show items with a specific license', 'dtransport')),
        array('file' => 'dt-item.tpl','description' => ''),
        array('file' => 'dt-get-file.tpl','description' => ''),
        array('file' => 'dt-search.tpl', 'description' => ''),
        array('file' => 'dt-tags.tpl','description' => ''),
        array('file' => 'dt-submit.tpl','description' => ''),
        array('file' => 'dt-cpanel.tpl','description' => __('Show administrative options for users in the frontend', 'dtransport')),
        array('file' => 'dt-cpanel-delete.tpl','description' => __('Shows a confirmation screen before to delete a download item.', 'dtransport')),
        array('file' => 'dt-featured-list.tpl', 'description' => __('Template to show the featured items list.','dtransport')),
        array('file' => 'dt-list-items.tpl', 'description' => __('Template to show the list for selected items.','dtransport')),
        array('file' => 'dt-explore.tpl', 'description' => __('Shows the items according to exploring parameters.','dtransport')),
        array('file' => 'dt-platforms.tpl', 'description' => __('Shows the items that belong to a specific platform.','dtransport')),
        array('file' => 'dt-screens.tpl', 'description' => __('Show screenshots for a download item in control panel.','dtransport')),
        array('file' => 'dt-features.tpl', 'description' => __('Show features list for a download item in control panel.','dtransport')),
        array('file' => 'dt-logs.tpl', 'description' => __('Show logs list for a download item in control panel.','dtransport')),
        array('file' => 'dt-files.tpl', 'description' => __('Show files list for a download item in control panel.','dtransport')),
        array('file' => 'dt-cp-header.tpl', 'description' => __('Show the header for Users Control Panel.','dtransport')),
        array('file' => 'dt-cp-item-options.tpl', 'description' => __('Show the toolbar to manage a specific download item.','dtransport'))
    ),

    /*
    SEARCH SUPPORT
    ------------------------
    */
    'hasSearch'     => 1,
    'search'        => array(
        'file'      => 'include/search.php',
        'func'      => 'mod_dtransport_search'
    ),

    /*
    MODULE BLOCKS
    ------------------------
    */
    'blocks'        => array(
        array(
            'file' => "dt-block-items.php",
            'name' => __('Downloads list','dtransport'),
            'description' => __('This block show the downloads list','dtransport'),
            'show_func' => "dt_block_items",
            'edit_func' => "dt_block_items_edit",
            'template' => 'dt-block-items.tpl',
            'options' => array('all', 0, 5, 1, 1, 1, 0, 0, 1, 0, 0,'thumbnail')
        ),

        array(
            'file' => "dt-block-tags.php",
            'name' => __('Tags','dtransport'),
            'description' => __('Show a list of downloads for popular tags','dtransport'),
            'show_func' => "dt_block_tags",
            'edit_func' => "dt_block_tags_edit",
            'template' => 'dt-block-tags.tpl',
            'options' => array(100, 30, 1, 10, "Arial, Helvetica, sans-serif")
        ),

        array(
            'file' => 'dt-block-categories.php',
            'name' => __('Categories','dtransport'),
            'description' => __('Show the categories tree for D-Transport','dtransport'),
            'show_func' => "dt_block_categories",
            'edit_func' => "dt_block_categories_edit",
            'template' => 'dt-block-categories.tpl',
            'options' => array(0)
        ),

        array(
            'file' => 'dt-block-links.php',
            'name' => __('Downloads Links','dtransport'),
            'description' => __('Show links to navigate in the module','dtransport'),
            'show_func' => "dt_block_links",
            'edit_func' => "",
            'template' => 'dt-block-links.tpl'
        )

    ),

    /*
     * SETTINGS CATEGORIES
     */
    'categories' => [
        'general' => __('General Options', 'dtransport'),
        'users' => __('Users', 'dtransport'),
        'downloads' => __('Downloads', 'dtransport'),
        'appearance' => __('Appearance', 'dtransport'),
        'maintenance' => __('Maintenance', 'dtransport')
    ],

    /*
    MODULE SETTINGS
    ------------------------
    */
    'config'        => array(

        // GENERAL OPTIONS //
        // --------------- //

        [
            'name'          => 'alertChecked',
            'category'      => 'general',
            'title'         => '',
            'description'   => '',
            'formtype'      => 'hidden',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'alertVerified',
            'category'      => 'general',
            'title'         => '',
            'description'   => '',
            'formtype'      => 'hidden',
            'valuetype'     => 'int',
            'default'       => 0
        ],
        
        [
            'name'          => 'permalinks',
            'category'      => 'general',
            'title'         => __('Enable short URL','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'htbase',
            'category'      => 'general',
            'title'         => __('Permalinks base path','dtransport'),
            'description'   => __('Don\'t start this value with a slash (/)', 'dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'text',
            'default'       => 'modules/dtransport'
        ],

        [
            'name'          => 'xpage',
            'category'      => 'general',
            'title'         => __('Results per page','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => '10'
        ],

        [
            'name'          => 'active_notify',
            'category'      => 'general',
            'title'         => __('Enable notifications for new downloads','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'groups_notif',
            'category'      => 'general',
            'title'         => __('Groups to be notified for new submitted downloads','dtransport'),
            'description'   => '',
            'formtype'      => 'group_multi',
            'valuetype'     => 'array',
            'default'       => array(XOOPS_GROUP_ADMIN)
        ],

        [
            'name'          => 'aprove_edit',
            'category'      => 'general',
            'title'         => __('Approve editions to existing downloads','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'edit_notify',
            'category'      => 'general',
            'title'         => __('Notify for editions','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'caracter_tags',
            'category'      => 'general',
            'title'         => __('Minimum length of tags, in characters','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 3,
        ],

        [
            'name'          => 'alerts',
            'category'      => 'maintenance',
            'title'         => __('Enable alerts for inactivity','dtransport'),
            'description'   => __('By enabling this option D-TRansport will alert to owner users about download items that had not been updated for a specific time period.', 'dtransport'),
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'alert_days',
            'category'      => 'maintenance',
            'title'         => __('Days without inactivity before to send an alert','dtransport'),
            'description'   => __('Owners of download items that had not been updated during this period of time will receive an alert in order to update data, otherwise download item could be deleted from database.', 'dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 30
        ],

        [
            'name'          => 'deletion',
            'category'      => 'maintenance',
            'title'         => __('Enable automatic deletion for non updated download items','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'deletion_days',
            'category'      => 'maintenance',
            'title'         => __('Days without updates before to delete a download item','dtransport'),
            'description'   => __('When a download item had not been updated between this period of time, it will be deleted in order to maintain updated the database.', 'dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 40
        ],

        [
            'name'          => 'deletion_mode',
            'category'      => 'maintenance',
            'title'         => __('Automatic deletion mode','dtransport'),
            'description'   => __('Select the mode in which automatically deleted items will be processed.', 'dtransport'),
            'formtype'      => 'select',
            'valuetype'     => 'text',
            'default'       => 'mark',
            'options'       => [
                __('Mark for deletion', 'dtransport') => 'mark',
                __('Delete inmediatly', 'dtransport') => 'delete'
            ]
        ],

        [
            'name'          => 'alert_mode',
            'category'      => 'general',
            'title'         => __('Message type for alerts','dtransport'),
            'description'   => '',
            'formtype'      => 'select',
            'valuetype'     => 'int',
            'default'       => 0,
            'options'       => array(__('Private message','dtransport') => 0, __('Email','dtransport') => 1)
        ],

        [
            'name'          => 'hrs_alerts',
            'category'      => 'general',
            'title'         => __('Interval in hours to send alerts for inactivity','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 24
        ],

        [
            'name'          => 'cron',
            'category'      => 'general',
            'title'         => __('Use the internal cronjob emulator to check alerts','dtransport'),
            'description'   => sprintf(__('If you enable this option D-Transport will run a check automatically. If disabled, then you need to setup a cronjob that execute %s file.', 'dtransport'), '/modules/dtransport/include/tasks.php'),
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'licenseData',
            'category'      => 'general',
            'title'         => '',
            'description'   => '',
            'formtype'      => 'hidden',
            'valuetype'     => 'text',
            'default'       => ''
        ],

        [
            'name'          => 'branding',
            'category'      => 'general',
            'title'         => '',
            'description'   => '',
            'formtype'      => 'hidden',
            'valuetype'     => 'int',
            'default'       => 1
        ],
        
        // USERS OPTIONS //
        // ------------- //
        [
            'name'          => 'mustLogin',
            'category'      => 'users',
            'title'         => __('Require login before to download a file','dtransport'),
            'description'   => __('If enabled, users must register before to downloading files.','dtransport'),
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'send_download',
            'category'      => 'users',
            'title'         => __('Allow downloads submission','dtransport'),
            'description'   => __('Allows to users submit new download items from public section of module.','dtransport'),
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'groups_send',
            'category'      => 'users',
            'title'         => __('Groups than can submit new downloads','dtransport'),
            'description'   => '',
            'formtype'      => 'group_multi',
            'valuetype'     => 'array',
            'default'       => array(XOOPS_GROUP_ADMIN,XOOPS_GROUP_USERS)
        ],

        [
            'name'          => '',
            'category'      => 'users',
            'title'         => __('Default groups that can download items','dtransport'),
            'description'   => __('These groups will be set as default when new items are created', 'dtransport'),
            'formtype'      => 'group_multi',
            'valuetype'     => 'array',
            'default'       => array(0)
        ],

        [
            'name'          => 'vote_anonymous',
            'category'      => 'users',
            'title'         => __('Allow votes from anonymous users','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'limits',
            'category'      => 'users',
            'title'         => __('Download limit per item for users','dtransport'),
            'description'   => __('Specify the times that a user can download a same download item','dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 0
        ],

        [
            'name'          => 'max_rating',
            'category'      => 'users',
            'title'         => __('Maximum users rating','dtransport'),
            'description'   => __('It is not recommended to change this value after that your site becomes public, due to calculations could be wrong.','dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 10
        ],

        [
            'name'          => 'usedec',
            'category'      => 'users',
            'title'         => __('Use decimals on ratings','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],
        
        
        // DOWNLOADS OPTIONS //
        // ----------------- //
        [
            'name'          => 'uselogo',
            'category'      => 'downloads',
            'title'         => __('Allow logotypes for downloads','dtransport'),
            'description'   => __('By enabling this option, you will able to provide a image logotype for downloads. Note that logos must be smaller in order to work properly.', 'dtransport'),
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'limit_screen',
            'category'      => 'downloads',
            'title'         => __('Maximum number of screenshots by download item','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 10
        ],

        [
            'name'          => 'directory_secure',
            'category'      => 'downloads',
            'title'         => __('Secure directory','dtransport'),
            'description'   => __('Specify the directory where secure download files will be stored. It is recomended that this directory will located outside from public directory.','dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'text',
            'default'       => ''
        ],

        [
            'name'          => 'directory_insecure',
            'category'      => 'downloads',
            'title'         => __('General directory','dtransport'),
            'description'   => __('Specify the directory where general files will be stored. This directory will exists within your public html directory.','dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'text',
            'default'       => XOOPS_UPLOAD_PATH.'/downloads'
        ],

        [
            'name'          => 'dest_download',
            'category'      => 'downloads',
            'title'         => __('Show featured downloads list in home page','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'inner_dest_download',
            'category'      => 'downloads',
            'title'         => __('Show featured downloads list at inner pages','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'limit_destdown',
            'category'      => 'downloads',
            'title'         => __('Limit number of featured downloads in list','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 10
        ],

        [
            'name'          => 'daydownload',
            'category'      => 'downloads',
            'title'         => __('Show daily downloads list in home page','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'inner_daydownload',
            'category'      => 'downloads',
            'title'         => __('Show daily downloads list at inner pages','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'limit_daydownload',
            'category'      => 'downloads',
            'title'         => __('Limit number of daily downloads in list','dtransport'),
            'description'   => '',
            'formtype'      => 'select',
            'valuetype'     => 'int',
            'default'       => 4,
            'options'       => [
                4 => 4,
                6 => 6,
                8 => 8
            ]
        ],

        [
            'name'          => 'size_file',
            'category'      => 'downloads',
            'title'         => __('Size of files for download in MB','dtransport'),
            'description'   => sprintf(__('Your PHP configuration specify a limit of %s in posts requests and allows a upload max file size of %s', 'dtransport'), '<strong>' . ini_get('post_max_size') . '</strong>', '<strong>' . ini_get('upload_max_filesize') . '</strong>'),
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'type_file',
            'category'      => 'downloads',
            'title'         => __('Allowed file extensions','dtransport'),
            'description'   => __('Separate each extensión with "|".','dtransport'),
            'formtype'      => 'textarea',
            'valuetype'     => 'array',
            'default'       => 'zip|tar|gz|gif|jpg|png|rar|7z'
        ],

        [
            'name'          => 'limit_recents',
            'category'      => 'downloads',
            'title'         => __('Limit number of downloads for home lists','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 10
        ],

        [
            'name'          => 'new',
            'category'      => 'downloads',
            'title'         => __('Number of days during downloads will consider as new','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 5
        ],

        [
            'name'          => 'update',
            'category'      => 'downloads',
            'title'         => __('Number of days during downloads will consider as updated after its edition','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 5
        ],

        [
            'name'          => 'showcats',
            'category'      => 'downloads',
            'title'         => __('Show categories in home page and categories page','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'active_relatsoft',
            'category'      => 'downloads',
            'title'         => __('Show related items','dtransport'),
            'description'   => '',
            'formtype'      => 'yesno',
            'valuetype'     => 'int',
            'default'       => 1
        ],

        [
            'name'          => 'limit_relatsoft',
            'category'      => 'downloads',
            'title'         => __('Number of items in related items list','dtransport'),
            'description'   => '',
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 5
        ],

        [
            'name'          => 'pause',
            'category'      => 'downloads',
            'title'         => __('Time before to start downloads','dtransport'),
            'description'   => __('This value must be specified in seconds. If you wish to start download immediately then set this value to "0".','dtransport'),
            'formtype'      => 'textbox',
            'valuetype'     => 'int',
            'default'       => 5
        ],
        
        
        // APPEARANCE OPTIONS //
        // ------------------ //

        [
            'name' => 'tplset',
            'category' => 'appearance',
            'title' => __('Layout appearance', 'dtransport'),
            'description' => __('Select the layout format to front-end', 'dtransport'),
            'formtype' => 'select',
            'valuetype' => 'text',
            'default' => 'default',
            'options' => [
                __('Default', 'dtransport') => 'default'
            ]
        ],

    ),

    /*
    MODULE SUB-PAGES
    ------------------------
    */
    'subpages'      => array(
        'index' =>  __('Home Page','dtransport'),
        'category' => __('Categories','dtransport'),
        'files' => __('Files','dtransport'),
        'item' => __('Item details','dtransport'),
        'explore-mine' => __('My Downloads','dtransport'),
        'explore-recent' => __('Recent Downloads','dtransport'),
        'download' => __('Download file','dtransport'),
        'tags' => __('Tags','dtransport'),
        'submit' => __('Submit download','dtransport'),
        'search' => __('Search','dtransport'),
        'comments' => __('Comments','dtransport'),
        'cp-list' => __('Control Panel','dtransport'),
        'cp-screens' => __('Screenshots Management','dtransport'),
    )

);
