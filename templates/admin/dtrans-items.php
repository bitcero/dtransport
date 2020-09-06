
<div class="panel panel-default">
    <div class="panel-body">
        <form name="frmSearch" id="frm-search" class="form-inline" method="GET" action="items.php">
            <div class="form-group">
                <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="<?php _e('Search download:','dtransport'); ?>">
                <button type="submit" class="btn btn-info" title="<?php _e('Search Now!','dtransport'); ?>"><span class="fa fa-search"></span></button>
                <?php if('' != $search): ?>
                <button type="reset" class="btn btn-grey" title="<?php _e('Clear search', 'dtransport'); ?>" id="search-reset">
                    <span class="fa fa-close"></span>
                </button>
                <?php endif; ?>
            </div>
            &nbsp;
            <div class="form-group">
                <label for="cat-select"><?php _e('Category:','dtransport'); ?></label>
                <select name="cat" onchange="submit()" id="cat-select" class="form-control">
                    <option value="0"><?php _e('Select...','dtransport'); ?></option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"<?php if($cat['id']==$catid): ?> selected="selected"<?php endif; ?>><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>
<form name="frmItems" id="frm-items" method="POST" action="items.php">

    <div class="cu-bulk-actions">

        <select name="action" id="bulk-top" class="form-control">
            <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
            <option value="bulk_approve"><?php _e('Approved','dtransport'); ?></option>
            <option value="bulk_unapproved"><?php _e('Not approved','dtransport'); ?></option>
            <option value="bulk_featured"><?php _e('Featured','dtransport'); ?></option>
            <option value="bulk_unfeatured"><?php _e('Not featured','dtransport'); ?></option>
            <option value="bulk_daily"><?php _e('Daily','dtransport'); ?></option>
            <option value="bulk_undaily"><?php _e('Not daily','dtransport'); ?></option>
            <option value="bulk_secure"><?php _e('Protected','dtransport'); ?></option>
            <option value="bulk_nosecure"><?php _e('Not protected','dtransport'); ?></option>
            <option value="delete"><?php _e('Delete','dtransport'); ?></option>
        </select>
        <button type="button" id="the-op-top" onclick="before_submit('frm-items');" class="btn btn-default"><?php _e('Apply','dtransport'); ?></button>

    </div>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('Existing Downloads', 'dtransport'); ?></h3>
        </div>

        <div class="table-responsive">

            <table class="table items" id="dt-downloads-list">
                <thead>
                <tr>
                    <th width="20" class="text-center">
                        <input type="checkbox" id="checkall" data-checkbox="chk-items">
                    </th>
                    <th width="20" class="text-center">
                        <?php _e('ID','dtransport'); ?>
                    </th>
                    <th></th>
                    <th>
                        <?php _e('Name','dtransport'); ?>
                    </th>
                    <th class="text-center">
                        <?php _e('Type','dtransport'); ?>
                    </th>
                    <th class="text-center">
                        <?php _e('Approved','dtransport'); ?>
                    </th>
                    <th>
                        <span class="icon icon-camera text-info"></span>
                    </th>
                    <th class="text-center">
                        <?php echo $common->icons()->getIcon('svg-rmcommon-star', ['title' => __('Featured', 'rmcommon')]); ?>
                    </th>
                    <th class="text-center">
                        <?php echo $common->icons()->getIcon('svg-dtransport-daily', ['title' => __('Daily', 'rmcommon')]); ?>
                    </th>
                    <th colspan="8" class="text-center">
                        <?php _e('Options','dtransport'); ?>
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach($items as $item): ?>
                    <?php $class = tpl_cycle("even,odd"); ?>
                    <tr class="<?php echo $class; ?><?php if($item['deletion']) echo ' deletion'; ?>" valign="top" id="row-<?php echo $item['id']; ?>">
                        <td class="text-center">
                            <input type="checkbox" name="ids[]" value="<?php echo $item['id']; ?>" id="item-<?php echo $item['id']; ?>" data-oncheck="chk-items">
                        </td>
                        <td class="text-center">
                            <strong><?php echo $item['id']; ?></strong>
                        </td>
                        <td class="in-edition">
                            <?php if($item['deletion']): ?>
                                <?php echo $common->icons()->getIcon('svg-rmcommon-trash', ['title' => __('Waiting for deletion', 'dtransport')]); ?>
                            <?php elseif($item['status'] == 'verify'): ?>
                                <?php echo $common->icons()->getIcon('svg-dtransport-verify', ['title' => __('Waiting for review', 'dtransport')]); ?>
                            <?php elseif($item['approved'] <= 0): ?>
                                <?php echo $common->icons()->getIcon('svg-rmcommon-sand-clock text-pink', ['title' => __('Waiting for approval', 'dtransport')]); ?>
                            <?php endif; ?>
                        </td>
                        <td class="dt-show-data" title="<?php _e('Click for Data','dtransport'); ?>">
                            <span title="<?php if($item['password']): _e('Protected with password','dtransport'); elseif($item['secure']): _e('Protected','dtransport'); endif; ?>" class="item_name"<?php if($item['secure']): ?> style="background: url(../images/<?php if($item['password']): ?>pass<?php else: ?>lockb<?php endif; ?>.png) no-repeat right; padding-right: 20px;"<?php endif; ?>>
                            <?php if('wait' != $type && $item['approved']): ?>
                                <strong><a href="<?php echo $item['link']; ?>"><?php echo $search!='' ? preg_replace("/($search)/i", '<span class="dt_srh">$1</span>', $item['name']) : $item['name']; ?></a></strong>
                            <?php else: ?>
                                <strong><?php echo $item['name']; ?></strong>
                            <?php endif; ?>
                                <?php if($item['deletion']): _e('(Deletion requested)','dtransport'); endif; ?>
                            </span>
                            <small class="help-block">
                                <?php echo sprintf(__('Last modification on %s by %s','dtransport'), $item['modified'], '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $item['user']['uid'] . '">'.$item['user']['uname'].'</a>'); ?>
                            </small>
                        </td>
                        <td align="center" class="secure_status"><?php echo $item['secure']?__('Protected','dtransport'):__('Normal','dtransport'); ?></td>
                        <td align="center"><input type="checkbox" class="approved" id="approved-<?php echo $item['id']; ?>" name="approved<?php echo $item['id']; ?>"<?php echo $item['approved']?' checked="checked"':''; ?> /></td>
                        <td align="center"><?php echo $item['screens']; ?></td>
                        <td align="center"><input type="checkbox" class="featured" id="featured-<?php echo $item['id']; ?>" name="featured<?php echo $item['id']; ?>"<?php echo $item['featured']?' checked="checked"':''; ?> /></td>
                        <td align="center"><input type="checkbox" class="daily" id="daily-<?php echo $item['id']; ?>" name="daily<?php echo $item['id']; ?>"<?php echo $item['daily']?' checked="checked"':''; ?> /></td>
                        <td class="dt-item-opts">
                            <a href="./items.php?action=edit&amp;id=<?php echo $item['id']; ?>&amp;pag=<?php echo $page; ?>&amp;search=<?php echo $search; ?>&amp;cat=<?php echo $catid; ?>&amp;type=<?php echo $type; ?>" title="<?php _e('Edit','dtransport'); ?>">
                                <span class="icon icon-pencil"></span>
                            </a>
                        </td>
                        <td class="dt-item-opts">
                            <a href="#" title="<?php !$item['secure'] ? _e('Not protected download','dtransport') : _e('Protected download','dtransport'); ?>">
                                <?php if( $item['secure'] ): ?>
                                    <span class="icon icon-lock"></span>
                                <?php else: ?>
                                    <span class="icon icon-unlocked"></span>
                                <?php endif; ?>
                            </a>
                        </td>
                        <?php if($type!='edit'): ?>
                            <td class="dt-item-opts">
                                <a href="./screens.php?item=<?php echo $item['id']; ?>" title="<?php _e('Images','dtransport'); ?>">
                                    <span class="icon icon-camera"></span>
                                </a>
                            </td>
                            <td class="dt-item-opts">
                                <a href="./features.php?item=<?php echo $item['id']; ?>" title="<?php _e('Features','dtransport'); ?>">
                                    <span class="icon icon-list"></span>
                                </a>
                            </td>
                            <td class="dt-item-opts">
                                <a href="files.php?item=<?php echo $item['id']; ?>" title="<?php _e('Files','dtransport'); ?>">
                                    <span class="icon icon-file-zip"></span>
                                </a>
                            </td>
                            <td class="dt-item-opts">
                                <a href="logs.php?item=<?php echo $item['id']; ?>" title="<?php _e('Logs','dtransport'); ?>">
                                    <span class="icon icon-calendar"></span>
                                </a>
                            </td>
                            <td class="dt-item-opts">
                                <a href="statistics.php?item=<?php echo $item['id']; ?>">
                                    <span class="icon icon-stats-dots"></span>
                                </a>
                            </td>
                        <?php endif; ?>
                        <td class="dt-item-opts">
                            <a href="#" onclick="dt_check_delete(<?php echo $item['id']; ?>, 'frm-items');" title="<?php _e('Delete','dtransport'); ?>">
                                <span class="icon icon-bin text-danger"></span>
                            </a>
                        </td>
                    </tr>
                    <tr class="dt_hidden_data <?php echo $class; ?>" id="data-<?php echo $item['id']; ?>" valign="top">
                        <td colspan="2">&nbsp;</td>
                        <td><?php echo $item['desc']; ?></td>
                        <td colspan="13" class="dt_the_data">
                            <div>
                                <label><?php _e('Version:','dtransport'); ?></label>
                                <?php echo $item['version']; ?>
                            </div>
                            <div>
                                <label><?php _e('Hits:','dtransport'); ?></label>
                                <?php echo $item['hits']; ?>
                            </div>
                            <div>
                                <label><?php _e('Screenshots:','dtransport'); ?></label>
                                <?php echo $item['screens']; ?>
                            </div>
                            <div>
                                <label><?php _e('Comments:','dtransport'); ?></label>
                                <?php echo $item['comments']; ?>
                            </div>
                            <div>
                                <label><?php _e('Created:','dtransport'); ?></label>
                                <?php echo $item['created']; ?>
                            </div>
                            <div>
                                <label><?php _e('Last update:','dtransport'); ?></label>
                                <?php echo $item['modified']; ?>
                            </div>
                            <div>
                                <label><?php _e('Votes:','dtransport'); ?></label>
                                <?php echo $item['votes']; ?>
                            </div>
                            <div>
                                <label><?php _e('Rating:','dtransport'); ?></label>
                                <?php echo $item['rating']; ?>
                            </div>


                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

                <tfoot>
                <tr>
                    <th width="20" class="text-center">
                        <input type="checkbox" id="checkall" data-checkbox="chk-items">
                    </th>
                    <th width="20" class="text-center">
                        <?php _e('ID','dtransport'); ?>
                    </th>
                    <th></th>
                    <th>
                        <?php _e('Name','dtransport'); ?>
                    </th>
                    <th class="text-center">
                        <?php _e('Type','dtransport'); ?>
                    </th>
                    <th class="text-center">
                        <?php _e('Approved','dtransport'); ?>
                    </th>
                    <th>
                        <span class="icon icon-camera text-info"></span>
                    </th>
                    <th class="text-center">
                        <?php echo $common->icons()->getIcon('svg-rmcommon-star', ['title' => __('Featured', 'rmcommon')]); ?>
                    </th>
                    <th class="text-center">
                        <?php echo $common->icons()->getIcon('svg-dtransport-daily', ['title' => __('Daily', 'rmcommon')]); ?>
                    </th>
                    <th colspan="8" class="text-center">
                        <?php _e('Options','dtransport'); ?>
                    </th>
                </tr>
                </tfoot>
            </table>

        </div>

    </div>

    <div class="cu-bulk-actions">

        <select name="actionb" id="bulk-bottom" class="form-control">
            <option value="" selected="selected"><?php _e('Bulk actions...','dtransport'); ?></option>
            <option value="bulk_approve"><?php _e('Approved','dtransport'); ?></option>
            <option value="bulk_unapproved"><?php _e('Not approved','dtransport'); ?></option>
            <option value="bulk_featured"><?php _e('Featured','dtransport'); ?></option>
            <option value="bulk_unfeatured"><?php _e('Not featured','dtransport'); ?></option>
            <option value="bulk_daily"><?php _e('Daily','dtransport'); ?></option>
            <option value="bulk_undaily"><?php _e('Not daily','dtransport'); ?></option>
            <option value="bulk_secure"><?php _e('Protected','dtransport'); ?></option>
            <option value="bulk_nosecure"><?php _e('Not protected','dtransport'); ?></option>
            <option value="bulk_delete"><?php _e('Delete','dtransport'); ?></option>
        </select>
        <button type="button" id="the-op-bottom" onclick="before_submit('frm-items');" class="btn btn-default"><?php _e('Apply','dtransport'); ?></button>

    </div>

    <?php echo $xoopsSecurity->getTokenHTML(); ?>
    <input type="hidden" name="page" value="<?php echo $page; ?>"/>
    <input type="hidden" name="limit" value="<?php echo $limit; ?>"/>
    <input type="hidden" name="type" value="<?php echo $type; ?>"/>

</form>

<div id="status-bar">
    <?php _e('Applying changes, please wait a second...','dtransport'); ?>
</div>
