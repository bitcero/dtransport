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

define('RMCLOCATION','licenses');
include ('header.php');

/**
* @desc Visualiza todas las licencias existentes
**/
function showLicences(){
    
    global $xoopsModule, $xoopsSecurity, $cuIcons;
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
	$sql="SELECT * FROM ".$db->prefix('mod_dtransport_licences');
	$result=$db->queryF($sql);
    $licences = array();
	while ($rows=$db->fetchArray($result)){

		$lc=new Dtransport_License();
		$lc->assignVars($rows);

		$licences[] = array(
            'id'=>$lc->id(),
            'name'=>$lc->name(),
            'url'=>$lc->link(),
            'type'=>$lc->getVar('type')
        );
        
	}

    RMTemplate::get()->add_style('cp.min.css', 'dtransport');
    RMTemplate::get()->add_script('jquery.validate.min.js', 'rmcommon');
    RMTemplate::get()->add_script('admin.min.js', 'dtransport');
    
    include DT_PATH . '/include/js-strings.php';

	$bc = RMBreadCrumb::get();
	$bc->add_crumb( __('Licences','dtransport') );

	xoops_cp_header();
    
    include RMTemplate::get()->get_template('admin/dtrans-licenses.php', 'module', 'dtransport');
    
	xoops_cp_footer();


}


/**
* @desc Formulario de licencias
**/
function formLicences($edit=0){

	global $xoopsModule, $common;

	$id = rmc_server_var($_REQUEST, 'id', 0);

	if ($edit){
		//Verificamos si la licencia es válida
		if ($id<=0){
            $common->uris()->redirect_with_message(
                'licenses.php',
                __('You must provide a valid license ID!','dtransport'),
                RMMSG_WARN
            );
			die();
		}

		//Verificamos si la licencia existe
		$lc=new Dtransport_License($id);
		if ($lc->isNew()){
            $common->uris()->redirect_with_message(
                'licenses.php',
                __('Specified licence ID does not exists!','dtransport'),
                RMMSG_WARN
            );
			die();
		}

	}


	$bc = RMBreadCrumb::get();
	$bc->add_crumb( __('Licenses', 'dtransport'), 'licenses.php' );
	$bc->add_crumb( $edit ? __('Edit Licence','dtransport') : __('New Licence','dtransport') );

	xoops_cp_header();

	$form=new RMForm($edit ? __('Edit Licence','dtransport') : __('New Licence','dtransport'),'frmlic','licenses.php');
	$form->addElement(new RMFormText(__('Licence name','dtransport'),'name',50,150,$edit ? $lc->name() : ''),true);
	$form->addElement(new RMFormText(__('Licence reference URL','dtransport'),'url',50,255,$edit ? $lc->link() : ''));

	$ele=new RMFormSelect(__('Licence type','dtranport'),'type');
	$ele->addOption(1, __('Open source licence','dtransport'),$edit ? ($lc->type()==1 ? 1 : 0) : 1);
	$ele->addOption(0, __('Restrictive licence','dtranport'),$edit ? ($lc->type()==0 ? 1 : 0) : 0);

	$form->addElement($ele);

	$form->addElement(new RMFormHidden('action',$edit ? 'saveedit' : 'save'));
	$form->addElement(new RMFormHidden('id',$id));

	$buttons =new RMFormButtonGroup();
	$buttons->addButton('sbt', __('Save Changes','dtransport'),'submit');
	$buttons->addButton('cancel',__('Cancel','stransport'),'button', 'onclick="window.location=\'licenses.php\';"');

	$form->addElement($buttons);
	

	$form->display();

	xoops_cp_footer();

}


