<?php
// $Id: submit.php 209 2013-01-29 04:03:49Z i.bitcero $
// --------------------------------------------------------------
// D-Transport
// Manage files for download in XOOPS
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$xoopsOption['noblocks'] = 1;

if (!$dtSettings->send_download) {
    $common->uris()::redirect_with_message(
        __('Operation not allowed!', 'dtransport'), DT_URL, RMMSG_WARN
    );
}

$common->privileges()->verify('dtransport', 'submit-downloads', '', true);

function collectData()
{
    global $common;

    // Collect information
    $data = [];

    $data['name'] = $common->httpRequest()->post('name', 'string', '');
    $data['nameId'] = $common->httpRequest()->post('nameid', 'string', '');
    $data['shortDescription'] = $common->httpRequest()->post('shortdesc', 'string', '');
    $data['description'] = $common->httpRequest()->post('description', 'string', '');
    $data['version'] = $common->httpRequest()->post('version', 'string', '');
    $data['downLimit'] = $common->httpRequest()->post('limits', 'string', '');
    $data['languages'] = $common->httpRequest()->post('langs', 'string', '');
    $data['password'] = $common->httpRequest()->post('password', 'string', '');
    $data['image'] = $common->httpRequest()->post('image', 'string', '');
    $data['logo'] = $common->httpRequest()->post('logo', 'string', '');
    $data['authorName'] = $common->httpRequest()->post('author', 'string', '');
    $data['authorUrl'] = $common->httpRequest()->post('url', 'string', '');
    $data['authorEmail'] = $common->httpRequest()->post('email', 'string', '');
    $data['search'] = $common->httpRequest()->post('search', 'string', '');

    $data['categories'] = $common->httpRequest()->post('catids', 'array', []);
    $data['licenses'] = $common->httpRequest()->post('lics', 'array', []);
    $data['platforms'] = $common->httpRequest()->post('platforms', 'array', []);
    $data['groups'] = $common->httpRequest()->post('groups', 'array', []);
    $data['tags'] = $common->httpRequest()->post('tags', 'array', []);

    $data['approved'] = $common->httpRequest()->post('approved', 'integer', 0);
    $data['featured'] = $common->httpRequest()->post('mark', 'integer', 0);
    $data['secure'] = $common->httpRequest()->post('secure', 'integer', 0);
    $data['notificationMode'] = $common->httpRequest()->post('mode', 'integer', 1);
    $data['authorContact'] = $common->httpRequest()->post('contact', 'integer', 1);
    $data['id'] = $common->httpRequest()->post('id', 'integer', 0);
    //$data['itemId'] = $common->httpRequest()->post('soft', 'integer', 0);
    $data['page'] = $common->httpRequest()->post('page', 'integer', 1);

    return $data;
}

