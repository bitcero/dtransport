$(document).ready(function(){
    $("#add-meta").click(function(){

        if ($("#meta-name").is(":visible")){
            var name = $("#meta-name").val().replace(/\s/g, "_");
        } else {
            var name = $("#meta-sel-name").val().replace(/\s/g, "_");
        }
        var error = false;

        if(name==''){
            $(".forname").html(jsLang.errorName);
            $(".forname").show();
            return false;
        }

        if($("#meta-value").val()==''){
            $(".forvalue").show();
            return false;
        }

        $("#the-fields input[type='text']").each(function(i){

            if($(this).val()==name){
                $(".forname").html(jsLang.alreadyName);
                $(".forname").show();
                if ($("#meta-name").is(":visible"))
                    $("#meta-name").focus();
                else
                    $("#meta-sel-name").focus();
                error = true;
            }

        });

        if(error) return false;

        var html = '<tr id="field-'+$("#meta-name").val()+'"><td>' +
            '<input type="text" name="dtMetas['+name+'][name]" value="'+name+'" size="20" class="form-control">' +
            '<a href="#" class="btn btn-link btn-sm text-danger del-field" onclick="delete_field($(this)); return false;">' +
            '<span class="fa fa-times-circle text-danger"></span> ' + jsLang.deleteField+'</a></td>';
        html += '<td><textarea name="dtMetas['+name+'][value]" rows="3" cols="45" class="form-control">'+$("#meta-value").val()+'</textarea></td></tr>';

        $("#the-fields tbody").append(html);

        $("#meta-name").val('');
        $("#meta-value").val('');
        if ($("#meta-name").is(":visible"))
            $("#meta-name").focus();
        else
            $("#meta-sel-name").focus();

    });

    $("#meta-name").change(function(){
        if($(this).val().replace(/\s/g, "_")!=''){
            $(".forname").fadeOut('slow');
        }
    });

    $("#meta-value").change(function(){
        if($(this).val().replace(/\s/g, "_")!=''){
            $(".forvalue").fadeOut('slow');
        }
    });

    $("#new-meta-name").click(function(){
        //$("#cancel-name").before("<br />");
        $("#meta-sel-name").hide();
        $("#meta-name").show();
        $("#cancel-name").show();
        $(this).hide();
        return false;
    });

    $("#cancel-name").click(function(){
        //$("#meta-name + br").remove();
        if ( $("#meta-sel-name").length > 0 ){
            $("#meta-name").hide();
            $(this).hide();
            $("#meta-sel-name").show();
            $("#new-meta-name").show();
        }

        return false;
    });

});

function delete_field(item){
    ele = $(item).parent().parent();
    ele.remove();
    return false;
}