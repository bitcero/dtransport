<!-- Default image -->
<div class="cu-box box-primary">
    <div class="box-header">
        <span class="box-handler"><span class="fa fa-caret-down"></span></span>
        <h3 class="box-title"><?php _e('Logo Image', 'dtransport'); ?></h3>
    </div>
    <div class="box-content">
        <?php
        if($common->plugins()->isInstalled('advform-pro')) {

            $logo = new RMFormImageUrl('', 'logo', $sw->logo);
            echo $logo->render();

        } else {

            $logo = new RMFormText('', 'logo', null, 255, $sw->logo);
            echo $logo->render();

        }
        ?>
    </div>
</div>