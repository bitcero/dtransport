<h1 class="cu-section-title"><?php echo sprintf(__('Licenses Management','dtransport')); ?></h1>

<div class="row">
    <div class="col-sm-6 col-md-4">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('Add New License', 'dtransport'); ?></h3>
            </div>
            <div class="panel-body">
                <form name="newLic" id="form-new-lic" method="post" action="licenses.php">
                    <div class="form-group">
                        <label for="name"><?php _e('License name','dtransport'); ?></label>
                        <input type="text" name="name" id="name" value="" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="url" class="captions"><?php _e('License URL','dtransport'); ?></label>
                        <input type="text" name="url" id="url" value="" class="form-control url" required>
                        <small class="help-block">
                            <?php _e('This URL will be used to give a reference to users that need to know more about specified license.','dtransport'); ?>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="type" class="captions"><?php _e('License type','dtransport'); ?></label>
                        <div class="radio">
                            <label>
                                <input type="radio" name="type" id="type-1" value="1" checked>
                                <?php _e('Open source license','dtransport'); ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="type" id="type-0" value="0">
                                <?php _e('Restrictive license','dtransport'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="XOOPS_TOKEN_REQUEST" value="<?php echo $xoopsSecurity->createToken(); ?>">
                        <button type="submit" id="lic-submit" class="btn btn-primary btn-lg"><?php _e('Add License','dtransport'); ?></button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <div class="col-sm-6 col-md-8">

        <form name="frmlic" id="frm-lics" method="POST" action="licenses.php">
            <div class="table-responsive">
                <div class="cu-bulk-actions">
                    <select name="action" id="bulk-top" class="form-control">
                        <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                        <option value="delete"><?php _e('Delete','dtransport'); ?></option>
                    </select>
                    <button type="button" id="the-op-top" onclick="before_submit('frm-lics');" class="btn btn-default">
                        <?php _e('Apply','docs'); ?>
                    </button>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _e('Existing Licences', 'dtransport'); ?></h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr align="center">
                                <th width="20" class="text-center">
                                    <input type="checkbox" data-checkbox="chk-licenses">
                                </th>
                                <th width="20" class="text-center"><?php _e('ID','dtransport'); ?></th>
                                <th><?php _e('Name','dtransport'); ?></th>
                                <th class="text-center"><?php _e('URL','dtransport'); ?></th>
                                <th class="text-center"><?php _e('Type','dtransport'); ?></th>
                                <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($licences)): ?>
                                <tr class="text-center">
                                    <td colspan="6">
                                        <span class="center-block text-info"><?php _e('There are not licenses created yet!','dtransport'); ?></span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($licences as $lic): ?>
                                <tr valign="top">
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" id="item-<?php echo $lic['id']; ?>" value="<?php echo $lic['id']; ?>" data-oncheck="chk-licenses">
                                    </td>
                                    <td class="text-center"><strong><?php echo $lic['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo $lic['name']; ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $lic['url']; ?>" target="_blank" class="btn btn-info"><span class="fa fa-link"></span></a>
                                    </td>
                                    <td class="text-center">
                                        <span class="fa <?php echo $lic['type'] ? ' fa-cc text-success' : 'fa-copyright text-danger'; ?>"></span>
                                    </td>
                                    <td class="text-center cu-options">
                                        <a href="licenses.php?action=edit&amp;id=<?php echo $lic['id']; ?>" class="warning" title="<?php _e('Edit','dtransport'); ?>">
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                            <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                                        </a>
                                        <a href="#" onclick="dt_check_delete(<?php echo $lic['id']; ?>, 'frm-lics'); return false;" class="danger" title="<?php _e('Delete','dtransport'); ?>">
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
                        <option value="delete"><?php _e('Delete','dtransport'); ?></option>
                    </select>
                    <button type="button" id="the-op-bottom" onclick="before_submit('frm-lics');" class="btn btn-default">
                        <?php _e('Apply','docs'); ?>
                    </button>
                </div>
                <?php echo $xoopsSecurity->getTokenHTML(); ?>
            </div>
        </form>
    </div>
</div>
