<h1 class="cu-section-title dt_titles"><span style="background-position: left -32px;">&nbsp;</span><?php echo sprintf(__('Features of "%s"','dtransport'), $sw->getVar('name')); ?></h1>

<form name="frmfeat" id="frm-feats" method="POST" action="features.php">
    <div class="cu-bulk-actions">
        <div class="row">
            <div class="col-sm-6">
                <select name="action" id="bulk-top" class="form-control">
                    <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                    <option value="delete"><?php _e('Delete Features','dtransport'); ?></option>
                </select>
                <input type="submit" id="the-op-top" class="btn btn-default" value="<?php _e('Apply','docs'); ?>">
            </div>
            <div class="col-sm-6 text-right">
                <a href="features.php?item=<?php echo $sw->id(); ?>&amp;action=new" class="btn btn-success" id="add-feature">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-plus'); ?>
                    <?php _e('New Feature', 'dtransport'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('Existing Features', 'dtransport'); ?></h3>
        </div>
        <div class="table-responsive">
            <table width="100%" class="table" id="table-features">
                <thead>
                <tr class="head" align="center">
                    <th class="text-center"><input type="checkbox" data-checkbox="features"></th>
                    <th class="text-center"><?php _e('ID','dtransport'); ?></th>
                    <th><?php _e('Title','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Created','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Modified','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr class="head" align="center">
                    <th class="text-center"><input type="checkbox" data-checkbox="features"></th>
                    <th class="text-center"><?php _e('ID','dtransport'); ?></th>
                    <th><?php _e('Title','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Created','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Modified','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                </tr>
                </tfoot>
                <tbody>
                <?php if(empty($features)): ?>
                    <tr class="even" align="center">
                        <td colspan="6"><?php _e('There are not features created for this download item','dtransport'); ?></td>
                    </tr>
                <?php endif; ?>
                <?php foreach($features as $feature): ?>
                    <tr data-id="<?php echo $feature['id']; ?>">
                        <td class="text-center"><input type="checkbox" name="ids[]" id="item-<?php echo $feature['id']; ?>" value="<?php echo $feature['id']; ?>" data-oncheck="features"></td>
                        <td class="text-center" width="20"><strong><?php echo $feature['id']; ?></strong></td>
                        <td class="name"><strong><?php echo $feature['title']; ?></strong></td>
                        <td class="text-center"><?php echo $feature['created']; ?></td>
                        <td class="text-center modified"><?php echo $feature['modified']; ?></td>
                        <td class="text-center cu-options">
                            <a href="#" class="warning edit" data-id="<?php echo $feature['id']; ?>" title="<?php _e('Edit','dtransport'); ?>">
                                <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                            </a>
                            <a href="#" class="danger delete" title="<?php _e('Delete','dtransport'); ?>">
                                <?php echo $cuIcons->getIcon('svg-rmcommon-trash'); ?>
                                <span class="sr-only"><?php _e('Delete','dtransport'); ?></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="cu-bulk-actions">
        <select name="actionb" id="bulk-bottom" class="form-control">
            <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
            <option value="delete"><?php _e('Delete Features','dtransport'); ?></option>
        </select>
        <input type="submit" id="the-op-bottom" class="btn btn-default" value="<?php _e('Apply','docs'); ?>">
    </div>
    <input type="hidden" name="item" id="item-id" value="<?php echo $item; ?>" />
</form>