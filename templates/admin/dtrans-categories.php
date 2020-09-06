<h1 class="cu-section-title"><?php _e('Categories Management','dtransport'); ?></h1>

<div class="row">

    <div class="col-sm-6 col-md-4">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><?php _e('Add Category', 'dtransport'); ?></h3>
            </div>
            <div class="panel-body">
                <form name="frmAdd" id="frm-add" method="post" action="categories.php">
                    <div class="form-group">
                        <label for="cat-name"><?php _e('Category name','dtransport'); ?></label>
                        <input type="text" name="name" id="cat-name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="cat-nameid"><?php _e('Short name','dtransport'); ?></label>
                        <input type="text" name="nameid" id="cat-nameid" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="cat-desc"><?php _e('Description','dtransport'); ?></label>
                        <textarea cols="30" rows="5" name="desc" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="cat-parent"><?php _e('Root category','dtransport'); ?></label>
                        <select name="parent" id="cat-parent" class="form-control">
                            <option value="0" selected="selected"><?php _e('Select category...','dtransport'); ?></option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo str_repeat("&#8212;", $cat['indent']); ?><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg"><?php _e('Add Category','dtransport'); ?></button>
                        <input type="hidden" name="action" value="save" />
                        <input type="hidden" name="active" value="1" />
                        <?php echo $xoopsSecurity->getTokenHTML("XT"); ?>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <div class="col-sm-6 col-md-8">

        <form name="frmcat" id="frm-categories" method="POST" action="categories.php">
            <div class="cu-bulk-actions">
                <select name="action" id="bulk-top" class="form-control">
                    <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                    <option value="active"><?php _e('Activate','dtransport'); ?></option>
                    <option value="desactive"><?php _e('Deactivate','dtransport'); ?></option>
                    <option value="delete"><?php _e('Delete','dtransport'); ?></option>
                </select>
                <button type="button" id="the-op-top" class="btn btn-default" onclick="before_submit('frm-categories');"><?php _e('Apply','docs'); ?></button>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="table-responsive">

                        <table class="table">
                            <thead>
                            <tr>
                                <th width="20" class="text-center">
                                    <input type="checkbox" data-checkbox="chk-categories">
                                </th>
                                <th class="text-center"><?php _e('ID','dtransport'); ?></th>
                                <th align="left"><?php _e('Category name','dtransport'); ?></th>
                                <th align="left"><?php _e('Description','dtransport'); ?></th>
                                <th class="text-center"><?php _e('Active','dtransport'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($categories as $cat): ?>
                                <tr class="<?php echo tpl_cycle('even,odd'); ?>" valign="top">
                                    <td>
                                        <input type="checkbox" name="ids[]" id="item-<?php echo $cat['id']; ?>" value="<?php echo $cat['id']; ?>" data-oncheck="chk-categories">
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo $cat['id']; ?></strong>
                                    </td>
                                    <td>
                                        <a href="items.php?cat=<?php echo $cat['id']; ?>"><?php echo str_repeat("&#8212;", $cat['indent']); ?> <?php echo $cat['name']; ?></a>
                                <span class="cu-item-options">
                                <a href="categories.php?action=edit&amp;id=<?php echo $cat['id']; ?>"><?php _e('Edit','dtransport'); ?></a> |
                                <a href="#" onclick="dt_check_delete(<?php echo $cat['id']; ?>,'frm-categories');"><?php _e('Delete','dtransport'); ?></a>
                                </span>
                                    </td>
                                    <td>
                                        <?php echo $cat['description']; ?>
                                    </td>
                                    <td class="text-center">
                                        <img src="../images/<?php if($cat['active']): ?>ok<?php else: ?>no<?php endif; ?>.png" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th width="20" class="text-center">
                                    <input type="checkbox" data-checkbox="chk-categories">
                                </th>
                                <th class="text-center"><?php _e('ID','dtransport'); ?></th>
                                <th align="left"><?php _e('Category name','dtransport'); ?></th>
                                <th align="left"><?php _e('Description','dtransport'); ?></th>
                                <th class="text-center"><?php _e('Active','dtransport'); ?></th>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>

            <div class="cu-bulk-actions">
                <select name="actionb" id="bulk-bottom" class="form-control">
                    <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
                    <option value="active"><?php _e('Activate','dtransport'); ?></option>
                    <option value="desactive"><?php _e('Deactivate','dtransport'); ?></option>
                    <option value="delete"><?php _e('Delete','dtransport'); ?></option>
                </select>
                <button type="button" id="the-op-bottom" onclick="before_submit('frm-categories');" class="btn btn-default"><?php _e('Apply','docs'); ?></button>
            </div>
            <?php echo $xoopsSecurity->getTokenHTML(); ?>
        </form>

    </div>

</div>
