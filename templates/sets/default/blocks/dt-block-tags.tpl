<div class="dt-block-tags">
    <{foreach item=tag from=$block.tags}>
        <a href="<{$tag.link}>" style="font-size: <{$tag.size}>px;"><{$tag.tag}></a>
    <{/foreach}>
</div>