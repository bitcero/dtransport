<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php _e('Featured Image', 'dtransport'); ?></h3>
    </div>
    <div class="panel-body">
        <?php echo $common->utilities()->image_manager('image', 'image', $sw->get('image'), array('accept' => 'thumbnail', 'multiple' => 'no'));; ?>
    </div>
</div>