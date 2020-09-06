<section class="comments-container">
    <{foreach item=comment from=$comments}>
        <div class="comment-item panel" id="comment-<{$comment.id}>">
            <div class="panel-body">
                <div class="text">
                    <{$comment.text}>
                </div>
            </div>
            <div class="panel-footer">
                <{if $comment.poster.avatar=="$xoops_url/uploads/blank.gif" || $comment.poster.avatar==''}>
                    <img src="<{$xoops_url}>/modules/rmcommon/images/avatar.gif" title="<{$poster.name}>" class=" pull-left"
                <{else}>
                    <img src="<{$comment.poster.avatar}>" title="<{$poster.name}>" class=" pull-left">
                <{/if}>
                <div class="data">
                    <small class="date"><{$comment.posted}><{if ($comment.edit!='')}> (<a href="<{$comment.edit}>"><{$lang_edit}></a>)<{/if}></small>
                    <strong>
                        <{if $comment.poster.id>0 && $comment.poster.url==''}>
                            <a href="<{$xoops_url}>/userinfo.php?uid=<{$comment.poster.id}>"><{$comment.poster.name}></a>
                        <{elseif $comment.poster.url!=''}>
                            <a href="<{$comment.poster.url}>" rel="external nofollow" target="_blank"><{$comment.poster.name}></a>
                        <{else}>
                            <{$comment.poster.name}>
                        <{/if}>
                    </strong>
                </div>
            </div>
        </div>
    <{/foreach}>
</section>
