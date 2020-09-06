<div class="cu-box box-primary">
    <div class="box-header">
        <span class="box-handler"><span class="fa fa-caret-down"></span></span>
        <h3 class="box-title"><?php _e('Download Options', 'dtransport'); ?></h3>
    </div>
    <div class="box-content no-padding">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#tab-alert" aria-controls="tab-alerts" role="tab" data-toggle="tab">
                    <span class="fa fa-warning text-warning"></span>
                    Alerts
                </a>
            </li>
            <li role="presentation">
                <a href="#tab-credits" aria-controls="tab-credits" role="tab" data-toggle="tab">
                    <span class="icon icon-user-tie"></span>
                    Author
                </a>
            </li>
        </ul>
        <div class="tab-content">

            <div role="tabpanel" id="tab-alert" class="tab-pane active">
                <?php $field = new RMFormYesNo('','alert', $edit ? $sw->alert() : 1); ?>
                <div class="form-group">
                    <table class="table">
                        <tr>
                            <td><label for="alert"><?php echo _e('Enable alerts:','dtransport'); ?></label></td>
                            <td><?php echo $field->render(); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="form-group">
                    <label for="limitalert"><?php _e('Limit of days','dtransport'); ?></label>
                    <input type="text" name="limitalert" id="limitalert" value="<?php echo $edit?$sw->alert()->limit:''; ?>" class="form-control">
                    <small class="help-block"><?php _e('Maximum number of days that an item can be without downloads before to send an alert to author.','dtransport'); ?></small>
                </div>
                <div class="form-group">
                    <label for="mode"><?php _e('Alert mode','dtransport'); ?></label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="mode" id="mode" value="0"<?php echo $edit ? ($sw->alert()->mode==0?' checked="checked"' : '') : ''; ?>>
                            <?php _e('Private message','dtransport'); ?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="mode" id="mode1" value="1"<?php echo $edit ? ($sw->alert()->mode==1?' checked="checked"' : '') : ''; ?>>
                            <?php _e('Email message','dtransport'); ?>
                        </label>
                    </div>
                </div>

            </div>

            <div class="tab-pane" role="tabpanel" id="tab-credits">

                <?php $field = new RMFormUser('', 'user', 0,$edit?array($sw->getVar('uid')):$xoopsUser->uid(), 50);; ?>
                <div class="form-group">
                    <label><?php _e('Published by','dtransport'); ?></label>
                    <?php echo $field->render(); ?>
                </div>
                <div class="form-group">
                    <label for="author"><?php _e('Author name','dtransport'); ?></label>
                    <input type="text" name="author" id="author" value="<?php echo $edit ? $sw->getVar('author_name') : ''; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="url"><?php _e('Author URL','dtransport'); ?></label>
                    <input type="text" name="url" id="url" value="<?php echo $edit ? $sw->getVar('author_url') : ''; ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label for="email"><?php _e('Author Email','dtransport'); ?></label>
                    <input type="text" name="email" id="email" value="<?php echo $edit ? $sw->getVar('author_email') : $xoopsUser->email(); ?>" class="form-control">
                    <small class="help-block"><?php _e('This email will not be visible for users','dtransport'); ?></small>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="contact" id="contact" value="1"<?php echo $sw->getVar('author_contact')?' checked="checked"':''; ?>>
                            <?php _e('Author can be contacted','dtransport'); ?>
                        </label>
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>