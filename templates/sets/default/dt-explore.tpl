<{include file="db:dt-header.tpl"}>

<{if $featured_items}>
    <{include file="db:dt-featured-list.tpl"}>
<{/if}>

<div class="dt-items-container">
    <h2 class="dt-list-title"><{$listTitle}></h2>
    <{include file="db:dt-list-explore-item.tpl" items=$items}>
</div>

<{$pagenav}>

<!-- Descargas del día -->
<{if $daily_items}>
    <{include file="db:dt-day-download.tpl" items=$daily_items}>
<{/if}>