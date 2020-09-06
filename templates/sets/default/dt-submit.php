<form name="frmItems" id="frm-items" method="post" action="<?php echo $formAction; ?>">

    <div class="row">

        <div class="col-sm-8">

            <!-- General data -->
            <div class="cu-box">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="name"><?php _e('Item name','dtransport'); ?></label>
                        <input type="text" name="name" id="name" value="<?php echo $edit ? $sw->get('name') : ''; ?>" size="50" class="form-control input-lg" required>
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
                            <input type="text" name="nameid" value="<?php echo $edit ? $sw->get('nameid') : ''; ?>" class="form-control">
                            <span class="input-group-btn">
                                <a href="<?php echo $sw->permalink(); ?>" target="_blank" class="btn btn-orange"><?php echo $cuIcons->getIcon('svg-rmcommon-eye'); ?></a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shortdesc"><?php _e('Short description','dtransport'); ?></label>
                        <textarea cols="50" rows="3" name="shortdesc" id="shortdesc" class="form-control" required><?php echo $edit?$sw->get('shortdesc','e'):''; ?></textarea>
                        <small class="help-block"><?php _e('This is a small description that will be used as an introduction of the item.','dtransport'); ?></small>
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _e('Categories','dtransport'); ?></h3>
                        </div>
                        <div class="panel-body">
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo _e('Licences','dtransport'); ?></h3>
                        </div>
                        <div class="panel-body">
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo _e('Platforms','dtransport'); ?></h3>
                        </div>
                        <div class="panel-body">
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _e('Allowed groups','dtransport'); ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="dt-form-lists">
                                <?php echo $groups; ?>
                            </div>
                            <small class="help-block"><?php _e('Only users that belong to selected groups, could donwload this item.','dtransport'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _e('Download tags','dtransport'); ?></h3>
                        </div>
                        <div class="panel-body">

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
                        <div class="panel-footer">
                            <small><?php _e('Click on any tag to delete it.','dtransport'); ?></small>
                        </div>
                    </div>
                </div>

            </div>

            <?php
            // Additional fields below allowed groups
            Dtransport_Functions::formAddtionalFields('groups');
            ?>

            <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" />
            <input type="hidden" name="id" id="id-item" value="<?php echo $sw->id_soft; ?>" />
            <input type="hidden" name="page" value="<?php echo $page; ?>" />
            <input type="hidden" name="search" value="<?php echo $search; ?>" />
            <input type="hidden" name="type" value="<?php echo $itemType; ?>" />
            <?php echo $xoopsSecurity->getTokenHTML(); ?>

            <hr>

            <!-- Form Submit -->
            <div id="down-commands"<?php echo $edit?' style="display: block;"':''; ?>>
                <button type="button" id="save-data" class="btn btn-default">
                    <?php echo $common->icons()->getIcon('svg-rmcommon-floppy-disk' ); ?>
                    <?php echo __('Save','dtransport'); ?>
                </button>

                <button type="button" id="verify-data" class="btn btn-primary">
                    <?php echo $common->icons()->getIcon('svg-dtransport-verify' ); ?>
                    <?php echo __('Submit for Review','dtransport'); ?>
                </button>

                <?php if($canPublish): ?>
                <button type="button" id="publish-data" class="btn btn-warning">
                    <?php echo $common->icons()->getIcon('svg-rmcommon-send' ); ?>
                    <?php echo __('Publish Now','dtransport'); ?>
                </button>
                <?php endif; ?>
                <button type="button" id="cancel-data" class="btn btn-default">
                    <?php _e('Cancel','dtransport') ?>
                </button>
            </div>

            <ul id="dt-buttons-ex">
                <li>
                    <small>
                        <?php echo $common->icons()->getIcon('svg-rmcommon-floppy-disk' ); ?> <?php _e('Save changes as draft.', 'dtransport'); ?>
                    </small>
                </li>
                <li>
                    <small>
                        <?php echo $common->icons()->getIcon('svg-dtransport-verify' ); ?> <?php _e('Save changes and request admin approval for publication.', 'dtransport'); ?>
                    </small>
                </li>
                <li>
                    <small>
                        <?php echo $common->icons()->getIcon('svg-rmcommon-send' ); ?> <?php _e('Publish changes inmediatly.', 'dtransport'); ?>
                    </small>
                </li>
            </ul>

        </div>

        <div class="col-sm-4">

            <!-- Download Information -->
            <?php include $common->template()->path('widgets/dt-information-fe.php', 'module', 'dtransport'); ?>

            <?php
            // Additional fields below Information widget
            Dtransport_Functions::formAddtionalFields('information');
            ?>

            <!-- Default image -->
            <?php include $common->template()->path('widgets/dt-image-fe.php', 'module', 'dtransport'); ?>

            <?php
            // Additional fields below image
            Dtransport_Functions::formAddtionalFields('image');
            ?>

            <?php if($xoopsModuleConfig['uselogo']): ?>
                <!-- Download logo -->
                <?php
                include $common->template()->path('widgets/dt-logo-fe.php', 'module', 'dtransport');
                Dtransport_Functions::formAddtionalFields('logo');
                ?>
            <?php endif; ?>


            <!-- Download Options -->
            <?php include $common->template()->path('widgets/dt-options-fe.php', 'module', 'dtransport'); ?>

            <?php
            // Additional fields below fields
            Dtransport_Functions::formAddtionalFields('files');
            ?>


        </div>

    </div>

</form>
