<?php
// $Id: dtsoftware.class.php 201 2013-01-27 06:47:22Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Dtransport_Software extends RMObject
{
    private $_tags = array();
    private $_groups = array();
    private $limit = 0;
    private $mode = 0;
    private $_licences = array();
    private $_platforms = array();
    private $_screens = array();
    private $_features = array();
    private $_file = null;
    private $_logs = array();
    // Categories
    private $_categories = array();
    private $_catobjs = array();
    private $_fields = array();

    public $alert = null;

    function __construct($id = null)
    {

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->_dbtable = $this->db->prefix("mod_dtransport_items");
        $this->setNew();
        $this->initVarsFromTable();

        $this->setVarType('groups', XOBJ_DTYPE_ARRAY);

        if ($id == null) return;

        if (is_numeric($id)) {
            if (!$this->loadValues($id)) return;
            $this->unsetNew();
        } else {
            $this->primary = "nameid";
            if ($this->loadValues($id)) $this->unsetNew();
            $this->primary = "id_soft";
        }

    }


    public function id()
    {
        return $this->getVar('id_soft');
    }


    /**
     * @desc Grupos con permiso de acceso al elemento
     **/
    public function canDownload($gid)
    {

        if (!is_array($gid) && $gid == XOOPS_GROUP_ADMIN) return true;

        $gid = !is_array($gid) ? array($gid) : $gid;

        if (in_array(0, $this->getVar('groups'))) return true;
        if (in_array(XOOPS_GROUP_ADMIN, $gid)) return true;

        foreach ($gid as $g) {
            if (in_array($g, $this->getVar('groups'))) return true;
        }

        return false;

    }

    /**
     * @desc Numero de comentarios
     */
    public function addComment()
    {
        $sql = "UPDATE " . $this->db->prefix("mod_dtransport_items") . " SET comments=comments+1 WHERE id_soft='" . $this->id() . "'";
        if (!$this->db->queryF($sql)) {
            $this->addError($this->db->error());
            return false;
        } else {
            return true;
        }
    }

    public function addHit()
    {
        $sql = "UPDATE " . $this->db->prefix("mod_dtransport_items") . " SET hits=hits+1 WHERE id_soft='" . $this->id() . "'";
        return $this->db->queryF($sql);
    }

    public function addVote($rate)
    {
        if ($this->isNew()) return;
        return $this->db->queryF("UPDATE " . $this->db->prefix("mod_dtransport_items") . " SET `votes`=`votes`+1, `rating`='" . ($this->getVar('rating') + $rate) . "' WHERE id_soft='" . $this->id() . "'");
        //$this->setRating($this->rating()+$rate);
    }

    public function incrementScreens()
    {
        $sql = "UPDATE " . $this->_dbtable . " SET screens=screens+1 WHERE id_soft='" . $this->id() . "'";
        return $this->db->queryF($sql);
    }

    public function decrementScreens()
    {
        $sql = "UPDATE " . $this->_dbtable . " SET screens=screens-1 WHERE id_soft='" . $this->id() . "'";
        return $this->db->queryF($sql);
    }

    /**
     * @desc Obtiene las etiquestas a las que pertenece el software
     * @param bool True devuelve como objetos
     * @param bool $assocArray Indicates, when $asobj is false, return 'id'=>value
     * @return array
     **/
    public function tags($asobj = false, $assocArray = true)
    {

        $tbl1 = $this->db->prefix("mod_dtransport_itemtag");
        $tbl2 = $this->db->prefix("mod_dtransport_tags");

        if (empty($this->_tags) || ($asobj && !is_a($this->_tags[0], 'DTTag'))) {
            $this->_tags = array();
            $sql = "SELECT b.* FROM $tbl1 AS a, $tbl2 AS b WHERE a.id_soft='" . $this->id() . "' AND b.id_tag=a.id_tag ORDER BY b.hits DESC";
            $result = $this->db->queryF($sql);
            while ($rows = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new DTTag();
                    $tmp->assignVars($rows);
                } else {
                    $tmp = $rows;
                }

                if($assocArray){
                    $this->_tags[$rows['id_tag']] = $rows['tag'];
                } else {
                    $this->_tags[] = $tmp;
                }

            }
        }

        return $this->_tags;

    }


    public function setTags($tags)
    {
        $this->_tags = $tags;
    }

    /**
     * @desc Obtenemos el archivo por defecto
     */
    public function file()
    {

        if (!$this->_file) {
            $sql = "SELECT * FROM " . $this->db->prefix("mod_dtransport_files") . " WHERE id_soft='" . $this->id() . "' AND `default`='1'";
            $result = $this->db->query($sql);
            if ($this->db->getRowsNum($result) > 0) {
                $row = $this->db->fetchArray($result);
                $this->_file = new Dtransport_File();
                $this->_file->assignVars($row);
            } else {
                $sql = "SELECT * FROM " . $this->db->prefix("mod_dtransport_files") . " WHERE id_soft='" . $this->id() . "' ORDER BY id_file LIMIT 0,1";
                $result = $this->db->query($sql);
                if ($this->db->getRowsNum($result) <= 0) return;
                $row = $this->db->fetchArray($result);
                $this->_file = new Dtransport_File();
                $this->_file->assignVars($row);
            }
        }

        return $this->_file;
    }

    /**
     * @desc Grupos de archivos que pertencen a este elemento
     * @param bool True devuelve objetos {@link Dtransport_FileGroup()}
     * @return array();
     **/
    public function fileGroups($asobj = false)
    {

        if (empty($this->_groups) || ($asobj && !is_a($this->_groups[0], 'Dtransport_FileGroup'))) {
            $this->_groups = array();
            $sql = "SELECT * FROM " . $this->db->prefix('mod_dtransport_groups') . " WHERE id_soft=" . $this->id() . " ORDER BY id_group DESC";
            $result = $this->db->queryF($sql);
            while ($rows = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new Dtransport_FileGroup();
                    $tmp->assignVars($rows);
                } else {
                    $tmp = $rows['id_group'];
                }
                $this->_groups[] = $tmp;
            }
        }

        return $this->_groups;

    }

    /**
     * @desc Obtiene la alerta correspondiente del software
     * @return object
     **/
    public function alert()
    {


        if (!is_a($this->alert, 'Dtransport_Alert')) {
            /*$sql = "SELECT * FROM " . $this->db->prefix('mod_dtransport_alerts') . " WHERE id_soft=" . $this->id();
            $result = $this->db->queryF($sql);
            $this->alert = new Dtransport_Alert();
            if ($this->db->getRowsNum($result) <= 0) return;
            $this->alert->assignVars($this->db->fetchArray($result));*/
            $alert = new Dtransport_Alert($this->id());

            if($alert->isNew()){die();
                return;
            }
            $this->alert = $alert;
            unset($alert);

        }

        return $this->alert;
    }


    /**
     * @desc Obtiene valor para determinar si se crea la alerta para el software
     **/
    public function createAlert()
    {
        $this->alert = new Dtransport_Alert($this->id());
    }


    /**
     * @desc Obtiene el limite de dias de la alerta
     * @param int $limit
     **/
    public function setLimit($limit)
    {
        if (!$this->alert) return false;
        $this->alert->setLimit($limit);
    }

    /**
     * @desc Obtiene el modo de envio de la alerta
     * @param int $mode
     **/
    public function setMode($mode)
    {
        if (!$this->alert) return false;
        $this->alert->setMode($mode);
    }


    /**
     * @desc Establece las licencias del elemento
     * @param array $licences Arreglo de ids de licencias
     **/
    public function setLicences($licences)
    {
        $this->_licences = $licences;

    }

    /**
     * @desc Obtiene las licencias del elemento
     * @param bool True devuelve objetos {@link Dtransport_License()}
     * @return array
     */
    public function licences($asobj = false)
    {

        $tbl1 = $this->db->prefix("mod_dtransport_licsoft");
        $tbl2 = $this->db->prefix("mod_dtransport_licences");

        if (empty($this->_licences) || ($asobj && !is_a($this->_licences[0], 'Dtransport_License'))) {
            $this->_licences = array();
            $sql = "SELECT b.* FROM $tbl1 a, $tbl2 b  WHERE a.id_soft='" . $this->id() . "' AND b.id_lic=a.id_lic";
            $result = $this->db->queryF($sql);
            while ($rows = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new Dtransport_License();
                    $tmp->assignVars($rows);
                } else {
                    $tmp = $rows['id_lic'];
                }
                $this->_licences[] = $tmp;
            }
        }

        return $this->_licences;

    }

    /**
     * Set the categories assigned to this download
     * @param array Categories ids
     */
    public function setCategories($cats)
    {
        $this->_categories = $cats;
    }

    /**
     * Get the categories from database
     * @param bool determines if function returns DTCategories objects or array with ids
     * @return array
     */
    public function categories($obj = false)
    {
        $tbl1 = $this->db->prefix("mod_dtransport_catitem");
        $tbl2 = $this->db->prefix("mod_dtransport_categories");

        if (!empty($this->_categories) && !$obj)
            return $this->_categories;
        elseif (!empty($this->_catobjs) && $obj)
            return $this->_catobjs;


        $sql = "SELECT b.* FROM $tbl1 a, $tbl2 b WHERE a.soft='" . $this->id() . "' AND b.id_cat=a.cat";
        $result = $this->db->query($sql);

        if ($obj) $ret = array();

        while ($row = $this->db->fetchArray($result)) {

            $this->_categories[] = $row['id_cat'];
            if ($obj) {
                $cat = new Dtransport_Category();
                $cat->assignVars($row);
                $this->_catobjs[$row['id_cat']] = $cat;
            }

        }

        return $obj ? $this->_catobjs : $this->_categories;


    }

    /**
     * @des Establece las plataformas del software
     **/
    public function setPlatforms($platforms)
    {

        $this->_platforms = $platforms;

    }

    /**
     * @desc Obtiene las plataformas a las que pertenece el elemento
     * @param bool True obtiene los objetos {@link Dtransport_Platform}
     * @return array
     */
    public function platforms($asobj = false)
    {

        $tbl1 = $this->db->prefix("mod_dtransport_platsoft");
        $tbl2 = $this->db->prefix("mod_dtransport_platforms");

        if (empty($this->_platforms) || ($asobj && !is_a($this->_platforms, 'Dtransport_Platform'))) {
            $this->_platforms = array();
            $sql = "SELECT b.* FROM $tbl1 a, $tbl2 b WHERE a.id_soft='" . $this->id() . "' AND b.id_platform=a.id_platform";
            $result = $this->db->queryF($sql);
            while ($rows = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new Dtransport_Platform();
                    $tmp->assignVars($rows);
                } else {
                    $tmp = $rows['id_platform'];
                }
                $this->_platforms[] = $tmp;
            }
        }

        return $this->_platforms;

    }

    /**
     * @desc Obtiene las pantallas del elemento
     * @param bool True devuelve objetos {@link Dtransport_Feature}
     * @return array
     */
    function features($asobj = false)
    {

        if (empty($this->_features) || ($asobj && !is_a($this->_features[0], 'Dtransport_Feature'))) {
            $this->_features = array();
            $sql = "SELECT * FROM " . $this->db->prefix("mod_dtransport_features") . " WHERE id_soft='" . $this->id() . "' ORDER BY created ASC";
            $result = $this->db->query($sql);
            while ($row = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new Dtransport_Feature();
                    $tmp->assignVars($row);
                } else {
                    $tmp = $row['id_feat'];
                }
                $this->_features[] = $tmp;
            }
        }

        return $this->_features;

    }

    /**
     * @desc Obtiene los logs del elemento
     * @param bool True devuelve objetos {@link Dtransport_Log}
     * @return array
     */
    function logs($asobj = false, $limit = 0)
    {

        if (empty($this->_logs) || ($asobj && !is_a($this->_logs[0], 'Dtransport_Log'))) {
            $this->_logs = array();
            $sql = "SELECT * FROM " . $this->db->prefix("mod_dtransport_logs") . " WHERE id_soft='" . $this->id() . "' ORDER BY date DESC";

            $sql .= $limit > 0 ? " LIMIT 0, $limit" : '';

            $result = $this->db->query($sql);
            while ($row = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new Dtransport_Log();
                    $tmp->assignVars($row);
                } else {
                    $tmp = $row['id_log'];
                }
                $this->_logs[] = $tmp;
            }
        }

        return $this->_logs;

    }

    /**
     * @desc Obtiene las caracteristicas del elemento
     * @param bool True devuelve objetos {@link STScreenshot}
     * @return array
     */
    public function screens($asobj = false)
    {

        if (empty($this->_screens) || ($asobj && !is_a($this->_screens[0], 'Dtransport_Screenshot'))) {
            $this->_screens = array();
            $sql = "SELECT * FROM " . $this->db->prefix("mod_dtransport_screens") . " WHERE id_soft='" . $this->id() . "' ORDER BY date DESC";
            $result = $this->db->query($sql);
            while ($row = $this->db->fetchArray($result)) {
                if ($asobj) {
                    $tmp = new Dtransport_Screenshot();
                    $tmp->assignVars($row);
                } else {
                    $tmp = $row['id_screen'];
                }
                $this->_screens[] = $tmp;
            }
        }

        return $this->_screens;

    }

    /**
     * @desc Determina el número de descargas realizadas para un archivo
     */
    public function downloadsCount($id_or_ip = '')
    {

        global $xoopsUser;
        $id = $id_or_ip;

        if ($id == '')
            $id = $xoopsUser ? $xoopsUser->uid() : $_SERVER['REMOTE_ADDR'];

        if (is_int($id))
            $sql = "SELECT COUNT(*) FROM " . $this->db->prefix("dtrans_downs") . " WHERE uid='" . $id . "' AND id_soft='" . $this->id() . "'";
        else
            $sql = "SELECT COUNT(*) FROM " . $this->db->prefix("dtrans_downs") . " WHERE ip='" . $id . "' AND id_soft='" . $this->id() . "'";

        $result = $this->db->query($sql);
        list($num) = $this->db->fetchRow($result);
        return $num;

    }


    /**
     * @desc Determina si usuario tiene permiso para enviar descargas
     * @param int array $gid  Ids de grupos a que pertenece usuario
     * @param int array $groups Ids de grupos con permiso a de enviar descargas
     **/
    public function isAllowedDowns($gid, $groups)
    {
        if (!is_array($gid)) {
            if ($gid == XOOPS_GROUP_ADMIN) return true;
            return in_array($gid, $groups);
        }

        if (in_array(XOOPS_GROUP_ADMIN, $gid)) return true;

        foreach ($gid as $k) {

            if (in_array($k, $groups)) return true;
        }

        return false;

    }

    /**
     * Get the permalink for item
     * @param int Determines if returned link will be formated to edition
     * @param string Indicate the type of permalink that will be returned (empty, download, screens, features, logs)
     * @return string
     */
    public function permalink($edit = 0, $type = '')
    {

        $dtSettings = RMSettings::module_settings('dtransport');
        $allowed = array('download', 'screens', 'features', 'logs');

        if ($dtSettings->permalinks) {

            if ($edit)
                $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/') . '/<span><em>' . $this->getVar('nameid') . '</em></span>/';
            elseif ($type == '' || in_array($type, $allowed))
                $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/') . '/' . $this->getVar('nameid') . '/' . ($type != '' ? $type . '/' : '');
            else
                $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/') . '/' . $this->getVar('nameid') . '/';

        } else {

            if ($type == '' || in_array($type, $allowed))
                $link = XOOPS_URL . '/modules/dtransport/?s=item&amp;id=' . $this->id() . ($type != '' ? '&amp;action=' . $type : '');
            else
                $link = XOOPS_URL . '/modules/dtransport/?s=download&amp;id=' . $this->id();
        }

        return $link;

    }

    /**
     * Get vote for current user
     */
    public function getUserVote()
    {
        global $xoopsUser;

        $uid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $sql = "SELECT * FROM " . $this->db->prefix("mod_dtransport_votedata") . " WHERE ";
        if ($uid > 0)
            $sql .= " `uid`=$uid";
        else
            $sql .= " `ip`='$ip' AND uid=0";

        $sql .= ' AND `id_soft`=' . $this->id();

        $result = $this->db->query($sql);
        if ($this->db->getRowsNum($result) <= 0)
            return false;

        $row = $this->db->fetchArray($result);

        return $row['rate'];

    }

    /**
     * @desc Almacena las etiquetas del elemento
     */
    private function saveTags()
    {
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_itemtag') . " WHERE id_soft=" . $this->id();
        $this->db->queryF($sql);

        $tc = TextCleaner::getInstance();
        $tags = array();

        foreach ($this->_tags as $tag) {
            if (is_array($tag))
                $tag = $tag['tag'];
            $ot = new DTTag($tc->sweetstring($tag));
            if ($ot->isNew()) {
                $ot->setVar('tag', $tag);
                $ot->setVar('tagid', $tc->sweetstring($tag));
                $ot->save();
            }
            $tags[] = $ot->id();
        }

        if (empty($tags)) return;

        $sql = "INSERT INTO " . $this->db->prefix('mod_dtransport_itemtag') . " (`id_soft`,`id_tag`) VALUES ";

        $sql1 = '';
        foreach ($tags as $k) {
            $sql1 .= $sql1 == "" ? "('" . $this->id() . "','$k')" : ",('" . $this->id() . "','$k')";
        }

        if ($this->db->queryF($sql . $sql1)) {
            return true;
        }

        $this->addError($this->db->error());
        return false;

    }

    /**
     * @desc Almacena la alerta del elemento si existe
     */
    private function saveAlert()
    {

        if (!$this->alert) return;

        if (!$this->alert->save()) {
            $this->addError($this->alert->errors());
            return false;
        }

        return true;

    }

    /**
     * @desc Almacena las licencias del elemento
     */
    private function saveLics()
    {
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_licsoft') . " WHERE id_soft=" . $this->id();
        $this->db->queryF($sql);

        if (empty($this->_licences))
            return true;

        $sql = "INSERT INTO " . $this->db->prefix('mod_dtransport_licsoft') . " (`id_soft`,`id_lic`) VALUES ";
        $sql1 = '';
        foreach ($this->_licences as $k) {
            $sql1 .= $sql1 == "" ? "('" . $this->id() . "','$k')" : ",('" . $this->id() . "','$k')";
        }

        if (!$this->db->queryF($sql . $sql1)) {
            $this->addError($this->db->error());
            return false;
        }

        return true;

    }

    /**
     * @desc Almacena las plataformas del elemento
     */
    private function savePlatforms()
    {

        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_platsoft') . " WHERE id_soft=" . $this->id();
        $this->db->queryF($sql);

        if (empty($this->_platforms)) return true;

        $sql = "INSERT INTO " . $this->db->prefix('mod_dtransport_platsoft') . " (`id_soft`,`id_platform`) VALUES ";
        $sql1 = '';
        foreach ($this->_platforms as $k) {
            $sql1 .= $sql1 == "" ? "('" . $this->id() . "','$k')" : ",('" . $this->id() . "','$k')";
        }

        if (!$this->db->queryF($sql . $sql1)) {
            $this->addError($this->db->error());
            return false;
        }

        return true;
    }

    /**
     * Save categories
     */
    public function saveCategories()
    {

        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_catitem') . " WHERE soft=" . $this->id();
        $this->db->queryF($sql);

        if (empty($this->_categories)) return true;

        $sql = "INSERT INTO " . $this->db->prefix('mod_dtransport_catitem') . " (`cat`,`soft`) VALUES ";
        $sql1 = '';
        foreach ($this->_categories as $k) {
            $sql1 .= $sql1 == "" ? "('$k','" . $this->id() . "')" : ",('$k','" . $this->id() . "')";
        }

        if (!$this->db->queryF($sql . $sql1)) {
            $this->addError($this->db->error());
            return false;
        }

        return true;

    }


    /**
     * @desc Almacena los datos del elemento
     */
    public function save()
    {

        $ret = false;

        if ($this->isNew()) {
            $ret = $this->saveToTable();
        } else {
            $ret = $this->updateTable();
        }

        if (!$ret) return false;


        // Etiquetas
        $this->saveTags();
        // ALerta
        if ($this->alert) {
            $this->alert->setSoftware($this->id());
            if (!$this->saveAlert()) {
                $this->addError($this->errors());
            }
        }
        // Licnecias
        $this->saveLics();
        // Plataformas
        $this->savePlatforms();
        // Categories
        $this->saveCategories();

        if ($this->errors() != '') return false;
        return true;

    }

    public function delete()
    {

        //Eliminamos las relaciones con etiquetas
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_itemtag') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        //Eliminar caracteristicas
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_features') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        //Eliminar pantallas
        $sql = "SELECT * FROM " . $this->db->prefix('mod_dtransport_screens') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);
        while ($rows = $this->db->fetchArray($result)) {
            $sc = new Dtransport_Screenshot();
            $sc->assignVars($rows);
            $sc->delete();
        }

        //Eliminar Archivos
        $sql = "SELECT * FROM " . $this->db->prefix('mod_dtransport_files') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);
        while ($rows = $this->db->fetchArray($result)) {
            $file = new Dtransport_File();
            $file->assignVars($rows);
            $file->delete($this);
        }

        //Eliminar Logs
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_logs') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        //Eliminar grupo
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_groups') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        //Eliminar relación de licencias
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_licsoft') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        //Eliminar relación de plataformas
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_platsoft') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        //Eliminar alerta
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_alerts') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        // Delete edited elementes
        $sql = "DELETE FROM " . $this->db->prefix('mod_dtransport_edited') . " WHERE id_soft=" . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

        return $this->deleteFromTable();
    }


}
