<div id="dt-cpanel-container">
    <{include file="db:dt-cp-header.tpl"}>

    <{include file="db:dt-cp-item-options.tpl"}>

    <div id="dt-forms"<{if !$edit}> style="display: none;"<{/if}>>
        <{$feat_form}>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="features-list" data-id="<{$downloadItem.id}>">
            <thead>
            <tr>
                <th></th>
                <th class="text-center"><{$dtLang.id}></th>
                <th class="text-left"><{$dtLang.name}></th>
                <th class="text-center"><{$dtLang.created}></th>
                <th class="text-center"><{$dtLang.modified}></th>
                <th class="text-content"><{$dtLang.content}></th>
            </tr>
            </thead>
            <tbody>
            <{if $features}>
                <{foreach item=feat from=$features}>
                    <tr>
                        <td class="text-center"><input type="radio" name="ids[]" value="<{$feat.id}>"></td>
                        <td class="text-center"><{$feat.id}></td>
                        <td><{$feat.name}></td>
                        <td class="text-center"><{$feat.created}></td>
                        <td class="text-center"><{$feat.modified}></td>
                        <td class="text-left"><{$feat.content}></td>
                    </tr>
                <{/foreach}>
            <{else}>
                <tr class="text-center">
                    <td class="text-info" colspan="6">
                        <{$dtLang.noFeatures}>
                    </td>
                </tr>
            <{/if}>
            </tbody>
        </table>
    </div>

    <{if $features}>
        <div class="text-left">
            <button class="btn btn-warning btn-sm" type="button" id="btn-edit-item" data-action="edit" data-section="features" data-object="feature">
                <span class="glyphicon glyphicon-pencil"></span>
                <{$dtLang.edit}>
            </button>
            <button class="btn btn-danger btn-sm" type="button" id="btn-delete-item" data-action="delete" data-section="features" data-object="feature">
                <span class="glyphicon glyphicon-remove"></span>
                <{$dtLang.delete}>
            </button>
        </div>
    <{/if}>

</div>
