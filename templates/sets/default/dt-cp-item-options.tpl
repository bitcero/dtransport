<div class="row text-left">
    <div class="col-md-4">
        <h4><a href="<{$itemData.link}>"><{$itemData.name}></a> <small>> <{$itemData.pageName}></small></h4>
    </div>
    <div class="col-md-8 text-right">
        <div class="item-options">
            <a href="#" class="btn btn-success show-form">
                <span class="glyphicon glyphicon-plus"></span>
                <{if $itemData.page == 'cp-files'}>
                    <{$dtLang.addFile}>
                <{elseif $itemData.page == 'cp-screens'}>
                    <{$dtLang.addScreen}>
                <{elseif $itemData.page == 'cp-features'}>
                    <{$dtLang.addFeature}>
                <{elseif $itemData.page == 'cp-logs'}>
                    <{$dtLang.addLog}>
                <{/if}>
            </a>
            <{if $itemData.page != 'cp-files'}>
                <a href="<{$itemData.links.files}>" class="btn btn-default" title="<{$dtLang.filesManage}>">
                    <span class="glyphicon glyphicon-file"></span>
                </a>
            <{/if}>

            <{if $itemData.page != 'cp-screens'}>
                <a href="<{$itemData.links.screens}>" class="btn btn-default" title="<{$dtLang.screensManage}>">
                    <span class="glyphicon glyphicon-picture"></span>
                </a>
            <{/if}>

            <{if $itemData.page != 'cp-features'}>
                <a href="<{$itemData.links.features}>" class="btn btn-default" title="<{$dtLang.featuresManage}>">
                    <span class="glyphicon glyphicon-list-alt"></span>
                </a>
            <{/if}>

            <a href="<{$itemData.links.logs}>" class="btn btn-default" title="<{$dtLang.logsManage}>">
                <span class="glyphicon glyphicon-calendar"></span>
            </a>
            <a href="<{$itemData.links.edit}>" class="btn btn-warning" title="<{$dtLang.editItem}>">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <a href="<{$itemData.links.delete}>" class="btn btn-danger" title="<{$dtLang.deleteItem}>">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
        </div>
    </div>
</div>

<hr>