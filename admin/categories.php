<?php
/**
 * D-Transport: Downloads Manager
 *
 * Copyright © 2015 Red Mexico http://www.eduardocortes.mx
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
 * @copyright    Red Mexico http://www.eduardocortes.mx
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package      dtransport
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 * @url          http://www.eduardocortes.mx
 */

define('RMCLOCATION','categories');
include ('header.php');

/**
* @desc Visualiza todas las categorías existentes
**/
function showCategories(){
	global $xoopsModule, $xoopsSecurity;

	$categos = array();
	Dtransport_Functions::getCategories($categos);
    $categories = array();
	foreach ($categos as $row){
		$cat = new Dtransport_Category();
		$cat->assignVars($row);

		$categories[] = array(
                    'id'=>$cat->id(),
                    'name'=>$cat->name(),
                    'parent'=>$cat->parent(),
                    'active'=>$cat->active(),
                    'description' => $cat->getVar( 'description' ),
                    'indent'=>$row['jumps']
                );

	}
        
    unset($categos);
	
    RMTemplate::get()->add_script('jquery.validate.min.js', 'rmcommon');
    RMTemplate::get()->add_script('admin.min.js', 'dtransport');
    
    // JS Strings
    include DT_PATH . '/include/js-strings.php';

    $bc = RMBreadCrumb::get( );
    $bc->add_crumb( __('Categories', 'dtransport') );

	xoops_cp_header();
    
    include RMTemplate::get()->get_template('admin/dtrans-categories.php', 'module', 'dtransport');
    
	xoops_cp_footer();	

}


/**
* @desc Formulario de creación/edición de categorías
**/
function formCategos($edit=0){
	global $xoopsModule,$db;

    $id = RMHttpRequest::get( 'id', 'integer', 0 );

	if ($edit){

		//Verificamos si categoría es válida
		if ($id<=0)
            RMUris::redirect_with_message(
                __('Specified category is not valid!','dtransport'), 'categories.php', RMMSG_ERROR
            );

        //Verificamos si la categoría existe
        $cat=new Dtransport_Category($id);
        if ($cat->isNew())
            RMUris::redirect_with_message(
                __('Specified category does not exists!','dtransport'), 'categories.php', RMMSG_ERROR
            );

	}

    $bc = RMBreadCrumb::get();
    $bc->add_crumb( $edit ? __('Edit category','dtransport') : __('New category','dtransport') );

	xoops_cp_header();

	$form=new RMForm($edit ? __('Edit Category','dtransport') : __('New Category','dtransport'),'frmcat','categories.php');
	$form->addElement(new RMFormText(__('Category name','dtransport'),'name',50,150,$edit ? $cat->name() : ''),true);

	if ($edit){
		$form->addElement(new RMFormText(__('Category short name','dtransport'),'nameid',50,150,$edit ? $cat->nameId() : ''));
	}

	$form->addElement(new RMFormTextArea(__('Description','dtransport'),'desc',5,40,$edit ? $cat->getVar('description', 'e') : ''));

	//Lista de categorías
	$ele=new RMFormSelect(__('Root category','dtransport'),'parent');
	$ele->addOption(0, __('Select category...','dtransport'));
	$categos = array();
	Dtransport_Functions::getCategories($categos, 0, 0, $edit ? $cat->id() : array());
	foreach ($categos as $catego){
		$ele->addOption($catego['id_cat'],str_repeat('--', $catego['jumps']).' '.$catego['name'],$edit ? ($catego['id_cat']==$cat->parent() ? 1 : 0) : 0);		
	}

	$form->addElement($ele);
	$form->addElement(new RMFormYesno(__('Active category','dtransport'),'active',$edit ? $cat->active() : 1));

	$form->addElement(new RMFormHidden('action',$edit ? 'saveedit' : 'save'));
	$form->addElement(new RMFormHidden('id',$id));

	$buttons =new RMFormButtonGroup();
	$buttons->addButton(new RMFormButton([
	    'caption' => __('Submit', 'dtransport'),
        'class' => 'btn btn-primary',
        'type' => 'submit'
    ]));

    $buttons->addButton(new RMFormButton([
        'caption' => __('Cancel', 'dtransport'),
        'class' => 'btn btn-default',
        'type' => 'button',
        'onclick' => 'window.location.href="categories.php"'
    ]));

	$form->addElement($buttons);
	
	$form->display();
	
	xoops_cp_footer();
}


/**
 * Stores the category data to database
 * @param int $edit
 */
