<?php
/**
 * D-Transport for Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.eduardocortes.mx
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
 * @url          http://www.eduardocortes.mx
 */

/**
 * @desc Funciones utilizadas en el módulo
 */
class Dtransport_Functions
{
    public static $additionalFields = [];
    private $langStrings = [];

    static function moduleURL()
    {
        global $common;

        $dtSettings = $common->settings()->module_settings('dtransport');

        if ($dtSettings->permalinks) {

            $link = XOOPS_URL . '/' . trim($dtSettings->htbase, '/');

        } else {

            $link = XOOPS_URL . '/modules/dtransport';

        }

        return $link;

    }

    static function formAddtionalFields($location)
    {
        $validLocations = ['general', 'categories', 'groups', ' custom-fields', 'information', 'image', 'files'];

        // Check that the location for field is valid
        if (false == in_array($location, $validLocations)) {
            return false;
        }

        $fields = self::$additionalFields;

        // Verify that provided field is valid
        if (isset($fields[$location]) && is_array($fields[$location]) && !empty($fields[$location])) {
            echo implode("\n", $fields[$location]);
        }

    }

    static function itemsToolbar($item)
    {
        global $common;

        $common->template()->add_tool([
            'title' => __('List', 'dtransport'),
            'link' => 'items.php',
            'icon' => 'svg-rmcommon-menu text-pink',
            'location' => 'items'
        ]);

        $common->template()->add_tool([
            'title' => __('Images', 'dtransport'),
            'link' => 'screens.php?item=' . $item->id(),
            'icon' => 'svg-rmcommon-camera text-blue',
            'location' => 'screens'
        ]);

        $common->template()->add_tool([
            'title' => __('Features', 'dtransport'),
            'link' => 'features.php?item=' . $item->id(),
            'icon' => 'svg-rmcommon-gear text-orange',
            'location' => 'features'
        ]);

        $common->template()->add_tool([
            'title' => __('Files', 'dtransport'),
            'link' => 'files.php?item=' . $item->id(),
            'icon' => 'icon icon-file-zip text-green',
            'location' => 'files'
        ]);

        $common->template()->add_tool([
            'title' => __('Logs', 'dtransport'),
            'link' => 'logs.php?item=' . $item->id(),
            'icon' => 'svg-rmcommon-calendar-2 text-purple',
            'location' => 'logs'
        ]);

        $common->template()->add_tool([
            'title' => __('Stats', 'dtransport'),
            'link' => 'statistics.php?item=' . $item->id(),
            'icon' => 'svg-rmcommon-bars-chart text-blue-grey',
            'location' => 'statistics'
        ]);
    }

    /**
     * @desc Comprueba si el usuario actual tiene permisos de envio
     * @return bool
     */
    public function canSubmit()
    {
        global $xoopsUser, $dtSettings;

        if (!$dtSettings->send_download) return false;

        if (in_array(0, $dtSettings->groups_send)) return true;

        if (!$xoopsUser) return false;

        if ($xoopsUser->isAdmin()) return true;

        foreach ($xoopsUser->getGroups() as $k) {
            if (in_array($k, $dtSettings->groups_send)) return true;
        }

    }

    /**
     * @desc Calcula el rating en base a los votos y el rating de la tabla
     * @param int Cantidad de Votos
     * @param int Rating Total
     * @return string
     */
    public function createRatingGraph($votes, $rating)
    {

        if ($votes <= 0 || $rating <= 0) {
            $rate = 0;
        } else {
            $rate = (($rating / $votes) * 6);
        }

        $rtn = Dtransport_Functions::ratingGrpahics($rate);

        return $rtn;

    }

    /**
     * @desc Genera el div con la imágen de la calificación
     * @return string
     */
    static function localRating($rate)
    {
        global $xoopsConfig, $rmTpl, $common;

        $set = $common->settings()->module_settings('dtransport', 'tplset');

        ob_start();
        include $rmTpl->get_template('sets/' . $set . '/dt-site-rate.php', 'module', 'dtransport');
        $rtn = ob_get_clean();
        return $rtn;
    }

    /**
     * Genera el gráfico con la calificación por parte de usuarios
     */
    public function usersRating($votes, $rate)
    {
        global $rmTpl;

        $dtSettings = RMSettings::module_settings('dtransport');

        if ($votes <= 0 || $rate <= 0)
            $rating = 0;
        else
            $rating = round($rate / $votes, 1);

        $max = $dtSettings->max_rating;
        $steps = $dtSettings->usedec ? 0.1 : 1;
        $mult = 100 / $max;

        ob_start();
        include $rmTpl->get_template('sets/' . $dtSettings->tplset . '/dt-users-rating.php', 'module', 'dtransport');
        $rtn = ob_get_clean();
        return $rtn;

    }

