<div class="row">
    <div class="col-sm-4">
        <div class="media">
            <div class="media-left">
                <a href="<{$xoops_url}>/userinfo.php?uid=<{$xoops_userid}>">
                    <img src="<{$cpanelData.avatar}>" alt="<{$cpanelData.name}>" class="media-object user-avatar">
                </a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<{$cpanelData.url}>">
                        <{if $cpanelData.name!=''}><{$cpanelData.name}><{else}><{$cpanelData.uname}><{/if}>
                    </a>
                </h4>
                <a href="<{$cpanelData.url}>"><{$dtLang.viewProfile}></a>
            </div>
        </div>
    </div>
    <div class="col-sm-8 text-right">

        <a href="<{$cpanelData.urls.myDownloads}>" class="btn btn-info">
            <span class="glyphicon glyphicon-th-list"></span>
            <{$dtLang.myDowns}>
        </a>
        <a href="<{$cpanelData.urls.pending}>" class="btn btn-warning">
            <span class="glyphicon glyphicon-time"></span>
            <{$dtLang.waiting}>
        </a>
        <{if !$showAdd}>
        <a href="<{$cpanelData.urls.submit}>" class="btn btn-success">
            <span class="glyphicon glyphicon-plus"></span>
            <{$dtLang.submit}>
        </a>
        <{/if}>
    </div>
</div>

<hr>