<h1 class="cu-section-title"><?php echo $title; ?></h1>

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('Add group of files','dtransport'); ?></h3>
            </div>
            <div class="panel-body">
                <form name="frmNewGroup" id="form-new-group" method="post" action="../ajax/files-ajax.php">
                    <div class="form-group">
                        <label for="group-name"><?php _e('Group name:','dtransport'); ?></label>
                        <input type="text" name="name" id="group-name" class="form-control">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-blue" id="create-group"><?php _e('Create Group','dtransport'); ?></button>
                    </div>
                    <div class="help-block">
                        <?php _e('Groups allows to organize different files according to specific features.'); ?>
                    </div>
                    <input type="hidden" name="action" value="save-group" />
                    <input type="hidden" name="item" value="<?php echo $item; ?>" />
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <form name="frmfiles" id="frm-files" method="POST" action="files.php">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo sprintf(__('Files for %s', 'dtransport'), $sw->name); ?></h3>
                </div>
                <div class="table-responsive">
                    <table class="table" id="table-files">
                        <thead>
                        <tr class="text-center">
                            <th width="20"><?php _e('ID','dtransport'); ?></th>
                            <th><?php _e('File','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Hits','dtransport'); ?></th>
                            <th class="text-center"><?php _e('External','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Group','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Default','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr class="text-center">
                            <th width="20"><?php _e('ID','dtransport'); ?></th>
                            <th><?php _e('File','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Hits','dtransport'); ?></th>
                            <th class="text-center"><?php _e('External','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Group','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Default','dtransport'); ?></th>
                            <th class="text-center"><?php _e('Options','dtransport'); ?></th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php if(empty($files)): ?>
                            <tr class="text-center">
                                <td colspan="8"><?php _e('There are not files with specified parameters currently!','dtransport'); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($files as $file): ?>
                            <?php if($file['type']=='group'): ?>
                                <tr class="group-heading" id="group-<?php echo $file['id']; ?>">
                                    <td colspan="6"><strong><?php echo $file['file']; ?></strong></td>
                                    <td class="cu-options text-center">
                                        <a href="files.php?item=<?php echo $item; ?>&amp;action=new" class="addfile blue" title="<?php _e('Add File','dtransport'); ?>">
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-plus'); ?>
                                            <span class="sr-only"><?php _e('Add File','dtransport'); ?></span>
                                        </a>
                                        <a href="#" class="editgroup warning" title="<?php _e('Edit','dtransport'); ?>">
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                            <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                                        </a>
                                        <a href="files.php?item=<?php echo $item; ?>&amp;id=<?php echo $file['id']; ?>&amp;action=deletegroup" class="deletegroup danger" title="<?php _e('Delete','dtransport'); ?>">
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-trash'); ?>
                                            <span class="sr-only"><?php _e('Delete','dtransport'); ?></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr class="<?php echo tpl_cycle('even,odd'); ?>" align="center">
                                    <td style="display: none;"><input type="checkbox" name="id[]" id="item-<?php echo $file['id']; ?>" value="<?php echo $file['id']; ?>" /></td>
                                    <td><strong><?php echo $file['id']; ?></strong></td>
                                    <td align="left"><?php echo $file['title']; ?></td>
                                    <td align="center"><?php echo $file['downs']; ?></td>
                                    <td align="center">
                                        <?php if($file['remote']): ?>
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-ok-circle text-success'); ?>
                                        <?php else: ?>
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-cross text-grey'); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td align="center">
                                        <select name="groups[<?php echo $file['id']; ?>]" class="group-selector form-control">
                                            <option value="0"><?php _e('Select group...','dtransport'); ?></option>
                                            <?php foreach($groups as $group): ?>
                                                <option value="<?php echo $group['id']; ?>" <?php if($group['id']==$file['group']): ?>selected<?php endif; ?>><?php echo $group['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td align="center">
                                            <?php if($file['default']): ?>
                                                <?php echo $cuIcons->getIcon('svg-rmcommon-ok-circle text-success'); ?>
                                            <?php else: ?>
                                        <a href="./files.php?item=<?php echo $item; ?>&amp;id=<?php echo $file['id']; ?>&amp;action=default" title="<?php _e('Set as default', 'dtransport'); ?>">
                                                <?php echo $cuIcons->getIcon('svg-rmcommon-cross text-grey'); ?>
                                                </a>
                                            <?php endif; ?>
                                    </td>
                                    <td class="text-right cu-options">
                                        <a href="./files.php?action=edit&amp;id=<?php echo $file['id']; ?>&amp;item=<?php echo $item; ?>" class="warning" title="<?php _e('Edit','dtransport'); ?>">
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-pencil'); ?>
                                            <span class="sr-only"><?php _e('Edit','dtransport'); ?></span>
                                        </a>
                                        <a href="#" class="delete-file danger" title="<?php _e('Delete','dtransport'); ?>">
                                            <?php echo $cuIcons->getIcon('svg-rmcommon-trash'); ?>
                                            <span class="sr-only"><?php _e('Delete','dtransport'); ?></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php echo $xoopsSecurity->getTokenHTML(); ?>
            <input type="hidden" name="item" id="item" value="<?php echo $item; ?>" />
        </form>
    </div>
</div>

<?php if($item>0): ?>

<div class="dt_table">
    <div class="dt_row">
        <div class="dt_cell dt_group_form">

        </div>
        <div class="dt_cell">


        </div>
    </div>
</div>
<?php endif; ?>

<div id="status-bar">
    <?php _e('Applying changes, please wait a second...','dtransport'); ?>
</div>