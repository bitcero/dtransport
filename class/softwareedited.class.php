<?php
// $Id: dtsoftwareedited.class.php 189 2013-01-06 08:45:34Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage download files in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Dtransport_SoftwareEdited extends RMObject
{

    public $data = [];

    /**
     * Dtransport_SoftwareEdited constructor.
     * Filter can be empty or 'item'. When filter is equal to item then
     * the object will construct from id software, if not, item will charge
     * from id_item field.
     *
     * @param null $id
     * @param string $filter
     */
    function __construct($id = null, $filter = '')
    {

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->_dbtable = $this->db->prefix("mod_dtransport_edited");

        $this->setNew();
        $this->initVarsFromTable(false);

        $this->setVarType('fields', XOBJ_DTYPE_ARRAY);

        if ($id == null) return;

        if ('item' == $filter) {
            $this->primary = 'id_soft';
        }

        if ($this->loadValues($id)) {
            $this->unsetNew();
            $this->data = $this->fields;

        }
        $this->primary = "id_item";

    }

    /**
     * Set a new index (field) for edited data
     * @param $name
     * @param $value
     */
    public function set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Get field value from data
     * @param $name
     * @return array|bool|mixed|null
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function tags()
    {
        if (!array_key_exists('tags', $this->data) || empty($this->data['tags'])) {
            return [];
        } else {
            return $this->data['tags'];
        }
    }

    public function tagsData()
    {
        $sql = "SELECT * FROM " . $this->db->prefix('mod_dtransport_tags') . " WHERE tag IN ('" . implode("','",$this->data['tags']) . "')";
        $result = $this->db->query($sql);
        $ret = [];

        while($row = $this->db->fetchArray($result)){
            $ret[$row['id_tag']] = $row['tag'];
        }

        return $ret;
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
                $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/') . '/<span><em>' . $this->get('nameid') . '</em></span>/';
            elseif ($type == '' || in_array($type, $allowed))
                $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/') . '/' . $this->get('nameid') . '/' . ($type != '' ? $type . '/' : '');
            else
                $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/') . '/' . $this->get('nameid') . '/';

        } else {

            if ($type == '' || in_array($type, $allowed))
                $link = XOOPS_URL . '/modules/dtransport/?s=item&amp;id=' . $this->id_soft . ($type != '' ? '&amp;action=' . $type : '');
            else
                $link = XOOPS_URL . '/modules/dtransport/?s=download&amp;id=' . $this->id_soft;
        }

        return $link;

    }

    /**
     * Sets the $data values from existing download item
     * @param Dtransport_Software $item
     * @return bool
     */
    public function setFromItem(Dtransport_Software $item)
    {
        if($item->isNew()){
            return false;
        }

        $this->data = $item->getVars(true);

        $this->set('categories', $item->categories());
        $this->set('licenses', $item->licences());
        $this->set('platforms', $item->platforms());
    }

    public function save()
    {
        $this->fields = $this->data;
        return parent::save();
    }
}
