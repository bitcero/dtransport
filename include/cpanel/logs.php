<?php
// $Id: logs.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

defined('XOOPS_MAINFILE_INCLUDED') or die("Not allowed");

$xoopsOption['template_main'] = 'dt-logs.tpl';
$xoopsOption['module_subpage'] = 'cp-logs';

/**
* Muestra las características existentes de una descarga
*/
function dt_show_logs($edit=0){
    global $xoopsOption,$db,$tpl,$xoopsTpl,$xoopsUser,$dtSettings, $dtfunc, $page, $item, $xoopsConfig, $xoopsModuleConfig,$log;
    
    include('header.php');

    $xoopsTpl->assign('downloadItem', [
        'id' => $item->id(),
        'name' => $item->name
    ]);

    Dtransport_Functions::getInstance()->cpanelHeader();
    // Item options
    Dtransport_Functions::getInstance()->makeItemCpanelOptions($item);
    
    if($log>0 && $edit){
        
        $log = new Dtransport_Log($log);
        if($log->isNew() || $log->software()!=$item->id())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/logs/'.$item->id().'/':'/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Specified log does not exists!','dtransport'));
               
    }

    $tc = TextCleaner::getInstance();
    $tf = new RMTimeFormatter('', "%M% %d%, %Y%");
    
    $sql = "SELECT * FROM ".$db->prefix('mod_dtransport_logs')." WHERE id_soft=".$item->id();
    $result=$db->queryF($sql);
    while ($rows=$db->fetchArray($result)){
        $lg = new Dtransport_Log();
        $lg->assignVars($rows);
        
        $xoopsTpl->append('logs',array(
            'id'=>$lg->id(),
            'title'=>$lg->title(),
            'date'=> $tf->format($lg->date()),
            'software'=>$item->getVar('name'),
            'links' => array(
                'edit' => DT_URL.($dtSettings->permalinks ? '/cp/logs/'.$item->getVar('nameid').'/edit/'.$lg->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=logs&amp;log='.$lg->id()),
                'delete' => DT_URL.($dtSettings->permalinks ? '/cp/logs/'.$item->getVar('nameid').'/delete/'.$lg->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=delete&amp;log='.$lg->id())
            )
        ));
    }
    
    $formurl = DT_URL.($dtSettings->permalinks ? '/cp/logs/'.$item->id().'/save/'.($edit ? $log->id() : '0').'/' : '/?s=cp');
    
    // logs Form
    $form = new RMForm($edit ? sprintf(__('Editing log of "%s"','dtransport'),$item->getVar('name')) : sprintf(__('New log for "%s"','dtransport'),$item->getVar('name')),'frmLog',$formurl);

    $form->addElement(new RMFormLabel(__('Download item','dtransport'),$item->getVar('name')));
    

    $form->addElement(new RMFormText(__('Log title','dtransport'),'title',50,200,$edit ? $log->title() : ''),true);
    $form->addElement(new RMFormEditor(__('Log content','dtransport'),'content','auto','350px',$edit ? $log->log('e') : ''),true);

    $form->addElement(new RMFormHidden('action', 'logs'));
    $form->addElement(new RMFormHidden('id',$item->id()));
    $form->addElement(new RMFormHidden('log',$edit ? $log->id() : 0));
    $form->addElement(new RMFormHidden('op','save'));

    $buttons =new RMFormButtonGroup();
    $buttons->addButton(new RMFormButton([
        'id' => 'file-submit',
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'caption' => __('Save Log', 'dtransport')
    ]));
    $buttons->addButton(new RMFormButton([
        'id' => 'form-cancel',
        'type' => 'button',
        'class' => 'btn btn-default',
        'caption' => __('Cancel', 'dtransport')
    ]));


    $form->addElement($buttons);
            
    $xoopsTpl->assign('log_form', $form->render());

    Dtransport_Functions::getInstance()->addLangString([
        'id' => __('ID','dtransport'),
        'title' => __('Title','dtransport'),
        'created' => __('Created','dtransport'),
        'noLogs' => __('There are not logs for this download item','dtransport'),
        'edit' => __('Edit', 'dtransport'),
        'delete' => __('Delete', 'dtransport'),
    ]);
    
    $xoopsTpl->assign('edit', $edit);
    
    include 'footer.php';
}

/**
* Save feature
*/
function dt_save_log($edit){
    
    global $item, $log, $tpl, $xoopsTpl, $dtSettings, $dtfunc;

    $query = '';
    foreach ($_POST as $k=>$v){
        $$k=$v;
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    if ($edit){

        //Verificamos que la característica exista
        $lg = new Dtransport_Log($log);
        if ($lg->isNew())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/logs/'.$item->id().'/':'/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Specified log does not exists!','dtransport'));

    }else{

        $lg = new Dtransport_Log();

    }

    $tc = TextCleaner::getInstance();

    //Comprueba que el título de la característica no exista
    $sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_logs')." WHERE title='$title' AND id_log!=".$lg->id()." AND id_soft=".$item->id();
    list($num) = $db->fetchRow($db->queryF($sql));
    if ($num>0)
        redirect_header(DT_URL.($dtSettings->permalinks?'/cp/logs/'.$item->id().'/edit/'.$lg->id():'/?s=cp&amp;action=logs&amp;id='.$item->id().'/&amp;op=edit&amp;log='.$lg->id()), 1, __('Another log with same title already exists!','dtransport'));

    $lg->setSoftware($item->id());
    $lg->setTitle($title);
    $lg->setLog($content);
    $lg->setDate(time());

    if (!$lg->save())
        redirect_header(DT_URL.($dtSettings->permalinks?'/cp/logs/'.$item->id().'/':'/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Log could not be saved! Please try again.','dtransport'));

    redirect_header(DT_URL.($dtSettings->permalinks?'/cp/logs/'.$item->id().'/':'/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Log saved successfully!','dtransport'));
    
}

/**
* Delete logs
*/
function dt_delete_log(){
    global $dtSettings, $item, $log;
    
    $lg = new Dtransport_Log($log);
    
    if($lg->isNew())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/logs/'.$item->id() : '/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Specified log is not valid!','dtransport'));

    if(!$lg->delete())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/logs/'.$item->id() : '/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Log could not be deleted! Please try again.','dtransport'));
    
    redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/logs/'.$item->id() : '/?s=cp&amp;action=logs&amp;id='.$item->id()), 1, __('Log deleted successfully!','dtransport'));
    
}
