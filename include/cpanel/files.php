<?php
/**
 * D-Transport for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

defined('XOOPS_MAINFILE_INCLUDED') or die("Not allowed");

$xoopsOption['template_main'] = 'dt-files.tpl';
$xoopsOption['module_subpage'] = 'cp-files';

/**
* Muestra las características existentes de una descarga
*/
function dt_show_files($edit=0){
    global $xoopsOption,$db,$common,$xoopsTpl,$xoopsUser,$dtSettings, $dtfunc, $page, $item, $xoopsConfig, $xoopsModuleConfig,$file;

    $common->template()->add_script('main.min.js', 'dtransport', ['id' => 'dtransport-js', 'footer' => 1]);

    include('header.php');

    $xoopsTpl->assign('downloadItem', [
        'id' => $item->id(),
        'name' => $item->name
    ]);

    Dtransport_Functions::getInstance()->cpanelHeader();
    
    if($file>0 && $edit){

        $file = new Dtransport_File($file);
        if($file->isNew() || $file->software()!=$item->id()){
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/':'/?s=cp&amp;action=files&amp;id='.$item->id()), 1, __('Specified feature does not exists!','dtransport'));
        }
               
    }

    $tc = TextCleaner::getInstance();
    $tf = new RMTimeFormatter('', "%m%/%d%/%Y% %h%:%i%");
    $rmu = RMUtilities::get();
    
    $tfiles = $db->prefix('mod_dtransport_files');
    $tgroup = $db->prefix('mod_dtransport_groups');
    
    $sql = "SELECT * FROM $tfiles WHERE id_soft=".$item->id();
    $gcache = array();
    $result=$db->queryF($sql);
    while ($rows=$db->fetchArray($result)){
        $fl = new Dtransport_File();
        $fl->assignVars($rows);
        
        if(!isset($gcache[$fl->group()]))
            $gcache[$fl->group()] = new Dtransport_FileGroup($fl->group());
        
        $group = $gcache[$fl->group()];
        
        $xoopsTpl->append('files',array(
            'id'=>$fl->id(),
            'title'=>$fl->title(),
            'default'=>$fl->default,
            'date'=> $tf->format($fl->date()),
            'software'=>$item->getVar('name'),
            'remote'=>$fl->remote(),
            'size' => $rmu->formatBytesSize($fl->size()),
            'hits' => $fl->hits(),
            'date' => $tf->format($fl->date()),
            'group' => $group->isNew() ? '' : $group->name(),
            'links' => array(
                'edit' => DT_URL.($dtSettings->permalinks ? '/cp/files/'.$item->getVar('nameid').'/edit/'.$fl->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=files&amp;feature='.$fl->id()),
                'delete' => DT_URL.($dtSettings->permalinks ? '/cp/files/'.$item->getVar('nameid').'/delete/'.$fl->id().'/' : '/?s=cp&amp;id='.$item->id().'&amp;action=delete&amp;feature='.$fl->id())
            )
        ));
    }
    
    $formurl = DT_URL.($dtSettings->permalinks ? '/cp/files/'.$item->id().'/save/'.($edit ? $file->id() : '0').'/' : '/?s=cp');
    
    // files Form
    $form = new RMForm([
        'title' => $edit ? sprintf(__('Editing file of "%s"','dtransport'),$item->getVar('name')) : sprintf(__('New file for "%s"','dtransport'),$item->getVar('name')),
        'id' => 'frmFile',
        'action' => $formurl,
        'enctype' => 'multipart/form-data'
    ]);
    $form->setExtra('enctype="multipart/form-data"');

    $form->addElement(new RMFormLabel(__('Download item','dtransport'),$item->getVar('name')));
    
    $form->addElement(new RMFormText(__('File title','dtransport'),'title',50,200,$edit ? $file->title() : ''),true);
    
    //Lista de grupos
    $sql ="SELECT * FROM ".$db->prefix('mod_dtransport_groups')." WHERE id_soft=".$item->id();
    $result=$db->query($sql);
    $groups = array();
    while($rows=$db->fetchArray($result)){
        $group=new Dtransport_FileGroup();
        $group->assignVars($rows);

        $groups[] = array(
            'id'=>$group->id(),
            'name'=>$group->name()
        );

    }
    
    $ele = new RMFormSelect(__('Group','dtransport'), 'group', 0, $edit ? [$file->group()] : '');
    $ele->addOption('', __('Select group...','dtransport'));
    foreach($groups as $group){
        $ele->addOption($group['id'], $group['name']);
    }
    $form->addElement($ele);
    $form->addElement(new RMFormYesNo(__('Default file','dtransport'),'default', $edit ? $file->isDefault() : 0));
    $form->addElement(new RMFormYesNo(__('Remote file','dtransport'),'remote', $edit ? $file->remote() : 0));
    $form->addElement(new RMFormFile(__('File','dtransport'), 'thefile', 50, $xoopsModuleConfig['size_file']*1024*1024));
    if($edit){
        $form->addElement(new RMFormLabel(__('Current file','dtransport'), $file->file));
    }
    $form->addElement(new RMFormText(__('File URL','dtransport'),'url',50,200,$edit ? $file->title() : ''))->setDescription(__('Used only when remote file is activated.','dtransport'));

    $form->addElement(new RMFormHidden('action', 'files'));
    $form->addElement(new RMFormHidden('id',$item->id()));
    $form->addElement(new RMFormHidden('file',$edit ? $file->id() : 0));
    $form->addElement(new RMFormHidden('op','save'));
        
    $buttons =new RMFormButtonGroup();
    $buttons->addButton(new RMFormButton([
        'id' => 'file-submit',
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'caption' => __('Save File', 'dtransport')
    ]));
    $buttons->addButton(new RMFormButton([
        'id' => 'form-cancel',
        'type' => 'button',
        'class' => 'btn btn-default',
        'caption' => __('Cancel', 'dtransport')
    ]));

    $form->addElement($buttons);
            
    $xoopsTpl->assign('file_form', $form->render());

    // Item options
    Dtransport_Functions::getInstance()->makeItemCpanelOptions($item);
    
    //$tpl->add_style('cpanel.min.css','dtransport');
    $common->template()->add_inline_script('$(document).ready(function(){
        
        $("a.delete").click(function(){
            if(!confirm("'.__('Do you really want to delete selected file?','dtransport').'")) return false;
        });
        
    });',1);

    Dtransport_Functions::getInstance()->addLangString([
        'id' => __('ID','dtransport'),
        'title' => __('Title','dtransport'),
        'group' => __('Group','dtransport'),
        'remote' => __('Remote','dtransport'),
        'attributes' => __('Attributes','dtransport'),
        'size' => __('Size','dtransport'),
        'hits' => __('Hits','dtransport'),
        'created' => __('Created','dtransport'),
        'noFiles' => __('There are not files in this download item','dtransport'),
        'edit' => __('Edit', 'dtransport'),
        'delete' => __('Delete', 'dtransport'),
    ]);
    
    $xoopsTpl->assign('edit', $edit);

    include 'footer.php';
}

