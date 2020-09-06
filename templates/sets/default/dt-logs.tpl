<div id="dt-cpanel-container">
    <{include file="db:dt-cp-header.tpl"}>

    <{include file="db:dt-cp-item-options.tpl"}>

    <div id="dt-forms"<{if !$edit}> style="display: none;"<{/if}>>
        <{$log_form}>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="logs-list" data-id="<{$downloadItem.id}>">
            <thead>
            <tr>
                <th></th>
                <th class="text-center"><{$dtLang.id}></th>
                <th class="text-left"><{$dtLang.title}></th>
                <th class="text-center"><{$dtLang.created}></th>
            </tr>
            </thead>
            <tbody>
            <{if $logs}>
                <{foreach item=log from=$logs}>
                    <tr>
                        <th class="text-center"><input type="radio" name="ids[]" value="<{$log.id}>"></th>
                        <th class="text-center"><{$log.id}></th>
                        <th><{$log.title}></th>
                        <th class="text-center"><{$log.created}></th>
                    </tr>
                <{/foreach}>
            <{else}>
                <tr class="text-center">
                    <td colspan="4" class="text-info">
                        <{$dtLang.noLogs}>
                    </td>
                </tr>
            <{/if}>
            </tbody>
        </table>
    </div>

    <{if $logs}>
        <div class="text-left">
            <button class="btn btn-warning btn-sm" type="button" id="btn-edit-item" data-action="edit" data-section="logs" data-object="log">
                <span class="glyphicon glyphicon-pencil"></span>
                <{$dtLang.edit}>
            </button>
            <button class="btn btn-danger btn-sm" type="button" id="btn-delete-item" data-action="delete" data-section="logs" data-object="log">
                <span class="glyphicon glyphicon-remove"></span>
                <{$dtLang.delete}>
            </button>
        </div>
    <{/if}>

</div>
