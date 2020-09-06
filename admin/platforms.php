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

define('RMCLOCATION','platforms');
include ('header.php');

/**
* @desc Visualiza las plataformas existentes y muestra formulario de plataformas
**/
function showPlatforms($edit=0){
    global $xoopsSecurity, $xoopsModule, $cuIcons;

	$id = rmc_server_var($_REQUEST, 'id', 0);

    $db = XoopsDatabaseFactory::getDatabaseConnection();    
	$sql="SELECT * FROM ".$db->prefix('mod_dtransport_platforms');
	$result=$db->queryF($sql);
    $platforms = array();
	while($rows=$db->fetchArray($result)){

		$plat=new Dtransport_Platform();
		$plat->assignVars($rows);

		$platforms[] = array(
            'id'=>$plat->id(),
            'name'=>$plat->name()
        );
	}

	if ($edit){

		//Verificamos si plataforma es válida
		if ($id<=0){
			redirectMsg('platforms.php', __('You must specified a valid platform ID!','dtransport'),1);
			die();
		}

		//Verificamos si plataforma existe
		$plat=new Dtransport_Platform($id);
		if ($plat->isNew()){
			redirectMsg('platforms.php', __('Sepecified platform does not exists!','dtransport'),1);
			die();
		}
	}
	
    RMTemplate::get()->add_style('cp.min.css', 'dtransport');
    RMTemplate::get()->add_script('jquery.validate.min.js', 'rmcommon');
    RMTemplate::get()->add_script('admin.min.js', 'dtransport');
    
    include DT_PATH .'/include/js-strings.php';

	$bc = RMBreadCrumb::get();
	$bc->add_crumb( $edit ? __('Edit Platform','dtransport') : __('New Platform','dtransport') );

	xoops_cp_header();
  
    include RMTemplate::get()->get_template('admin/dtrans-platforms.php', 'module', 'dtransport');
	
	xoops_cp_footer();

}


/**
* @desc Almacena la información de las plataformas
**/
function savePlatforms($edit=0){
	global $xoopsSecurity;

	foreach ($_POST as $k=>$v){
		$$k=$v;
	}
    
    if(!$xoopsSecurity->check()){
        redirectMsg('Session token expired!','dtransport');
    }
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $tc = TextCleaner::getInstance();
    $nameid = $tc->sweetstring($name);

	if ($edit){

		//Verificamos si plataforma es válida
		if ($id<=0){
			redirectMsg('platforms.php', __('You must specify a valid platform ID!','dtrasnport'),1);
			die();
		}

		//Verificamos si plataforma existe
		$plat=new Dtransport_Platform($id);
		if ($plat->isNew()){
			redirectMsg('platforms.php', __('Specified platform does not exists!','dtransport'),1);
			die();
		}

		//Comprueba que la plataforma no exista
		$sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_platforms')." WHERE (name='$name' OR nameid='$nameid') AND id_platform<>".$plat->id();
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0){
			redirectMsg('platforms.php', __('Another platform with same name already exists!','dtransport'),1);	
			die();
		}


	}else{

		//Comprueba que la plataforma no exista
		$sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_platforms')." WHERE name='$name' OR nameid='$nameid'";
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0){
			redirectMsg('platforms.php', __('Another platform with same name already exists!','dtransport'),1);	
			die();
		}
	
		$plat=new Dtransport_Platform();
        
	}
    
	$plat->setName($name);
    $plat->setNameId($nameid);

	if (!$plat->save()){
		redirectMsg('platforms.php', __('Database could not be updated!','dtransport').'<br />'.$plat->errors(),1);
		die();
	}else{
		redirectMsg('./platforms.php', __('Platform saved successfully!','dtransport'),0);
		die();
	}


}



/**
* @desc Elimina la plataforma especificada
**/
function deletePlatforms(){

	global $xoopsModule,$xoopsSecurity;

	$ids = rmc_server_var($_POST, 'ids', array());

	//Verificamos si nos proporcionaron alguna plataforma
	if (!is_array($ids) || empty($ids)){
		redirectMsg('platforms.php', __('You must select at least one platform to delete!','dtransport'),1);
		die();	
	}
	
	if (!$xoopsSecurity->check()){
	    redirectMsg('platforms.php', __('Session token expired','dtransport'), 1);
		die();
	}

	$errors='';
	foreach ($ids as $k){		
	    //Verificamos si la plataforma es válida
		if ($k<=0){
		    $errors .= sprintf(__('Invalid platform ID: %s','dtransport'),$k);
			continue;	
		}

		//Verificamos si la plataforma existe
		$plat=new Dtransport_Platform($k);
		if ($plat->isNew()){
		    $errors .= sprintf(__('Does nto exists platform with ID %s','dtransport'),$k);
			continue;			
		}

		if (!$plat->delete()){
		    $errors .= sprintf(__('Platform "%s" could not be deleted!','dtransport'),$plat->name());
		}

	}

	if ($errors!='')
	    redirectMsg('platforms.php', __('Errors ocurred while trying to delete platforms:','dtransport').'<br />'.$errors,1);
	else
	    redirectMsg('platforms.php', __('Platforms deleted successfully!','dtransport'),0);

}


$action = rmc_server_var($_REQUEST, 'action', '');

switch ($action){
	case 'edit':
		showPlatforms(1);
	    break;
	case 'save':
		savePlatforms();
	    break;
	case 'saveedit':
		savePlatforms(1);
	    break;
	case 'delete':
		deletePlatforms();
	    break;
	default:
		showPlatforms();

}
