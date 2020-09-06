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
 * Handles the response for submit form
 * Class Dtransport_Steps
 */
class Dtransport_Steps
{
    public function stepTwo()
    {
        global $common;

        $name = $common->httpRequest()->post('name', 'string', '');
        $version = $common->httpRequest()->post('version', 'string', '');
        $platforms = $common->httpRequest()->post('platforms', 'array', []);
        $author_name = $common->httpRequest()->post('author_name', 'string', '');
        $author_url = $common->httpRequest()->post('author_url', 'string', '');
        $logo = $common->httpRequest()->post('logo', 'string', '');
        $langs = $common->httpRequest()->post('langs', 'string', '');
        $id = $common->httpRequest()->post('id', 'integer', 0);

        if ($id > 0){

        }

        // Check provided data
        if(
            '' == $name ||
            '' == $version ||
            empty($platforms) ||
            '' == $author_name ||
            '' == $author_url
        ){
            $common->ajax()->notifyError(
                __('Please fill all required data before to preceed to next step!', 'dtransport')
            );
        }



    }

    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Dtransport_Steps();
        }

        return $instance;
    }
}