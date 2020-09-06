<?php
/**
 * D-Transport for XOOPS
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

function dt_block_categories($options)
{
    global $common;
    $dtSettings = RMSettings::module_settings('dtransport');

    $url = $common->uris()::current_url();

    $rpath = parse_url($url);
    $xpath = parse_url(XOOPS_URL);

    if ($dtSettings->permalinks) {
        $params = trim(str_replace($xpath['path'] . '/' . trim($dtSettings->htbase, '/'), '', rtrim($rpath['path'], "/")), '/');
        $search = array('category', 'publisher', 'recents', 'popular', 'rated', 'updated');

        if ($params == '')
            $params = array();
        else
            $params = explode("/", trim($params));

        if (!empty($params) && $params[0] == 'category') {

            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $params = explode("page", implode("/", array_slice($params, 1)));
            $path = explode("/", $params[0]);
            foreach ($path as $k) {

                if ($k == '') continue;

                $category = new Dtransport_Category();
                $sql = "SELECT * FROM " . $db->prefix("mod_dtransport_categories") . " WHERE nameid='$k' AND parent='$idp'";
                $result = $db->query($sql);

                if ($db->getRowsNum($result) > 0) {
                    $row = $db->fetchArray($result);
                    $idp = $row['id_cat'];
                    $category->assignVars($row);
                } else {
                    $dtfunc->error_404();
                }

            }

        } else {
            $category = new Dtransport_Category();
        }

    }

    $tpl = RMTemplate::get();
    $tpl->add_style('blocks-default.min.css', 'dtransport');

    $categories = array();

    Dtransport_Functions::getCategories($categories, 0, $category->id(), array(), false, 1);

    $block = array();
    foreach ($categories as $cat) {

        if ($cat['jumps'] > $options[0] - 1 && $options[0] > 0) continue;

        $block['categories'][] = $cat;

    }

    if (!$category->isNew())
        $block['parent'] = array('name' => $category->name(), 'link' => $category->permalink());

    // All Dtransport blocks must provide this property
    $block['tplPath'] = XOOPS_ROOT_PATH . '/modules/dtransport/templates/sets/' . $dtSettings->tplset . '/blocks';

    return $block;

}

function dt_block_categories_edit($options)
{

    $tpl = RMTemplate::get();
    $tpl->add_style('admin_block.css', 'dtransport');

    ob_start()
    ?>
    <div class="row form-group">
        <div class="col-sm-4 col-md-3">
            <label for="cat-level"><?php _e('Show categories levels:', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-8 col-md-4">
            <select name="options[0]" id="cat-level" class="form-control">
                <option value="0"<?php if ($options[0] == 0): ?> selected="selected"<?php endif; ?>><?php _e('All levels', 'dtransport'); ?></option>
                <option value="1"<?php if ($options[0] == 1): ?> selected="selected"<?php endif; ?>><?php _e('1 level', 'dtransport'); ?></option>
                <option value="2"<?php if ($options[0] == 2): ?> selected="selected"<?php endif; ?>><?php _e('2 levels', 'dtransport'); ?></option>
                <option value="3"<?php if ($options[0] == 3): ?> selected="selected"<?php endif; ?>><?php _e('3 levels', 'dtransport'); ?></option>
                <option value="4"<?php if ($options[0] == 4): ?> selected="selected"<?php endif; ?>><?php _e('4 levels', 'dtransport'); ?></option>
                <option value="5"<?php if ($options[0] == 5): ?> selected="selected"<?php endif; ?>><?php _e('5 levels', 'dtransport'); ?></option>
            </select>
        </div>
    </div>
    <?php
    $ret = ob_get_clean();
    return $ret;
}