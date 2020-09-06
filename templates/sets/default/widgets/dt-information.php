<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php _e('Download Information', 'dtransport'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="version"><?php _e('Current Version','dtransport'); ?></label>
            <input type="text" name="version" id="version" value="<?php echo $edit ? $sw->get('version') : ''; ?>" class="form-control" required>
            <small class="help-block"><?php _e('Indicate the current version of this item.','dtransport'); ?></small>
        </div>
        <?php if($common->privileges()::verify('dtransport', 'limit-downloads', '', false)): ?>
        <div class="form-group">
            <label for="limits"><?php _e('Downloads limit per user','dtransport'); ?></label>
            <input type="text" name="limits" id="limits" value="<?php echo $edit ? $sw->get('limits') : '0'; ?>" size="20" class="form-control" required>
            <small class="help-block"><?php _e('Users could download this item only this times. Leave 0 for unlimited times.','dtransport'); ?></small>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="langs"><?php _e('Available languages','dtransport'); ?></label>
            <input type="text" name="langs" id="langs" value="<?php echo $edit ? $sw->get('langs') : 'English'; ?>" size="20" class="form-control" required>
            <small class="help-block"><?php _e('Specify every language separated by comma.','dtransport'); ?></small>
        </div>

        <table class="table">
            <?php if($common->privileges()::verify('dtransport', 'approve-items', '', false)): ?>
            <tr>
                <td>
                    <label for="approved"><?php _e('Publish:','dtransport'); ?></label>
                </td>
                <td>
                    <?php echo $approved; ?>
                </td>
            </tr>
            <?php endif; ?>

            <?php if($common->privileges()::verify('dtransport', 'featured-items', '', false)): ?>
            <tr>
                <td><label for="mark"><?php _e('Featured:','dtransport'); ?></label></td>
                <td><?php echo $featured; ?></td>
            </tr>
            <?php endif; ?>

            <?php if($common->privileges()::verify('dtransport', 'secure-items', '', false)): ?>
            <tr>
                <td><label for="secure"><?php _e('Protected:','dtransport'); ?></label></td>
                <td><?php echo $secure; ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <?php if($common->privileges()::verify('dtransport', 'secure-items', '', false)): ?>
        <div class="form-group">
            <label for="password"><?php _e('Download password','dtransport'); ?></label><br />
            <input type="password" name="password" id="password" value="<?php echo $edit ? $sw->get('password') : ''; ?>" class="form-control">
            <small class="help-block"><?php _e('If you specify a password for this item, users must know it in order to download files belonging to it.','dtransport'); ?></small>
            <small class="help-block"><?php _e('If a password is provided for this item, the protected status will set to on automatically.','dtransport'); ?></small>
        </div>
        <?php endif; ?>
    </div>
</div>