<?php
/**
 * D-Transport Downloads Manager for XOOPS
 * More info at Eduardo Cortés Website (www.eduardocortes.mx)
 *
 * Copyright © 2017 Eduardo Cortés (http://www.eduardocortes.mx)
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

class DtransportRmcommonPreload
{
    // Register Smarty plugins
    static function eventRmcommonSmartyPlugins($plugins)
    {
        $plugins['dtransport'] = XOOPS_ROOT_PATH . '/modules/dtransport/include/smarty';
        return $plugins;
    }

    public function eventRmcommonRegisterIconProvider($providers)
    {

        $providers[] = [
            'id' => 'dtransport',
            'name' => 'D-Transport',
            'directory' => XOOPS_ROOT_PATH . '/modules/dtransport/icons'
        ];

        return $providers;

    }

    /**
     * Add new mimetypes
     */
    static function eventRmcommonGetMimeTypes($types)
    {

        if (in_array("rar", $types)) return $types;
        $types['rar'] = 'application/rar';
        return $types;
    }

    /**
     * Return the feed options to show in RSS Center
     */
    static function eventRmcommonGetFeedsList($feeds)
    {

        include_once XOOPS_ROOT_PATH . '/modules/dtransport/class/dtfunctions.class.php';
        include_once XOOPS_ROOT_PATH . '/modules/dtransport/class/dtcategory.class.php';
        load_mod_locale('dtransport');

        $module = RMFunctions::load_module('dtransport');
        $config = RMSettings::module_settings('dtransport');
        $url = XOOPS_URL . '/' . ($config->permalinks ? $config->htbase : 'modules/dtransport') . '/';
        $dtFunc = new Dtransport_Functions();

        $data = array(
            'title' => $module->name(),
            'url' => $url,
            'module' => 'dtransport'
        );

        $options[] = array(
            'title' => __('All Recent Downloads', 'dtransport'),
            'params' => 'show=all',
            'description' => __('Show all recent downloads', 'dtransport')
        );

        $categories = array();
        $dtFunc->getCategories($categories);

        $table = '<table cellpadding="2" cellspacing="2" width="100%"><tr class="even">';
        $count = 0;
        foreach ($categories as $cat) {
            if ($count >= 3) {
                $count = 0;
                $table .= '</tr><tr class="' . tpl_cycle("odd,even") . '">';
            }
            $table .= '<td width="33%"><a href="' . XOOPS_URL . '/backend.php?action=showfeed&amp;mod=dtransport&amp;show=cat&amp;cat=' . $cat['id_cat'] . '">' . $cat['name'] . '</a></td>';
            $count++;
        }
        $table .= '</tr></table>';

        $options[] = array(
            'title' => __('Downloads by category', 'dtransport'),
            'description' => __('Select a category to see the downloads published recently.', 'dtransport') . ' <a href="javascript:;" onclick="$(\'#dtcategories-feed\').slideToggle(\'slow\');">Show Categories</a>
                            <div id="dtcategories-feed" style="padding: 10px; display: none;">' . $table . '</div>'
        );

        unset($categories);

        $feed = array('data' => $data, 'options' => $options);
        $feeds[] = $feed;
        return $feeds;

    }

    static function eventRmcommonDashboardPanels($objects)
    {
        global $common;

        $dl = $common->settings()::module_settings('dtransport');

        if(false == empty($dl->licenseData) && 0 == $dl->branding){
            return $objects;
        }

        $objects[] = '<div class="size-1" data-dashboard="item" id="dt-activation-info">
            <div class="cu-box box-green">
                <div class="box-header">
                    <h3 class="box-title">' . __('D-Transport Activation', 'dtransport') . '</h3>
                </div>
                <div class="box-content">
                    ' . $common->icons()->getIcon('svg-dtransport-verify', ['class' => 'icon3x pull-left text-green']) . '
                    <p style="font-size: 1.1em; margin-left: 58px;">
                        ' . sprintf(__('D-Transport is not activated yet. To have it running at 100&#37; and get all benefits of a subscription %s.', 'dtransport'), '<a href="../dtransport/admin/">' . __('click here', 'dtransport') . '</a>') . '</p>

                    <p style="font-size: 1.1em; margin-left: 58px;">
                        <a href="../dtransport/admin/" class="btn btn-green">' . __('Activate D-Transport', 'dtransport') . '</a>
                    </p>
                </div>
            </div>
        </div>';
        return $objects;
    }

    static function eventRmcommonFooterAdminEnd()
    {
        global $common, $xoopsModule;
        $pbzzba = $common; $kbbcfZbqhyr = $xoopsModule;
        $abj = time(); // now
        if($kbbcfZbqhyr && $kbbcfZbqhyr->getVar('dirname') != 'dtransport'){return;}
        $jura = $pbzzba->settings()::module_settings('dtransport', 'alertVerified'); // when
        if($jura > $abj-86400){return;}
        $yvprafr = $pbzzba->settings()::module_settings('dtransport', 'licenseData'); // license
        $hey = $kbbcfZbqhyr->getInfo('updateurl'); // url
        $dhrel = 'id=dtransport&type=module&version=' . implode('.', $kbbcfZbqhyr->getInfo('rmversion')); // query
        $dhrel .= '&data=' . urlencode($yvprafr) . '&action=verify';
        $erfcbafr = $pbzzba->httpRequest()::load_url($hey, $dhrel, true); // response
        if('194fed4b' == $erfcbafr){$pbzzba->settings()->setValue('dtransport', 'branding', 1);
        unlink(XOOPS_VAR_PATH.'/data/dtinfo.dt');if($_SESSION['npgvingrQgenafcbeg']){$pbzzba->settings()->setValue('dtransport','licenseData','');
        unset($_SESSION['npgvingrQgenafcbeg']);}}else{$pbzzba->settings()->setValue('dtransport', 'branding', 0);}
        $pbzzba->settings()->setValue('dtransport', 'alertVerified', $abj);
    }

}
