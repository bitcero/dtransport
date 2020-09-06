<div class="cu-box box-primary">
    <div class="box-header">
        <span class="box-handler"><span class="fa fa-caret-down"></span></span>
        <h3 class="box-title"><?php _e('Download Information', 'dtransport'); ?></h3>
    </div>
    <div class="box-content">
        <div class="form-group">
            <label for="version"><?php _e('Current Version','dtransport'); ?></label>
            <input type="text" name="version" id="version" value="<?php echo $edit ? $sw->getVar('version') : ''; ?>" class="form-control" required>
            <small class="help-block"><?php _e('Indicate the current version of this item.','dtransport'); ?></small>
        </div>
        <div class="form-group">
            <label for="limits"><?php _e('Downloads limit per user','dtransport'); ?></label>
            <input type="text" name="limits" id="limits" value="<?php echo $edit ? $sw->getVar('limits') : '0'; ?>" size="20" class="form-control" required>
            <small class="help-block"><?php _e('Users could download this item only this times. Leave 0 for unlimited times.','dtransport'); ?></small>
        </div>
        <div class="form-group">
            <label for="langs"><?php _e('Available languages','dtransport'); ?></label>
            <input type="text" name="langs" id="langs" value="<?php echo $edit ? $sw->getVar('langs') : 'English'; ?>" size="20" class="form-control" required>
            <small class="help-block"><?php _e('Specify every language separated by comma.','dtransport'); ?></small>
        </div>

        <div class="form-group">
            <label for="siterate"><?php echo _e('Site Rating:','dtransport'); ?></label>
            <select name="siterate" id="siterate" class="form-control" required>
                <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?php echo $i; ?>"<?php echo $sw->getVar('siterate')==$i?' selected="selected"':''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <table class="table">
            <tr>
                <td>
                    <label for="approved"><?php _e('Approved:','dtransport'); ?></label>
                </td>
                <td>
                    <?php echo $approved; ?>
                </td>
            </tr>
            <tr>
                <td><label for="mark"><?php _e('Featured:','dtransport'); ?></label></td>
                <td><?php echo $featured; ?></td>
            </tr>
            <tr>
                <td><label for="secure"><?php _e('Protected:','dtransport'); ?></label></td>
                <td><?php echo $secure; ?></td>
            </tr>
        </table>

        <div class="form-group">
            <label for="password"><?php _e('Download password','dtransport'); ?></label><br />
            <input type="password" name="password" id="password" value="<?php echo $edit ? $sw->getVar('password') : ''; ?>" class="form-control">
            <small class="help-block"><?php _e('If you specify a password for this item, users must know it in order to download files belonging to it.','dtransport'); ?></small>
            <small class="help-block"><?php _e('If a password is provided for this item, the protected status will set to on automatically.','dtransport'); ?></small>
        </div>
    </div>
</div>