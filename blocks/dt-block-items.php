<?php
// $Id: dtrasn_bk_items.php 259 2013-11-13 21:35:24Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function dt_block_items($options){
	global $db, $xoopsModule, $common;
	
	//include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtsoftware.class.php';
	//include_once XOOPS_ROOT_PATH.'/modules/dtransport/class/dtfunctions.class.php';

    $dtSettings = $common->settings()::module_settings('dtransport');
    
    $tpl = RMTemplate::getInstance();
    $tpl->add_style('blocks-default.min.css','dtransport');
    
    $dtfunc = new Dtransport_Functions();
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $tbls = $db->prefix("mod_dtransport_items");
    $tblc = $db->prefix("mod_dtransport_catitem");
    
    if($options['category']>0){
        
        $sql = "SELECT s.* FROM $tbls as s, $tblc as c WHERE c.cat='".$options['category']."' AND s.id_soft=c.soft AND s.approved=1 AND s.`delete`=0";
        
    } else {
        
        $sql = "SELECT s.* FROM $tbls as s WHERE s.`approved`=1 AND s.`deletion`=0 ";
        
    }
	
	if (trim($options['user'])>0){
        $user = new RMUser(trim($options['user']));
        if($user->isNew()) return;
		$sql .= " AND s.uid='".$user->id()."' ";
	}
    
    if ($options['category']>0) $sql .= "AND id_cat='$options[11]'";
	
	switch ($options['type']){
        case 'all':
            $sql .= ' ORDER BY RAND() ';
            break;
		case 'recent':
			$sql .= " ORDER BY s.modified DESC, created DESC ";
			break;
		case 'popular':
			$sql .= " ORDER BY s.hits DESC ";
			break;
		case 'rated':
			$sql .= " ORDER BY s.`rating`/s.`votes` DESC ";
			break;
        case 'featured':
            $sql .= " AND featured=1 ORDER BY RAND() ";
            break;
        case 'daily':
            $sql = " AND daily=1 ORDER BY RAND() ";
            break;
	}
	
    $options['limit'] = $options['limit']>0 ? $options['limit'] : 5;
	$sql .= " LIMIT 0, $options[limit]";
	
	$result = $db->query($sql);
	$block = array();
	while($row = $db->fetchArray($result)){
		$item = new Dtransport_Software();
		$item->assignVars($row);
		$rtn = array();
		$rtn['name'] = $item->getVar('name');
        $rtn['version'] = $item->getVar('version');
        
        if($options['image']){

            if($options['image_type'] == 'icon' && '' != $row['logo']){
                $image = $common->resize()::resize($row['logo'], ['width' => $options['size'], 'height' => $options['size']]);
                $rtn['image'] = $image->url;
            }

            if($options['image_type'] == 'image' || ('' == $row['logo'] && '' != $row['image'])){
                $image = $row['image'];
                if('' != $image){
                    $image = $common->resize()::resize($image, ['width' => $options['size']]);
                    $rtn['image'] = $image->url;
                } else {
                    $rtn['image'] = '';
                }
            }

        }
        
		if ($options['description']) $rtn['description'] = $item->getVar('shortdesc');
		if ($options['hits']) $rtn['hits'] = sprintf(__('Downloaded %s times.','dtransport'), '<strong>'.$item->getVar('hits').'</strong>');

		if($options['urating'] && $item->rate > 0 && $item->votes > 0){
            $rtn['urate'] = @number_format($item->getVar('rate')/$item->getVar('votes'), 1);
        } else {
		    $rtn['urate'] = '';
        }

		if ($options['srating']){
			$rtn['siterate'] = Dtransport_Functions::localRating($item->getVar('siterate'));
		}
        $rtn['link'] = $item->permalink();
        $rtn['metas'] = $dtfunc->get_metas('down', $item->id());
		if($options['author']) $rtn['author'] = array('name'=>$item->getVar('author_name'),'url'=>$item->getVar('author_url'));
		$block['downs'][] = $rtn;
	}

    // All Dtransport blocks must provide this property
    $block['tplPath'] = XOOPS_ROOT_PATH . '/modules/dtransport/templates/sets/' . $dtSettings->tplset . '/blocks';

	$block['layout'] = $options['layout'];
	$block['showbutton'] = $options['link'];
	$block['downlang'] = __('Download','dtransport');
    $block['lang_urate'] = __('User rating: %s','dtransport');
	$block['lang_author'] = __('Author: %s','dtransport');
	$block['langhits'] = _BK_DT_HITSTEXT;
	$block['langurate'] = _BK_DT_URATETEXT;
	$block['languser'] = _BK_DT_USERBY;

	return $block;

}