function dtRenderForm(Dtransport_SoftwareEdited $sw, int $edit)
{
    global $xoopsDB, $common, $dtSettings, $page, $search;
    $form = new RMForm('', '');

    //Lista de categorías
    $tempArr = [];
    Dtransport_Functions::getCategories($tempArr, 0, 0, [], false);
    foreach ($tempArr as $category) {
        $cat = new Dtransport_Category();
        $cat->assignVars($category);
        $categories[] = array(
            'id' => $cat->id(),
            'name' => $cat->name(),
            'parent' => $cat->parent(),
            'active' => $cat->active(),
            'description' => $cat->desc(),
            'indent' => $category['jumps'],
            'selected' => in_array($cat->id(), $sw->get('categories'))
        );
    }
    unset($tempArr, $category, $cat);

    // Licencias
    $sql = "SELECT * FROM " . $xoopsDB->prefix('mod_dtransport_licences');
    $result = $xoopsDB->queryF($sql);
    $lics = array();
    $lics[] = array(
        'id' => 0,
        'name' => __('Other license', 'dtransport'),
        'selected' => in_array(0, $sw->get('licenses'))
    );
    while ($row = $xoopsDB->fetchArray($result)) {
        $lic = new Dtransport_License();
        $lic->assignVars($row);
        $lics[] = array(
            'id' => $lic->id(),
            'name' => $lic->name(),
            'selected' => in_array($lic->id(), $sw->get('licenses'))
        );
    }
    unset($lic);

    // Platforms
    // Plataformas
    $sql = "SELECT * FROM " . $xoopsDB->prefix('mod_dtransport_platforms');
    $result = $xoopsDB->queryF($sql);
    $oss = array();
    $oss[] = array(
        'id' => 0,
        'name' => __('Other platform', 'dtransport'),
        'selected' => in_array(0, $sw->get('platforms'))
    );
    while ($row = $xoopsDB->fetchArray($result)) {
        $os = new Dtransport_Platform();
        $os->assignVars($row);
        $oss[] = array(
            'id' => $os->id(),
            'name' => $os->name(),
            'selected' => in_array($os->id(), $sw->get('platforms'))
        );
    }
    unset($os);

    // Allowed groups
    $groupsAllowed = $common->privileges()->verify('dtransport', 'assign-groups', '', false);

    if ($groupsAllowed) {
        $field = new RMFormGroups('', 'groups', 1, 1, 1, array(1, 2));
        $groups = $field->render();
    }

    // Tags
    $ftags = $sw->get('tags');
    $ftags = empty($ftags) ? [] : $ftags;
    $tags = [];
    foreach ($ftags as $tag) {
        $tags[] = $tag;
    }
    unset($ftags);

    // Approved
    $field = new RMFormYesNo('', 'approved', 1);
    $approved = $field->render();

    // Featured download
    $field = new RMFormYesNo('', 'mark', 1);
    $featured = $field->render();

    // Descarga segura
    $field = new RMFormYesno('', 'secure', 0);
    $secure = $field->render();

    Dtransport_Functions::getInstance()->addLangString([
        'submitTitle' => __('Submit Download', 'dtransport'),
        'overallProgress' => __('Overall Progress', 'dtransport'),
        'itemName' => __('Item name', 'dtransport'),
        'descriptiveName' => __('Name of download item...', 'dtransport'),
        'itemShort' => __('Short description', 'dtransport'),
        'itemDescription' => __('Full description', 'dtransport'),
        'basicData' => __('Basic Item Data', 'dtransport'),
        'basic' => __('Basic', 'dtransport'),
        'details' => __('Details', 'dtransport'),
        'next' => __('Siguiente', 'dtransport'),
        'previous' => __('Anterior', 'dtransport')
    ]);

    // Breadcrumb
    $common->breadcrumb()->add_crumb(__('Submit Download Item', 'dtransport'));

    $common->template()->add_script('cu-handler.js', 'rmcommon', ['id' => 'handler-js', 'footer' => 1]);
    $common->template()->add_script('submit.min.js', 'dtransport', ['id' => 'submit-js', 'footer' => 1]);

    // Form action URL
    $formAction = Dtransport_Functions::getInstance()->getURL('submit');

    // Verify if user can publish
    if ($edit) {
        $canPublish = $common->privileges()::verify('dtransport', 'approve-editions');
    } else {
        $canPublish = $common->privileges()::verify('dtransport', 'approve-items');
    }

    $common->template()->assign([
        'sw' => $sw,
        'edit' => $edit,
        'categories' => $categories,
        'lics' => $lics,
        'oss' => $oss,
        'tags' => $tags,
        'groups' => $groupsAllowed ? $groups : [],
        'id' => $sw->id(),
        'id_sw' => $sw->id_soft,
        'page' => $page,
        'search' => $search,
        'approved' => $approved,
        'featured' => $featured,
        'secure' => $secure,
        'formAction' => $formAction,
        'canPublish' => $canPublish
    ]);

    $common->template()->assign('ed', new RMFormEditor([
        'caption' => '',
        'name' => 'description',
        'id' => 'item-description',
        'height' => '350px',
        'value' => $sw->get('description')
    ]));

    /**
     * Additional fields for items
     * Third elements can intercept this event and return new fields
     */
    Dtransport_Functions::$additionalFields = $common->events()->trigger('dtransport.frontend.item.form.fields', Dtransport_Functions::$additionalFields, $sw);
}

