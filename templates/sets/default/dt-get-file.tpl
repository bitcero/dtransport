<{include file="db:dt-header.tpl"}>

<div class="media get-file-header">
    <div class="media-left">
        <{if $item.logo}>
            <img src="<{resize file=$item.logo w=100 h=100}>" alt="<{$item.name}>" class="media-object">
        <{else}>
            <img src="<{resize file=$item.image w=100 h=100}>" alt="<{$item.name}>" class="media-object">
        <{/if}>
    </div>
    <div class="media-body">
        <h1 class="media-heading">
            <{$dtLang.headerTitle}>
        </h1>
        <div class="item-data">
            <{$dtLang.version}> <small>|</small>
            <{$dtLang.size}> <small>|</small>
            <{$dtLang.fileName}>
        </div>
    </div>
    <div class="media-right">
        <{cuIcon icon=svg-rmcommon-spinner-02}>
    </div>
</div>

<div id="will-start">
    <span class="message"><{$dtLang.message}></span>
    <span class="problems"><{$dtLang.problems}></span>
</div>