$(document).ready(function(){

    $("#screens-selector").click(function(){
        cuHandler.imagesManager({
            idContainer: 'dt-screenshots',
            type: 'external',
            target: 'dtScreens_Selector',
            multiple: 'yes',
            title: jsLang.insertScreen
        });
    });

    $("#table-screens").on('click','a.edit-screen', function(){

        $(this).cuSpinner();

        $("#editor").slideUp('fast', function(){
            $("tr[data-id='"+$(this).find("#scr-id").val()+"']").slideDown('fast');
            $(this).remove();
        });

        var row = $(this).parents('tr');
        var id = $(this).parents('tr').data("id");

        var params = {
            op: 'image-info',
            CUTOKEN_REQUEST: $("#cu-token").val(),
            id: id
        };

        var anchor = $(this);

        $.get('screens.php', params, function(data){

            $(anchor).cuSpinner();
            if(false == cuHandler.retrieveAjax(data)){
                return false;
            }

            var html = '<tr id="editor" style="display: none;">';
            html += '<td colspan="3" align="center"><img src="'+$(row).find("img").attr("src")+'" style="width: auto; height: auto;" /></td>'
            html += '<td colspan="3"><div class="dt_table"><div class="dt_row">';
            html += '<div class="dt_cell"><strong>'+jsLang.titleField+'</strong></div>';
            html += '<div class="dt_cell"><input type="text" name="title" id="the-title" value="'+data.title+'" class="form-control"></div></div>';
            html += '<div class="dt_row">';
            html += '<div class="dt_cell"><strong>'+jsLang.descField+'</strong></div>';
            html += '<div class="dt_cell"><textarea name="desc" id="the-desc" rows="4" class="form-control">'+data.description+'</textarea></div></div>'
            html += '<div class="dt_row">';
            html += '<div class="dt_cell">&nbsp;</div>';
            html += '<div class="dt_cell"><button type="button" class="btn btn-primary" id="save-data">'+jsLang.saveData+'</button>';
            html += ' <button type="button" id="cancel-save" class="btn btn-default">'+jsLang.cancel+'</button><input type="hidden" name="id" id="scr-id" value="'+data.id+'" /></div></div>';
            html += '</div></td></tr>';

            $(row).after(html);

            $(row).fadeOut(function(){
                $("#editor").fadeIn();
            });

        },'json');

        return false;
    });

    $("#table-screens").on('click','a.delete-screen', function(){

        var row = $(this).parents('tr');

        $(row).find("input:checkbox").prop("checked", true);

        $("#bulk-top").val('delete-screen');
        $("#frm-screens").submit();

    });

    $("#frm-screens").submit(function(){

        if('' == $("#bulk-top").val()){
            return false;
        }

        if($(this).find('input:checked').length <= 0){
            cuHandler.notify({
                text: jsLang.noCheckedScreen,
                type: 'alert-danger',
                icon: 'svg-rmcommon-error'
            });
            return false;
        }

        if('delete-screen' == $("#bulk-top").val()){
            if(false == confirm(jsLang.deleteScreen))
                return false;
        }

        var params = $("#frm-screens").serialize();
        params += '&CUTOKEN_REQUEST=' + $("#cu-token").val();

        $.post('screens.php', params, function(data){

            if(false == cuHandler.retrieveAjax(data)){
                return false;
            }

            var images = data.images;

            for(var i = 0; i < images.length; i++){
                $("tr[data-id='"+images[i]+"']").fadeOut(function(){$(this).remove();});
            }

        },'json');

        return false;

    });

    $("#table-screens").on("click", '#cancel-save', function(){
        $("#editor").hide(function(){
            $("tr[data-id='"+$(this).find("#scr-id").val()+"']").fadeIn();
            $(this).remove();
        });
    });

    $("#table-screens").on("click", '#save-data', function(){

        if($("#the-title").val()==''){
            alert(jsLang.noTitle);
            return;
        }

        var params = {
            title: $("#the-title").val(),
            desc: $("#the-desc").val(),
            id: $("#scr-id").val(),
            CUTOKEN_REQUEST: $("#cu-token").val(),
            op: 'save-screen-data'
        };

        $.post('screens.php', params, function(data){

            if(false == cuHandler.retrieveAjax(data)){
                return false;
            }

            var newTitle = '<strong>'+data.title+'</strong>';

            $("tr[data-id='"+data.id+"'] td.the-title").html(newTitle);
            $("tr[data-id='"+data.id+"'] td.the-desc").html(data.description);

            $("#editor").fadeOut(function(){
                $("tr[data-id='"+$(this).find("#scr-id").val()+"']").fadeIn();
                $(this).remove();
            });

        },'json');

    });
});

function dtScreens_Selector(data, container){

    var images = [];

    if(data.length > 0){
        images = data;
    } else {
        images.push(data);
    }

    var params = {
        CUTOKEN_REQUEST: $("#cu-token").val(),
        op: 'save',
        images: images,
        item: $("#item-id").val()
    };

    // Send images
    $.post('screens.php', params, function(response){

        cuHandler.retrieveAjax(response);

        if(undefined == response.images || response.images.length <= 0){
            return false;
        }

        var tpl = '<tr data-id="%image-id%">' +
            '<td class="text-center">' +
            '<input type="checkbox" name="ids[]" value="%image-id%" data-oncheck="screens">' +
            '</td>' +
            '<td class="text-center"><strong>%image-id%</strong></td>' +
            '<td class="text-center">' +
            '<a href="%image-url%" target="_blank"><img src="%image-th%"></a></td>' +
            '<td class="the-title">' +
            '<strong>%image-title%</strong></td>' +
            '<td class="the-desc">%image-desc%</td>' +
            '<td class="cu-options">' +
            '<a href="#" class="edit-screen warning">' + jsLang.edit + '</a> ' +
            '<a href="#" class="delete-screen danger">' + jsLang.delete + '</a></td></tr>';

        var $table = $("#table-screens > tbody");
        var $images = response.images;

        for(var i = 0; i < $images.length; i++){
            $($table).append(tpl
                .replace(/\%image-id\%/g, $images[i].id)
                .replace('%image-url%', $images[i].link)
                .replace('%image-th%', $images[i].thumbnail)
                .replace('%image-title%', $images[i].title)
                .replace('%image-desc%', $images[i].description)
            );
        }

    }, 'json');

}