function saveCategories($edit=0){
    global $xoopsSecurity;
    
    $id = RMHttpRequest::post( 'id', 'integer', 0 );
    $name = RMHttpRequest::post( 'name', 'string', '' );
    $nameid = RMHttpRequest::post( 'nameid', 'string', '' );
    $desc = RMHttpRequest::post( 'desc', 'string', '' );
    $parent = RMHttpRequest::post( 'parent', 'integer', 0 );
    
	if (!$xoopsSecurity->check(true, false, $edit ? 'XOOPS_TOKEN': 'XT'))
        RMUris::redirect_with_message(
            __('Session token expired!','dtransport'), 'categories.php', RMMSG_ERROR
        );
		
        
    $db = XoopsDatabaseFactory::getDatabaseConnection();	

	if ($edit){

		// Is a valid category ID?
		if ($id<=0)
            RMUris::redirect_with_message(
                __('Specified category is not valid!','dtransport'), 'categories.php', RMMSG_ERROR
            );

		// check if category exists
		$cat = new Dtransport_Category($id);
		if ($cat->isNew())
            RMUris::redirect_with_message(
                __('Specified category does not exists!', 'dtransport'), 'categories.php', RMMSG_ERROR
            );

		// Check if category name already exists
		$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_categories')." WHERE name='$name' AND id_cat<>'".$id."' AND parent=$parent";
		list($num)=$db->fetchRow($db->queryF($sql));
        
		if ($num>0)
            RMUris::redirect_with_message(
                __('Another category with same name already exists!','dtransport'), 'categories.php?action=edit&id='.$id, RMMSG_ERROR
            );

		// Check the nameid
		if ($nameid){
			$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_categories')." WHERE nameid='$nameid' AND id_cat != $id AND parent=$parent";
			list($num)=$db->fetchRow($db->queryF($sql));
			if ($num>0){
                RMUris::redirect_with_message(
                    __('Already exists another category with same short name!','dtransport'), 'categories.php?action=edit&id='.$id, RMMSG_ERROR
                );
			}

		}

	}else{

		// Check if another category with same name already exists
		$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_categories')." WHERE name='$name' AND parent=$parent";
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0){
            RMUris::redirect_with_message(
                __('Already exists another category with same name!','dtransport'), 'categories.php', RMMSG_ERROR
            );
		}

		$cat = new Dtransport_Category();
        $active = 1;

	}

	// Create a nameId if not provided
	$found=false; 
	$i = 0;
	if ($name!=$cat->name() || empty($nameid)){
		do{
			
			$nameid = TextCleaner::getInstance()->sweetstring($name).($found ? $i : '');
        		$sql = "SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_categories'). " WHERE nameid = '$nameid' AND parent=$parent";
        		list ($num) =$db->fetchRow($db->queryF($sql));
        		if ($num>0){
        			$found =true;
        		    $i++;
        		}else{
        			$found=false;
        		}
		}while ($found==true);
	}
	
	$cat->setVar( 'name', $name );
	$cat->setVar( 'description', $desc );
	$cat->setVar( 'parent', $parent );
	$cat->setVar( 'active', $active );
	$cat->setVar( 'nameid', $nameid );

	if (!$cat->save()){
        RMUris::redirect_with_message(
            __('Category could not be saved!','dtransport') . '<br>' . $cat->errors(), 'categories.php', RMMSG_ERROR
        );
	}else{
        RMUris::redirect_with_message(
            __('Category saved successfully!','dtransport'), 'categories.php', RMMSG_SUCCESS
        );
	}

}


/**
* @desc Elimina las categorías especificadas
**/
function deleteCategos(){
	global $xoopsModule, $xoopsSecurity;

    $ids = RMHttpRequest::post( 'ids', 'array', array() );

	//Verificamos si nos proporcionaron alguna categoria
	if (!is_array($ids) || empty($ids))
        RMUris::redirect_with_message(
            __('You must select at least one category to delete!','dtransport'), 'categories.php', RMMSG_ERROR
        );

	if (!$xoopsSecurity->check())
        RMUris::redirect_with_message(
            __('Session token expired!','dtransport'), 'categories.php', RMMSG_ERROR
        );

	$errors='';
	foreach ($ids as $k){
	    //Verificamos si la categoría es válida
		if ($k<=0){
		    $errors.=sprintf(__('Category ID "%s" is not valid!','dtransport'),$k);
			continue;	
		}

		//verificamos si la categoría existe
		$cat=new Dtransport_Category($k);
		if ($cat->isNew()){
		    $errors.=sprintf(__('Category with ID "%s" does not exists!','dtransport'),$k);
			continue;
		}

		if (!$cat->delete()){
            $errors.=sprintf(__('Category "%s" could not be deleted!','dtransport'),$cat->name());
		}

	}

	if ($errors!=''){
        RMUris::redirect_with_message(
            __('There was errors trying to delete categories','dtransport').'<br />'.$errors, 'categories.php', RMMSG_ERROR
        );
	}else{
        RMUris::redirect_with_message(
            __('Categories deleted successfully!','dtransport'), 'categories.php', RMMSG_SUCCESS
        );
	}

}


/**
* @desc Activa/deactiva categorías
**/
function activeCategos($act=0){

    $ids = RMHttpRequest::post( 'ids', 'array', array() );
	
	//Verificamos si nos proporcionaron alguna categoria
	if (!is_array($ids) || empty($ids)){
        RMUris::redirect_with_message(
            __('You must select at least one category to delete!','dtransport'), 'categories.php', RMMSG_ERROR
        );
	}
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "UPDATE ".$db->prefix("mod_dtransport_categories")." SET active=$act WHERE id_cat IN (".implode(",",$ids).")";

	if (!$db->queryF($sql))
        RMUris::redirect_with_message(
            __('Database could not be updated!','dtransport').'<br />'.$db->error(), 'categories.php', RMMSG_ERROR
        );
	else
        RMUris::redirect_with_message(
            __('Database updated successfully!','dtransport'), 'categories.php', RMMSG_SUCCESS
        );

}


$action = RMHttpRequest::request( 'action', 'string', '' );

switch ($action){
	case 'new':
		formCategos();
	    break;
	case 'edit':
		formCategos(1);
	    break;
	case 'save':
		saveCategories();
	    break;
	case 'saveedit':
		saveCategories(1);
	    break;
	case 'delete':
		deleteCategos();
	    break;
	case 'active':
		activeCategos(1);
	    break;
	case 'desactive':
		activeCategos();
	    break;
	default:
		showCategories();
        break;
}
