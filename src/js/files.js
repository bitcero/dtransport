(function($){

    var dropzone;

    $(document).ready(function(){

        if($("#dtfiles-uploader").length > 0){

            var errorExists = false;

            //Dropzone.options.imagesUploader = false;
            Dropzone.options.dtfilesUploader = false;

            dropzone = new Dropzone("#dtfiles-uploader", {
                url: xoUrl + '/modules/dtransport/ajax/upload.php',
                acceptedFiles: dtExts,
                maxFileSize: dtSize,
                parallelUpload: 1,
                autoProcessQueue: true,
                maxFiles: 1,
                params: {
                    CUTOKEN_REQUEST: $("#cu-token").val(),
                    item: $("#item").val()
                },

                // JS strings for rmcommon must be included
                dictDefaultMessage: cuLanguage.dzDefault,

                // Events listerners
                init: function(){
                    this.on('success', function(file, response){
                        errorExists = false;

                        response = JSON.parse(response);

                        if(false == cuHandler.retrieveAjax(response)){
                            return false;
                        }
                        
                        $('#dtfiles-preview > .name').html(response.file);
                        $('#dtfiles-preview > .size').html(response.size);
                        $('#size').val(response.fullsize);
                        $('#dtfiles-preview .type').html(response.mime);
                        $('#dtfiles-preview .secure').html(response.secure);

                    });

                    this.on('queuecomplete', function(){
                        if(false == errorExists){
                            $("#dtfiles-preview").fadeIn();
                            $("#dtfiles-uploader").fadeOut();
                        }
                    });

                    this.on('error', function(file, error, xhr){
                        errorExists = true;
                    });
                }
            });

            dropzone.on('complete', function(file) {
                dropzone.removeFile(file);
            });

        }

        $("#create-group").click(function(){

            if($("#group-name").val()==''){

                cuHandler.notify({
                    text:jsLang.groupName,
                    type: 'alert-warning',
                    icon: 'svg-rmcommon-alert'
                });

                $("#group-name").focus();
                return false;
            }

            var params = $("#form-new-group").serialize();
            params += '&XOOPS_TOKEN_REQUEST='+$("#XOOPS_TOKEN_REQUEST").val();

            block_screen(1);
            $("#status-bar").slideDown('fast');

            $.post("../ajax/files-ajax.php", params, function(data){

                if(data.error==1){

                    alert(data.message);
                    if(data.token=='')
                        window.location.reload();

                    $("#XOOPS_TOKEN_REQUEST").val(data.token);

                    block_screen(0);
                    $("#status-bar").slideUp('fast');

                    $("#group-name").focus();

                    return;

                }

                block_screen(0);
                $("#status-bar").slideUp('fast');

                $("#XOOPS_TOKEN_REQUEST").val(data.token);

                if(data.action=='create'){
                    var html = '<tr class="head" id="group-'+data.id+'">';
                    html += '<td colspan="6"><strong>'+data.name+'</strong></td>';
                    html += '<td class="cu-options text-center"><a href="files.php?item='+data.item+'&amp;edit=1&amp;id='+data.id+'" class="editgroup warning">'+jsLang.edit+'</a> ';
                    html += '<a href="files.php?item='+data.item+'&amp;id='+data.id+'&amp;action=deletegroup" class="deletegroup danger">'+jsLang.delete+'</a></td></tr>';
                    $("#table-files tbody").append(html);
                    $("#group-name").val('');
                } else {
                    $("#group-"+data.id+" td:first-child").html(data.name);
                    $("#group-name").val('');
                    $("#cancel-edition").remove();
                    $("input#id").remove();
                    $("#create-group").val(jsLang.createGroup).removeClass('buttonGreen').addClass('buttonBlue');
                    $("#form-new-group input[name=action]").val("save-group");
                }

                $("#group-"+data.id).effect("highlight", {}, 1000);


            }, 'json');

        });

        $("#remote").change(function(){
            if($(this).is(":checked")){
                $(".url-container").slideDown('fast', function(){
                    $(this).find('input').effect('highlight',{},1000);
                });
                $("#dtfiles-uploader").fadeOut('fast');
                $("#dtfiles-preview").addClass("transparent");
            }else{
                $(".url-container").slideUp('fast');
                $("#dtfiles-preview").removeClass("transparent");
                if(!$("#dtfiles-preview").is(":visible"))
                    $("#dtfiles-uploader").fadeIn('fast');
            }
        });

        $("#dtfiles-preview .delete").click(function(){

            if(!confirm(jsLang.deleteFile)) return false;

            var params = {
                file: $("#dtfiles-preview .name").html(),
                secure: $("#secure").val(),
                action: 'delete_hfile',
                CUTOKEN_REQUEST: $("#cu-token").val()
            };

            block_screen(1);
            $("#status-bar").slideDown('fast');

            $.post('../ajax/files-ajax.php', params, function(data){

                block_screen(0);

                if(false == cuHandler.retrieveAjax(data)){
                    $("#status-bar").slideUp('fast');
                    return false;
                }

                dropzone.options.params.CUTOKEN_REQUEST = $("#cu-token").val();

                $("#dtfiles-preview").fadeOut('fast', function(){
                    $("#dtfiles-uploader").fadeIn('fast');
                });

                $("#status-bar").slideUp('fast');

            },'json');

        });

        $("#save-data").click(function(){

            // Check if a file has been provided
            if($("#remote").is(":checked")){

                if($("#url").val()==''){
                    cuHandler.notify({
                        text: jsLang.noURL,
                        type: 'text-danger',
                        icon: 'svg-rmcommon-error'
                    });
                    $("#url").focus();
                    return false;
                }

            } else {

                if($("#dtfiles-preview span.name").html()==''){
                    cuHandler.notify({
                        text: jsLang.noFile,
                        type: 'text-danger',
                        icon: 'svg-rmcommon-error'
                    });
                    return false;
                }

            }

            if($("#title").val()==''){
                cuHandler.notify({
                    text: jsLang.noTitle,
                    type: 'text-danger',
                    icon: 'svg-rmcommon-error'
                });
                $("#title").focus();
            }

            var params = {
                remote: $("#remote").is(":checked")?1:0,
                title: $("#title").val(),
                group: $("#group").val(),
                default: $("#default").is(":checked")?1:0,
                file: $("#remote").is(":checked")?$("#url").val():$("#dtfiles-preview .name").html(),
                action: $("#action").val(),
                id: $("#id").length>0?$("#id").val():0,
                CUTOKEN_REQUEST: $("#cu-token").val(),
                item: $("#item").val(),
                secure: $("#secure").val(),
                mime: $("#dtfiles-preview span.type").html(),
                size: $("#size").val()
            };

            block_screen(1);
            $("#status-bar").slideDown('fast');

            $.post("../ajax/files-ajax.php", params, function(data){

                block_screen(0);

                if(false == cuHandler.retrieveAjax(data)){
                    $("#status-bar").slideUp('fast');
                    return false;
                }

                window.location.href = 'files.php?item='+$("#item").val();

            },'json');

            return true;

        });

        $("#table-files").on('click', 'a.deletegroup', function(){

            if(!confirm(jsLang.confirmDeletion)) return false;

            var url = $(this).attr("href")+"&XOOPS_TOKEN_REQUEST="+$("#XOOPS_TOKEN_REQUEST").val();

            window.location.href = url;
            return false;

        });

        $("#table-files").on('click', 'a.editgroup', function(){

            var id = $(this).parent().parent().attr("id").replace('group-','');
            var name = $("#group-"+id+" td:first-child > strong").html();

            $("#group-name").val(name);
            $("#form-new-group").append("<input type='hidden' value='"+id+"' id='id' name='id' />");
            if($("#cancel-edition").length<=0) $("#create-group").after(' <button type="button" name="cancel" id="cancel-edition" class="btn btn-default">'+jsLang.cancel+'</button>');
            $("#form-new-group input[name=action]").val("update-group");
            $(".dt_group_form").effect('hightlight',{},1000);
            $("#group-name").focus();

            $("#cancel-edition").click(function(){

                $("#group-name").val('');
                $("input#id").remove();
                $("#create-group").val(jsLang.createGroup).removeClass('buttonGreen').addClass('buttonBlue');
                $("#form-new-group input[name=action]").val("save-group");
                $(this).remove();
                return true;

            });

            return false;

        });

        $("#table-files").on('change', 'select.group-selector', function(){

            var ele = $(this).parent().parent();
            var id = $(this).val();

            block_screen(1);
            $("#status-bar").slideDown('fast');
            var bg = $("#status-bar").css('background');

            // Send data
            var params = {
                action: 'reasign-file',
                idgroup: id,
                id: ele.find("input[type='checkbox']").attr("id").replace('item-',''),
                item: $("#item").val(),
                XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val()
            };

            if(id>0){
                if($("#group-"+id).length<=0) return;
            }

            $.post('../ajax/files-ajax.php', params, function(data){

                if(data.error==1){

                    dt_show_error(data);
                    return;

                }

                $("#XOOPS_TOKEN_REQUEST").val(data.token);
                $("#status-bar").html(data.message);
                $("#status-bar").css('background','#389931');
                block_screen(0, bg);

                $(ele).fadeOut('fast', function(){

                    var two = ele.clone();

                    if(id<=0){
                        ele.remove();
                        $("#table-files tbody").prepend(two);
                    } else {

                        if($("#group-"+id).length<=0){
                            ele.fadeIn('fast');
                            return;
                        }

                        ele.remove();
                        $("#group-"+id).after(two);

                    }

                    two.fadeIn('fast');
                    two.find("select").val(id);
                });

            }, 'json');

        });

        $("#table-files").on('click', 'a.delete-file', function(){

            if(!confirm(jsLang.confirmDeletion)) return false;

            var ele = $(this).parent().parent(); // tr

            var params = {
                id: ele.find("input[type='checkbox']").attr("id").replace("item-",''),
                item: $("#item").val(),
                XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val(),
                action: 'delete-file'
            }

            block_screen(1);
            $("#status-bar").slideDown('fast');
            var bg = $("#status-bar").css('background');

            $.post('../ajax/files-ajax.php', params, function(data){

                if(data.error==1){

                    dt_show_error(data);
                    return;

                }

                $("#XOOPS_TOKEN_REQUEST").val(data.token);
                $("#status-bar").html(data.message);
                $("#status-bar").css('background','#389931');

                setTimeout(function(){
                    block_screen(0, bg);
                }, 3000);

                ele.fadeOut('fast',function(){ele.remove();})

            });

            return false;

        });

    });

    function getFilesToken(){

        params = {
            identifier: $("#identifier").val(),
            action: 'identifier'
        };

        $.post('../ajax/files-ajax.php', params, function(data){

            if(data.error==1){
                alert(data.message);
                window.location.reload();
                return;
            }

            $("#XOOPS_TOKEN_REQUEST").val(data.token);

        },'json');

    }

}(jQuery));