<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php _e('Credits', 'dtransport'); ?></h3>
    </div>
    <div class="panel-body">

        <div class="form-group">
            <label for="author"><?php _e('Author name', 'dtransport'); ?></label>
            <input type="text" name="author" id="author" value="<?php echo $edit ? $sw->get('author_name') : $xoopsUser->getVar('name'); ?>"
                   class="form-control">
        </div>
        <div class="form-group">
            <label for="url"><?php _e('Author URL', 'dtransport'); ?></label>
            <input type="text" name="url" id="url" value="<?php echo $edit ? $sw->get('author_url') : ''; ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="email"><?php _e('Author Email', 'dtransport'); ?></label>
            <input type="text" name="email" id="email" value="<?php echo $edit ? $sw->get('author_email') : $xoopsUser->email(); ?>" class="form-control">
            <small class="help-block"><?php _e('This email will not be visible for users', 'dtransport'); ?></small>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="contact" id="contact" value="1"<?php echo $sw->get('author_contact') ? ' checked="checked"' : ''; ?>>
                    <?php _e('Author can be contacted', 'dtransport'); ?>
                </label>
            </div>
        </div>

    </div>
</div>