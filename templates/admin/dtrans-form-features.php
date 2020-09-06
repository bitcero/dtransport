<?php
include_once RMCPATH . '/class/form.class.php';
$form = new RMActiveForm([
    'action' => 'features.php',
    'validation' => 'local',
    'id' => 'frm-features'
]);
$form->open();
?>
<div class="row">
    <div class="col-sm-7">
        <div class="form-group">
            <label for="feat-title"><?php _e('Feature title', 'dtransport'); ?>*</label>
            <input type="text" name="title" id="feat-title" maxlength="200" class="form-control" value="<?php echo $ft->title; ?>" placeholder="<?php _e('e.g. Fully Responsive', 'dtransport'); ?>" required>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="form-group">
            <label for="feat-short"><?php _e('Feature short name (optional)', 'dtransport'); ?></label>
            <input type="text" name="nameid" id="feat-short" maxlength="200" class="form-control" value="<?php echo $ft->nameid; ?>" placeholder="<?php _e('e.g. fully-responsive', 'dtransport'); ?>">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="content"><?php _e('Feature content', 'dtransport'); ?>*</label>
    <?php

    $editor = new RMFormEditor([
        'caption' => '',
        'name' => 'content',
        'id' => 'content',
        'height' => '300px',
        'value' => $cuSettings->editor_type == 'tiny' ? $ft->content : $ft->getVar('content', 'e')
    ]);
    echo $editor->render();

    ?>
</div>

<div class="cu-content-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'dtransport'); ?></button>
    <button type="submit" class="btn btn-orange"><?php _e('Save Feature', 'dtransport'); ?></button>
</div>
<input type="hidden" name="action" value="<?php echo $ft->isNew() ? 'save' : 'save-edited'; ?>">
<?php if(false == $ft->isNew()): ?>
<input type="hidden" name="id" value="<?php echo $ft->id(); ?>">
<?php endif; ?>
<input type="hidden" name="item" value="<?php echo $sw->id(); ?>">
<?php
$form->close();