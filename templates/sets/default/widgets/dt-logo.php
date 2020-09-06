<!-- Default image -->
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php _e('Logo Image', 'dtransport'); ?></h3>
    </div>
    <div class="panel-body">
        <?php
        if($common->plugins()->isInstalled('advform-pro')) {

            $logo = new RMFormImageUrl('', 'logo', $sw->get('logo'));
            echo $logo->render();

        } else {

            $logo = new RMFormText('', 'logo', null, 255, $sw->get('logo'));
            echo $logo->render();

        }
        ?>
    </div>
</div>