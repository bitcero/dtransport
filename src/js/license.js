/*!
 * More info at Eduardo Cortes Website (www.eduardocortes.mx)
 *
 * Author:  Eduardo Cortes
 * URI:     http://eduardocortes.mx
 *
 * Copyright (c) 2017, Eduardo Cortés Hervis
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function ($) {

    this.LicenseApp = function () {

        var _self = this;

        this.checkEmail = function (email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        };

        /*
         * Initialize listeners for register form
         */
        this.init = function(){

            $("#dt-license-form .btn-submit").click(function(){
                if(false == _self.verifyForm()){
                    return false;
                }

                _self.requestLicense();
            });

            $("#form-email, #form-api").keyup(function(e){

                if($(this).val() != ''){
                    $(this).removeClass('has-error')
                        .find("+ .error").hide();
                }

            });

        };

        /*
         * Verify that fields in license form are filled
         */
        this.verifyForm = function(){
            var email = $("#form-email");
            var api = $("#form-api");
            var key = $("#form-key");

            if($(email).val() == ''){
                $(email).addClass('has-error')
                    .find("+ .error").fadeIn();
                return false;
            }

            if(false == _self.checkEmail($(email).val())){
                $(email).addClass('has-error')
                    .find("+ .error").fadeIn();
                return false;
            }

            if($(api).val() == '' || $(api).val().length != 40){
                $(api).addClass('has-error')
                    .find("+ .error").fadeIn();
                return false;
            }

            if($(key).val() == '' || $(key).val().length != 35){
                $(key).addClass('has-error')
                    .find("+ .error").fadeIn();
                return false;
            }
        };

        this.requestLicense = function(){
            var email = $("#form-email");
            var api = $("#form-api");
            var key = $("#form-key");

            var params = {
                api: $(api).val(),
                email: $(email).val(),
                key: $(key).val(),
                CUTOKEN_REQUEST: $("#cu-token").val()
            };

            $.post('register.php', params, function(response){

                if(false == cuHandler.retrieveAjax(response)){
                    return false;
                }

                var template = '<div id="dt-license-info">' +
                    '<h4 class="title">' + jsLang.nowActive + '</h4>' +
                    '<div class="content"><div class="info">' + jsLang.activationInfo + '</div>' +
                    '<div class="form-group">' +
                    '<label>' + jsLang.email + '</label>' +
                    '<span class="form-control input-lg">%email</span></div>' +
                    '<div class="form-group">' +
                    '<label>' + jsLang.serial + '</label>' +
                    '<span class="form-control input-lg">%serial</span></div>' +
                    '<div class="form-group">' +
                    '<label>' + jsLang.activationDate + '</label>' +
                    '<span class="form-control input-lg">%date</span></div>' +
                    '<div class="notice">' + jsLang.saveInfo + '</div>' +
                    '<div class="form-group">' +
                    '<button type="button" class="btn btn-green btn-lg btn-block" onclick="window.location.reload(true);">' + jsLang.reloadNow + '</button>' +
                    '</div></div>' +
                    '</div>';

                // All is ok
                $("#dt-license-form").fadeOut(500, function(){
                    $(this).remove();

                    template = template
                        .replace('%email', response.email)
                        .replace('%serial', response.chain)
                        .replace('%date', response.date);

                    $("#dt-license-cover").append(template);
                    $("#dt-license-info").fadeIn('500');

                });

            }, 'json');
        };

    };

    $(document).ready(function(){
        var licenseApp = new LicenseApp();
        licenseApp.init();
    });

})(jQuery);