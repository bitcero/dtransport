<h1 class="cu-section-title dt_titles">
    <span class="icon icon-cog"></span> <?php _e('Platforms Management','dtransport'); ?>
</h1>

<script type="text/javascript">
    $(document).ready(function(){
        $("#form-new-platform").validate({
            messages: {
                name: "<?php _e('Please specify a name for this platform','dtransport'); ?>"
            }
        });
    });
</script>

<div class="row">

    <div class="col-sm-6 col-md-4">
        <form name="newPlat" id="form-new-platform" method="post" action="platforms.php">
            <h4>Add New Platform</h4>

            <div class="form-group">
                <label for="name" class="captions"><?php _e('Platform name','dtransport'); ?></label>
                <input type="text" name="name" id="name" value="<?php echo $edit ? $plat->name() : ''; ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <input type="hidden" name="action" value="<?php echo $edit ? 'saveedit' : 'save'; ?>" />
                <input type="hidden" name="XOOPS_TOKEN_REQUEST" value="<?php echo $xoopsSecurity->createToken(); ?>" />
                <button type="submit" id="plat-submit" class="btn btn-primary btn-lg"><?php $edit ? _e('Save Platform','dtransport') : _e('Add Platform','dtransport'); ?></button>
                <?php if($edit): ?><input type="hidden" name="id" value="<?php echo $plat->id(); ?>" /><?php endif; ?>
            </div>

        </form>
    </div>

    <div class="col-sm-6 col-md-8">

        <form name="frmplat" id="frm-plats" method="POST" action="platforms.php">

            <div class="table-responsive">

                <div class="cu-bulk-actions">
                    <select name="action" id="bulk-top" class="form-control">
                        <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                        <option value="delete"><?php _e('Delete','dtransport'); ?></option>
                    </select>
                    <button type="button" id="the-op-top" onclick="before_submit('frm-lics');" class="btn btn-default"><?php _e('Apply','docs'); ?></button>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _e('Existing Platforms', 'dtransport'); ?></h3>
                    </div>

                    <table class="outer" width="100%" cellspacing="1">
                        <thead>
                        <tr class="head" align="center">
                            <th width="20"><input type="checkbox" data-checkbox="platforms"></th>
                            <th><?php _e('ID','dtransport'); ?></th>
                            <th><?php _e('Name','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($platforms)): ?>
                            <tr class="even" align="center">
                                <td colspan="3"><?php _e('There are not platforms created yet!', 'dtransport'); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($platforms as $plat): ?>
                            <tr class="<?php echo tpl_cycle('even,odd'); ?>" align="center" valign="top">
                                <td><input type="checkbox" name="ids[]" id="item-<?php echo $plat['id']; ?>" value="<?php echo $plat['id']; ?>" data-oncheck="platforms"></td>
                                <td width="20"><strong><?php echo $plat['id']; ?></strong></td>
                                <td align="left">
                                    <?php echo $plat['name']; ?>
                                </td>
                                <td class="text-center cu-options">
                                    <a href="platforms.php?action=edit&amp;id=<?php echo $plat['id']; ?>" class="warning" title="<?php _e('Edit','dtransport'); ?>">
                                        <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                        <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                                    </a>
                                    <a href="#" onclick="dt_check_delete(<?php echo $plat['id']; ?>, 'frm-plats'); return false;" class="danger" title="<?php _e('Delete','dtransport'); ?>">
                                        <?php echo $cuIcons->getIcon('svg-rmcommon-trash'); ?>
                                        <span class="sr-only"><?php _e('Delete','dtransport'); ?></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>

                <?php echo $xoopsSecurity->getTokenHTML(); ?>
                <div class="cu-bulk-actions">
                    <select name="actionb" id="bulk-bottom" class="form-control">
                        <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                        <option value="delete"><?php _e('Delete','dtransport'); ?></option>
                    </select>
                    <button type="button" id="the-op-bottom" onclick="before_submit('frm-lics');" class="btn btn-default"><?php _e('Apply','docs'); ?></button>
                </div>

            </div>
        </form>

    </div>

</div>
