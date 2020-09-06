<?php
// $Id: dt_items.php 201 2013-01-27 06:47:22Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Este widget muestra las categorias disponibles y permite
* agregar la descarga a una o varias de ellas
*/
function dt_widget_information($edit=0){
    
    $widget['title'] = __('Download Information','dtransport');
    $widget['icon'] = '../images/item.png';
    
    if($edit)
        $sw = new Dtransport_Software(rmc_server_var($_GET, 'id', 0));
    else
        $sw = new Dtransport_Software();
        
    // Featured download
    $field = new RMFormYesNo('','mark',$edit ? $sw->getVar('featured') : 1);
    $featured = $field->render();
    
    // Descarga segura
    $field = new RMFormYesno('','secure',$edit ? $sw->getVar('secure') : 0);
    $secure = $field->render();
    
    // Approved
    $field = new RMFormYesNo('','approved',$edit ? $sw->getVar('approved') : 1);
    $approved = $field->render();
    
    unset($field);
    
    ob_start();
    include RMTemplate::get()->get_template( 'widgets/dtrans-information.php', 'module', 'dtransport' );
    $widget['content'] = ob_get_clean();
    return $widget;
    
}

/**
 * Show fields for default image
 */
function dt_widget_defimg($edit = 0){

    $id     = RMHttpRequest::get( 'id', 'integer', 0 );
    $type   = rmc_server_var($_REQUEST, 'type', '');

    $widget = array();
    $widget['title'] = __('Default Image','dtransport');
    $widget['icon'] = '../images/shots.png';
    $util = new RMUtilities();

    if ($edit){
        //Verificamos que el software sea válido
        if ($id<=0)
            $params = '';

        //Verificamos que el software exista
        if ($type=='edit')
            $sw = new Dtransport_SoftwareEdited($id);
        else
            $sw=new Dtransport_Software($id);

        if ($sw->isNew())
            $params = '';
        else
            $params = $sw->getVar('image','e');

    } else {
        $params = '';
    }

    $widget['content'] = '<form name="frmDefimage" id="frm-defimage" method="post">';
    $widget['content'] .= $util->image_manager('image', 'image', $params, array('accept' => 'thumbnail', 'multiple' => 'no'));
    $widget['content'] .= '</form>';
    return $widget;

}

/**
 * Muestra las opciones adicionales para la descarga creada
 */
function dt_widget_options($edit = 0){
    
    $widget['title'] = __('Download Options','dtransport');
    $widget['icon'] = '../images/options.png';
    
    ob_start();
    ?>
    <div id="dt-down-opts">
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
            <?php echo dt_widget_alert($edit); ?>
            <?php echo dt_widget_credits($edit); ?>
        </div>
    </div>
    <?php
    $widget['content'] = ob_get_clean();
    
    return $widget;
    
}

/**
* Presenta las opciones para configurar la alerta
*/
function dt_widget_alert($edit=0){
    
    //$widget['title'] = __('Inactivity Alert','dtransport');
    //$widget['icon'] = '../images/alert.png';
    if($edit)
        $sw = new Dtransport_Software(rmc_server_var($_GET, 'id', 0));
    else
        $sw = new Dtransport_Software();
    
    // Alerta
    $field = new RMFormYesNo('','alert',$edit ? ($type=='edit' ? ($fields['alert']['limit'] ? 1 : 0) : ($sw->alert() ? 1 : 0)) : 0);
    $enable_alert = $field->render();
    
    unset($field);
    ob_start();
    include RMTemplate::get()->get_template( 'widgets/dtrans-alerts.php', 'module', 'dtransport' );
    //$widget['content'] = ob_get_clean();
    $content = ob_get_clean();
    return $content;
    
}

/**
* Author information
*/
function dt_widget_credits($edit=0){
    global $xoopsUser;
    
    //$widget['title'] = __('Author Information','dtransport');
    //$widget['icon'] = '../images/author.png';
    if($edit)
        $sw = new Dtransport_Software(rmc_server_var($_GET, 'id', 0));
    else
        $sw = new Dtransport_Software();

    
    $field = new RMFormUser('', 'user', 0,$edit?array($sw->getVar('uid')):$xoopsUser->uid(), 50);
    $user = $field->render();
    unset($field);
    
    ob_start();
    include RMTemplate::get()->get_template('widgets/dtrans-author.php', 'module', 'dtransport' );
    $content = ob_get_clean();
    return $content;
    
}

/**
* Widget that shows download options
*/
function dt_widget_item_options(){
    global $xoopsModule;
    
    if($xoopsModule->dirname()!='dtransport')
        return;

    if(RMCSUBLOCATION!='newitem') return;

    $item = rmc_server_var($_GET, RMCSUBLOCATION=='newitem' ? 'id' : 'item', 0);
    if($item<=0) return;
    
    $item = new Dtransport_Software($item);
    if($item->isNew()) return;

    $widget['title'] = __('Item Options', 'dtransport');
    
    ob_start(); ?>
    

    
<?php
    $widget['content'] = ob_get_clean();
    return $widget;
    
}

/**
* Widget para mostrar las estadísticas de un elemento
*/
function dt_widget_statistics(){
    global $xoopsModule, $rmTpl;
    
    if($xoopsModule->dirname()!='dtransport')
        return;
        
    $item = rmc_server_var($_GET, 'item', 0);
    if($item<=0) return;
    
    $item = new Dtransport_Software($item);
    if($item->isNew()) return;
    
    $widget['title'] = sprintf(__('%s Statistics', 'dtransport'), $item->getVar('name'));
    $widget['icon'] = '../images/chart.png';
    $widget['content'] = '<iframe id="dt-widget-statistics" src="../ajax/statistics.php?item='.$item->id().'"></iframe>';

    return $widget;
    
}