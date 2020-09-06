<{include file="db:dt-header.tpl"}>

<{if $featured_items}>
    <{include file="db:dt-featured-list.tpl"}>
<{/if}>

<div class="dt-items-tags">
    <h2 class="dt-list-title"><{$dtLang.inOs}></h2>
    <{include file="db:dt-list-explore-item.tpl" items=$download_items}>
</div>

<{if $pagenav}><{$pagenav}><{/if}>

<{if $daily_items}>
    <{include file="db:dt-day-download.tpl" items=$daily_items}>
<{/if}>