<h1 class="cu-section-title"><?php echo sprintf(__('"%s" Change Log','dtransport'), $sw->getVar('name')); ?></h1>

<form name="frmlog" id="frm-log" method="POST" action="logs.php">

    <div class="cu-bulk-actions">
        <div class="row">
            <div class="col-sm-8">
                <select name="action" id="bulk-top" class="form-control">
                    <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                    <option value="delete"><?php _e('Delete Logs','dtransport'); ?></option>
                </select>
                <button type="button" id="the-op-top" class="btn btn-default" onclick="before_submit('frm-categories');"><?php _e('Apply','docs'); ?></button>
            </div>
            <div class="col-sm-4 text-right">
                <a href="logs.php?item=<?php echo $item; ?>&amp;action=new" class="btn btn-primary">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-plus'); ?>
                    <?php _e('Add Log', 'dtransport'); ?>
                </a>
            </div>
        </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('Changes Log', 'dtransport'); ?></h3>
        </div>
        <div class="table-responsive">
            <table class="table" width="100%">
                <thead>
                <tr align="center">
                    <th class="text-center" width="20"><input type="checkbox" data-checkbox="logs"></th>
                    <th class="text-center"><?php _e('ID','dtransport'); ?></th>
                    <th><?php _e('Log title','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Date','dtransport'); ?></th>
                    <th><?php echo __('Content','dtransport'); ?></th>
                    <th class="text-center"><?php echo __('Options','dtransport'); ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr align="center">
                    <th class="text-center" width="20"><input type="checkbox" data-checkbox="logs"></th>
                    <th class="text-center"><?php _e('ID','dtransport'); ?></th>
                    <th><?php _e('Log title','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Date','dtransport'); ?></th>
                    <th><?php echo __('Content','dtransport'); ?></th>
                    <th class="text-center"><?php echo __('Options','dtransport'); ?></th>
                </tr>
                </tfoot>
                <tbody>
                <?php if(empty($logs)): ?>
                    <tr class="head">
                        <td colspan="5" align="center"><?php _e('There are not logs for this download item.','dtransport'); ?></td>
                    </tr>
                <?php endif; ?>
                <?php foreach($logs as $log): ?>
                    <tr>
                        <td class="text-center"><input type="checkbox" name="ids[]" id="item-<?php echo $log['id']; ?>" value="<?php echo $log['id']; ?>" data-oncheck="logs"></td>
                        <td width="20" class="text-center"><strong><?php echo $log['id']; ?></strong></td>
                        <td>
                            <?php echo $log['title']; ?>
                        </td>
                        <td class="text-center"><?php echo $log['date']; ?></td>
                        <td><?php echo $log['log']; ?></td>
                        <td class="cu-options text-center">
                            <a href="logs.php?action=edit&amp;id=<?php echo $log['id']; ?>&amp;item=<?php echo $item; ?>" class="warning">
                                <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                            </a>
                            <a href="#" onclick="dt_check_delete(<?php echo $log['id']; ?>,'frm-log');" class="danger" title="<?php _e('Delete','dtransport'); ?>">
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
            <option value="delete"><?php _e('Delete Logs','dtransport'); ?></option>
        </select>
        <button type="button" id="the-op-bottom" class="btn btn-default" onclick="before_submit('frm-categories');"><?php _e('Apply','docs'); ?></button>
    </div>
<input type="hidden" name="item" value="<?php echo $item; ?>" />
<?php echo $xoopsSecurity->getTokenHTML(); ?>
</form>

