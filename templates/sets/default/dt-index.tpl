<{include file="db:dt-header.tpl"}>

<{if $featured_items}>
    <{include file="db:dt-featured-list.tpl"}>
<{/if}>

<div class="dt-home-content">
    <ul class="nav nav-tabs dt-nav-tab" role="tablist">
        <li role="presentation" class="active">
            <a href="#recent-items" aria-controls="recent-items" role="tab" data-toggle="tab"><{$dtLang.recents}></a>
        </li>
        <li role="presentation">
            <a href="#best-rated-items" aria-controls="best-rated-items" role="tab" data-toggle="tab"><{$dtLang.bestRated}></a>
        </li>
        <li role="presentation">
            <a href="#dt-panel-updated" aria-controls="dt-panel-updated" role="tab" data-toggle="tab"><{$dtLang.updated}></a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in active" role="tabpanel" id="recent-items">
            <{include file="db:dt-list-item.tpl" items=$recent_items}>
            <div class="more-link">
                <a href="<{$moreRecentLink}>"><{$dtLang.viewMore}></a>
            </div>
        </div>
        <div class="tab-pane fade" role="tabpanel" id="best-rated-items">
            <{include file="db:dt-list-item.tpl" items=$rated_items}>
            <div class="more-link">
                <a href="<{$moreRatedLink}>"><{$dtLang.viewMore}></a>
            </div>
        </div>
        <div class="tab-pane fade" role="tabpanel" id="dt-panel-updated">
            <{include file="db:dt-list-item.tpl" items=$updated_items}>
            <div class="more-link">
                <a href="<{$moreUpdatedLink}>"><{$dtLang.viewMore}></a>
            </div>
        </div>
    </div>
</div>

<!-- Descargas del día -->
<{if $daily_items}>
    <{include file="db:dt-day-download.tpl" items=$daily_items}>
<{/if}>