    /**
     * @desc Genera el arreglo con las categorías existentes en la base de datos
     * @param array Referencia al array a rellenar
     * @param int Espacios en el árbol
     * @param int Identificar de la Categoría padre
     * @param int , array Identificador de la Categoría a ignorar (Junto con sus subcategoría)
     * @param bool True devuelve objetos {@link Dtransport_Category}
     * @param int Especifica si se buscaran catagorias inactivas(0), activas(1), todas(2)
     * @return array
     */
    static public function getCategories(&$categos, $jumps = 0, $parent = 0, $exclude = array(), $asobj = false, $active = 2)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        if (!is_array($exclude)) $exclude = array($exclude);

        $result = $db->query("SELECT * FROM " . $db->prefix("mod_dtransport_categories") . " WHERE parent='$parent' " . ($active > 0 ? ($active == 2 ? "" : " AND active=1") : " AND active=0 ") . " ORDER BY `id_cat`");


        while ($row = $db->fetchArray($result)) {

            if (is_array($exclude) && (in_array($row['parent'], $exclude) || in_array($row['id_cat'], $exclude))) {
                $exclude[] = $row['id_cat'];
            } else {
                $cat = new Dtransport_Category();
                $cat->assignVars($row);
                $rtn = array();
                if ($asobj) {
                    $rtn['object'] = $cat;
                    $rtn['jumps'] = $jumps;
                } else {
                    $rtn = $row;
                    $rtn['jumps'] = $jumps;
                    $rtn['link'] = $cat->permalink();
                }
                $categos[] = $rtn;
            }
            Dtransport_Functions::getCategories($categos, $jumps + 1, $row['id_cat'], $exclude, $asobj, $active);
        }

