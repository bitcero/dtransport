<div class="tab-pane" role="tabpanel" id="tab-credits">

        <div class="form-group">
            <label><?php _e('Published by','dtransport'); ?></label>
            <?php echo $user; ?>
        </div>
        <div class="form-group">
            <label for="author"><?php _e('Author name','dtransport'); ?></label>
            <input type="text" name="author" id="author" value="<?php echo $edit ? $sw->getVar('author_name') : ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="url"><?php _e('Author URL','dtransport'); ?></label>
            <input type="text" name="url" id="url" value="<?php echo $edit ? $sw->getVar('author_url') : ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="email"><?php _e('Author Email','dtransport'); ?></label>
            <input type="text" name="email" id="email" value="<?php echo $edit ? $sw->getVar('author_email') : $xoopsUser->email(); ?>" class="form-control">
            <small class="help-block"><?php _e('This email will not be visible for users','dtransport'); ?></small>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="contact" id="contact" value="1"<?php echo $sw->getVar('author_contact')?' checked="checked"':''; ?>>
                    <?php _e('Author can be contacted','dtransport'); ?>
                </label>
            </div>
        </div>

</div>