/*!
D-Transport For XOOPS
Author:     Eduardo Cortés (www.eduardocortes.mx)
Url:        http://www.redmexico.com.mx
Copyright:  © Red México, © Eduardo Cortés
License:    GPL 2
*/

var RMMSG_INFO = 0;
var RMMSG_WARN = 1;
var RMMSG_SUCCESS = 2;
var RMMSG_SAVED = 3;
var RMMSG_ERROR = 4;
var RMMSG_OTHER = 5;
var pni = '';

(function($){
    $(document).ready(function(){

        $("#frm-items #name").keyup(function(){

            if($(this).val()!=''){

                if(!$("#down-commands").is(":visible"))
                    $("#down-commands").fadeIn('fast');

            } else {

                if($("#down-commands").is(":visible"))
                    $("#down-commands").fadeOut('fast');

            }

        });

        $("#frm-items #name").change(function(){
            $("#frm-items #name").keyup();
        });

        $("#cancel-data").click(function(){

            document.location.href="items.php";
            return false;

        });

        $("#save-data").click(function(){

            var msg = $("#down-loader span");
            msg.removeClass();

            $("#down-blocker").fadeIn(200, function(){
                msg.html(jsLang.checkForm);
                $("#down-loader").fadeIn(700, function(){

                    // Verify form fields
                    if(!validate()){
                        dt_message(jsLang.errForm, DT_ERROR);
                        dt_close(4);
                        return false;
                    }
                    dt_message(jsLang.okForm,DT_OK);

                    if ( 'undefined' !== typeof ( tinymce ) )
                        tinymce.activeEditor.save();
                    else if ( 'undefined' != typeof mdEditor )
                        mdEditor.save('desc');

                    var params = $("#frm-items").serialize();

                    $.post(DT_URL+"/ajax/items-ajax.php", params, function(data){

                        dt_message(data.message, data.error==1?DT_ERROR:DT_OK);

                        if(data.token==''){
                            window.location.reload();
                            return;
                        }

                        $("#XOOPS_TOKEN_REQUEST").val(data.token);
                        $("#soft-id").val(data.id);
                        $("#item-permalink .permalink").html(data.link);
                        $("#item-permalink").slideDown('fast', function(){
                            $(this).effect('highlight', {}, 1000);
                        });
                        $("#action").val('saveedit');

                        dt_close(2);


                    }, 'json');

                    return false;
                });
            });

            return false;
        });

        $("#add-tags").click(function(){

            if($("#tags").val()=='') return false;

            var tags = $("#tags").val().split(",");
            var str = '';
            var existing = [];
            var eles = $("#tags-container span.tag");
            for(i=0;i<eles.length;i++){
                existing[i] = $(eles[i]).html();
            }

            for(i=0;i<tags.length;i++){
                str = tags[i].replace(/^\s+|\s+$/g, "");
                if(jQuery.inArray(str, existing)>=0) continue;
                $("#tags-container").append('<span class="tag">'+str+'</span><input type="hidden" name="tags[]" value="'+str+'" />');
            }

            $("#tags")
                .val('')
                .focus();

        });

        $("#tags").keyup(function(k){
            if (k.which == 13){
                $("#add-tags").click();
            }
        });

        $("#item-permalink .permalink span em").on('click', function(){
            if($(this).children("input").length>0) return;

            pni = $(this).html();
            var html = '<input type="text" name="nameid" size="5" id="nameid" value="'+$(this).html()+'" />';
            html += '<span rel="ok"></span><span rel="cancel"></span>';
            $(this).html('');
            $("#item-permalink .permalink span").addClass("editing");
            $("#item-permalink .permalink span").append(html);

            $("#item-permalink .permalink span.editing span").on('click', function(){

                if($(this).attr("rel")=='cancel'){

                    $("#item-permalink .permalink span input").remove();
                    $("#item-permalink .permalink span span").remove();
                    $("#item-permalink .permalink span em").html(pni);
                    $("#item-permalink .permalink span").removeClass("editing");
                    return;

                }

                var params = {
                    id: $("#soft-id").val(),
                    nameid: $("#item-permalink .permalink span input").val(),
                    XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val(),
                    action: 'permaname'
                };

                $.post(DT_URL+"/ajax/items-ajax.php", params, function(data){

                    if(data.error==1){
                        alert(data.message);
                        if(data.token==''){
                            window.location.reload();
                            return;
                        }
                        $("#XOOPS_TOKEN_REQUEST").val(data.token);
                        return;
                    }

                    $("#item-permalink .permalink span input").remove();
                    $("#item-permalink .permalink span span").remove();
                    $("#item-permalink .permalink span em").html(data.nameid);
                    $("#item-permalink .permalink span").removeClass("editing");

                },'json');

            });

        });

        $("#tags-container").on('click', 'span.tag', function(){
            $(this).remove();
        });

    });

    function dt_close(s){

        setTimeout(function(){
            $("#down-loader").fadeOut(400, function(){
                $("#down-blocker").fadeOut(200);
            });
        }, s*1000);

    }

    function dt_close_now(){

        $("#down-loader").fadeOut(400, function(){
            $("#down-blocker").fadeOut(200);
        });

    }

    function dt_message(m, e){

        $("#down-loader span").fadeOut(100, function(){
            $(this).html(m);
            $(this).removeClass();
            if(e!='')
                $(this).addClass(e);

            $(this).fadeIn(100);
        });

    }

    function validate(){

        var f = $("#frm-items");

        var eles = $("#frm-items [required]");
        var err = false;
        for(i=0;i<eles.length;i++){
            if($(eles[i]).val()==''){
                $("label[for='"+$(eles[i]).attr("id")+"']").addClass('error_field');
                err = true;
            }
        }


        if(err) return false;

        eles = $("ul.dt-categories :checked");
        if(eles.length<=0){
            $("label.dt_lcats").addClass('error_field');
            return false;
        }

        eles = $("ul.dt-licenses :checked");
        if(eles.length<=0){
            $("label.dt_llics").addClass('error_field');
            return false;
        }

        eles = $("ul.dt-platforms :checked");
        if(eles.length<=0){
            $("label.dt_lplats").addClass('error_field');
            return false;
        }

        eles = $("ul.groups_field_list :checked");
        if(eles.length<=0){
            $("label.dt_lgroups").addClass('error_field');
            return false;
        }

        return true;

    }


})(jQuery);