        return true;
    }

    /**
     * Get IDs from a category tree
     * @param array Array where ids will be stored
     * @param int Id of category where the search will start
     * @param bool Only select categories active or inactive
     */
    public function categoryTreeId(&$ids, $parent = 0, $active = true)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $result = $db->query("SELECT id_cat FROM " . $db->prefix("mod_dtransport_categories") . " WHERE parent='$parent' " . ($active ? ($active == 2 ? "" : " AND active=1") : " AND active=0 ") . " ORDER BY `id_cat`");

        if ($parent > 0) $ids[] = $parent;
        while (list($idcat) = $db->fetchRow($result)) {
            //$ids[] = $idcat;
            $this->categoryTreeId($ids, $idcat, $active);
        }

        return true;
    }

    /**
     * Get the path for a category
     */
    public function category_path($id)
    {

        if ($id <= 0)
            return false;

        $cat = new Dtransport_Category($id);

        $path[] = $cat->nameId();

        if ($cat->parent() <= 0) {
            return $path;
        }

        $path = array_merge($path, $this->category_path($cat->parent()));

        return $path;

    }

    /**
     * @desc Genera un array con los datos de un elemento específico
     * @param object {@link Dtransport_Software()}
     * @return array
     */
    public function createItemData(Dtransport_Software &$item)
    {
        global $dtSettings, $xoopsUser, $common;

        if (!$dtSettings)
            $dtSettings = RMSettings::module_settings('dtransport');
        $rmfunc = RMFunctions::get();

        $data = array();
        $data['link'] = $item->permalink();        // Vinculo para detalles
        $data['dlink'] = $item->permalink(0, 'download');    // Vinculo de descarga
        $data['id'] = $item->id();
        $data['name'] = $item->getVar('name');
        $data['version'] = $item->getVar('version');
        $data['description'] = $item->getVar('shortdesc');
        $data['votes'] = $item->getVar('votes');
        $data['comments'] = $item->getVar('comments');
        $data['siterate'] = Dtransport_Functions::localRating($item->getVar('siterate'));
        $data['rating'] = self::usersRating($item->getVar('votes'), $item->getVar('rating'));
        $data['language'] = $item->getVar('langs');
        // Image
        $img = new RMImage();
        $data['image'] = $img->load_from_params($item->getVar('image'));

        $data['logo'] = $item->logo;

        $data['created'] = formatTimestamp($item->getVar('created'), 's');
        $data['creation'] = $item->getVar('created');
        if ($item->getVar('created') < $item->getVar('modified')) {
            $data['modified'] = formatTimestamp($item->getVar('modified'), 's');
        }
        $data['is_new'] = $item->getVar('created') > (time() - ($dtSettings->new * 86400));
        $data['is_updated'] = $data['is_new'] ? false : $item->getVar('modified') > (time() - ($dtSettings->update * 86400));
        $data['approved'] = $item->getVar('approved');
        $data['downs'] = $item->getVar('hits');
        $data['screens'] = $item->getVar('screens');
        $data['featured'] = $item->getVar('featured');
        $data['nameid'] = $item->getVar('nameid');
        $data['candownload'] = $item->canDownload($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS);

        // Licencias
        $data['lics'] = '';
        foreach ($item->licences(true) as $lic) {
            $data['lics'] .= $data['lics'] == '' ? '<a href="' . $lic->link() . '" target="_blank">' . $lic->name() . '</a>' : ', <a href="' . $lic->link() . '" target="_blank">' . $lic->name() . '</a>';
        }

        //  Plataformas
        $data['os'] = '';
        foreach ($item->platforms(true) as $os) {
            $data['os'] .= $data['os'] == '' ? $os->name() : ', ' . $os->name();
        }

        $data['metas'] = $this->get_metas('down', $item->id());

        if($item->rating > 0 && $item->votes > 0){
            $data['usersRate'] = number_format($item->rating / $item->votes, 1);
            $data['percent'] = number_format($item->rating / $item->votes, 0) * 10;
        } else {
            $data['usersRate'] = 0;
            $data['percent'] = 0;
        }

        $data['langVotes'] = sprintf(__('%s votes', 'dtransport'), $common->format()->quantity($item->votes));
        $data['langDownloads'] = sprintf(__('%s downloads', 'dtransport'), $common->format()->quantity($item->hits));

        return $data;

    }

    /**
     * @desc Genera la barra de navegación para el listado de descargas
     */
    public function createNavigation($total, $xpage, $pactual)
    {
        global $tpl;

        if ($total <= $xpage) {
            return;
        }
        $tpages = ceil($total / $xpage);

        if ($tpages <= 1) return;

        $prev = $pactual - 1;

        if ($pactual > 1) {
            if ($pactual > 4 && $tpages > 5) {
                /**
                 * Si la página actual es mayor que 2 y el numero total de
                 * página es mayor que once entonces podemos mostrar la imágen
                 * "Primer Página" de lo contario no tiene caso tener este botón
                 */
                $tpl->append('dtNavPages', array('id' => 'first', 'num' => 1));
            }
            /**
             * Si la página actual es mayor que uno entonces mostramos
             * la imágen "Página Anterior"
             */
            $tpl->append('dtNavPages', array('id' => 'previous', 'num' => ($pactual - 1)));
        }

        // Identificamos la primer página y la última página
        $pstart = $pactual - 4 > 0 ? $pactual - 4 + 1 : 1;
        $pend = ($pstart + 6) <= $tpages ? ($pstart + 6) : $tpages;

        if ($pstart > 3 && $tpages > 4 + 1 + 3) {
            $tpl->append('dtNavPages', array('id' => 3, 'salto' => 1, 'num' => 3));
        }

        if ($tpages > 0) {
            for ($i = $pstart; $i <= $pend; $i++) {
                $tpl->append('dtNavPages', array('id' => $i, 'num' => $i));
            }
        }

        if ($pend < $tpages - 3 && $tpages > 11) {
            $tpl->append('dtNavPages', array('id' => $tpages - 3, 'salto' => 2, 'num' => ($tpages - 3)));
        }

        if ($pactual < $tpages && $tpages > 1) {
            $tpl->append('dtNavPages', array('id' => 'next', 'num' => ($pactual + 1)));
            if ($pactual < $tpages - 1 && $tpages > 11) {
                $tpl->append('dtNavPages', array('id' => 'last', 'num' => $tpages));
            }
        }

        $tpl->assign('dtTotalPages', $tpages);

    }

    /**
     * @desc Determina el tiempo transcurrido del envio de una alerta al tiempo actual
     **/
    function checkAlert()
    {

        global $xoopsModuleConfig, $db, $xoopsConfig;

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $file = XOOPS_ROOT_PATH . "/cache/alerts.php";

        $datelast = file_get_contents($file);

        if ($datelast <= time() - $xoopsModuleConfig['hrs_alerts'] * 86400) {
            //Ejecutamos verificación de alertas
            $sql = "SELECT * FROM " . $db->prefix('dtrans_alerts');
            $result = $db->query($sql);
            while ($rows = $db->fetchArray($result)) {
                $alert = new Dtransport_Alert();
                $alert->assignVars($rows);

                //Obtenemos los datos de la descarga
                $sw = new Dtransport_Software($alert->software());

                if (!$sw->getVar('approved')) continue;

                if (!$alert->lastActivity()) {
                    if ($sw->getVar('created') >= time() - $alert->limit() * 86400) {
                        continue;
                    }
                }


                //Verificamos la fecha de la última descarga del modulo
                if ($alert->lastActivity() <= time() - $alert->limit() * 86400) {

                    if ($alert->alerted() && ($alert->alerted() < $alert->lastActivity() + $alert->limit() * 86400)) {
                        continue;
                    }

                    $errors = '';
                    //Enviamos alerta al autor de la descarga
                    $xoopsMailer =& getMailer();
                    $alert->mode() ? $xoopsMailer->useMail() : $xoopsMailer->usePM();
                    $xoopsMailer->setTemplate('alert.tpl');
                    $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                    $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                    $xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
                    $xoopsMailer->assign('DOWNLOAD', $sw->name());
                    if ($xoopsModuleConfig['urlmode']) {
                        $url = DT_URL . "/item/" . $sw->nameId();

                    } else {
                        $url = DT_URL . "/item.php?id=" . $sw->id();
                    }

                    $xoopsMailer->assign('LINK_RESOURCE', $url);
                    $xoopsMailer->assign('DAYS', $alert->limit());
                    $xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH . "/modules/dtransport/language/" . $xoopsConfig['language'] . "/mail_template/");
                    $xu = new XoopsUser($sw->uid());
                    $xoopsMailer->setToUsers($xu);
                    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                    $xoopsMailer->setFromName($xoopsConfig['sitename']);
                    $xoopsMailer->setSubject(sprintf(_MS_DT_SUBJECTALERT, $sw->name()));
                    $xoopsMailer->send(true);

                }

            }
            //Almacenamos la fecha de la última verificación de alertas
            file_put_contents($file, time());

        } else {
            return false;
        }


    }

    /**
     * Send a message in json format
     * @param string Message to be sent
     * @param int Indicates if message is an error
     * @param int Indicates if token must be sent
     */
    public function dt_send_message($message, $e = 0, $t = 1)
    {
        global $xoopsSecurity;

        if ($e) {
            $data = array(
                'message' => $message,
                'error' => 1,
                'token' => $t ? $xoopsSecurity->createToken() : ''
            );
        } else {

            $data = array(
                'error' => 0,
                'token' => $t ? $xoopsSecurity->createToken() : '',
            );
            $data = array_merge($data, $message);
        }

        echo json_encode($data);
        die();

    }

    /**
     * Obtiene los campos personalizados de un elemento
     */
    public function get_metas($type, $id, $all = false)
    {
        // Get existing metas
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT * FROM " . $db->prefix("mod_dtransport_meta") . " WHERE type='$type' AND id_element='$id'";
        $result = $db->query($sql);

        while ($row = $db->fetchArray($result)) {
            if ($all)
                $metas[] = $row;
            else
                $metas[$row['name']] = $row['value'];
        }

        return !isset($metas) ? false : $metas;
    }

    /**
     * Show the meta values for a specific element
     * @param $type
     * @param int $id
     * @param RMForm|null $form
     * @return array|bool|string
     */
    static function meta_form($type, $id = 0, RMForm &$form = null)
    {

        $tpl = RMTemplate::get();

        // Get existing metas
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT `name` FROM " . $db->prefix("mod_dtransport_meta") . " WHERE type='$type'";
        $result = $db->query($sql);

        $metaNames = array();
        $metas = array();

        while ($row = $db->fetchArray($result)) {
            if (!in_array($row['name'], $metaNames))
                $metaNames[] = $row['name'];
        }

        if ($id > 0) {
            $sql = "SELECT * FROM " . $db->prefix("mod_dtransport_meta") . " WHERE type='$type' AND id_element='$id'";
            $result = $db->query($sql);

            while ($row = $db->fetchArray($result)) {
                $metas[] = $row;
            }
        }

        $tpl->add_style('metas.css', 'dtransport');
        $tpl->add_script('metas.min.js', 'dtransport');
        include_once DT_PATH . '/include/js-strings.php';

        ob_start();
        include $tpl->get_template("admin/dtrans-metas.php", 'module', 'dtransport');
        $metas = ob_get_clean();

        if ($form) {

            $form->addElement(new RMFormLabel(__('Custom Fields', 'dtransport'), $metas))->setDescription(__('Custom fields allows you to add extra information to elements, that can be used on templates, plugins or another elements.', 'dtransport'));
            return true;

        }

        return $metas;

    }

    /**
     * Save meta data for elements
     */
    public function save_meta($type, $id)
    {

        $metas = rmc_server_var($_REQUEST, 'dtMetas', array());

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $db->queryF("DELETE FROM " . $db->prefix("mod_dtransport_meta") . " WHERE `type`='$type' AND id_element=$id");

        if (empty($metas)) return true;

        $sql = "INSERT INTO " . $db->prefix("mod_dtransport_meta") . " (`type`,`id_element`,`name`,`value`) VALUES ";
        foreach ($metas as $meta) {
            $sql .= "('$type','$id','$meta[name]','$meta[value]'),";
        }

        $sql = rtrim($sql, ',');

        return $db->queryF($sql);

    }

    /**
     * Get featured items
     * @param int Allows to select items from a specific category
     * @param string Smarty varibale name. Useful when assign is true
     * @param string Type of items (all, featured, recent, daily, rated, updated
     * @return array
     */
    public function get_items($cat = 0, $type = 'all', $limit = 10)
    {
        global $xoopsTpl, $dtSettings;

        $items = array();
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        switch ($type) {
            case 'featured':
                $filter = "items.approved=1 AND items.featured=1 AND items.deletion=0";
                $order = "ORDER BY RAND()";
                break;
            case 'recent':
                $filter = "items.approved=1 AND items.deletion=0";
                $order = "ORDER BY items.created DESC";
                break;
            case 'daily':
                $filter = "items.approved=1 AND items.daily=1 AND items.deletion=0";
                $order = "ORDER BY RAND()";
                break;
            case 'rated':
                $filter = "items.approved=1 AND items.deletion=0";
                $order = "ORDER BY items.rating DESC";
                break;
            case 'updated':
                $filter = "items.approved=1 AND items.deletion=0";
                $order = "ORDER BY items.modified DESC";
                break;
            default:
                $filter = 'items.deletion=0';
                $order = "ORDER BY created DESC";
                break;
        }

        // Tables
        $tItems = $db->prefix("mod_dtransport_items");
        $tRels = $db->prefix("mod_dtransport_catitem");
        $tCats = $db->prefix("mod_dtransport_categories");

        if ($cat > 0) {
            // Categories under current category
            $categos = array();
            $this->categoryTreeId($categos, $cat);

            $sql = "SELECT items.*, c.`name` as cat_name, b.soft AS namecat, c.id_cat as cat_id
                    FROM " . $db->prefix("mod_dtransport_items") . " AS items, " . $db->prefix("mod_dtransport_catitem") . " AS b,
                    " . $db->prefix("mod_dtransport_categories") . " AS c
                    WHERE b.cat IN(" . implode(",", $categos) . ") AND items.id_soft=b.soft AND $filter AND c.id_cat=b.cat
                    GROUP BY b.soft
                    $order LIMIT 0,$limit";
        } else {

            $sql = "SELECT items.*, cats.name as cat_name, cats.id_cat as cat_id FROM $tItems items
                    LEFT JOIN $tRels relations ON items.id_soft = relations.soft
                    LEFT JOIN $tCats cats ON relations.cat = cats.id_cat WHERE $filter $order LIMIT 0, $limit";

            /*$sql = "SELECT a.*, c.`name`, b.soft as namecat, c.id_cat
                    FROM " . $db->prefix("mod_dtransport_items") . " a, " . $db->prefix("mod_dtransport_catitem") . " b,
                    " . $db->prefix("mod_dtransport_categories") . " c
                    WHERE $filter AND a.id_soft=b.soft AND c.id_cat=b.cat
                    GROUP BY b.soft
                    $order LIMIT 0,$limit";*/
        }

        $result = $db->query($sql);
        $items = [];
        while ($row = $db->fetchArray($result)) {

            $item = new Dtransport_Software();
            $item->assignVars($row);
            $ocat = new Dtransport_Category($row['cat_id']);

            if (array_key_exists($item->id(), $items)) {
                $items[$item->id()]['categories'][] = ['name' => $row['cat_name'], 'id' => $row['cat_id'], 'link' => $ocat->permalink()];
            } else {
                $items[$item->id()] = $this->createItemData($item);
                $items[$item->id()]['categories'][] = ['name' => $row['cat_name'], 'id' => $row['cat_id'], 'link' => $ocat->permalink()];
            }
        }

        return $items;
    }

    /**
     * Get featured items
     * @param int $category
     * @return array
     */
    public function getFeatured($category = 0)
    {
        global $common;

        $limit = $common->settings()->module_settings('dtransport', 'limit_destdown');
        $items = $this->get_items($category, 'featured', $limit);

        $featured = [];

        foreach ($items as $item) {
            if (array_key_exists($item['id'], $featured)) {
                // The item exists, then add categories
                $featured[$item['id']]['categories'][] = $item['category'];
            } else {
                // The item does not exists, create it.
                $category = $item['category'];
                unset($item['category']);
                $featured[$item['id']] = $item;
                $featured[$item['id']]['categories'][] = $category;
            }
        }

        return $featured;
    }

    /**
     * Get items by tag(s)
     */
    public function items_by($elements, $by, $exclude = 0, $type = 'all', $start = 0, $limit = 10)
    {

        if (!is_array($elements) AND $elements <= 0)
            return;

        if (!is_array($elements))
            $elements = array($elements);

        switch ($type) {
            case 'featured':
                $filter = "s.approved=1 AND s.featured=1 AND s.deletion=0";
                $order = "ORDER BY RAND()";
                break;
            case 'recent':
                $filter = "s.approved=1 AND s.deletion=0";
                $order = "ORDER BY s.created DESC";
                break;
            case 'daily':
                $filter = "s.approved=1 AND s.daily=1 AND s.deletion=0";
                $order = "ORDER BY RAND()";
                break;
            case 'rated':
                $filter = "s.approved=1 AND s.deletion=0";
                $order = "ORDER BY s.rating DESC";
                break;
            case 'updated':
                $filter = "s.approved=1 AND s.deletion=0";
                $order = "ORDER BY s.modified DESC";
                break;
            default:
                $filter = 's.approved=1 AND s.deletion=0';
                $order = "ORDER BY created DESC";
                break;
        }

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $softt = $db->prefix('mod_dtransport_items');

        switch ($by) {
            case 'tags':
                $byt = $db->prefix('mod_dtransport_itemtag');
                $sql = "SELECT s.* FROM $softt AS s, $byt AS t WHERE t.id_tag IN (" . implode(",", $elements) . ") AND t.id_soft!=$exclude AND s.id_soft=t.id_soft AND $filter GROUP BY t.id_soft $order LIMIT $start, $limit";
                break;
            case 'platforms':
                $byt = $db->prefix('mod_dtransport_platsoft');
                $sql = "SELECT s.* FROM $softt AS s, $byt AS t WHERE t.id_platform IN (" . implode(",", $elements) . ") AND t.id_soft!=$exclude AND s.id_soft=t.id_soft AND $filter GROUP BY t.id_soft $order LIMIT $start, $limit";
                break;
            case 'licenses':
                $byt = $db->prefix('mod_dtransport_licsoft');
                $sql = "SELECT s.* FROM $softt AS s, $byt AS t WHERE t.id_lic IN (" . implode(",", $elements) . ") AND t.id_soft!=$exclude AND s.id_soft=t.id_soft AND $filter GROUP BY t.id_soft $order LIMIT $start, $limit";
                break;
        }

        $result = $db->query($sql);
        $items = array();
        while ($row = $db->fetchArray($result)) {
            $item = new Dtransport_Software();
            $item->assignVars($row);
            $cats = $item->categories(true);
            $cat = $cats[array_rand($cats, 1)];
            $items[] = array_merge($this->createItemData($item), array('category' => $cat->name(), 'categoryid' => $cat->id(), 'categorylink' => $cat->permalink()));
        }

        return $items;

    }

    /**
     * Envia un encabezado statuis 404 al navegador
     */
    /**
     * Generate an 404 error
     */
    function error_404()
    {
        global $common;

        RMFunctions::error_404(__('Item not Found', 'dtransport'), 'dtransport');
    }

    /**
     * Get ip
     */
    public function ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Add a language string to temporary container.
     * Strings must be identified by 'key'. Be careful when you add new strings becouse existing ones could ben replaced.
     * @param $key
     * @param $text
     */
    public function addLangString($key, $text = '')
    {
        if (is_array($key)) {
            $this->langStrings = array_merge($this->langStrings, $key);
        } else {
            $this->langStrings[$key] = $text;
        }
    }

    /**
     * Assigns all existing language strings to a Smarty variable.
     * @param string $varName
     * @return bool
     */
    public function assignLang($varName = 'dtLang')
    {
        global $xoopsTpl;

        if ('' == $varName) {
            return false;
        }

        $xoopsTpl->assign($varName, $this->langStrings);
    }

    /**
     * Makes the general cpanel header
     */
    public function cpanelHeader()
    {
        global $xoopsOption, $common, $xoopsUser, $dtSettings;

        if (!$xoopsUser) {
            return null;
        }

        // User avatar
        if ($common->services()->service('avatar')) {
            $avatar = $common->services()->avatar->getAvatarSrc($xoopsUser, 100);
        } else {
            $avatar = HELIUM_URL . "/images/avatar.png";
        }

        // User information
        $cpanelData = [
            'uname' => $xoopsUser->getVar('uname'),
            'name' => $xoopsUser->getVar('name'),
            'url' => RMUris::relative_url(XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->getVar('uid')),
            'id' => $xoopsUser->getVar('uid'),
            'avatar' => $avatar,
            'urls' => [
                'myDownloads' => DT_URL . ($dtSettings->permalinks ? '/cp/' : '/?s=cp'),
                'pending' => DT_URL . ($dtSettings->permalinks ? '/cp/pending/' : '/?s=cp&action=pending'),
                'submit' => DT_URL . ($dtSettings->permalinks ? '/submit/' : '/?s=submit'),
            ]
        ];

        $common->xoopsTpl()->assign('cpanelData', $cpanelData);

        if ($xoopsOption['module_subpage'] == 'cp-files') {
            $common->xoopsTpl()->assign('showAdd', true);
        }

        $this->addLangString([
            'viewProfile' => __('View Profile', 'dtransport'),
            'submit' => __('Submit', 'dtransport'),
            'myDowns' => __('My Downloads', 'dtransport'),
            'waiting' => __('Pending', 'dtransport'),
            'addFile' => __('Add File', 'dtransport'),
            'addScreen' => __('Add Screenshot', 'dtransport'),
            'addFeature' => __('Add Feature', 'dtransport'),
            'addLog' => __('Add Log', 'dtransport'),
            'filesManage' => __('Files', 'dtransport'),
            'logsManage' => __('Logs', 'dtransport'),
            'featuresManage' => __('Features', 'dtransport'),
            'screensManage' => __('Screesnhots', 'dtransport'),
            'editItem' => __('Edit download item', 'dtransport'),
            'deleteItem' => __('Delete download item', 'dtransport'),
        ]);
    }

    /**
     * Creates the item cpanel toolbar
     * @param Dtransport_Software $item
     */
    public function makeItemCpanelOptions(Dtransport_Software $item)
    {
        global $common, $xoopsTpl, $xoopsOption;

        $pageName = '';
        switch ($xoopsOption['module_subpage']) {
            case 'cp-files':
                $pageName = __('Files', 'dtransport');
                break;
            case 'cp-screens':
                $pageName = __('Screenshots', 'dtransport');
                break;
            case 'cp-features':
                $pageName = __('Features', 'dtransport');
                break;
            case 'cp-logs':
                $pageName = __('Logs', 'dtransport');
                break;
        }

        $xoopsTpl->assign('itemData', [
            'name' => $item->name,
            'link' => $item->permalink(),
            'page' => $xoopsOption['module_subpage'],
            'pageName' => $pageName,
            'links' => [
                'files' => $this->getURL('cp', ['action' => 'files', 'id' => $item->id()]),
                'logs' => $this->getURL('cp', ['action' => 'logs', 'id' => $item->id()]),
                'screens' => $this->getURL('cp', ['action' => 'screens', 'id' => $item->id()]),
                'features' => $this->getURL('cp', ['action' => 'features', 'id' => $item->id()]),
                'edit' => $this->getURL('submit', ['action' => 'edit', 'id' => $item->id()]),
                'delte' => $this->getURL('cp', ['action' => 'delete', 'id' => $item->id()]),
            ]
        ]);
    }

    /**
     * Generates an internal URL according to given parameters
     * @param $section
     * @param array $params
     * @return string
     */
    public function getURL($section = '', $params = [])
    {
        $dtSettings = RMSettings::module_settings('dtransport');
        if ($dtSettings->permalinks) {

            $base = XOOPS_URL . '/' . rtrim($dtSettings->htbase, '/');

            if ('' != $section) {
                $base .= '/' . $section;
            }

        } else {

            $base = XOOPS_URL . '/modules/dtransport';

            if ('' != $section) {
                $base .= '/?s=' . $section;
            }

        }

        /*
         * If section has not been provided, return the
         * current URL as is
         */
        if ('' == $section || empty($params)) {
            return TextCleaner::getInstance()->clean_url($base);
        }

        foreach ($params as $param => $value) {

            if($param == 'action' || $param == 'id'){
                $base .= "/$value";
                continue;
            }

            if ($dtSettings->permalinks) {
                $base .= "/$param/$value/";
            } else {
                $base .= "&$param=$value";
            }

        }

        return TextCleaner::getInstance()->clean_url($base);

    }

    /**
     * Check if a nameId has been assigned
     * @param $nameId
     * @param $item
     * @return bool
     */
    static function nameIdExists($nameId, $item)
    {
        global $xoopsDB;

        $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix("mod_dtransport_items") . " WHERE nameId='$nameId' and id_soft != $item";
        list($num) = $xoopsDB->fetchRow($xoopsDB->queryF($sql));
        if ($num > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Dtransport_Functions();
        }

        return $instance;
    }

    /**
     * Sends a message to download owner with confirmation about
     * the approval or unapproval
     * @param Dtransport_Software $item
     * @param string $message
     * @return null
     */
    static function sendApprovalConfirmation(Dtransport_Software $item, string $message)
    {
        global $common, $xoopsConfig;

        if($common->settings()::module_settings('dtransport', 'active_notify') <= 0){
            return null;
        }

        if (empty($item) || $item->isNew()) {
            return false;
        }

        // Creating a message
        $mailer = new RMMailer('text/html');
        $mailer->add_xoops_users($item->uid);
        $mailer->set_subject(__('Your download has been approved', 'dtransport'));

        $tpl = DT_PATH . '/templates/mail/' . $common->settings->lang;
        if (file_exists($tpl . '/approval-status.php')) {
            $tpl .= '/approval-status.php';
        } else {
            $tpl = DT_PATH . '/templates/mail/en/approval-status.php';
        }

        $user = new RMUser($item->uid);
        $userName = '' != $user->name ? $user->name : $user->uname;

        $mailer->template($tpl);

        $mailer->assign('userName', $userName);
        $mailer->assign('download', $item->name);
        $mailer->assign('status', $item->approved ? __('Approved', 'dtransport') : __('Unapproved', 'dtransport'));
        $mailer->assign('message', $message);
        $mailer->assign('urlManage', Dtransport_Functions::getInstance()->getURL('cp'));
        $mailer->assign('urlView', $item->permalink());
        $mailer->assign('siteUrl', XOOPS_URL);
        $mailer->assign('siteName', $xoopsConfig['sitename']);

        if (!$mailer->send()) {
            $common->utilities()->showMessage(__('There was errors while sending this email', 'rmcommon') . implode('<br>', $mailer->errors()));
        }

        return null;
    }

    /**
     * Send a notification for admins
     * @param Dtransport_Software $item
     * @return bool|null
     */
    static function sendReviewRequest(Dtransport_Software $item)
    {
        global $common, $xoopsConfig;

        if($common->settings()::module_settings('dtransport', 'edit_notify') <= 0){
            return null;
        }

        if (empty($item) || $item->isNew()) {
            return false;
        }

        // Creating a message
        $mailer = new RMMailer('text/html');
        $mailer->add_xoops_users($item->uid);
        $mailer->set_subject(__('A donwload has been submited for approval', 'dtransport'));

        $tpl = DT_PATH . '/templates/mail/' . $common->settings->lang;
        if (file_exists($tpl . '/approval-request.php')) {
            $tpl .= '/approval-request.php';
        } else {
            $tpl = DT_PATH . '/templates/mail/en/approval-request.php';
        }

        $user = new RMUser($item->uid);
        $userName = '' != $user->name ? $user->name : $user->uname;

        $mailer->template($tpl);

        $mailer->assign('userName', $userName);
        $mailer->assign('userUrl', XOOPS_URL . '/userinfo.php?uid=' . $user->uid);
        $mailer->assign('reviewUrl', XOOPS_URL . '/modules/dtransport/admin/items.php?action=edit&id=' . $item->id());
        $mailer->assign('previewUrl', $item->permalink());
        $mailer->assign('screensUrl', XOOPS_URL . '/modules/dtransport/admin/screens.php?item=' . $item->id());
        $mailer->assign('featuresUrl', XOOPS_URL . '/modules/dtransport/admin/features.php?item=' . $item->id());
        $mailer->assign('deleteUrl', XOOPS_URL . '/modules/dtransport/admin/items.php?action=delete&id=' . $item->id());
        $mailer->assign('siteUrl', XOOPS_URL);
        $mailer->assign('siteName', $xoopsConfig['sitename']);

        return null;
    }

    static function getNameId($nameId, $down, $item = null){

        // Check if short name already exists
        $count = 0;
        $nameCheck = $nameId;

        do
        {
            global $xoopsDB;

            $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix("mod_dtransport_items") . " WHERE nameid='$nameCheck' " . ($down->isNew() ? '' : "AND id_soft != $down->id_soft");
            if (false == $down->isNew() && null != $item){
                $sql .= " AND id_item != " . $item->id();
            }

            list($num) = $xoopsDB->fetchRow($xoopsDB->query($sql));

            if ($num > 0) {
                $found = true;
                $count++;
                $nameCheck = $nameId . '-' . $count;
            } else {
                $found = false;
            }
        } while(true == $found);

        return $nameCheck;

    }

    static function cronJob()
    {
        global $common;

        $cron = $common->settings()::module_settings('dtransport', 'cron');

        if($cron != 1){
            return null;
        }

        $last = $common->settings()::module_settings('dtransport', 'alertChecked');
        $lapse = $common->settings()::module_settings('dtransport', 'hrs_alerts');
        $lapse = $lapse * 3600;

        if($last < (time() - $lapse)){
            $common->template()->add_inline_script('$(document).ready(function(){$.get(xoUrl + "/modules/dtransport/include/tasks.php");});', 1);
            $common->settings()->setValue('dtransport', 'alertChecked', time());
        }

        return null;
    }

}
