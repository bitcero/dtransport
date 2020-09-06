<?php
// $Id: features.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

defined('XOOPS_MAINFILE_INCLUDED') or die("Not allowed");

$xoopsOption['template_main'] = 'dt-features.tpl';
$xoopsOption['module_subpage'] = 'cp-features';

/**
* Muestra las características existentes de una descarga
*/
function dt_show_features($edit=0){
    global $db,$xoopsTpl,$dtSettings, $dtfunc, $item,$feature, $common, $xoopsConfig, $xoopsUser;
    
    include('header.php');

    $xoopsTpl->assign('downloadItem', [
        'id' => $item->id(),
        'name' => $item->name
    ]);

    Dtransport_Functions::getInstance()->cpanelHeader();
    // Item options
    Dtransport_Functions::getInstance()->makeItemCpanelOptions($item);

    if($feature>0 && $edit){
        
        $feature = new Dtransport_Feature($feature);
        if($feature->isNew() || $feature->software()!=$item->id())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/features/'.$item->id().'/':'/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Specified feature does not exists!','dtransport'));
               
    }

    $tc = TextCleaner::getInstance();
    
    $sql = "SELECT * FROM ".$db->prefix('mod_dtransport_features')." WHERE id_soft=".$item->id();
    $result=$db->queryF($sql);
    while ($rows=$db->fetchArray($result)){
        $feat = new Dtransport_Feature();
        $feat->assignVars($rows);
        
        $xoopsTpl->append('features',array(
            'id'=>$feat->id(),
            'name'=>$feat->title(),
            'content'=> $tc->truncate($tc->clean_disabled_tags($feat->content()), 80),
            'created' => $common->timeFormat(__('%m%/%d%/%Y%', 'dtransport'))->format($feat->created),
            'modified' => $common->timeFormat(__('%m%/%d%/%Y%', 'dtransport'))->format($feat->modified),
            'software'=>$item->getVar('name'),
            'links' => array(
                'permalink' => $feat->permalink(),
                'edit' => DT_URL.($dtSettings->permalinks ? '/cp/features/'.$item->getVar('nameid').'/edit/'.$feat->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=features&amp;feature='.$feat->id()),
                'delete' => DT_URL.($dtSettings->permalinks ? '/cp/features/'.$item->getVar('nameid').'/delete/'.$feat->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=delete&amp;feature='.$feat->id())
            )
        ));
    }
    
    $formurl = DT_URL.($dtSettings->permalinks ? '/cp/features/'.$item->id().'/save/'.($edit ? $feature->id() : '0').'/' : '/?s=cp');
    
    // Features Form
    $form = new RMForm($edit ? sprintf(__('Editing feature of "%s"','dtransport'),$item->getVar('name')) : sprintf(__('New feature for "%s"','dtransport'),$item->getVar('name')),'frmfeat',$formurl);

    $form->addElement(new RMFormLabel(__('Download item','dtransport'),$item->getVar('name')));
    

    $form->addElement(new RMFormText(__('Feature title','dtransport'),'title',50,200,$edit ? $feature->title() : ''),true);
    $form->addElement(new RMFormText(__('Short name','dtransport'), 'nameid', 50, 200, $edit ? $feature->nameId() : ''));
    $form->addElement(new RMFormEditor(__('Feature content','dtransport'),'content','auto','350px',$edit ? $feature->content('e') : ''),true);

    $dtfunc->meta_form('feat', $edit ? $feature->id() : 0, $form);

    $form->addElement(new RMFormHidden('action', 'features'));
    $form->addElement(new RMFormHidden('id',$item->id()));
    $form->addElement(new RMFormHidden('feature',$edit ? $feature->id() : 0));
    $form->addElement(new RMFormHidden('op','save'));

    $buttons =new RMFormButtonGroup();
    $buttons->addButton(new RMFormButton([
        'id' => 'file-submit',
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'caption' => __('Save Feature', 'dtransport')
    ]));
    $buttons->addButton(new RMFormButton([
        'id' => 'form-cancel',
        'type' => 'button',
        'class' => 'btn btn-default',
        'caption' => __('Cancel', 'dtransport')
    ]));


    $form->addElement($buttons);
            
    $xoopsTpl->assign('feat_form', $form->render());

    $xoopsTpl->assign('isEdition', $edit);

    Dtransport_Functions::getInstance()->addLangString([
        'id' => __('ID','dtransport'),
        'name' => __('Name','dtransport'),
        'created' => __('Created','dtransport'),
        'modified' => __('modified','dtransport'),
        'content' => __('Content','dtransport'),
        'noFeatures' => __('There are not features in this download item','dtransport'),
        'edit' => __('Edit', 'dtransport'),
        'delete' => __('Delete', 'dtransport'),
    ]);
    
    $xoopsTpl->assign('edit', $edit);
    
    include 'footer.php';
}

/**
* Save feature
*/
function dt_save_feature($edit){
    
    global $item, $feature, $tpl, $xoopsTpl, $dtSettings, $dtfunc;

    $query = '';
    foreach ($_POST as $k=>$v){
        $$k=$v;
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    if ($edit){

        //Verificamos que la característica exista
        $ft = new Dtransport_Feature($feature);
        if ($ft->isNew())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/features/'.$item->id().'/':'/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Specified feature does not exists!','dtransport'));

    }else{

        $ft = new Dtransport_Feature();

    }

    $tc = TextCleaner::getInstance();

    if(trim($nameid)=='')
        $nameid = $tc->sweetstring($title);
    else
        $nameid = $tc->sweetstring($nameid);

    //Comprueba que el título de la característica no exista
    $sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_features')." WHERE (title='$title' OR nameid='$nameid' AND id_feat!=".$ft->id()." AND id_soft=".$item->id();
    list($num) = $db->fetchRow($db->queryF($sql));
    if ($num>0)
        redirect_header(DT_URL.($dtSettings->permalinks?'/cp/features/'.$item->id().'/edit/'.$ft->id():'/?s=cp&amp;action=features&amp;id='.$item->id().'/&amp;op=edit&amp;feature='.$ft->id()), 1, __('Another feature with same title already exists!','dtransport'));

    $ft->setSoftware($item->id());
    $ft->setTitle($title);
    $ft->setContent($content);
    if (!$edit) $ft->setCreated(time());
    $ft->setModified(time());
    $ft->setNameId($nameid);

    if (!$ft->save())
        redirect_header(DT_URL.($dtSettings->permalinks?'/cp/features/'.$item->id().'/':'/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Feature could not be saved! Please try again.','dtransport'));

    if(!$dtfunc->save_meta('feat', $ft->id()))
        redirectMsg(DT_URL.($dtSettings->permalinks?'/cp/features/'.$item->id().'/':'/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Feature saved correctly, however custom fields could not be stored in database.','dtransport'));

    redirect_header(DT_URL.($dtSettings->permalinks?'/cp/features/'.$item->id().'/':'/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Featured saved successfully!','dtransport'));
    
}

/**
* Delete features
*/
function dt_delete_feature(){
    global $dtSettings, $item, $feature;
    
    $ft = new Dtransport_Feature($feature);
    
    if($ft->isNew())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/features/'.$item->id() : '/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Specified feature is not valid!','dtransport'));

    if(!$ft->delete())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/features/'.$item->id() : '/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Feature could not be deleted! Please try again.','dtransport'));
    
    redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/features/'.$item->id() : '/?s=cp&amp;action=features&amp;id='.$item->id()), 1, __('Feature deleted successfully!','dtransport'));
    
}

/**
* Show content for a specific feature
*/
function dt_return_feature(){
    global $dtSettings, $feature, $item, $common;

    $common->ajax()->prepare();
    
    if($feature<=0)
        return '';

    $ft = new Dtransport_Feature($feature);
    if($ft->isNew())
        return;

    include $common->template()->path('dt-feature.php', 'module', 'dtransport');
    
}
