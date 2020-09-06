<?php
// $Id: screens.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

defined('XOOPS_MAINFILE_INCLUDED') or die("Not allowed");

$xoopsOption['template_main'] = 'dt-screens.tpl';
$xoopsOption['module_subpage'] = 'cp-screens';

/**
* @desc Visualiza las pantallas del software y 
* el formulario de creación de pantallas
**/
function dt_screens($edit=0){
	global $common,$db,$tpl,$xoopsTpl,$xoopsUser,$dtSettings, $dtfunc, $page, $item, $xoopsConfig, $xoopsModuleConfig,$screen;
	
	include('header.php');

    $xoopsTpl->assign('downloadItem', [
        'id' => $item->id(),
        'name' => $item->name
    ]);

    Dtransport_Functions::getInstance()->cpanelHeader();
    // Item options
    Dtransport_Functions::getInstance()->makeItemCpanelOptions($item);

    $tc = TextCleaner::getInstance();
    
	$sql = "SELECT * FROM ".$db->prefix('mod_dtransport_screens')." WHERE id_soft=".$item->id();
	$result=$db->queryF($sql);
	while ($rows=$db->fetchArray($result)){
		$sc = new Dtransport_Screenshot();
		$sc->assignVars($rows);
		
		$xoopsTpl->append('screens',array(
            'id'=>$sc->id(),
            'title'=>$sc->title,
		    'desc'=> $tc->clean_disabled_tags($sc->desc()),
            'software'=>$item->name,
            'image' => $sc->image,
            'links' => array(
                'edit' => DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->getVar('nameid').'/edit/'.$sc->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=screens&amp;screen='.$sc->id()),
                'delete' => DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->getVar('nameid').'/delete/'.$sc->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=delete&amp;screen='.$sc->id())
            )
        ));
	}

	//Formulario de pantallas
	if ($edit){
		//Verificamos si la pantalla es válida
		if ($screen<=0)
			redirect_header(DT_URL.($dtSettings->permalinks?'/screens/'.$item->getVar('nameid'):'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Invalid screen','dtransport'));

		//Verificamos si la pantalla existe
		$sc = new Dtransport_Screenshot($screen);
		if ($sc->isNew())
			redirect_header(DT_URL.($dtSettings->permalinks?'/screens/'.$item->getVar('nameid'):'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Invalid screen','dtransport'));

	}
	
	if ($edit || $dtSettings->limit_screen>$item->getVar('screens')){
        
        if($edit)
            $faction = DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->id().'/save/'.$sc->id().'/':'/?s=cp');
        else
           $faction = DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->id().'/save/0/':'?s=cp');
        
		$form = new RMForm($edit ? sprintf(__('Edit Screenshot of %s','dtransport'),$item->getVar('name')) : sprintf(__('Add screen for %s','dtransport'),$item->getVar('name')),'frmscreen', $faction);
		$form->setExtra("enctype='multipart/form-data'");	
	
		$form->addElement(new RMFormLabel(__('Download item','dtransport'),$item->getVar('name')));

	
		$form->addElement(new RMFormText(__('Title','dtransport'),'title',50,100,$edit ? $sc->title() : ''),true);
		$form->addElement(new RMFormEditor(__('Description','dtransport'),'desc','auto','100px',$edit ? $sc->desc() :'','simple'));

		$plugins = $common->plugins();
		$advField = $plugins::isInstalled('advform') || $plugins::isInstalled('advform-pro');

		if($advField){
            $form->addElement(new RMFormImageUrl([
                'caption' => __('Image URL', 'dtransport'),
                'id' => 'image',
                'name' => 'image',
                'value' => $sc->image
            ]));
        } else {
            $form->addElement(new RMFormText(__('Image file','dtransport'),'image',45, null, $edit ? $sc->image : ''),$edit ? '':true);
        }
	
		if ($edit && false == $advField){
			$img = "<img src='".$sc->url('image')."' border='0' />";
			$form->addElement(new RMFormLabel(__('Current image','dtransport'),$img));	
		}	

        $form->addElement(new RMFormHidden('s','cp'));
		$form->addElement(new RMFormHidden('action', 'screens'));
        $form->addElement(new RMFormHidden('id',$item->id()));
        $form->addElement(new RMFormHidden('op', 'save'));
		$form->addElement(new RMFormHidden('screen', $edit ? $sc->id() : 0));
        $buttons =new RMFormButtonGroup();
        $buttons->addButton(new RMFormButton([
            'id' => 'file-submit',
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'caption' => __('Save Screenshot', 'dtransport')
        ]));
        $buttons->addButton(new RMFormButton([
            'id' => 'form-cancel',
            'type' => 'button',
            'class' => 'btn btn-default',
            'caption' => __('Cancel', 'dtransport')
        ]));

		$form->addElement($buttons);
	
		$xoopsTpl->assign('formscreens',$form->render());

	}
    
    $xoopsTpl->assign('isEdition', $edit);

    Dtransport_Functions::getInstance()->addLangString([
        'id' => __('ID','dtransport'),
        'image' => __('Image','dtransport'),
        'title' => __('Title','dtransport'),
        'description' => __('Description','dtransport'),
        'created' => __('Created','dtransport'),
        'noScreens' => __('There are not screenshots in this download item','dtransport'),
        'edit' => __('Edit', 'dtransport'),
        'delete' => __('Delete', 'dtransport'),
    ]);

	include('footer.php');

}


