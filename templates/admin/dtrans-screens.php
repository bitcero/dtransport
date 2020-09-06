<h1 class="cu-section-title dt_titles"><?php echo sprintf(__('"%s" Screenshots','dtransport'), $sw->getVar('name')); ?></h1>

<form name="frmscreen" id="frm-screens" method="POST" action="screens.php">

    <div class="cu-bulk-actions">
        <div class="row">
            <div class="col-sm-6">
                <select name="op" id="bulk-top" class="form-control">
                    <option value=""><?php _e('Bulk Actions...','rmcommon'); ?></option>
                    <option value="delete-screen"><?php _e('Delete','rmcommon'); ?></option>
                </select>
                <button type="submit" class="btn btn-default" id="the-op-top"><?php _e('Apply','rmcommon'); ?></button>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-primary" id="screens-selector">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-plus'); ?>
                    <?php _e('Add Image', 'dtransport'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo sprintf(__('Screenshots from "%s"', 'dtransport'), $sw->name); ?></h3>
        </div>
        <div class="table-responsive">
            <table class="table" id="table-screens">
                <thead>
                <tr class="text-center">
                    <th><input type="checkbox" data-checkbox="screens"></th>
                    <th width="30"><?php _e('ID','dtransport'); ?></th>
                    <th width="80"><?php _e('Image','dtransport'); ?></th>
                    <th><?php _e('Title','dtransport'); ?></th>
                    <th><?php _e('Description','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr align="center">
                    <th><input type="checkbox" data-checkbox="screens"></th>
                    <th width="30"><?php _e('ID','dtransport'); ?></th>
                    <th width="80"><?php _e('Image','dtransport'); ?></th>
                    <th><?php _e('Title','dtransport'); ?></th>
                    <th><?php _e('Description','dtransport'); ?></th>
                    <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                </tr>
                </tfoot>
                <tbody>
                <?php if(empty($screens)): ?>
                    <tr class="text-info text-center">
                        <td colspan="6"><?php _e('There are not screenshots created for this download item!','dtransport'); ?></td>
                    </tr>
                <?php endif; ?>
                <?php foreach($screens as $screen): ?>
                    <tr data-id="<?php echo $screen['id']; ?>" valign="top">
                        <td class="text-center">
                            <input type="checkbox" name="ids[]" value="<?php echo $screen['id']; ?>" data-oncheck="screens">
                        </td>
                        <td class="text-center" width="20">
                            <strong><?php echo $screen['id']; ?></strong>
                        </td>
                        <td align="center">
                            <a href="<?php echo $screen['image']; ?>" target="_blank">
                                <img src="<?php echo $common->resize()->resize($screen['image'], ['width' => 200, 'height' => 200])->url; ?>">
                            </a>
                        </td>
                        <td class="the-title">
                            <strong><?php echo $screen['title']; ?></strong>
                        </td>
                        <td class="the-desc"><?php echo $screen['desc']; ?></td>
                        <td class="cu-options">
                            <a href="#" class="edit-screen warning" title="<?php _e('Edit','dtransport'); ?>">
                                <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                            </a>
                            <a href="#" class="delete-screen danger" title="<?php _e('Delete','dtransport'); ?>">
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

    <input type="hidden" name="item" id="item-id" value="<?php echo $sw->id(); ?>">
</form>

<div class="row">
    <div class="col-sm-4 col-md-3">
        <div class="screens_uploader">
            <div id="images-uploader"></div>
            <div id="dt-errors"></div>
        </div>
    </div>

    <div class="col-sm-8 col-md-9">



    </div>

</div>
