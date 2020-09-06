<div id="dt-cpanel-container">

    <{include file="db:dt-cp-header.tpl"}>

    <!-- Downloads list -->
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th class="text-center"><{$dtLang.id}></th>
                <th><{$dtLang.name}></th>
                <th class="text-center"><{$dtLang.created}></th>
                <th class="text-center"><{$dtLang.modified}></th>
                <th class="text-center"><{$dtLang.status}></th>
                <th class="text-center"><{$dtLang.hits}></th>
                <th class="text-center"><{$dtLang.options}></th>
            </tr>
            </thead>
            <tbody>
            <{if $items}>
                <{foreach item=item from=$items}>
                    <tr>
                        <td class="text-center"><{$item.id}></td>
                        <td class="text-left">
                            <{if $item.deletion}>
                                <strong><em><{$item.name}></em></strong>
                            <{else}>
                                <a href="<{$item.links.permalink}>"><strong><{$item.name}></strong></a>
                            <{/if}>
                        </td>
                        <td class="text-center"><{$item.created.formated}></td>
                        <td class="text-center"><{$item.modified.formated}></td>
                        <td class="text-center">

                            <{if $item.deletion}>
                                <span class="text-pink" title="<{$dtLang.forDelete}>">
                                    <{cuIcon icon=svg-dtransport-delete}>
                                </span>
                            <{else}>

                                <{if $item.approved}>
                                    <span class="text-success" title="<{$dtLang.approved}>"><{cuIcon icon=svg-rmcommon-ok-circle class="text-success"}></span>
                                <{else}>
                                    <span class="text-danger" title="<{$dtLang.notApproved}>"><{cuIcon icon=svg-rmcommon-close class="text-danger"}></span>
                                <{/if}>

                                <{if $item.status == 'verify'}>
                                    <span class="text-warning" title="<{$dtLang.waitingVerify}>">
                                        <{cuIcon icon=svg-dtransport-verify}>
                                    </span>
                                <{/if}>

                            <{/if}>
                        </td>
                        <td class="text-center"><{$item.hits}></td>
                        <td class="text-center">
                            <div class="item-options">
                                <{if $item.deletion <= 0}>
                                    <a href="<{$item.links.files}>" class="btn btn-default btn-sm">
                                        <span class="glyphicon glyphicon-file"></span>
                                    </a>
                                    <a href="<{$item.links.screens}>" class="btn btn-default btn-sm">
                                        <span class="glyphicon glyphicon-th-large"></span>
                                    </a>
                                    <a href="<{$item.links.features}>" class="btn btn-default btn-sm">
                                        <span class="glyphicon glyphicon-list-alt"></span>
                                    </a>
                                    <a href="<{$item.links.logs}>" class="btn btn-default btn-sm">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </a>
                                    <a href="<{$item.links.edit}>" class="btn btn-warning btn-sm">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </a>
                                    <a href="<{$item.links.delete}>" class="btn btn-danger btn-sm">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                <{else}>
                                    <a href="<{$item.links.canceldel}>" class="btn btn-success btn-sm" title="<{$dtLang.cancelDelete}>">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </a>
                                <{/if}>

                            </div>
                        </td>
                    </tr>
                <{/foreach}>
            <{else}>
                <tr>
                    <td class="text-center text-info" colspan="6">
                        <{$dtLang.noItems}>
                    </td>
                </tr>
            <{/if}>
            </tbody>
        </table>
    </div>
</div>