/**
* @desc almacena la informacion de la pantalla en la base de datos
**/
function dt_save_screens($edit=0){
	global $item, $xoopsModuleConfig, $screen, $dtSettings;
    
	foreach ($_POST as $k=>$v){
		$$k=$v;
	}
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();

	if ($edit){

		//Verificamos que la pantalla exista
		$sc=new Dtransport_Screenshot($screen);
		if ($sc->isNew()){
		    RMUris::redirect_with_message(
                __('Specified screenshot is not valid!','dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp',['action' => 'screens', 'id' => $item->id()]),
                RMMSG_ERROR
            );
        }

		//Comprueba que el título de la pantalla no exista
		$sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_screens')." WHERE title='$title' AND id_soft=".$item->id()." AND id_screen!=".$sc->id();
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0)
			redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/edit/'.$sc->id():'/?s=cp&amp;action=screens&amp;id='.$item->id().'&amp;op=edit&amp;screen'.$sc->id()),1, __('Already exist another screenshot with the same name!','dtransport'));

	}else{

		//Comprueba que el título de la pantalla no exista
		$sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_screens')." WHERE title='$title' AND id_soft=".$item->id();
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0)
			redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/':'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Already exist another screenshot with the same name!','dtransport'));
		
        $sc=new Dtransport_Screenshot();

	}

	$sc->setTitle($title);
	$sc->setDesc($desc);
	$sc->setDate(time());
	$sc->setSoftware($item->id());
	
	//Cargamos la imagen
    // Directorio de almacenamiento
    
    if(isset($_FILES['image']) && $_FILES['image']['name']!=''){
        
        $dir = XOOPS_UPLOAD_PATH.'/screenshots';
        
        // Eliminamos la imagen existente
        if($edit){
            $dir .= '/'.date('Y', $sc->date()).'/'.date('m', $sc->date());
            unlink($dir.'/'.$sc->image());
            unlink($dir.'/ths/'.$sc->image());
            $dir = XOOPS_UPLOAD_PATH.'/screenshots';
        }
        
        if (!is_dir($dir))
            mkdir($dir, 511);

        $dir .= '/'.date('Y', time());
        if (!is_dir($dir))
            mkdir($dir, 511);

        $dir .= '/'.date('m',time());
        if (!is_dir($dir))
            mkdir($dir, 511);

        if (!is_dir($dir.'/ths'))
            mkdir($dir.'/ths', 511);

        if(!is_dir($dir))
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/':'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Image could not be upload due to an internal error!','dtransport'));
        
	    include RMCPATH.'/class/uploader.php';
	    $uploader = new RMFileUploader($dir, $dtSettings->image*1024, array('jpg','gif','png'));
	    $err = array();
        if (!$uploader->fetchMedia('image'))
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/':'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Image could not be upload due to an internal error!','dtransport'));
        
        if (!$uploader->upload())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/':'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Image could not be upload due to an internal error!','dtransport'));
            
        $sc->setImage($uploader->getSavedFileName());
        
        // Resize image
        $thumb = explode(":",$dtSettings->size_ths);
        $big = explode(":",$dtSettings->size_image);
        $sizer = new RMImageResizer($dir.'/'.$sc->getVar('image'), $dir.'/ths/'.$sc->getVar('image'));

        // Thumbnail
        if(!isset($thumb[2]) || $thumb[2]=='crop'){
            $sizer->resizeAndCrop($thumb[0], $thumb[1]);
        } else {
            $sizer->resizeWidthOrHeight($thumb[0], $thumb[1]);
        }

        // Full size image
        $sizer->setTargetFile($dir.'/'.$sc->image());
        if(!isset($big[2]) || $big[2]=='crop'){
            $sizer->resizeAndCrop($big[0], $big[1]);
        } else {
            $sizer->resizeWidthOrHeight($big[0], $big[1]);
        }
    }
	
	if (!$sc->save()){
		if ($sc->isNew())
			redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/':'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Already exist another screenshot with the same name!','dtransport'));
		 else
			redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/edit/'.$sc->id():'/?s=cp&amp;action=screens&amp;id='.$item->id().'&amp;op=edit&amp;screen'.$sc->id()),1, __('Already exist another screenshot with the same name!','dtransport'));
			
	}else
		redirect_header(DT_URL.($dtSettings->permalinks?'/cp/screens/'.$item->id().'/':'/?s=cp&amp;action=screens&amp;id='.$item->id()),1, __('Screenshot saved successfully!','dtransport'));

}


/**
* @desc Elmina las pantallas de la base de datos
**/
function dt_delete_screen(){
    global $dtSettings, $item, $screen, $tpl, $xoopsTpl;
    
    $sc = new Dtransport_Screenshot($screen);
    
    if($sc->isNew())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->id() : '/?s=cp&amp;action=screens&amp;id='.$item->id()), 1, __('Specified screenshot is not valid!','dtransport'));

	if(!$sc->delete())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->id() : '/?s=cp&amp;action=screens&amp;id='.$item->id()), 1, __('Screenshot could not be deleted! Please try again.','dtransport'));
    
    redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/screens/'.$item->id() : '/?s=cp&amp;action=screens&amp;id='.$item->id()), 1, __('Screenshot deleted successfully!','dtransport'));

}
