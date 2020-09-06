<div class="dt-categories-block">
    <{if $block.parent}>
        <h5>
            <a href="<{$block.parent.link}>">
                <{cuIcon icon=svg-rmcommon-folder}>
                <{$block.parent.name}></a>
        </h5>
    <{/if}>
    <ul>
        <{foreach item=cat from=$block.categories}>
            <li style="padding-left: <{$cat.jumps*10}>px;"><a href="<{$cat.link}>"><{$cat.name}></a></li>
        <{/foreach}>
    </ul>
</div>