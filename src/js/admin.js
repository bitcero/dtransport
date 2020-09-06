/*!
 * D-Transport for Xoops
 * More info at Eduardo Cortés Website (http://www.rmcommon.com)
 *
 * Author:  Eduardo Cortés
 * URI:     http://eduardocortes.mx
 *
 * Copyright (c) 2016 - 2017, Eduardo Cortés Hervis
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

// @prepros-prepend features.js
// @prepros-prepend files.js
// @prepros-prepend license.js

$(document).ready(function(){
    
    $("#bulk-top").change(function(){
        
        $("#bulk-bottom").val($(this).val());
        
    });
    
    $("#bulk-bottom").change(function(){
        
        $("#bulk-top").val($(this).val());
        
    });

    if($("#frm-add").length>0)
        $("#frm-add").validate();
    
});

function dt_check_delete(id, form){
    
    if(id<=0) return false;
    
    $("#"+form+" input[type=checkbox]").removeAttr("checked");
    $("#item-"+id).prop("checked",'checked');
    
    $("#bulk-top").val('delete');
    
    before_submit(form);
    
}

function before_submit(form){

    if($("#bulk-top").val()=='') return false;

    var eles = $("#"+form+" tbody input:checked");
    
    if (eles.length <= 0){
        cuHandler.notify({
            text: jsLang.noSelectMsg,
            type: 'alert-warning',
            icon: 'svg-rmcommon-alert'
        });
        return false;
    }
    
    if ($("#bulk-top").val()=='delete'){
        if (confirm(jsLang.confirmDeletion))
            $("#"+form).submit();
    } else {
        $("#"+form).submit();
    }
}

function block_screen(block,bg){

    if(block==1){
        $('body').append("<div id='items-blocker'></div>");
        $("#items-blocker").fadeIn('fast');
    } else {
        $("#items-blocker").slideUp('fast', function(){
            $("#status-bar").slideUp('fast', function(){
                $("#status-bar").css('background', bg);
                $("#items-blocker").remove();
            });
        });
    }

}


function dt_show_error(data){

    cuHandler.notify({
        text: data.message,
        type: 'alert-danger',
        icon: 'svg-rmcommon-error'
    });

    if(data.token=='')
        window.location.reload();

    $("#XOOPS_TOKEN_REQUEST").val(data.token);
}
