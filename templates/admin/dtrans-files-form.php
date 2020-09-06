<h1 class="cu-section-title"><?php echo sprintf(__('New file in %s','dtransport'), $sw->getVar('name')); ?></h1>

<div class="row">
    <div class="col-md-5">
        <div id="dtfiles-preview"<?php echo $edit&&$file_exists&&!$fl->remote() ? ' style="display: block"' : ''; ?>>
            <div class="image"></div>
            <span class="delete btn btn-danger"><?php _e('Delete File','dtransport'); ?></span>
            <span class="name"><?php echo $edit ? $fl->file() : ''; ?></span>
            <span class="size"><?php echo $edit ? $rmu->formatBytesSize($fl->size()) : ''; ?></span>
            <span class="type"><?php echo $edit ? $fl->mime() : ''; ?></span>
            <span class="secure"><?php $sw->getVar('secure')?_e('Protected Download','dtransport'):_e('Normal Download','dtransport'); ?></span>
        </div>
        <form id="dtfiles-uploader" class="dropzone"<?php echo $edit ? ' style="display: none;"' : ''; ?>>

        </form>
        <div class="text-center">
            <?php echo sprintf(__('You have a limit of %s per file.', 'dtransport'), $common->format()->bytes_format($common->settings()->module_settings('dtransport', 'size_file'), 'mb')); ?>
        </div>
        <div class="dt-errors"></div>
    </div>

    <div class="col-md-7">
        <div class="panel panel-blue-grey">
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('File Details','dtransport'); ?></h3>
            </div>
            <div class="panel-body">
                <div class="help-block"><?php _e('Fill the next fields to create the new file.','dtransport'); ?></div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remote" value="1" id="remote"<?php echo $edit&&$fl->remote()?' checked="checked"':''; ?> />
                            <?php _e('Remote file','dtransport'); ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label><?php _e('Title:','dtransport'); ?></label>
                    <input type="text" id="title" name="title" value="<?php echo $edit ? $fl->title() : ''; ?>" class="form-control">
                </div>

                <div class="form-group">
                    <label><?php _e('Group:','dtransport'); ?></label>
                    <select class="form-control" name="group" id="group">
                        <option value="0"<?php if($edit): ?><?php echo $fl->group()==0?' selected="selected"':''; ?><?php else: ?> selected='selected'<?php endif; ?>><?php _e('Select group...','dtransport'); ?></option>
                        <?php foreach($groups as $g): ?>
                            <option value="<?php echo $g['id']; ?>"<?php echo $edit&&$fl->group()==$g['id']?' selected="selected"':''; ?>><?php echo $g['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group url-container"<?php echo $edit&&$fl->remote() ? ' style="display: block;"': ''; ?>>
                    <label><?php _e('File URL:','dtransport'); ?></label>
                    <input type="text" name="url" id="url" value="<?php echo $edit&&$fl->remote() ? $fl->file():''; ?>" class="form-control">
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="default" id="default" value="1"<?php echo $edit&&$fl->isDefault()?' checked="checked"':''; ?>>
                            <?php _e('This is the default file','dtransport'); ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="button" id="save-data" class="btn btn-blue-grey"><?php $edit ? _e('Save Changes','dtransport') : _e('Save File','dtransport'); ?></button>
                    <button type="button" id="cancel-data" class="btn btn-default"><?php _e('Cancel','dtransport'); ?></button>
                </div>



                <?php echo $common->security()->getTokenHTML(); ?>
                <input type="hidden" name="secure" value="<?php echo $sw->getVar('secure'); ?>" id="secure" />
                <input type="hidden" name="item" value="<?php echo $sw->id(); ?>" id="item" />
                <input type="hidden" name="size" value="<?php echo $edit ? $fl->size() : ''; ?>" id="size" />
                <input type="hidden" name="action" value="<?php echo $edit?'save-edit':'save-file'; ?>" id="action" />
                <?php if($edit): ?>
                    <input type="hidden" name="id" value="<?php echo $fl->id(); ?>" id="id" />
                <?php endif; ?>
                <input type="hidden" name="identifier" id="identifier" value="<?php echo $tc->encrypt(session_id().'|'.$xoopsUser->uid()); ?>">
            </div>
        </div>
    </div>
</div>

<div id="status-bar">
    <?php _e('Applying changes, please wait a second...','dtransport'); ?>
</div>