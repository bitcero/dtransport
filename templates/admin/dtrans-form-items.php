<h1 class="cu-section-title dt_titles">
    <?php echo $location; ?>
</h1>

<form name="frmItems" id="frm-items" method="post" action="items.php">

    <div class="row">

        <div class="col-sm-8">

            <!-- General data -->
            <div class="cu-box">
                <div class="box-content">
                    <div class="form-group">
                        <label for="name"><?php _e('Item name','dtransport'); ?></label>
                        <input type="text" name="name" id="name" value="<?php echo $edit ? $sw->getVar('name') : ''; ?>" size="50" class="form-control input-lg" required>
                        <small class="help-block">
                            <?php _e('Specify a name that identifies this download. This name must be unique and different of other downloads.','dtransport'); ?>
                        </small>
                    </div>

                    <div class="form-group" id="item-permalink"<?php echo $edit?' style="display: block;"':''; ?>>
                        <label><?php _e('Item permalink:','dtransport'); ?></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <?php echo Dtransport_Functions::moduleURL(); ?>/
                            </span>
                            <input type="text" name="nameid" value="<?php echo $edit ? $sw->nameid : ''; ?>" class="form-control">
                            <span class="input-group-btn">
                                <a href="<?php echo $sw->permalink(); ?>" target="_blank" class="btn btn-orange"><?php echo $cuIcons->getIcon('svg-rmcommon-eye'); ?></a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shortdesc"><?php _e('Short description','dtransport'); ?></label>
                        <textarea cols="50" rows="3" name="shortdesc" id="shortdesc" class="form-control" required><?php echo $edit?$sw->getVar('shortdesc','e'):''; ?></textarea>
                        <span class="description"><?php _e('This is a small description that will be used as an advance of the item.','dtransport'); ?></span>
                    </div>

                    <div class="form-group">
                        <?php
                        echo $ed->render();
                        ?>
                    </div>
                </div>
            </div>

            <?php
            // Additional fields below general data
            Dtransport_Functions::formAddtionalFields('general');
            ?>

            <!-- Categories, Licenses and Platforms -->
            <div class="row">

                <div class="col-sm-4">
                    <div class="cu-box box-default">
                        <div class="box-header">
                            <span class="fa fa-caret-up box-handler"></span>
                            <h3 class="box-title"><?php _e('Categories','dtransport'); ?></h3>
                        </div>
                        <div class="box-content">
                            <div class="dt-form-lists">
                                <ul class="list-unstyled dt-categories">
                                    <?php foreach($categories as $cat): ?>
                                        <li style="padding-left: <?php echo ($cat['indent']*10); ?>px;<?php if($cat['indent']==0): ?> color: #333;<?php endif; ?>"><label><input type="checkbox" name="catids[]" value="<?php echo $cat['id']; ?>" id="cat-id-<?php echo $cat['id']; ?>"<?php echo $cat['selected']?' checked="checked"':''; ?> /> <?php echo $cat['name']; ?></label></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <small class="help-block">
                                <?php _e('Select the categories that you want to assign to this item.','dtransport'); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="cu-box box-default">
                        <div class="box-header">
                            <span class="fa fa-caret-up box-handler"></span>
                            <h3 class="box-title"><?php echo _e('Licences','dtransport'); ?></h3>
                        </div>
                        <div class="box-content">
                            <div class="dt-form-lists">
                                <ul class="list-unstyled dt-licenses">
                                    <?php foreach($lics as $lic): ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="lics[]" id="lic-<?php echo $lic['id']; ?>" value="<?php echo $lic['id']; ?>"<?php echo $lic['selected']?' checked="checked"':''; ?> /> <?php echo $lic['name']; ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <small class="help-block">
                                <?php _e('Select the licenses that you want to assign to this item.','dtransport'); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="cu-box box-default">
                        <div class="box-header">
                            <span class="fa fa-caret-up box-handler"></span>
                            <h3 class="box-title"><?php echo _e('Platforms','dtransport'); ?></h3>
                        </div>
                        <div class="box-content">
                            <div class="dt-form-lists">
                                <ul class="list-unstyled dt-platforms">
                                    <?php foreach($oss as $os): ?>
                                        <li>
                                            <label><input type="checkbox" name="platforms[]" id="lic-<?php echo $os['id']; ?>" value="<?php echo $os['id']; ?>"<?php echo $os['selected']?' checked="checked"':''; ?> /> <?php echo $os['name']; ?></label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <small class="help-block"><?php _e('Select that platforms over this item can be used.','dtransport'); ?></small>
                        </div>
                    </div>
                </div>

            </div>

            <?php
            // Additional fields below categories
            Dtransport_Functions::formAddtionalFields('categories');
            ?>

            <!-- Allowed groups and tags -->
            <div class="row">

                <div class="col-sm-5">
                    <div class="cu-box box-primary">
                        <div class="box-header">
                            <span class="fa fa-caret-up box-handler"></span>
                            <h3 class="box-title"><?php _e('Allowed groups','dtransport'); ?></h3>
                        </div>
                        <div class="box-content">
                            <div class="dt-form-lists">
                                <?php echo $groups; ?>
                            </div>
                            <small class="help-block"><?php _e('Only users that belong to selected groups, could donwload this item.','dtransport'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-7">
                    <div class="cu-box box-primary">
                        <div class="box-header">
                            <span class="fa fa-caret-up box-handler"></span>
                            <h3 class="box-title"><?php _e('Download tags','dtransport'); ?></h3>
                        </div>
                        <div class="box-content">

                            <div class="list_small">
                                <div class="input-group">
                                    <input type="text" name="tags" id="tags" size="50" class="form-control">
                            <span class="input-group-btn">
                                <button type="button" id="add-tags" class="btn btn-primary"><?php _e('Add Tags','dtransport'); ?></button>
                            </span>
                                </div>
                                <small class="help-block"><?php _e('Separate each tag with a comma (,).','dtransport'); ?></small>

                                <div id="tags-container">
                                    <?php foreach($tags as $tag): ?>
                                        <span class="tag"><?php echo $tag; ?></span>
                                        <input type="hidden" name="tags[]" value="<?php echo $tag; ?>" />
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                        <div class="box-footer">
                            <?php _e('Click on any tag to delete it.','dtransport'); ?>
                        </div>
                    </div>
                </div>

            </div>

            <?php
            // Additional fields below allowed groups
            Dtransport_Functions::formAddtionalFields('groups');
            ?>

            <!-- Custom fields -->
            <div class="cu-box box-default">
                <div class="box-header">
                    <span class="fa fa-caret-down box-handler"></span>
                    <h3 class="box-title"><?php _e('Custom fields','dtransport'); ?></h3>
                </div>
                <div class="box-content">
                    <?php echo $functions->meta_form('down', $edit ? $sw->id() : ''); ?>
                </div>
            </div>

            <?php
            // Additional fields below general data
            Dtransport_Functions::formAddtionalFields('custom-fields');
            ?>

            <input type="hidden" name="action" id="action" value="<?php echo $edit ? ($type=='edit' ? 'savewait' : 'saveedit') : 'save'; ?>" />
            <input type="hidden" name="id" id="soft-id" value="<?php echo $id; ?>" />
            <input type="hidden" name="page" value="<?php echo $page; ?>" />
            <input type="hidden" name="search" value="<?php echo $search; ?>" />
            <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
            <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
            <input type="hidden" name="cat" value="<?php echo $catid; ?>" />
            <input type="hidden" name="type" value="<?php echo $type; ?>" />
            <?php echo $xoopsSecurity->getTokenHTML(); ?>
        </div>

        <div class="col-sm-4">

            <?php if($edited && false == $edited->isNew()): ?>
            <!-- Message field -->
                <div class="cu-box box-blue-grey">
                    <div class="box-header">
                        <span class="box-handler"><span class="fa fa-caret-down"></span></span>
                        <h3 class="box-title">
                            <?php echo $common->icons()->getIcon('svg-rmcommon-envelope'); ?>
                            <?php _e('Message to Owner', 'dtransport'); ?>
                        </h3>
                    </div>
                    <div class="box-content">
                        <textarea class="form-control" name="message" value="" rows="3"></textarea>
                        <small class="help-block">
                            <?php _e('You can send a customized message to owner in order to inform about any detail related to this download item (e.g. approval or unapproval reasons)', 'dtransport'); ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Download Information -->
            <?php include $common->template()->path('widgets/dtrans-information.php', 'module', 'dtransport'); ?>

            <?php
            // Additional fields below Information widget
            Dtransport_Functions::formAddtionalFields('information');
            ?>

            <?php if($xoopsModuleConfig['uselogo']): ?>
                <!-- Download logo -->
                <?php
                include $common->template()->path('widgets/dtrans-logo.php', 'module', 'dtransport');
                Dtransport_Functions::formAddtionalFields('logo');
                ?>
            <?php endif; ?>

            <!-- Default image -->
            <div class="cu-box box-primary">
                <div class="box-header">
                    <span class="box-handler"><span class="fa fa-caret-down"></span></span>
                    <h3 class="box-title"><?php _e('Default Image', 'dtransport'); ?></h3>
                </div>
                <div class="box-content">
                    <?php
                    if($common->plugins()->isInstalled('advform-pro')) {

                        $image = new RMFormImageUrl('', 'image', $sw->image);
                        echo $image->render();

                    } else {

                        echo $common->utilities()->image_manager('image', 'image', $sw->image, array('accept' => 'thumbnail', 'multiple' => 'no'));

                    }
                    ?>
                </div>
            </div>

            <?php
            // Additional fields below image
            Dtransport_Functions::formAddtionalFields('image');
            ?>


            <!-- Download Options -->
            <?php include $common->template()->path('widgets/dtrans-options.php', 'module', 'dtransport'); ?>

            <?php
            // Additional fields below fields
            Dtransport_Functions::formAddtionalFields('files');
            ?>


        </div>

    </div>

</form>

<div id="down-blocker">
    
</div>
<div id="down-loader">
    <img src="../images/219.gif" title="<?php _e('Saving data...','dtransport'); ?>" width="64" height="64" /><br />
    <span></span>
</div>
<div id="down-commands"<?php echo $edit?' style="display: block;"':''; ?>>
    <a href="#" id="save-data" class="btn btn-primary btn-lg">
        <span class="icon icon-floppy-disk"></span>
        <?php echo $edit ? __('Save Changes','dtransport') : __('Save Download','dtransport'); ?>
    </a>
    <a href="#" id="cancel-data" class="btn btn-default btn-lg">
        <span class="icon icon-cancel-circle text-danger"></span>
        <?php _e('Cancel','dtransport') ?>
    </a>
</div>