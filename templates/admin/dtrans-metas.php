<div id="dt-metas-container">
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th><strong><?php _e('Field name','dtransport'); ?></strong></th>
                <th><strong><?php _e('Field value','dtransport'); ?></strong></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <input type="text" name="meta_name" id="meta-name" class="form-control" style="display: <?php echo empty($metaNames) ? 'block' : 'none'; ?>" />
                    <?php if(!empty($metaNames)): ?>
                        <a href="#" id="cancel-name"<?php echo !empty($metaNames) ? ' style="display: none"' : ''; ?> class="btn btn-link btn-sm">
                            <span class="fa fa-ban text-danger"></span>
                            <?php _e('Cancel','dtransport'); ?>
                        </a>
                        <select name="selname" id="meta-sel-name" class="form-control">
                            <option value=""><?php _e('Select field...','dtransport'); ?></option>
                            <?php foreach($metaNames as $meta): ?>
                                <option value="<?php echo $meta; ?>"><?php echo $meta; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a href="#" id="new-meta-name" style="display: block;"><?php _e('New Field','dtransport'); ?></a>
                    <?php endif; ?>
                    <label class="error forname"></label>
                </td>
                <td>
                    <textarea rows="5" cols="40" name="meta_value" id="meta-value" class="form-control"></textarea>
                    <label class="error forvalue"><?php _e('You must specify a value for this custom field!','dtransport'); ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="button" class="btn btn-warning" id="add-meta"><?php _e('Add Meta','dtransport'); ?></button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<hr>

<div class="table-responsive">
    <table class="table" id="the-fields">
        <thead>
        <tr>
            <th><?php _e('Field name','dtransport'); ?></th>
            <th><?php _e('Field value','dtransport'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($metas as $meta): ?>
            <tr id="field-<?php echo $meta['name']; ?>">
                <td>
                    <input type="text" name="dtMetas[<?php echo $meta['name']; ?>][name]" value="<?php echo $meta['name']; ?>" class="form-control"><br />
                    <a href="#" class="btn btn-link btn-sm text-danger del-field" onclick="delete_field($(this)); return false;">
                        <span class="fa fa-times-circle text-danger"></span>
                        <?php _e('Delete Field','dtransport'); ?>
                    </a>
                </td>
                <td>
                    <textarea name="dtMetas[<?php echo $meta['name']; ?>][value]" rows="3" cols="45" class="form-control"><?php echo $meta['value']; ?></textarea>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
