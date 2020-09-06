(function($){

    var _self;

    this.DownloadsApp = function(){
        this.url = '';
        this.permalinks = false;
        this.toi = 0;
        this.direction = 'left';
    };

    DownloadsApp.prototype.init = function(){
        _self = this;
        this.listeners();
    };

    DownloadsApp.prototype.listeners = function(){

        $("#dt-cpanel-container .show-form").click(this.showFileForm);
        $("#dt-forms #form-cancel").click(this.hideFileForm);
        $("#btn-edit-item").click(this.actionFile);
        $("#btn-delete-item").click(this.actionFile);

    };

    DownloadsApp.prototype.actionFile = function(){
        var action= $(this).data('action');
        var section= $(this).data('section');
        var object= $(this).data('object');
        var url = _self.url + '/';
        var id = $("#"+section+"-list").data('id');
        var item = $("#"+section+"-list :radio:checked").val();

        if(undefined == item){
            alert(dtLang.selectItem);
            return false;
        }

        if(action=='delete'){
            if(false == confirm(dtLang.confirmDeletion)){
                return false;
            }
        }

        if(_self.permalinks){
            url += 'cp/'+section+'/' + id + '/'+action+'/' + item + '/';
        } else {
            url += '?s=cp&action='+section+'&id=' + id + '&op='+action+'&'+object+'=' + item;
        }

        window.location.href = url;
    };

    DownloadsApp.prototype.showFileForm = function(){
        $("#dt-forms").slideDown(600);
        $(this).attr("disabled", 'disabled');
        return false;
    };

    DownloadsApp.prototype.hideFileForm = function(){
        $("#dt-forms > form").trigger('reset');
        $("#dt-forms").slideUp(600);
        $("#dt-cpanel-container .show-form").removeAttr("disabled");
    };

    DownloadsApp.prototype.delayDownload = function(){

        var ele = $("#will-start .message");

        var msg = down_message.replace("{x}", '<strong>'+timeCounter+'</strong>');
        ele.html(msg);

        if(timeCounter<=0){
            window.location.href = dlink;
            return;
        }

        timeCounter--;

        toi = setTimeout('dtApp.delayDownload()', 1000);

    };

    DownloadsApp.prototype.scroll = function(){

        var ele = $("#dt-screens-row div.dt_scroller div.container div");
        var pos = ele.position();
        var cw = $("#dt-screens-row .dt_scroller div.container").width(); // Scroller width
        var imgw = $("#dt-screens-row div.dt_scroller div img:first-child").width() + 6;

        if(ele.width() < cw){
            var left = (cw - ele.width());
            left = left / 2;
            ele.animate({
                left: left+'px',
                speed: 500
            });
            return;
        }

        if((ele.width()-(pos.left*-1))<cw && direction=='left'){
            direction = 'right';
        }else if(pos.left>=0 && direction=='right'){
            direction = 'left';
        }

        ele.animate({
            left: direction=='left'?pos.left-imgw+'px':pos.left+imgw+'px',
            speed: 800
        });

        toi = setTimeout('dt_scroll()', 1000);

    };

    $(document).ready(function(){

        if($(".dt_scroller").lengt>0)
            toi = setTimeout('dtApp.scroll()',500);

        $(".dt_scroller .container img").hover(function(){

            clearTimeout(toi);

        });

        $(".dt_scroller img.control").hover(function(){

            $(this).css('opacity', '0.7');

        });

        $(".dt_scroller img.control").mouseout(function(){

            $(this).css('opacity', '0.2');

        });

        $(".dt_scroller .dt_backward").click(function(){clearTimeout(toi);direction='left';dt_scroll();});
        $(".dt_scroller .dt_forward").click(function(){clearTimeout(toi);direction='right';dt_scroll();});

        if($("#will-start").length>0){
            toi = setTimeout('dtApp.delayDownload()', 1000);
        }


        if($("#frm-rating").length>0){

            var wMult = 100 / dt_max;

            var select = $( "#dt-rates" );
            $("#dt-rating-thumbs > span").css('width', ((dt_steps<1 ? (dt_vote).toFixed(1) : dt_vote) * wMult)+'%');
            $("#dt-rating-legend").html(dt_steps<1 ? (dt_vote).toFixed(1) : dt_vote);
            var slider = $( "<div id='slider'></div>" ).insertAfter( select ).slider({
                min: 1,
                max: dt_max+1,
                range: "min",
                step: dt_steps,
                value: dt_vote + 1,
                slide: function( event, ui ) {
                    select[ 0 ].selectedIndex = dt_steps<1 ? (ui.value - 1).toFixed(1) * dt_max : ui.value - 1;
                    $("#dt-rating-legend").html(dt_steps<1 ? (ui.value - 1).toFixed(1) : ui.value - 1);
                    $("#dt-rating-thumbs > span").css('width', ((dt_steps<1 ? (ui.value - 1).toFixed(1) : ui.value - 1) * wMult)+'%');
                },
                start: function(event, ui){
                    $("#dt-rate-msgs").fadeOut('fast');
                },
                stop: function(event, ui){
                    $.post(dtURL+'/ajax/vote.php', $("#frm-rating").serialize(), function(data){
                        if(data.error==1){

                            $("#dt-rate-msgs").fadeIn('fast').html(data.message).removeClass('ok').addClass('error');
                            if(data.token!='')
                                $("#XOOPS_TOKEN_REQUEST").val(data.token);

                            if(data.url!='')
                                window.location.href = data.url;

                            return;

                        }

                        $("#dt-rate-msgs").fadeIn('fast').html(data.message).removeClass('error').addClass('ok');
                        if(data.token!='')
                            $("#XOOPS_TOKEN_REQUEST").val(data.token);

                    }, 'json');
                }
            });
            $( "#dt-rates" ).change(function() {
                slider.slider( "value", this.selectedIndex + 1 );
            });

        }

        // Load features
        $("#dt-item-features a").click(function(){
            var url = $(this).attr('href');

            if($("#dt-features-loader .dt-feature-container").is(":visible")){
                $("#dt-features-loader .dt-feature-container").slideUp(function(){
                    $(this).remove();
                });
            }

            $("#dt-features-loader").slideDown(function(){
                $("body,html").animate({
                    scrollTop: $("#dt-features-loader").offset().top
                }, 1000)
            });

            $.get(url, {}, function(response){

                $("#dt-features-loader .cu-icon").fadeOut(function(){
                    $("#dt-features-loader")
                        .append(response)
                        .find('.dt-feature-container').slideDown();
                });

            }, 'html');

            return false;
        });

    });

    this.dtApp = new DownloadsApp();

}(jQuery));