function dt_block_items_edit($options){
	
	include_once RMCPATH.'/class/form.class.php';

	$categories = [];
	Dtransport_Functions::getCategories($categories, 0, 0, [], false, 1);

	ob_start() ?>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-type"><?php _e('Donwloads type','dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <select name="options[type]" class="form-control" id="d-type">
                <option value="all"<?php echo $options['type'] == 'all' ? ' selected' : ''; ?>><?php _e('All types', 'dtransport'); ?></option>
                <option value="recent"<?php echo $options['type'] == 'recent' ? ' selected' : ''; ?>><?php _e('Recent downloads', 'dtransport'); ?></option>
                <option value="popular"<?php echo $options['type'] == 'popular' ? ' selected' : ''; ?>><?php _e('Popular downloads', 'dtransport'); ?></option>
                <option value="rated"<?php echo $options['type'] == 'rated' ? ' selected' : ''; ?>><?php _e('Best rated downloads', 'dtransport'); ?></option>
                <option value="featured"<?php echo $options['type'] == 'featured' ? ' selected' : ''; ?>><?php _e('Featured downloads', 'dtransport'); ?></option>
                <option value="daily"<?php echo $options['type'] == 'daily' ? ' selected' : ''; ?>><?php _e('Daily downloads', 'dtransport'); ?></option>
            </select>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-category"><?php _e('Downloads from category','dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <select class="form-control" name="options[category]" id="d-category">
                <option value="0"<?php $options['category']==0 ? ' selected' : ''; ?>><?php _e('All categories', 'dtransport'); ?></option>
                <?php foreach($categories as $category): ?>
                <option value="<?php echo $category['id_cat']; ?>"<?php $options['category']==$category['id_cat'] ? ' selected' : ''; ?>><?php echo str_repeat("&#151;", $category['jumps']).' '.$category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-limit"><?php _e('Items limit', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <input type="number" class="form-control" name="options[limit]" id="d-limit" value="<?php echo $options['limit']; ?>" min="1" max="20">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-image"><?php _e('Show image', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[image]" value="1" id="d-image"<?php echo $options['image'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[image]" value="0"<?php echo $options['image'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-image-type"><?php _e('Prefered image type', 'dtransport'); ?></label>
            <small class="help-block"><?php _e('If you select "icon" and download icon has not been specified, then the block will show the image.', 'dtransport'); ?></small>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[image_type]" value="image" id="d-image"<?php echo !isset($options['image_type']) || $options['image_type'] == 'image' ? ' checked' : ''; ?>>
                <?php _e('Image', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[image_type]" value="icon"<?php echo $options['image_type'] == 'icon' ? ' checked' : ''; ?>>
                <?php _e('Icon', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-img-size"><?php _e('Image size', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <input type="number" class="form-control" name="options[size]" id="d-img-size" value="<?php echo $options['size']; ?>" min="20" max="500">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-description"><?php _e('Show description', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[description]" value="1" id="d-description"<?php echo $options['description'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[description]" value="0"<?php echo $options['description'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-hits"><?php _e('Show hits', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[hits]" value="1" id="d-hits"<?php echo $options['hits'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[hits]" value="0"<?php echo $options['hits'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-urating"><?php _e('Show users rating', 'dtransport'); ?></label>
            <small class="help-block"><?php _e('If download has not received votes this field will be omitted.', 'dtransport'); ?></small>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[urating]" value="1" id="d-urating"<?php echo $options['urating'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[urating]" value="0"<?php echo $options['urating'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-srating"><?php _e('Show site rating', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[srating]" value="1" id="d-srating"<?php echo $options['srating'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[srating]" value="0"<?php echo $options['srating'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-link"><?php _e('Show download link', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[link]" value="1" id="d-link"<?php echo $options['link'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[link]" value="0"<?php echo $options['link'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-author"><?php _e('Show author', 'dtransport'); ?></label>
        </div>
        <div class="col-sm-7 col-md-9">
            <label class="radio-inline">
                <input type="radio" name="options[author]" value="1" id="d-author"<?php echo $options['author'] == 1 ? ' checked' : ''; ?>>
                <?php _e('Yes', 'dtransport'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[author]" value="0"<?php echo $options['author'] == 0 ? ' checked' : ''; ?>>
                <?php _e('No', 'dtransport'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-user"><?php _e('Show downloads from a single user','dtransport'); ?></label>
            <small class="help-block"><?php _e('You can specify a user name or a integer id of the user.','dtransport'); ?></small>
        </div>
        <div class="col-sm-7 col-md-9">
            <input type="text" class="form-control" name="options[user]" id="d-user" value="<?php echo $options['user']; ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-5 col-md-3">
            <label for="d-format"><?php _e('Layout','dtransport'); ?></label>
            <small class="help-block"><?php _e('Select the prefered layout format for block. Vertical format is recommended for lateral blocks or narrow spaces.','dtransport'); ?></small>
        </div>
        <div class="col-sm-7 col-md-9">
            <div class="table-responsive">
                <table class="table" style="width: auto;">
                    <tr>
                        <td style="padding-right: 15px; border: 0;">
                            <label for="d-layout-v" style="cursor: pointer;"><img src="<?php echo XOOPS_URL; ?>/modules/dtransport/images/layout-v.svg" alt="<?php _e('Vertical', 'dtransport'); ?>" style="width: 70px;"></label>
                        </td>
                        <td style="padding-left: 15px; border: 0;">
                            <label for="d-layout-h"  style="cursor: pointer;"><img src="<?php echo XOOPS_URL; ?>/modules/dtransport/images/layout-h.svg" alt="<?php _e('Horizontal', 'dtransport'); ?>" style="width: 70px;"></label>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="padding-right: 15px; border: 0;">
                            <input type="radio" name="options[layout]" value="vertical" id="d-layout-v"<?php echo $options['layout']=='vertical' ? ' checked' : ''; ?>>
                        </td>
                        <td class="text-center" style="padding-left: 15px; border: 0;">
                            <input type="radio" name="options[layout]" value="horizontal" id="d-layout-h"<?php echo $options['layout']=='horizontal' ? ' checked' : ''; ?>>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <?php

    $form = ob_get_clean();

    return $form;
	
}

