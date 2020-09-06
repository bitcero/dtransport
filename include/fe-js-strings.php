<?php

ob_start();
?>

var dtLang = {

    fieldsErrors: '<?php _e('There are some errors in data. Please verify input fields in red color.', 'dtransport'); ?>',
    confirmDeletion: '<?php _e('Do you relly want to delete selected item?', 'dtransport'); ?>',
    selectItem: '<?php _e('You must to select an item before to do this!', 'dtransport'); ?>',

};

<?php
$strings = ob_get_clean();
$common->template()->add_inline_script($strings);