/**
 * Check if data is correct
 * @param array $data
 * @return string
 */
function dtCheckRequired(array $data)
{
    global $common;

    $query = '';

    foreach ($data as $key => $value) {
        $query .= '' == $query ? '?' : '&';
        $query .= $key . '=' . urlencode($value);
    }

    if ('' == $data['name'] || '' == $data['shortDescription'] || '' == $data['description'] || '' == $data['version']) {
        $common->uris()::redirect_with_message(
            __('You must provide all required data before to create a new download!', 'dtransport'),
            Dtransport_Functions::getInstance()->getURL('submit') . $query, RMMSG_ERROR
        );
    }

    if (empty($data['categories']) || empty($data['licenses']) || empty($data['platforms']) || empty($data['groups'])) {
        $common->uris()::redirect_with_message(
            __('You must provide all required data before to create a new download!', 'dtransport'),
            Dtransport_Functions::getInstance()->getURL('submit') . $query, RMMSG_ERROR
        );
    }

    return $query;

}

switch ($action) {
    case 'saveedit':
    case 'save':
    case 'verify':

        $data = collectData();
        $query = dtCheckRequired($data);

        extract($data);

        if ($nameId == '') {
            $nameId = TextCleaner::getInstance()::sweetstring($name);
        }

        /**
         * Load current download item (if exists)
         */
        $down = new Dtransport_Software($id);

        if ($down->isNew()) {

            /**
             * When download item does not exists we will create a new one
             */
            $down->setVar('name', $name);
            $down->setVar('nameid', Dtransport_Functions::getNameId($nameId, $down));
            $down->setVar('version', $version);
            $down->setVar('shortdesc', $shortDescription);
            $down->setVar('description', $description);
            $down->setVar('image', $image);
            $down->setVar('logo', $logo);

            if ($common->privileges()::verify('dtransport', 'limit-downloads', '', false)) {
                $down->setVar('limits', $downLimit);
            } else {
                $down->setVar('limits', $dtSettings->limits);
            }

            $down->setVar('created', time());
            $down->setVar('modified', time());
            $down->setVar('uid', $xoopsUser->uid());

            if ($common->privileges()::verify('dtransport', 'secure-items', '', false)) {
                $down->setVar('password', $password);
                $down->setVar('secure', $password != '' ? 1 : $secure);
            } else {
                $down->setVar('password', '');
                $down->setVar('secure', 0);
            }

            if ($common->privileges()->verify('dtransport', 'assign-groups', '', false)) {
                $down->setVar('groups', $groups);
            } else {
                $down->setVar('groups', $dtSettings->groups_default);
            }

            $down->setVar('approved', 0);
            $down->setVar('author_name', $authorName);
            $down->setVar('author_email', $authorEmail);
            $down->setVar('author_url', $authorUrl);
            $down->setVar('author_contact', $authorContact);
            $down->setVar('langs', $languages);

            // Categories
            $down->setCategories($categories);
            // Licences
            $down->setLicences($licenses);
            // Platforms
            $down->setPlatforms($platforms);
            // Tags
            $down->setTags($tags);
            // Alert
            $down->createAlert();
            $down->alert()->mode = 1;
            $down->alert()->lastactivity = time();
            $down->limit = $dtSettings->alert_days;

        } else {

            /**
             * Load edited item, but if this not exists then
             * we will create a new one
             */
            $item = new Dtransport_SoftwareEdited($id, 'item');

            $item->id_soft = $id;
            if ($item->isNew()) {
                $item->created = date('Y-m-d H:i:s', time());
            } else {
                $item->modified = date('Y-m-d H:i:s', time());
            }

            $item->set('name', $name);
            $item->set('nameid', Dtransport_Functions::getNameId($nameId, $down, $item));
            $item->set('version', $version);
            $item->set('shortdesc', $shortDescription);
            $item->set('description', $description);
            $item->set('image', $image);
            $item->set('logo', $logo);

            if ($common->privileges()::verify('dtransport', 'limit-downloads', '', false)) {
                $item->set('limits', $downLimit);
            } else {
                $item->set('limits', $dtSettings->limits);
            }

            //$item->set('created', time());
            //$item->set('modified', time());
            $item->set('uid', $down->uid);

            if ($common->privileges()::verify('dtransport', 'secure-items', '', false)) {
                $item->set('password', $password);
                $item->set('secure', $password != '' ? 1 : $secure);
            } else {
                $item->set('password', '');
                $item->set('secure', 0);
            }

            if ($common->privileges()->verify('dtransport', 'assign-groups', '', false)) {
                $item->set('groups', $groups);
            } else {
                $item->set('groups', $dtSettings->groups_default);
            }

            $item->set('author_name', $authorName);
            $item->set('author_email', $authorEmail);
            $item->set('author_url', $authorUrl);
            $item->set('author_contact', $authorContact);
            $item->set('langs', $languages);

            // Categories
            $item->set('categories', $categories);
            // Licences
            $item->set('licenses', $licenses);
            // Platforms
            $item->set('platforms', $platforms);
            // Tags
            $item->set('tags', $tags);


        }

        /**
         * If this item has been submited for approval, then we need
         * to mark it as "verify". In this way, administrator will know
         * that this item must be reviewed.
         */
        $down->setVar('status', 'verify' == $action ? 'verify' : 'editing' );

        // Check if exists another download with same name
        $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix("mod_dtransport_items") . " WHERE name='$name' " . ($down->isNew() ? '' : "AND id_soft != $item->id_soft");
        if (false == $down->isNew()){
            $sql .= " AND id_item != " . $item->id();
        }

        list($num) = $xoopsDB->fetchRow($xoopsDB->query($sql));
        if ($num > 0) {
            RMUris::redirect_with_message(
                __('Another item with same name already exists', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('submit', $down->isNew() ? [] : ['edit' => $down->id()]) . $query,
                RMMSG_WARN
            );
        }

        if($down->isNew()){
            $return = $down->save();
        } else {
            $return = $item->save();
            // Save status
            $down->save();
        }

        if ($return) {

            Dtransport_Functions::sendReviewRequest($down);

            RMUris::redirect_with_message(
                __('Changes saved successfully!', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp'),
                RMMSG_SUCCESS
            );
        } else {
            RMUris::redirect_with_message(
                __('Download item could not be saved! Please try again.', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('submit', $down->isNew() ? [] : ['edit' => $down->id()]) . $query,
                RMMSG_ERROR
            );
        }

        break;

    case 'publish':

        // Get item parameters
        $data = collectData();

        // Can publish download items?
        if (false == $common->privileges()::verify('dtransport', 'approve-items', '', false)) {
            $common->uris()::redirect_with_message(
                __('You are not allowed to publish downloads directly. The item will be stored for revision by our administrators. Once the file is approved, we will notify you.', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp'), RMMSG_WARN
            );
        }

        $query = dtCheckRequired($data);

        extract($data);

        if ($nameId == '') {
            $nameId = TextCleaner::getInstance()::sweetstring($name);
        }

        if ($id > 0) {
            $down = new Dtransport_Software($id);

            if ($down->isNew()) {
                $common->uris()::redirect_with_message(
                    __('Specified item does not exists!', 'dtransport'),
                    Dtransport_Functions::getInstance()->getURL('cp'), RMMSG_WARN
                );
            }

        } else {
            $down = new Dtransport_Software();
        }

        $down->setVar('name', $name);
        $down->setVar('nameid', Dtransport_Functions::getNameId($nameId, $down));
        $down->setVar('version', $version);
        $down->setVar('shortdesc', $shortDescription);
        $down->setVar('description', $description);
        $down->setVar('image', $image);
        $down->setVar('logo', $logo);
        $down->setVar('limits', $downLimit);
        if ($down->isNew()){
            $down->setVar('created', time());
        }
        $down->setVar('modified', time());
        $down->setVar('uid', $xoopsUser->uid());
        $down->setVar('password', $password);
        // Todo: verify next line
        $down->setVar('secure', $password != '' ? 1 : $secure);
        $down->setVar('groups', $groups);
        $down->setVar('approved', 1);
        $down->setVar('author_name', $authorName);
        $down->setVar('author_email', $authorEmail);
        $down->setVar('author_url', $authorUrl);
        $down->setVar('author_contact', $authorContact);
        $down->setVar('langs', $languages);

        $down->status = '';

        // Categories
        $down->setCategories($categories);
        // Licences
        $down->setLicences($licenses);
        // Platforms
        $down->setPlatforms($platforms);
        // Tags
        $down->setTags($tags);
        // Alert
        $down->createAlert();
        $down->alert()->mode = 1;
        $down->alert()->lastactivity = time();
        $down->limit = $dtSettings->alert_days;

        $db = $xoopsDB;
        // Check if exists another download with same name
        $sql = "SELECT COUNT(*) FROM " . $db->prefix("mod_dtransport_items") . " WHERE name='$name' AND nameid='" . $down->getVar('nameid') . "'";
        if (false == $down->isNew()) {
            $sql .= ' AND id_soft != ' . $id;
        }

        list($num) = $db->fetchRow($db->query($sql));

        if ($num > 0) {
            $common->uris()::redirect_with_message(
                __('Another item with same name already exists!', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('submit') . $query,
                RMMSG_ERROR
            );
        }

        // Save item
        if ($down->save()) {

            $common->events()->trigger('dtransport.saving.item', $down);

            $common->uris()::redirect_with_message(
                __('Download item saved successfully', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp', ['files' => $down->id()]),
                RMMSG_SUCCESS
            );

        } else {

            $common->uris()::redirect_with_message(
                __('Errors occurs while trying to save download item. Please try again', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('submit') . $query,
                RMMSG_ERROR
            );

        }

        break;

    case 'edit':
    case 'editold':

        // Check if element is valid
        if ($id <= 0)
            redirect_header(DT_URL, 2, __('Item not found. Please try again!', 'dtransport'));

        /**
         * Check if a previous edition exists in DB
         * If exists, then we will work over this edition.
         * If not, we need to create a new edition objetct
         */
        $swTemp = new Dtransport_SoftwareEdited($id, 'item');
        if ($swTemp->isNew()) {
            $sw = new Dtransport_SoftwareEdited();
            $sw->id_soft = $id;
        } else {
            $sw = $swTemp;
        }

        unset($swTemp);

        $common->template()->assign('itemType', 'edit');

        /*
         * Load existing download item:
         * 1. To check if download item exists
         * 2. To load all current data
         */
        $item = new Dtransport_Software($sw->id_soft);
        if ($item->isNew()) {
            RMUris::redirect_with_message(
                __('Seems to be that this item is not valid!', 'dtransport'),
                Dtransport_Functions::getInstance()->getURL('cp')
                , RMMSG_ERROR);
        }

        /**
         * If edition object is new then we need to assign
         * current software data
         */
        if ($sw->isNew()) {

            $sw->data = $item->getVars(true);
            $sw->set('categories', $item->categories());
            $sw->set('tags', $item->tags());
            $sw->set('platforms', $item->platforms());
            $sw->set('licenses', $item->licences());

        }

        if ($sw->get('uid') != $xoopsUser->uid())
            redirect_header(DT_URL, 1, __('You can not edit this download item!', 'dtransport'));

        include 'header.php';

        dtRenderForm($sw, 1);

        $common->template()->display('dt-submit.php', 'module', 'dtransport');

        include 'footer.php';

        break;

    default:

        // MOSTRAR FORMULARIO PARA NUEVA DESCARGA

        //$xoopsOption['template_main'] = 'dt-submit.tpl';
        $xoopsOption['module_subpage'] = 'submit';

        // URL parameters
        $page = $common->httpRequest()->get('page', 'integer', 1);
        $search = $common->httpRequest()->get('search', 'string', '');

        $sw = new Dtransport_SoftwareEdited();
        $common->template()->assign('itemType', 'new');

        //print_r($sw->data);
        include('header.php');

        dtRenderForm($sw, 0);
        // Check approve permissions
        //$canApprove = $common->privileges()->verify('dtransport', 'approve-editions', '', false);

        $common->template()->display('dt-submit.php', 'module', 'dtransport');

        include('footer.php');

}
