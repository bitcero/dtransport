<ul class="dt-block-links">
    <{foreach item=link from=$block.links}>
    <li>
        <a href="<{$link.link}>">
            <{cuIcon icon=$link.icon}>
            <span class="caption"><{$link.title}></span>
        </a>
    </li>
    <{/foreach}>
</ul>