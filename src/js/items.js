$(document).ready(function(){

    $(".dt_item_opts a").height($(".dt_item_opts").height()-4);

    $("a.lock,a.unlock").on('click', function(){

        block_screen(1);
        var id = $(this).parent().parent().attr("id").replace("row-",'');

        var params = {
            XOOPS_TOKEN_REQUEST:$("#XOOPS_TOKEN_REQUEST").val(),
            id:id,
            action:$(this).attr("class")
        };

        var current = $(this).attr("class"); // Current control

        $("#status-bar").html(jsLang.applying);
        var bg = $("#status-bar").css('background');

        $.post('../ajax/items-ajax.php',params,function(data){

            cuHandler.closeLoader();

            if(data.error==1){

                dt_show_error(data);

                $("#status-bar").css('background', bg);

                return;
            }

            if(params.action=='lock')
                $("#row-"+id+" span.item_name").attr("style", 'background: url(../images/lockb.png) no-repeat right; padding-right: 20px;');
            else
                $("#row-"+id+" span.item_name").removeAttr("style");

            $("#row-"+id+" a."+current).css('background','url(../images/'+(current=='lock'?'unlock.png':'lock.png')+') no-repeat center');
            $("#row-"+id+" a."+current).attr("class", current=='lock'?'unlock':'lock');
            $("#row-"+id+" .secure_status").html(current=='lock'?jsLang.secure:jsLang.normal);
            $("#row-"+id+" .secure_status").effect('highlight',{}, 1000);
            $("#XOOPS_TOKEN_REQUEST").val(data.token);

            cuHandler.notify({
                text: data.message,
                type: 'alert-success',
                icon: 'svg-rmcommon-ok-circle'
            });

        },'json');

        return false;
    });

    // Change featured status
    $("input.featured, input.daily, input.approved").on('click', function(){

        //block_screen(1);
        cuHandler.showLoader();
        var id = $(this).parent().parent().attr("id").replace("row-",'');

        var clss = $(this).attr("class");
        if(clss=='daily'){
            var action = $(this).is(":checked")?'daily':'undaily';
        }  else if(clss=='featured') {
            var action = $(this).is(":checked")?'featured':'unfeatured';
        } else {
            var action = $(this).is(":checked")?'approved':'unapproved';
        }

        var params = {
            XOOPS_TOKEN_REQUEST:$("#XOOPS_TOKEN_REQUEST").val(),
            id:id,
            action:action
        };

        $.post('../ajax/items-ajax.php',params,function(data){

            cuHandler.closeLoader();

            if(data.error==1){

                dt_show_error(data);

                return;
            }

            if(clss=='approved'){
                // Delete or add link
                if(params.action=='approved'){
                    $("#row-"+id+" span.item_name").html('<a href="'+data.link+'">'+data.name+'</a>');
                } else {
                    $("#row-"+id+" span.item_name").html(data.name);
                }
            }

            $("#row-"+id+" td").effect('highlight',{}, 1000);
            $("#XOOPS_TOKEN_REQUEST").val(data.token);

            cuHandler.notify({
                text: data.message,
                type: 'alert-success',
                icon: 'svg-rmcommon-ok-circle'
            });

        },'json');

    });
    
    $(".dt-show-data").click(function(){
        
        id = $(this).parent().attr("id").replace("row-",'');
        
        $(".dt_hidden_data").each(function(){
            if($(this).is(":visible") && $(this).attr("id")!='data-'+id)
                $(this).hide();
        });
        
        $("#data-"+id).slideToggle('fast');
        
    });

    $("#search-reset").click(function(){

        $("#frm-search input[name='search']").val('');
        //$("#frm-search select[name='cat']").val(0);
        $("#frm-search").submit();

    });

});