/**
* Save file
*/
function dt_save_file($edit){
    
    global $item, $file, $tpl, $xoopsTpl, $dtSettings, $dtfunc;

    foreach ($_POST as $k=>$v){
        $$k=$v;
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    if ($edit){

        // Check that file exists
        $fl = new Dtransport_File($file);
        if ($fl->isNew())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/':'/?s=cp&amp;action=files&amp;id='.$item->id()), 1, __('Specified file does not exists!','dtransport'));

    }else{

        $fl = new Dtransport_File();

    }

    $tc = TextCleaner::getInstance();

    // Check that file with same name does not exists
    $sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_files')." WHERE title='$title' AND id_file!=".$fl->id()." AND id_soft=".$item->id();
    list($num) = $db->fetchRow($db->queryF($sql));
    if ($num>0)
        redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/edit/'.$fl->id():'/?s=cp&amp;action=files&amp;id='.$item->id().'&amp;op=edit&amp;file='.$fl->id()), 1, __('Another log with same title already exists!','dtransport'));

    // Check if a file has been provided
    if($_FILES['thefile']['name']==''){
        // Comprobamos si se ha proporcionado un archivo
        if(!$edit && !$remote)
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/edit/'.$fl->id():'/?s=cp&amp;action=files&amp;id='.$item->id().'&amp;op=edit&amp;file='.$fl->id()), 1, __('You must provide a file to upload!','dtransport'));
        elseif($remote && $url=='')
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/edit/'.$fl->id():'/?s=cp&amp;action=files&amp;id='.$item->id().'&amp;op=edit&amp;file='.$fl->id()), 1, __('You must provide a file URL when remote type is activated!','dtransport'));
            
    } else {
        
        if($edit && !$fl->remote()){
            $path = $item->getVar('secure') ? rtrim($dtSettings->directory_secure, '/').'/'.$fl->file() : rtrim($dtSettings->directory_insecure).'/'.$fl->file();
            unlink($path);
        }
        
        if($item->getVar('secure'))
            $dir = $dtSettings->directory_secure;
        else
            $dir = $dtSettings->directory_insecure;
        
        include RMCPATH.'/class/uploader.php';

        $uploader = new RMFileUploader($dir, $dtSettings->size_file*1024*1024, $dtSettings->type_file);

        if (!$uploader->fetchMedia('thefile'))
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/edit/'.$fl->id():'/?s=cp&amp;action=files&amp;id='.$item->id().'&amp;op=edit&amp;file='.$fl->id()), 1, __('File could not be uploaded!, Please try again.','dtransport').$uploader->getErrors());

        if (!$uploader->upload())
            redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/edit/'.$fl->id():'/?s=cp&amp;action=files&amp;id='.$item->id().'&amp;op=edit&amp;file='.$fl->id()), 1, __('File could not be uploaded!, Please try again.','dtransport').$uploader->getErrors());
        
    }
    
    $fl->setSoftware($item->id());
    $fl->setTitle($title);
    $fname = !$uploader && $edit ? ($remote ? $url : $fl->file()) : $uploader->getSavedFileName();
    $fl->setFile($fname);
    $fl->setRemote($remote);
    $fl->setGroup($group);
    $fl->setDefault($default);
    $fl->setDate(time());
    $fl->setSize($remote ? '' : (isset($uploader) ? $uploader->getMediaSize() : $fl->size()));
    $fl->setMime($remote ? '' : (isset($uploader) ? $uploader->getMediaType() : $fl->mime()));

    if (!$fl->save())
        redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/'.($edit ? 'edit/'.$fl->id() : ''):'/?s=cp&amp;action=files&amp;id='.$item->id().($edit ? '&amp;op=edit&amp;file='.$fl->id() : '')), 1, __('File could not be saved! Please try again.','dtransport'));
    
    if($fl->isDefault())
        $db->queryF("UPDATE ".$db->prefix("mod_dtransport_files")." SET `default`=0 WHERE id_soft=".$item->id()." AND id_file !=".$fl->id());
        
    redirect_header(DT_URL.($dtSettings->permalinks?'/cp/files/'.$item->id().'/':'/?s=cp&amp;action=files&amp;id='.$item->id()), 1, __('File saved successfully!','dtransport'));
    
}

/**
* Delete files
*/
function dt_delete_file(){
    global $dtSettings, $item, $file;
    
    $fl = new Dtransport_File($file);
    
    if($fl->isNew())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/files/'.$item->id() : '/?s=cp&amp;action=files&amp;id='.$item->id()), 1, __('Specified file is not valid!','dtransport'));

    if(!$fl->delete())
        redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/files/'.$item->id() : '/?s=cp&amp;action=files&amp;id='.$item->id()), 1, __('File could not be deleted! Please try again.','dtransport'));
    
    if($item->getVar('secure'))
        $dir = rtrim($dtSettings->directory_secure, '/');
    else
        $dir = rtrim($dtSettings->directory_insecure, '/');
    
    if(!$fl->remote())
        unlink($dir.'/'.$fl->file());
    
    redirect_header(DT_URL.($dtSettings->permalinks ? '/cp/files/'.$item->id() : '/?s=cp&amp;action=files&amp;id='.$item->id()), 1, __('File deleted successfully!','dtransport'));
    
}

