<div id="dt-cpanel-container">
    <{include file="db:dt-cp-header.tpl"}>

    <{include file="db:dt-cp-item-options.tpl"}>

    <div id="dt-forms"<{if $isEdition<=0}> style="display: none;"<{/if}>>
        <{$formscreens}>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="screens-list" data-id="<{$downloadItem.id}>">
            <thead>
            <tr>
                <th></th>
                <th class="text-center"><{$dtLang.id}></th>
                <th class="text-center"><{$dtLang.image}></th>
                <th class="text-left"><{$dtLang.title}></th>
                <th class="text-left"><{$dtLang.description}></th>
            </tr>
            </thead>
            <tbody>
            <{if $screens}>
                <{foreach item=screen from=$screens}>
                    <tr>
                        <td class="text-center">
                            <input type="radio" name="ids[]" value="<{$screen.id}>">
                        </td>
                        <td class="text-center"><{$screen.id}></td>
                        <td class="text-center">
                            <img src="<{resize file=$screen.image w=100 h=100}>" class="thumbnail">
                        </td>
                        <td class="text-left"><{$screen.title}></td>
                        <td class="text-left"><{$screen.desc}></td>
                    </tr>
                <{/foreach}>
            <{else}>
                <tr class="text-center">
                    <td colspan="5" class="text-info">
                        <{$dtLang.noScreens}>
                    </td>
                </tr>
            <{/if}>
            </tbody>
        </table>
    </div>

    <{if $screens}>
        <div class="text-left">
            <button class="btn btn-warning btn-sm" type="button" id="btn-edit-item" data-action="edit" data-section="screens" data-object="screen">
                <span class="glyphicon glyphicon-pencil"></span>
                <{$dtLang.edit}>
            </button>
            <button class="btn btn-danger btn-sm" type="button" id="btn-delete-item" data-action="delete" data-section="screens" data-object="screen">
                <span class="glyphicon glyphicon-remove"></span>
                <{$dtLang.delete}>
            </button>
        </div>
    <{/if}>

</div>