/**
* @desc Almacena la informaciónj de las licencias en la base de datos
**/
function saveLicences($edit=0){

	global $xoopsSecurity, $common;

    $id = $common->httpRequest()->post('id', 'integer', 0);
    $name = $common->httpRequest()->post('name', 'string', '');
    $url = $common->httpRequest()->post('url', 'string', '');
    $type = $common->httpRequest()->post('type', 'integer', 1);

	if (!$xoopsSecurity->check()){
        $common->uris()->redirect_with_message(
            __('Session token expired!','dtransport'),
            'licenses.php',
            RMMSG_DANGER
        );
		die();
	}

    $tc = TextCleaner::getInstance();
    $nameid = $tc->sweetstring($name);

    $db = XoopsDatabaseFactory::getDatabaseConnection();

	if ($edit){
		//Verificamos si la licencia es válida
		if ($id<=0){
            $common->uris()->redirect_with_message(
                __('You must provide a licence identifier in order to edit its data','dtransport'),
                'licenses.php',
                RMMSG_DANGER
            );
			die();
		}

		//Verificamos si la licencia existe
		$lc=new Dtransport_License($id);
		if ($lc->isNew()){
            $common->uris()->redirect_with_message(
                __('Specified licence does not exists!','dtransport'),
                'licenses.php',
                RMMSG_DANGER
            );
			die();
		}

		//Comprueba que la licencia no exista
		$sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_licences')." WHERE (name='$name' OR nameid='$nameid') AND id_lic!=".$lc->id();
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0){
            $common->uris()->redirect_with_message(
                __('Another licence with same name already exists!','dtransport'),
                'licenses.php?action=edit&id='.$id."&name=$name&url=$url",
                RMMSG_DANGER
            );
			die();
		}


	}else{

		//Comprueba que la licencia no exista
		$sql="SELECT COUNT(*) FROM ".$db->prefix('mod_dtransport_licences')." WHERE name='$name' OR nameid='$nameid'";
		list($num)=$db->fetchRow($db->queryF($sql));
		if ($num>0){
            $common->uris()->redirect_with_message(
                __('Another licence with same name already exists!','dtransport'),
                'licenses.php',
                RMMSG_DANGER
            );
			die();
		}

		$lc=new Dtransport_License();
	}

	$lc->setName($name);
    $lc->setNameId($nameid);
	//Verificamos si se proporcionó una url correcta
	$lc->setLink($url);
	$lc->setType($type);

	if (!$lc->save()){
		if (!$lc->isNew()){
            $common->uris()->redirect_with_message(
                __('Licence could not be saved! Please try again','dtransport'),
                'licenses.php?action=edit&id='.$id."&name=$name&url=$url&type=$type",
                RMMSG_DANGER
            );
			die();
		}
	}else{
        $common->uris()->redirect_with_message(
            __('License saved successfully!','dtransport'),
            'licenses.php',
            RMMSG_SUCCESS
        );
		die();
	}
	
}
	

/**
* @desc Elimina la licencia especificada
**/
function deleteLicences(){

	global $xoopsSecurity;

	$ids = rmc_server_var($_REQUEST, 'ids', array());
	
	//Verificamos si nos proporcionaron alguna licencia
	if (empty($ids) || !is_array($ids)){
		redirectMsg('licenses.php', __('No licenses has been specified!','dtransport'),1);
		die();	
	}

	if (!$xoopsSecurity->check()){
	    redirectMsg('licenses.php', __('Session token expired!','dtransport'), 1);
		die();
	}

	$errors='';
	foreach ($ids as $k){

	    //Verificamos si la licnecia es válida
		if ($k<=0){
		    $errors.=sprintf(__('Specified ID for licence does not exists: %s','dtransport'),$k);
			continue;
		}

		//Verificamos si la licencia existe
		$lic=new Dtransport_License($k);
		if ($lic->isNew()){
		    $errors.=sprintf(__('Sepecified licence does not exists: %s','dtransport'),$k);
			continue;
		}

		if (!$lic->delete()){
		    $errors .= sprintf(__('Licence "%s" could not be deleted!','dtransport'),$lic->name()) . '<br>' . $lic->errors();
		}

	}

	if ($errors!=''){
	    redirectMsg('licenses.php', __('Errors ocurred whilke trying to delete licenses:','dtransport').'<br />'.$errors,1);
	}else{
	    redirectMsg('licenses.php', __('Licences deleted successfully!','dtransport'),0);
	}

}

$action = rmc_server_var($_REQUEST,'action','');

switch ($action){
	case 'new':
		formLicences();
	break;
	case 'edit':
		formLicences(1);
	break;
	case 'save':
		saveLicences();
	break;
	case 'saveedit':
		saveLicences(1);
	break;
	case 'delete':
		deleteLicences();
	break;
	default:
		showLicences();

}
