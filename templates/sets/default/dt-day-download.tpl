<div class="panel dt-daily">
    <div class="panel-heading">
        <h3 class="panel-title"><{$dtLang.dayDownload}></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <{foreach item=item from=$daily_items}>
                <div class="col-sm-<{if $dtSettings->limit_daydownload%3==0}>2<{else}>3<{/if}> dt-day-item">
                    <a href="<{$item.link}>" title="<{$item.name}>">
                        <img src="<{resize file=$item.image w=250 h=250}>" alt="<{$item.name}>" class="img-responsive">
                    </a>
                    <a href="<{$item.link}>" class="name"><{$item.name}></a>
                    <a href="<{$item.link}>" class="btn btn-default btn-download">
                        <{$dtLang.download}>
                        <{cuIcon icon=svg-dtransport-download}>
                    </a>
                    <{$item.siterate}>
                </div>
            <{/foreach}>
        </div>
    </div>
</div>