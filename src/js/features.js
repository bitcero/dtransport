(function($){

    var editor;

    $(document).ready(function(){

        $("body").on('shown.bs.modal', '#modal-features', function(){

            setTimeout(function(){
                if(typeof mdEditor != 'undefined'){
                    mdEditor.init('content');
                } else if('undefined' != typeof tinyMCE){
                    if (tinymce.get("content")) {
                        tinymce.get("content").destroy();
                    }
                    editor = initMCE('#content');
                    edToolbar('content');
                } else if('undefined' != typeof edToolbar){
                    edToolbar('content');
                }
            }, 500);

        });

        /**
         * Show dialog to add a new feature
         */
        $("#add-feature").click(function(){

            var params = {
                CUTOKEN_REQUEST: $("#cu-token").val(),
                item: $("#item-id").val(),
                action: 'new'
            };

            $.get('features.php', params, function(response){

                if(false == cuHandler.retrieveAjax(response)){
                    return false;
                }

            }, 'json');

            return false;

        });

        $('body').on('submit', "#frm-features", function(){

            var params = $(this).serialize();
            params += '&CUTOKEN_REQUEST=' + $("#cu-token").val();

            $.post($(this).attr('action'), params, function(response){

                if(false == cuHandler.retrieveAjax(response)){
                    return false;
                }

                var tr = $("#table-features tr[data-id='"+response.feature.id+"']");

                if(tr.length > 0){
                    // Row exists
                    $(tr).find('td.name').html('<strong>' + response.feature.title + '</strong>');
                    $(tr).find('td.modified').html(response.feature.modified);
                } else {

                    // New feature
                    var table = $("#table-features > tbody");
                    var tpl = '<tr data-id="'+response.feature.id+'">' +
                        '<td class="text-center">' +
                        '<input type="checkbox" name="ids[]" value="' + response.feature.id + '" data-oncheck="features"></td>' +
                        '<td class="text-center" width="20"><strong>' + response.feature.id + '</strong></td>' +
                        '<td class="name"><strong>' + response.feature.title + '</strong></td>' +
                        '<td class="text-center">' + response.feature.created + '</td>' +
                        '<td class="text-center modified">' + response.feature.modified + '</td>' +
                        '<td class="text-center cu-options">' +
                        '<a href="#" data-id="'+response.feature.id+'" class="warning">'+jsLang.edit+'</a> ' +
                        '<a href="#" data-id="'+response.feature.id+'" class="danger">'+jsLang.delete+'</a>' +
                        '</td></tr>';

                    $(table).append(tpl);
                    $('html, body').stop().animate({
                        'scrollTop': $("#table-features tr[data-id='"+response.feature.id+"']").offset().top
                    }, 900, 'swing');

                }

            }, 'json');
            return false;
        });

        $('body').on('click', '#table-features .cu-options .edit', function(){

            var params = {
                id: $(this).parents('tr').data('id'),
                CUTOKEN_REQUEST: $("#cu-token").val(),
                item: $("#item-id").val(),
                action: 'edit'
            };

            $.get('features.php', params, function(response){
                if(false == cuHandler.retrieveAjax(response)){
                    return false;
                }

                if(typeof mdEditor != 'undefined'){
                    mdEditor.init('content');
                } else if('undefined' != typeof tinyMCE){
                    initMCE('content');
                    edToolbar('content');
                } else if('undefined' != typeof edToolbar){
                    edToolbar('content');
                }

            }, 'json');

        });

        $('body').on('click', '#table-features .cu-options .delete', function(){

            $("#table-features :checkbox").prop('checked', false);
            $(this).parents('tr').find(":checkbox").prop('checked', true);
            $("#bulk-top, #bulk-bottom").val('delete');
            $("#frm-feats").submit();

        });

        $("#frm-feats").submit(function(){

            if($(this).find('tbody :checkbox:checked').length <= 0){
                cuHandler.notify({
                    text: jsLang.noSelectFeature,
                    type: 'alert-warning',
                    icon: 'svg-rmcommon-alert'
                });
                return false;
            }

            if(false == confirm(jsLang.confirmFeatureDeletion)){
                return false;
            }

            var params = $(this).serialize();
            params += '&CUTOKEN_REQUEST=' + $("#cu-token").val() + '&action=delete';

            $.post('features.php', params, function(response){

                cuHandler.retrieveAjax(response);

                // Deletes all rows
                for(var i = 0; i < response.ids.length; i++){
                    $("#table-features tr[data-id='" + response.ids[i] + "']").fadeOut(function(){$(this).remove();});
                }

                $("#bulk-top, #bulk-bottom").val('');
                $("#table-features :checkbox:checked").prop('checked', false);

            }, 'json');

            return false;
        });

    });

}(jQuery));