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


function dt_block_tags($options)
{
    global $common, $xoopsDB;

    include_once XOOPS_ROOT_PATH . '/modules/dtransport/class/dttag.class.php';

    $dtSettings = $common->settings()->module_settings('dtransport');

    $sql = "SELECT MAX(hits) FROM " . $xoopsDB->prefix('mod_dtransport_tags');
    list($maxhit) = $xoopsDB->fetchRow($xoopsDB->query($sql));
    $sql = "SELECT * FROM " . $xoopsDB->prefix('mod_dtransport_tags');

    if ($options['size_zero'] < 1) {
        $sql .= " WHERE hits>0";
    }

    $sql .= " LIMIT 0,$options[limit]";
    $result = $xoopsDB->query($sql);
    $sz = $options['size'] / $maxhit;

    $block = array();

    while ($row = $xoopsDB->fetchArray($result)) {
        $tag = new DTTag();
        $tag->assignVars($row);
        $link = $tag->permalink();

        $size = intval($tag->hit() * $sz);
        if ($size < $options['msize']) {
            $size = $options['msize'];
        }

        $rtn = array();
        $rtn['id'] = $tag->id();
        $rtn['tag'] = $tag->tag();
        $rtn['hits'] = $tag->hit();
        $rtn['link'] = $link;
        $rtn['size'] = $size;

        $block['tags'][] = $rtn;

    }

    // All Dtransport blocks must provide this property
    $block['tplPath'] = XOOPS_ROOT_PATH . '/modules/dtransport/templates/sets/' . $dtSettings->tplset . '/blocks';

    return $block;

}

function dt_block_tags_edit($options)
{
    global $myts;

    ob_start(); ?>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-limit"><?php _e('Number of tags to show:', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-3">
            <input type="number" name="options[limit]" id="d-limit" value="<?php echo $options['limit']; ?>" class="form-control" min="1">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-size"><?php _e('Maximum font size:', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-3">
            <input type="number" name="options[size]" id="d-size" value="<?php echo isset($options['size']) ? $options['size'] : 20; ?>" class="form-control" min="1" max="30">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-msize"><?php _e('Minimum font size:', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-3">
            <input type="number" name="options[msize]" id="d-msize" value="<?php echo isset($options['msize']) ? $options['msize'] : 6; ?>" class="form-control" min="1" max="30">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-size-zero"><?php _e('Show tags with value zero:', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[size_zero]" id="d-size-zero" value="1"<?php echo $options['size_zero'] == 1 ? ' checked' : ''; ?>> <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[size_zero]" value="0"<?php echo $options['size_zero'] == 0 ? ' checked' : ''; ?>> <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <?php
    $form = ob_get_clean();

    return $form;
}
