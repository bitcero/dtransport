<div id="dt-cpanel-container">
    <{include file="db:dt-cp-header.tpl"}>

    <{include file="db:dt-cp-item-options.tpl"}>

    <div id="dt-forms"<{if !$edit}> style="display: none;"<{/if}>>
        <{$file_form}>
    </div>

    <div class="table-responsive">
        <table class="table table-striped" id="files-list" data-id="<{$downloadItem.id}>">
            <thead>
            <tr>
                <th></th>
                <th class="text-center"><{$dtLang.id}></th>
                <th class="text-center"></th>
                <th class="text-left"><{$dtLang.title}></th>
                <th class="text-center"><{$dtLang.group}></th>
                <th class="text-center"><{$dtLang.remote}></th>
                <th class="text-center"><{$dtLang.size}></th>
                <th class="text-center"><{$dtLang.hits}></th>
                <th class="text-center"><{$dtLang.created}></th>
            </tr>
            </thead>
            <tbody>
            <{if $files}>
                <{foreach item=file from=$files}>
                    <tr>
                        <td class="text-center"><input type="radio" value="<{$file.id}>" name="ids[]"></td>
                        <td class="text-center"><{$file.id}></td>
                        <td class="text-center">
                            <{if $file.default}><span class="glyphicon glyphicon-star text-warning"></span><{/if}>
                        </td>
                        <td class="text-left"><{$file.title}></td>
                        <td class="text-center"><{$file.group}></td>
                        <td class="text-center">
                            <{if $file.remote}>
                                <span class="glyphicon glyphicon-ok text-success"></span>
                            <{/if}>
                        </td>
                        <td class="text-right"><{$file.size}></td>
                        <td class="text-center"><{$file.hits}></td>
                        <td class="text-center"><{$file.date}></td>
                    </tr>
                <{/foreach}>
            <{else}>
            <tr class="text-center">
                <td colspan="9"><{$dtLang.noFiles}></td>
            </tr>
            <{/if}>
            </tbody>
        </table>
    </div>

    <{if $files}>
        <div class="text-left">
            <button class="btn btn-warning btn-sm" type="button" id="btn-edit-item" data-action="edit" data-section="files" data-object="file">
                <span class="glyphicon glyphicon-pencil"></span>
                <{$dtLang.edit}>
            </button>
            <button class="btn btn-danger btn-sm" type="button" id="btn-delete-item" data-action="delete" data-section="files" data-object="file">
                <span class="glyphicon glyphicon-remove"></span>
                <{$dtLang.delete}>
            </button>
        </div>
    <{/if}>

    <br />
    <div id="dtFormGroup" style="<{if $editg}>display: block;<{else}>display: none;<{/if}>">
        <{$form_group}>
    </div>
    <div id="dtFormFile" style="<{if $edit}>display: block;<{else}>display: none;<{/if}>">
        <{$form_files}>
    </div>


</div>