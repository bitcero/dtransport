/*!
 * Dtransport for XOOPS - submission controller
 * https://www.eduardocortes.mx
 * Copyright © 2017 Eduardo Cortés;
 * Licensed as GPL2
 * Version: 1.0
 */

// @prepros-prepend 'itemsform.js';

(function($){

    var instance, form;

    this.DownloadsApp = function(){

        this.init = function(){

            instance = this;
            form = $("#frm-items");

            $("#publish-data").click(function(){
                instance.publish();
            });

            $("#save-data").click(function(){
                instance.save();
            });

            $("#verify-data").click(function(){
                instance.verify();
            });

        };

        /**
         * Used when user wants to save current
         * modifications without publish changes
         */
        this.publish = function(){
            $("#action").val('publish');
            $(form).validate();
            $(form).submit();
        };

        /**
         * Used when user has finished edition
         * from an item
         */
        this.save = function(){
            $("#action").val('save');
            $(form).validate();
            $(form).submit();
        };

        this.verify = function(){
            $("#action").val('verify');
            $(form).validate();
            $(form).submit();
        };

    };

    var dtApp = new DownloadsApp();
    dtApp.init();

}(jQuery));
